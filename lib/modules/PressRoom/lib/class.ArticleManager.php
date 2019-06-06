<?php
namespace PressRoom;
use PressRoom;
use CMSMS\Database\Connection as Database;
use cms_route_manager;
use cms_cache_driver;
use CmsRoute;

class ArticleManager
{

    private $db;

    private $_cache; // cache of loaded articles for this request

    private $mod;

    private $fdmgr;

    private $catm;

    private $cache_driver;

    public function __construct( Database $db, PressRoom $mod, FieldDefManager $fdmgr, CategoriesManager $catm, cms_cache_driver $driver = null )
    {
        $this->db = $db;
        $this->mod = $mod;
        $this->fdmgr = $fdmgr;
        $this->catm = $catm;
        $this->cache_driver = $driver;
    }

    public function createNew( array $opts = null )
    {
        if( !is_array($opts) ) $opts = [];
        return Article::from_row( $opts );
    }

    public function createFilter( array $opts = null ) : ArticleFilter
    {
        if( is_null($opts) ) $opts = [];
        return ArticleFilter::from_row( $opts );
    }

    public function countPublishedArticlesByCategory( bool $usetimeperiod = true )
    {
        $sql = 'SELECT category_id,COUNT(id) AS cnt
                  FROM '.self::news_table_name().'
                 WHERE status = ?';
        $parms = [ Article::STATUS_PUBLISHED ];
        if( $timeperiod ) {
            $sql .= ' AND COALESCE(0,start_time) < UNIX_TIMESTAMP()';
            $sql .= ' AND COALESCE(end_time,UNIX_TIMESTAMP()+3600) > UNIX_TIMESTAMP()';
        }
        $sql .= ' GROUP BY category_id';
        $list = $this->db->GetArray( $sql, [ $parms ] );
        if( !is_array($list) || !count($list) ) return;

        $out = null;
        foreach( $list as $row ) {
            $out[$row['category_id']] = $row['cnt'];
        }
        return $out;
    }

    public function urlSlugExists( string $slug, int $articleid = null ) : bool
    {
        if( !$slug ) throw new \InvalidArgumentException( 'Invalid slug specified in '.__METHOD__);
        $sql = 'SELECT id FROM '.self::news_table().' WHERE url_slug = ?';
        $parms = [ $slug ];
        if( $articleid > 0 ) {
            $sql .= ' AND id != ?';
            $parms[] = $articleid;
        }
        $test = $this->db->GetOne( $sql, $parms );
        if( $test > 0 ) return true;

        cms_route_manager::load_routes();
        $route = cms_route_manager::find_match( $slug, TRUE );
        if( $route ) {
            // route exists in the route database, just confirm it is not for this module/article.
            if( $route['key1'] != __NAMESPACE__ ) return true;
            if( $articleid < 1 ) return true;
            $defaults = $route->get_defaults();
            if( !isset($defaults['article']) || $defaults['article'] != $articleid ) {
                return true;
            }
        }

        return false;
    }

    protected function clearInternalCache()
    {
        if( $this->cache_driver ) $this->cache_driver->clear(__CLASS__);
        $this->_cache = null;
    }

    protected function getFromInternalCache( $article_id )
    {
        if( is_array($this->_cache) && isset($this->_cache[$article_id]) ) return $this->_cache[$article_id];
        if( $this->cache_driver && $this->cache_driver->exists( $article_id, __CLASS__) ) {
            return $this->cache_driver->get($article_id, __CLASS__);
        }
    }

    protected function storeToInternalCache( Article $article )
    {
        if( $article->id < 1 ) throw new \LogicException('Invalid article passed to '.__METHOD__);
        $this->_cache[$article->id] = $article;
        if( $this->cache_driver ) $this->cache_driver->set( $article->id, $article, __CLASS__ );
    }

