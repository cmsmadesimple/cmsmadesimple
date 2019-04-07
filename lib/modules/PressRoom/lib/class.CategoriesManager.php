<?php
namespace PressRoom;
use PressRoom;
use CMSMS\Database\Connection as Database;
use cms_cache_driver;

class CategoriesManager
{

    private $db;

    private $mod;

    private $cache_driver;

    public function __construct( Database $db, PressRoom $mod, cms_cache_driver $driver = null )
    {
        $this->db = $db;
        $this->mod = $mod;
        $this->cache_driver = $driver;
    }

    public function hasChildren( int $catid )
    {
        // does not use cache (todo)
        $sql = 'SELECT * FROM '.self::table_name().' WHERE parent_id = ?';
        $tmp = $this->db->GetOne( $sql, $catid );
        if( $tmp ) return true;
    }

    public function createNew( array $in = null ) : Category
    {
        if( is_null($in) ) $in = [];
        return Category::from_row( $in );
    }

    /**
     * @internal
     */
    public function loadAllArray()
    {
        // does not use cache
        $sql = 'SELECT * FROM '.self::table_name().' ORDER BY hierarchy';
        $list = $this->db->GetArray( $sql );
        return $list;
    }

    public function loadTree( int $from = -1 )
    {
        // does not use cache
        if( $from < 1 ) $from = -1;

        $allArray = $this->loadAllArray();
        if( !is_array($allArray) || !count($allArray) ) return;

        $tree = $this->_arrayToTree( $allArray, $from );
        return $tree;
    }

    protected function getFromCacheByAlias(string $alias)
    {
        if( !$this->cache_driver ) return;
        if( !$this->cache_driver->exists('alias_map',__CLASS__) ) return;

        $map = $this->cache_driver->get('alias_map',__CLASS__);
        if( !$map || !isset($map[$alias] ) ) return;

        $id = $map[$alias];
        return $this->loadByID($id);
    }

    protected function getFromCacheByID(int $id)
    {
        if( !$this->cache_driver ) return;
        if( !$this->cache_driver->exists( $id, __CLASS__) ) return;
        return $this->cache_driver->get( $id, __CLASS__);
    }

    public function cacheCategory( Category $obj )
    {
        if( !$this->cache_driver ) return;
        if( $obj->alias ) {
            $map = $this->cache_driver->get('alias_map',__CLASS__);
            if( !$map ) $map = [];
            $map[$obj->alias] = $obj->id;
            $this->cache_driver->set('alias_map',$map,__CLASS__);
        }
        $this->cache_driver->set($obj->id, $obj, __CLASS__);
    }

    public function loadAll()
    {
        $list = $this->loadAllArray();
        if( empty($list) ) return;

        $out = null;
        foreach( $list as $row ) {
            $obj = Category::from_row( $row );
            $this->cacheCategory($obj);
            $out[] = $obj;
        }
        return $out;
    }

    public function loadByAlias( string $alias )
    {
        $obj = $this->getFromCacheByAlias($alias);
        if( $obj ) return $obj;

        $sql = 'SELECT * FROM '.self::table_name().' WHERE alias = ?';
        $row = $this->db->GetRow( $sql, $alias );
        if( !$row ) return;

        $obj = Category::from_row( $row );
        $this->cacheCategory($obj);
        return $obj;
    }

    public function loadByID( int $id )
    {
        $obj = $this->getFromCacheByID($id);
        if( $obj ) return $obj;

        $sql = 'SELECT * FROM '.self::table_name().' WHERE id = ?';
        $row = $this->db->GetRow( $sql, $id );
        if( !$row ) return;

        $obj = Category::from_row( $row );
        $this->cacheCategory( $obj );
        return $obj;
    }

