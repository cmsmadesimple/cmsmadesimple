<?php
if (!isset($gCms)) exit;
use Search\SearchItemCollection;

/////////////////////////////////////////////////////////////////////////////////////

$hook_mgr = $this->cms->get_hook_manager();
$template = null;
if( isset($params['resulttemplate']) ) {
    $template = trim($params['resulttemplate']);
}
else {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('Search::searchresults');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default summary template found');
        return;
    }
    $template = $tpl->get_name();
}
$tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null);

$use_like = false;
if( isset($_REQUEST['use_like']) ) $use_like = cms_to_bool($_REQUEST['use_like']);
if( isset($params['use_like']) ) $use_like = cms_to_bool($params['use_like']);
$searchinput = null;
if( isset($_REQUEST['term']) ) $searchinput = filter_var($_REQUEST['term'],FILTER_SANITIZE_STRING);
if( isset($params['searchinput']) ) $searchinput = $params['searchinput'];
if( $searchinput ) {
    // Fix to prevent XSS like behaviour. See: http://www.securityfocus.com/archive/1/455417/30/0/threaded
    $searchinput = trim(strip_tags(cms_html_entity_decode($searchinput, ENT_COMPAT, 'UTF-8')));

    $searchstarttime = microtime(true);
    $filter_stopwords = true;  // if there are stopwords set, or default stop words... this will filter them.
    $do_stemming = cms_to_bool($this->GetPreference('usesstemming','false'));

    $tpl_ob->assign('phrase', $searchinput);
    $words = array_values($this->StemPhrase($searchinput,$filter_stopwords,$do_stemming));
    $words = $hook_mgr->emit('Search::SearchFilterWords', $words );
    $nb_words = count($words);
    $max_weight = 1;

    $searchphrase = '';
    if ($nb_words > 0) {
        #$searchphrase = implode(' OR ', array_fill(0, $nb_words, 'word = ?'));
        $ary = array();
        foreach ($words as $word) {
            $word = trim($word);
            if( $use_like && strpos($word,'%') === FALSE ) {
		$word = str_replace('_','\_',$word);
                $ary[] = 'word LIKE ' . $db->qstr($word.'%');
            } else {
                $ary[] = "word = " . $db->qstr($word);
            }
        }
        $searchphrase = implode(' OR ', $ary);

        // Update the search words table
        if( $this->GetPreference('savephrases','false') == 'false' ) {
            foreach( $words as $word ) {
                $q = 'SELECT count FROM '.CMS_DB_PREFIX.'module_search_words WHERE word = ?';
                $tmp = $db->GetOne($q,array($word));
                if( $tmp ) {
                    $q = 'UPDATE '.CMS_DB_PREFIX.'module_search_words SET count=count+1 WHERE word = ?';
                    $db->Execute($q,array($word));
                }
                else {
                    $q = 'INSERT INTO '.CMS_DB_PREFIX.'module_search_words (word,count) VALUES (?,1)';
                    $db->Execute($q,array($word));
                }
            }
        }
        else {
            $q = 'SELECT count FROM '.CMS_DB_PREFIX.'module_search_words WHERE word = ?';
            $tmp = $db->GetOne($q,array($searchinput));
            if( $tmp ) {
                $q = 'UPDATE '.CMS_DB_PREFIX.'module_search_words SET count=count+1 WHERE word = ?';
                $db->Execute($q,array($searchinput));
            }
            else {
                $q = 'INSERT INTO '.CMS_DB_PREFIX.'module_search_words (word,count) VALUES (?,1)';
                $db->Execute($q,array($searchinput));
            }
        }
    }

    // $val = 100 * 100 * 100 * 100 * 25;
    $query = "SELECT DISTINCT i.module_name, i.content_id, i.extra_attr, COUNT(*) AS nb, SUM(idx.count) AS total_weight FROM ".CMS_DB_PREFIX."module_search_items i INNER JOIN ".CMS_DB_PREFIX."module_search_index idx ON idx.item_id = i.id WHERE (".$searchphrase.") AND (COALESCE(i.expires,NOW()) >= NOW())";
    if( isset( $params['modules'] ) ) {
        $modules = explode(",",$params['modules']);
        for( $i = 0; $i < count($modules); $i++ ) {
            $modules[$i] = $db->qstr($modules[$i]);
        }
        $query .= ' AND i.module_name IN ('.implode(',',$modules).')';
    }
    $query .= " GROUP BY i.module_name, i.content_id, i.extra_attr";
    /*
    if( !isset($params['use_or']) || $params['use_or'] == 0 ) {
        //This makes it an AND query
        $query .= " HAVING nb >= $nb_words";
    }
    */
    $query .= " ORDER BY nb DESC, total_weight DESC";

    $result = $db->Execute($query);
    $hm = $gCms->GetHierarchyManager();
    $col = new SearchItemCollection();

    while ($result && !$result->EOF) {
        //Handle internal (templates, content, etc) first...
        if ($result->fields['module_name'] == $this->GetName()) {
            if ($result->fields['extra_attr'] == 'content') {
                //Content is easy... just grab it out of hierarchy manager and toss the url in
                $node = $hm->sureGetNodeById($result->fields['content_id']);
                if (isset($node)) {
                    $content = $node->GetContent();
                    if (isset($content) && $content->Active()) {
                        $col->AddItem($content->Name(), $content->GetURL(),
                                      $content->Name(), $result->fields['total_weight'].
                                      '_content_', $result->fields['content_id']);
                    }
                }
            }
        }
        else {
            $thepageid = $this->GetPreference('resultpage',-1);
            if( $thepageid == -1 ) $thepageid = $returnid;
            if( isset($params['detailpage']) ) {
                $tmppageid = '';
                $manager = $gCms->GetHierarchyManager();
                $node = $manager->sureGetNodeByAlias($params['detailpage']);
                if (isset($node)) {
                    $tmppageid = $node->getID();
                }
                else {
                    $node = $manager->sureGetNodeById($params['detailpage']);
                    if (isset($node)) $tmppageid= $params['detailpage'];
                }
                if( $tmppageid ) $thepageid = $tmppageid;
            }
            if( $thepageid == -1 ) $thepageid = $returnid;

            //Start looking at modules...
            $modulename = $result->fields['module_name'];
            $moduleobj = $this->GetModuleInstance($modulename);
            if ($moduleobj != FALSE) {
                if (method_exists($moduleobj, 'SearchResultWithParams' )) {
                    // search through the params, for all the passthru ones
                    // and get only the ones matching this module name
                    $parms = array();
                    foreach( $params as $key => $value ) {
                        $str = 'passthru_'.$modulename.'_';
                        if( preg_match( "/$str/", $key ) > 0 ) {
                            $name = substr($key,strlen($str));
                            if( $name != '' ) $parms[$name] = $value;
                        }
                    }
                    $searchresult = $moduleobj->SearchResultWithParams( $thepageid, $result->fields['content_id'],
                                                                        $result->fields['extra_attr'], $parms);
                    if (count($searchresult) == 3) {
                        $col->AddItem($searchresult[0], $searchresult[2], $searchresult[1],
                                      $result->fields['total_weight'], $modulename, $result->fields['content_id']);
                    }
                }
                else if (method_exists($moduleobj, 'SearchResult')) {
                    $searchresult = $moduleobj->SearchResult( $thepageid, $result->fields['content_id'], $result->fields['extra_attr']);
                    if (count($searchresult) == 3) {
                        $col->AddItem($searchresult[0], $searchresult[2], $searchresult[1],
                                      $result->fields['total_weight'], $modulename, $result->fields['content_id']);
                    }
                }
            }
        }

        $result->MoveNext();
    }

    $col->CalculateWeights();
    if ($this->GetPreference('alpharesults', 'false') == 'true') $col->Sort();

    // now we're gonna do some post processing on the results
    // and replace the search terms with <span class="searchhilite">term</span>

    $results = $col->_ary;
    $newresults = array();
    foreach( $results as $result ) {
        $title = cms_htmlentities($result->title);
        $txt = cms_htmlentities($result->urltxt);
        foreach( $words as $word ) {
            $word = preg_quote($word);
            $title = preg_replace('/\b('.$word.')\b/i', '<span class="searchhilite">$1</span>', $title);
            $txt = preg_replace('/\b('.$word.')\b/i', '<span class="searchhilite">$1</span>', $txt);
        }
        $result->title = $title;
        $result->urltxt = $txt;
        $newresults[] = $result;
    }
    $col->_ary = $newresults;

    $hook_mgr->emit( 'Search::SearchCompleted', [ &$searchinput, &$col->_ary ] );

    $tpl_ob->assign('searchwords',$words);
    $tpl_ob->assign('results', $col->_ary);
    $tpl_ob->assign('itemcount', (!empty($col->ary)) ? count($col->_ary) : 0);

    $searchendtime = microtime(true);
    $tpl_ob->assign('timetook', ($searchendtime - $searchstarttime));
}
else {
    $tpl_ob->assign('phrase', '');
    $tpl_ob->assign('results', 0);
    $tpl_ob->assign('itemcount', 0);
    $tpl_ob->assign('timetook', 0);
}

$tpl_ob->assign('use_or_text',$this->Lang('use_or'));
$tpl_ob->assign('searchresultsfor', $this->Lang('searchresultsfor'));
$tpl_ob->assign('noresultsfound', $this->Lang('noresultsfound'));
$tpl_ob->assign('timetaken', $this->Lang('timetaken'));
$tpl_ob->display();