    protected function loadFieldsForArticle( int $id )
    {
        if( $id < 1 ) throw new \InvalidArgumentException('Invalid id passed to '.__METHOD__);

        $out = null;
        $sql = 'SELECT * FROM '.self::fieldvals_table().' WHERE news_id = ?';
        $list = $this->db->GetArray( $sql, $id );
        if( is_array($list) && count($list) ) {
            foreach( $list as $frow ) {
                $id = $frow['news_id'];
                $val = $frow['value'];
                $tmp = json_decode( $val );
                if( $tmp ) $val = $tmp;
                $fd = $this->fdmgr->loadByID( $frow['fielddef_id']);
                if( !$fd ) continue;
                $out[$fd->name] = $val;
            }
        }
        return $out;
    }

    public function loadByID( int $id )
    {
        if( $id < 1 ) throw new \InvalidArgumentException('Invalid id passed to '.__METHOD__);
        if( ($article = $this->getFromInternalCache( $id ) ) ) return $article;

        $sql = 'SELECT * FROM '.self::news_table().' WHERE id = ?';
        $row = $this->db->GetRow( $sql, $id );
        if( !$row ) return;

        $fields = $this->loadFieldsForArticle( $id );
        if( $fields ) $row['fields'] = $fields;

        $article = Article::from_row( $row );
        $this->storeToInternalCache( $article );
        return $article;
    }

    public function loadArticlesWithURLSlug( int $offset )
    {
        // loads all articles with a url slug
        // does not populate cache, and does not load fields
        $sql = 'SELECT * FROM '.self::news_table().' WHERE status = ? AND url_slug IS NOT NULL';
        $rs = $this->db->SelectLimit( $sql, 250, $offset, [ Article::STATUS_PUBLISHED ] );

        $out = null;
        while( $rs && !$rs->EOF() ) {
            $out[] = Article::from_row( $rs->fields );
            $rs->MoveNext();
        }
        return $out;
    }

    public function loadFirstAvailableAfter( Article $in )
    {
        // used only for the {news_nextpublished} plugin.
        $sql = 'SELECT * FROM '.self::news_table().'
                WHERE status = ?
                  AND news_date >= ?
                  AND COALESCE(start_time,0) < UNIX_TIMESTAMP()
                  AND COALESCE(end_time,UNIX_TIMESTAMP()+3600) > UNIX_TIMESTAMP()
                  AND id != ?
                ORDER BY news_date ASC, id ASC';
        $row = $this->db->GetRow( $sql, [ Article::STATUS_PUBLISHED, $in->news_date, $in->id ] );
        if( !$row ) return;

        $fields = $this->loadFieldsForArticle( $row['id'] );
        if( $fields ) $row['fields'] = $fields;

        $article = Article::from_row( $row );
        $this->storeToInternalCache( $article );
        return $article;
    }

    public function loadLastAvailableBefore( Article $in )
    {
        // does not populate cache, does not load fields.
        // used only for the {news_nextpublished} plugin.
        $sql = 'SELECT * FROM '.self::news_table().'
                WHERE status = ?
                  AND news_date <= ?
                  AND COALESCE(start_time,0) < UNIX_TIMESTAMP()
                  AND COALESCE(end_time,UNIX_TIMESTAMP()+3600) > UNIX_TIMESTAMP()
                  AND id != ?
                ORDER BY news_date DESC, id DESC';
        $row = $this->db->GetRow( $sql, [ Article::STATUS_PUBLISHED, $in->news_date, $in->id ] );
        if( !$row ) return;

        $fields = $this->loadFieldsForArticle( $row['id'] );
        if( $fields ) $row['fields'] = $fields;

        $article = Article::from_row( $row );
        $this->storeToInternalCache( $article );
        return $article;
    }