    public function getCategoryList( array $prepend = null )
    {
        // does not use cache
        $all = $this->loadAllArray();
        if( empty($all) ) return;

        $out = $prepend;
        foreach( $all as $row ) {
            $tmp = explode( '.', $row['hierarchy'] );
            $depth = count($tmp) - 1;
            $str = str_repeat('  >',$depth) . ' ' . $row['name'];
            if( $row['alias'] ) $str =  $str . " ({$row['alias']}) ";
            $out[$row['id']] = $str;
        }
        return $out;
    }

    protected function _insert( Category $obj )
    {
        // check that this alias is not yet used
        if( $obj->alias ) {
            $sql = 'SELECT id FROM '.self::table_name().' WHERE alias = ?';
            $id = $this->db->GetOne( $sql, $obj->alias );
            if( $id > 0 ) throw new \RuntimeException( $this->mod->Lang('err_categoryalias_exists') );
        }

        // check that this name is not used with this parent
        $sql = 'SELECT id FROM '.self::table_name().' WHERE parent_id = ? AND name = ?';
        $tmp = $this->db->GetOne( $sql, [ $obj->parent_id, $obj->name ]);
        if( $tmp > 0 ) throw new \RuntimeException( $this->mod->Lang('err_categoryname_existsatparent') );

        // get the item order for this parent, or 1
        $sql = 'SELECT COALESCE(MAX(item_order),0) FROM '.self::table_name().' WHERE parent_id = ?';
        $item_order = $this->db->GetOne( $sql, $obj->parent_id ) + 1;

        // and insert the bugger
        $sql = 'INSERT INTO '.self::table_name().' (name, alias, image_url, parent_id, detailpage, item_order) VALUES (?,?,?,?,?,?)';
        $this->db->Execute( $sql, [ $obj->name, $obj->alias ?? null, $obj->image_url, $obj->parent_id, $obj->detailpage, $item_order] );
        return $this->db->Insert_ID();
    }

    protected function _update( Category $obj )
    {
        // check that this alias is not yet used
        if( $obj->alias ) {
            $sql = 'SELECT id FROM '.self::table_name().' WHERE alias = ? AND id != ?';
            $tmp = $this->db->GetOne( $sql, $obj->alias, $obj->id );
            if( $tmp > 0 ) throw new \RuntimeException( $this->mod->Lang('err_categoryalias_exists') );
        }

        // check that this name is not used with this parent
        $sql = 'SELECT id FROM '.self::table_name().' WHERE parent_id = ? AND name = ? AND id != ?';
        $tmp = $this->db->GetOne( $sql, [ $obj->parent_id, $obj->name, $obj->id ] );
        if( $tmp > 0 ) throw new \RuntimeException( $this->mod->Lang('err_categoryname_existsatparent') );

        // get the old parent id for this item
        $sql = 'SELECT parent_id,item_order FROM '.self::table_name().' WHERE id = ?';
        $row = $this->db->GetRow( $sql, $obj->id );
        $old_parent_id = $row['parent_id'];
        $old_item_order = $row['item_order'];
        $item_order = $obj->item_order;

        if( $obj->parent_id != $old_parent_id ) {
            // reparenting
            // get the new item order
            $sql = 'SELECT COALESCE(MAX(item_order),0) FROM '.self::table_name().' WHERE parent_id = ?';
            $item_order = $this->db->GetOne( $sql, $obj->parent_id ) + 1;

            // adjust the item_orders from the old parent
            $sql = 'UPDATE '.self::table_name().' SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?';
            $this->db->Execute( $sql, [ $old_parent_id, $old_item_order ]);
        }

        // update the record, and clear the hierarchy and longname stuff
        $sql = 'UPDATE '.self::table_name().' SET name = ?, alias = ?, image_url = ?, parent_id = ?, detailpage = ?, item_order = ?,
                       hierarchy = null, long_name = null
                WHERE id = ? ORDER BY parent_id';
        $this->db->Execute( $sql, [ $obj->name, $obj->alias ?? null, $obj->image_url, $obj->parent_id, $obj->detailpage, $item_order , $obj->id ] );
        return $obj->id;
    }

