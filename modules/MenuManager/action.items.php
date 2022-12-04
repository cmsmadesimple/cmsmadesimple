<?php
if( !isset($gCms) ) exit;
if( !isset($params['items']) ) return;

$template = null;
if( isset($params['template']) ) {
    $template = trim($params['template']);
}
else {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('MenuManager::navigation');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default template found');
        return;
    }
    $template = $tpl->get_name();
}

$cache_id = '|ns'.md5(serialize($params));
$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template),$cache_id,null,$smarty);
if( !$tpl->isCached() ) {
    // init.
    $showparents = array();
    $nodelist = array();
    $origdepth = $prevdepth = 0;
    $count = 0;
    $deep = 1;
    $hm = $gCms->GetHierarchyManager();
    $origdepth = $prevdepth = 1;
    $cachefn = '';
    $cached = 0;

    //
    // setup
    //
    if( isset($params['loadprops']) && $params['loadprops'] == 0 )
        $deep = 0;
    $items = explode(',',$params['items']);
    if( count($items) <= 0 ) return;

    reset($items);
    for( $idx = 0; $idx < count($items); $idx++ ) {
        $key = trim($items[$idx]);
        $curnode = $hm->sureGetNodeByAlias($key);
        if( !$curnode ) continue;

        $content = $curnode->GetContent();
        if( !is_object($content) ) continue;
        if( !$content->Active() ) continue;

        // add this item to the nodelist
        $this->FillNode($content, $curnode, $nodelist, $count, $prevdepth, $origdepth, $deep);

        // only one node, correct the depth
        $mnode = $nodelist[count($nodelist)-1];
        $mnode->depth = 1;
        if( count($nodelist) > 1 ) {
            $mnode->prevdepth = $nodelist[count($nodelist)-2]->depth;
        }
        if( $idx == 0 ) {
            $mnode->first = 1;
        }
        else if( $idx == count($items) - 1 ) {
            $mnode->last = 1;
        }

        if( !isset($params['number_of_levels']) || ($params['number_of_levels'] > 1) ) {
            $ocount = $count;
            // we are getting some children.
            $this->GetChildNodes($curnode,$nodelist,$gCms,$prevdepth,$count,$params,$origdepth,$showparents,$deep);

            if( $count > $ocount ) {
                // there were children.
                $bdepth = $nodelist[$ocount]->depth;
                for( $i = $ocount; $i < count($nodelist); $i++ ) {
                    $nodelist[$i]->depth = $nodelist[$i]->depth - $bdepth + $mnode->depth + 1;;
                    $nodelist[$i]->prevdepth = $nodelist[$i-1]->depth;
                }
                $prevdepth = $nodelist[$i-1]->depth;
            }
        }
    } // for

    if( count($nodelist) > 0 ) {
        // pass it thru smarty.
        $tpl->assign('menuparams',$params);
        $tpl->assign('count',count($nodelist));
        $tpl->assign('nodelist',$nodelist);
    }
}

$tpl->display();

?>