    protected function forceLoadByIDList( array $idlist )
    {
        // preload fields for these articles
        $sql = 'SELECT * FROM '.self::fieldvals_table().' WHERE news_id IN ('.implode(',',$idlist).') ORDER BY news_id,fielddef_id';
        $tmp = $this->db->GetArray( $sql );
        $fieldvals = null;
        if( is_array($tmp) && count($tmp) ) {
            foreach( $tmp as $row ) {
                $id = $row['news_id'];
                $val = $row['value'];
                $tmp = json_decode( $row['value'] );
                if( $tmp ) $val = $tmp;
                $fd = $this->fdmgr->loadByID( $row['fielddef_id']);
                if( !$fd ) continue;
                $fieldvals[$id][$fd->name] = $val;
            }
        }

        // load all the rows for this idlist
        $sql = 'SELECT * FROM '.self::news_table().' WHERE id IN ('.implode(',',$idlist).')';
        $list = $this->db->GetArray( $sql );

        // put the shit together.
        for( $i = 0, $n = count($list); $i < $n; $i++ ) {
            $id = $list[$i]['id'];
            if( isset($fieldvals[$id]) ) $list[$i]['fields'] = $fieldvals[$id];
        }
        unset($fieldvals);

        // sort by the order specified in the idlist
        usort($list,function( $a, $b) use ($idlist){
                $pos_a = array_search( $a['id'], $idlist);
                $pos_b = array_search( $b['id'], $idlist);
                return $pos_a - $pos_b;
        });

        // build objects
        $out = null;
        foreach( $list as $row ) {
            $article = Article::from_row( $row );
            $this->storeToInternalCache( $article );
            $out[$article->id] = $article;
        }
        return $out;
    }

    public function loadByIDList( array $idlist )
    {
        if( empty($idlist) ) return;

        // only load the articles that are not already in the cache
        $loaded = $precached = [];
        foreach($idlist as $one) {
            $article = $this->getFromInternalCache( $one );
            if( $article ) $precached[$one] = $article;
        }
        $mustload = array_diff($idlist,array_keys($precached));
        if( !empty($mustload) ) $loaded = $this->forceLoadByIDList($mustload);

        $out = null;
        foreach( $idlist as $one ) {
            if( isset($precached[$one]) ) {
                $out[] = $precached[$one];
            }
            else if( isset($loaded[$one]) ) {
                $out[] = $loaded[$one];
            }
            else {
                // could not find this article for some reason.
            }
        }
        return $out;
    }