    protected function _updateRow( array $row )
    {
        $sql = 'UPDATE '.self::table_name().' SET hierarchy = ?, long_name = ? WHERE id = ?';
        $this->db->Execute( $sql, [ $row['hierarchy'], $row['long_name'], $row['id'] ] );
    }

    protected function _arrayToTree( $flat, $parent_id = -1 )
    {
        // build arrays of rows grouped by parent id.
        $grouped = [];
        foreach( $flat as $row ) {
            $grouped[$row['parent_id']][] = $row;
        }

        $fnBuilder = function( $siblings ) use (&$fnBuilder, $grouped) {
            foreach( $siblings as $key => $sibling ) {
                $id = $sibling['id'];
                if( isset($grouped[$id]) ) {
                    // sibling is itself a parent
                    $sibling['children'] = $fnBuilder( $grouped[$id] );
                }
                $siblings[$key] = $sibling;
            }
            return $siblings;
        };

        $tree = $fnBuilder( $grouped[$parent_id] );
        return $tree;
    }

    public function updateHierarchyPositions()
    {
        // load them all
        $all = $this->loadAllArray();
        // convert to tree
        $tree = $this->_arrayToTree( $all );
        unset( $all );

        $walkTreeTD = function( array &$tree, Callable $fn, $depth = 0, $parent_hier = null, $parent_long = null ) use (&$walkTreeTD) {
            foreach( $tree as &$node ) {
                $node = $fn( $node, $parent_hier, $parent_long );
                // calculate hierarchy and long name
                if( !empty($node['children']) ) $walkTreeTD( $node['children'], $fn, $depth + 1, $node['hierarchy'], $node['long_name'] );
            }
        };

        $walkTreeTD( $tree, function( $node, $parent_hier, $parent_long ) {
                $node['hierarchy'] = ($parent_hier) ? $parent_hier . '.' . $node['item_order'] : $node['item_order'];
                $node['long_name'] = ($parent_long) ? $parent_long . ' | ' . $node['name'] : $node['name'];
                // save this thing.
                // $obj = Category::from_row( $node );
                // $this->save( $obj );
                return $node;
        });

        // now save the damned thing.
        $this->db->StartTrans();
        $walkTreeTD( $tree, function( $node ){
                $this->_updateRow( $node );
                return $node;
        });
        $this->db->CompleteTrans();
        if( $this->cache_driver ) $this->cache_driver->clear(__CLASS__);
    }

    public function delete( Category $obj )
    {
        if( !$obj->id ) throw new \LogicException('Cannot delete a category with no id');
        if( $this->hasChildren( $obj->id ) ) throw new \RuntimeException( $this->mod->Lang('err_del_category_children') );

        $sql = 'UPDATE '.self::articles_table().' SET category_id = null where category_id = ?';
        $this->db->Execute( $sql, $obj->id );

        $sql = 'DELETE FROM '.self::table_name().' WHERE id = ?';
        $this->db->Execute( $sql, $obj->id );

        $this->updateHierarchyPositions();
        if( $this->cache_driver ) $this->cache_driver->clear(__CLASS__);
    }

    public function save( Category $obj )
    {
        $id = $obj->id;
        if( $obj->id > 0 ) {
            $this->_update( $obj );
        } else {
            $id = $this->_insert( $obj );
        }

        $this->updateHierarchyPositions();
        if( $this->cache_driver ) $this->cache_driver->clear(__CLASS__);
        return $id;
    }

    public function get_detailpage_for_category( int $category_id )
    {
        while( $category_id > 0 ) {
            $cat = $this->loadByID( $category_id );
            if( $cat->detailpage > 0 ) return $cat->detailpage;
            $category_id = $cat->parent_id;
        }
    }

    public static function articles_table()
    {
        return CMS_DB_PREFIX.'mod_pressroom_articles';
    }

    public static function table_name()
    {
        return CMS_DB_PREFIX.'mod_pressroom_categories';
    }

} // class