    /**
     * Takes an article filter, and returns an SQL string and an array of paramters as an array
     *
     * @internal
     */
    protected function filterToSQL( ArticleFilter $filter ) : array
    {
        $wildcard = function( string $in ) {
            // if a string has % characters, but not %% characters
            if( strpos( $in, '%') !== FALSE ) $in = str_replace( '%', '\%', $in );
            if( strpos( $in, '_') !== FALSE ) $in = str_replace( '_', '\_', $in );
            if( strpos( $in, '*') !== FALSE ) {
                // wildcards already included
                $in = str_replace( '*', '%', $in );
            }
            else {
                $in = '%'.$in.'%';
            }
            return $in;
        };

        // get the article id list and total rows
        $sql = 'SELECT SQL_CALC_FOUND_ROWS A.id FROM '.self::news_table().' A';
        $parms = $where = $joins = [];
        if( $filter->sortby == $filter::SORT_FIELD && $filter->sortdata ) {
            $fielddef = $this->fdmgr->loadByName( $filter->sortdata );
            if( $fielddef ) {
                $fdid = $fielddef->id;
                $joins[] = self::fieldvals_table()." F ON F.news_id = A.id AND F.fielddef_id = $fdid";
            }
        }
        if( $filter->id_list ) {
            $where[] = 'A.id IN ('.implode(',',$filter->id_list).')';
        }
        if( $filter->status ) {
            $where[] = 'A.status = ?';
            $parms[] = $filter->status;
        }
        if( $filter->author_id > 0 ) {
            $where[] = 'A.author_id = ?';
            $parms[] = $filter->author_id;
        }
        if( $filter->title_substr ) {
            $where[] = 'A.title LIKE ?';
            $parms[] = $wildcard( $filter->title_substr );
        }
        else if( $filter->textmatch ) {
            $or = null;
            $str = $wildcard( $filter->textmatch );
            $or[] = 'A.title LIKE ?';
            $or[] = 'A.summary LIKE ?';
            $or[] = 'A.content LIKE ?';
            $orparms = [ $str, $str, $str ];
            if( $filter->usefields ) {
                $fielddefs = $this->fdmgr->loadAll();
	        if( !empty($fielddefs) ) {
                    for( $i = 0; $i < count($fielddefs); $i++ ) {
                        $fdid = $fielddefs[$i]->id;
                        $tmp = 'FV'.$i;
                        // $fields[] = "$tmp.value";
                        $joins[] = self::fieldvals_table()." $tmp ON A.id = $tmp.news_id AND $tmp.fielddef_id = $fdid";
                        $or[] = "$tmp.value LIKE ?";
                        $orparms[] = $str;
                    }
	        }
            }
            $where[] = '('.implode(' OR ',$or).')';
            foreach( $orparms as $one ) {
                $parms[] = $one;
            }
        }

        switch( $filter->useperiod ) {
            case -1: // no period filtering (anything)
                break;
            case 2: // started articles, independent of end time.
                $where[] = 'COALESCE(A.start_time,0) < UNIX_TIMESTAMP()';
                break;
            case 3: // expired articles only.
                $where[] = 'A.end_time IS NOT NULL AND A.end_time < UNIX_TIMESTAMP()';
                break;
            case 4: // unstarted articles.
                $where[] = 'A.start_time IS NOT NULL AND A.start_time >= UNIX_TIMESTAMP()';
                break;
            case 5: // articles with no start time or end time
                $where[] = 'A.start_time IS NULL AND A.end_time IS NULL';
                break;
            case 1:  // articles that either have no period, or within the time window to display.
            default:
                $where[] = 'COALESCE(A.start_time,0) < UNIX_TIMESTAMP()';
                $where[] = 'COALESCE(A.end_time,UNIX_TIMESTAMP()+3600) > UNIX_TIMESTAMP()';
                break;
        }
        if( $filter->category_id > 0 ) {
            if( $filter->withchildren ) {
                $cat = $this->catm->loadByID( $filter->category_id );
                $joins[] = $this->catm->table_name().' B ON B.id = A.category_id';
                $where[] = 'upper(B.long_name) LIKE ?';
                $parms[] = strtoupper($cat->long_name.'%');
            }
            else {
                $where[] = 'A.category_id = ?';
                $parms[] = $filter->category_id;
            }
        }
        if( $filter->not_category_id > 0 ) {
            if( $filter->category_id < 1 || $filter->category_id != $filter->not_category_id ) {
                if( $filter->withchildren ) {
                    $cat = $this->catm->loadByID( $filter->not_category_id );
                    $joins[] = $this->catm->table_name().' C ON C.id = A.category_id';
                    $where[] = 'upper(C.long_name) NOT LIKE ?';
                    $parms[] = strtoupper($cat->long_name.'%');
                }
                else {
                    $where[] = 'A.category_id != ?';
                    $parms[] = $filter->not_category_id;
                }
            }
        }
        if( !is_null($filter->searchable) && !empty($filter->searchable) ) {
            $where[] = 'A.searchable = ?';
            $parms[] = $filter->searchable;
        }

        // put everything together.
        if( count($joins) ) $sql .= ' LEFT JOIN '.implode(' LEFT JOIN ',$joins);
        if( count($where) ) $sql .= ' WHERE '.implode(' AND ',$where);
        switch( $filter->sortby ) {
            case $filter::SORT_TITLE:
                $sql .= ' ORDER BY A.title';
                break;
            case $filter::SORT_STATUS:
                $sql .= ' ORDER BY A.status';
                break;
            case $filter::SORT_FIELD:
                $sql .= ' ORDER BY F.value';
                break;
            case $filter::SORT_NEWSDATE:
                $sql .= ' ORDER BY A.news_date';
                break;
            case $filter::SORT_CREATEDATE:
                $sql .= ' ORDER BY A.create_date';
                break;
            case $filter::SORT_STARTDATE:
                $sql .= ' ORDER BY A.start_time';
                break;
            case $filter::SORT_ENDDATE:
                $sql .= ' ORDER BY A.end_time';
                break;
            case $filter::SORT_MODIFIEDDATE:
            default:
                $sql .= ' ORDER BY COALESCE(A.modified_date,A.create_date)';
                break;
        }
        $sql .= ' '.$filter->sortorder;
        return [ $sql, $parms ];
    }

    public function loadByFilter( ArticleFilter $filter )
    {
        $idlist = $total_rows = $cache_key1 = $cachekey_2 = null;
        if( $this->cache_driver ) {
            $cache_key1 = md5('filter_idlist'.__FILE__.serialize($filter));
            $cache_key2 = md5('filter_idlist2'.__FILE__.serialize($filter));
            if( $this->cache_driver->exists($cache_key1,__CLASS__) ) {
                $idlist = $this->cache_driver->get($cache_key1,__CLASS__);
                $total_rows = $this->cache_driver->get($cache_key2,__CLASS__);
            }
        }

        if( !$idlist ) {
            // master idlist not cached
            // remove pagination from the filter
            $newfilter = $filter->resetPagination();
            // and get a master idlist.
            list( $sql, $parms ) = $this->filterToSQL( $newfilter );
            $qry = $this->db->SelectLimit( $sql, $newfilter->limit, $newfilter->offset, $parms );
            while( $qry && !$qry->EOF() ) {
                $row = $qry->fields;
                $idlist[] = (int) $row['id'];
                $qry->MoveNext();
            }

            $total_rows = (int) $this->db->GetOne( 'SELECT FOUND_ROWS()' );

            if( $cache_key1 ) {
                $this->cache_driver->set($cache_key1,$idlist,__CLASS__);
                $this->cache_driver->set($cache_key2,$total_rows,__CLASS__);
            }
        }

        // if we have limit and offset, slice up the idlist
        if( !empty($idlist) && ($filter->limit > 0 || $filter->offset > 0) ) {
            $idlist = array_slice($idlist,$filter->offset,$filter->limit);
        }

        $out = null;
        if( $idlist ) $out = $this->loadByIDList( $idlist );
        if( !$out ) $out = [];
        $obj = new ArticleSet( $filter, $total_rows, $out );
        return $obj;
    }

    protected function _update( Article $article )
    {
        $sql = 'UPDATE '.self::news_table().'
                SET category_id = ?, title = ?, summary = ?, content = ?, news_date = ?, start_time = ?, end_time = ?, status = ?,
                    modified_date = UNIX_TIMESTAMP(), author_id = ?, url_slug = ?, searchable = ?
                WHERE id = ?';
        $this->db->Execute( $sql,
                            [ $article->category_id, $article->title, $article->summary, $article->content,
                              $article->news_date, $article->start_time, $article->end_time, $article->status, $article->author_id,
                              $article->url_slug, $article->searchable, $article->id
                            ]);

        $sql = 'DELETE FROM '.self::fieldvals_table().' WHERE news_id = ?';
        $this->db->Execute( $sql, $article->id );

        $sql = 'INSERT INTO '.self::fieldvals_table().' (news_id, fielddef_id, value) VALUES (?,?,?)';
        if( is_array($article->fields) && count($article->fields) ) {
            foreach( $article->fields as $key => $val ) {
                $fd = $this->fdmgr->loadByName( $key );
                if( !$fd ) continue;
                if( is_array($val) ) $val = json_encode($val);
                if( !$val && strlen($val) == 0 ) continue;
                $this->db->Execute( $sql, [ $article->id, $fd->id, $val ] );
            }
        }

        return $article->id;
    }

    protected function _insert( Article $article )
    {
        $now = time();
        $sql = 'INSERT INTO '.self::news_table() . '
                (category_id, title, summary, content, news_date, start_time, end_time, status, create_date, modified_date,
                 author_id, extra, url_slug, searchable) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $this->db->Execute( $sql,
                            [ $article->category_id, $article->title, $article->summary, $article->content,
                              $article->news_date, $article->start_time, $article->end_time, $article->status, $now, null,
                              $article->author_id, null, $article->url_slug, $article->searchable
                            ]);
        $new_id = $this->db->Insert_ID();

        // and now do field values
        if( is_array($article->fields) && count($article->fields) ) {
            $sql = 'INSERT INTO '.self::fieldvals_table().' (news_id, fielddef_id, value) VALUES (?,?,?)';
            foreach( $article->fields as $key => $val ) {
                $fd = $this->fdmgr->loadByName( $key );
                if( !$fd ) continue;
                if( is_array($val) ) $val = json_encode($val);
                if( empty($val) ) continue;
                $this->db->Execute( $sql, [ $new_id, $fd->id, $val ] );
            }
        }

        return $new_id;
    }

    public function registerRouteForArticle( Article $article, int $detailpage )
    {
        if( !$article->id ) return;
        if( !$article->url_slug ) return;
        if( $article->status != $article::STATUS_PUBLISHED ) return;
        $article_id = $article->id;

        $parms = [ 'action'=>'detail', 'returnid'=>$detailpage, 'article'=>$article_id ];
        cms_route_manager::del_static('',__NAMESPACE__,$article_id);
        $route = CmsRoute::new_builder( $article->url_slug, __NAMESPACE__, $article_id, $parms, TRUE );
        cms_route_manager::add_static( $route );
    }

    public function save( Article $article ) : int
    {
        try {
            $this->db->BeginTrans();

            $article_id = null;
            if( $article->id < 1 ) {
                $article_id = $this->_insert( $article );
            } else {
                $article_id = $this->_update( $article );
            }

            if( $article->url_slug && $article->status == Article::STATUS_PUBLISHED ) {
                $detailpage = $this->mod->getDefaultDetailPage();
                if( $detailpage ) {
                    $parms = [ 'action'=>'detail', 'returnid'=>$detailpage, 'article'=>$article_id ];
                    cms_route_manager::del_static('',__NAMESPACE__,$article_id);
                    $route = CmsRoute::new_builder( $article->url_slug, __NAMESPACE__, $article_id, $parms, TRUE );
                    cms_route_manager::add_static( $route );
                }
            }

            $this->db->CompleteTrans();
            $this->clearInternalCache();
            return $article_id;
        }
        catch( \Exception $e ) {
            $this->db->CompleteTrans(false);
            throw $e;
        }
    }

    public function delete( Article $article )
    {
        if( $article->id < 1 ) throw new \InvalidArgumentException('You cannot delete an article that has not been saved');

        $this->db->StartTrans();

        $sql = 'DELETE FROm '.self::fieldvals_table().' WHERE news_id = ?';
        $this->db->Execute( $sql, $article->id );

        $sql = 'DELETE FROm '.self::news_table().' WHERE id = ?';
        $this->db->Execute( $sql, $article->id );

        $this->db->CompleteTrans();
        $this->clearInternalCache();

        if( $article->url_slug ) cms_route_manager::del_static('',__NAMESPACE__,$article->id);
    }

    public static function news_table()
    {
        return CMS_DB_PREFIX.'mod_pressroom_articles';
    }

    public static function fieldvals_table()
    {
        return CMS_DB_PREFIX.'mod_pressroom_fieldvals';
    }
} // class
