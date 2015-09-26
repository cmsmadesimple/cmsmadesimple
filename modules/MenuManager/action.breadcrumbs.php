<?php
if (!isset($gCms)) exit;
debug_buffer('', 'Start of BreadCrumbs Display');

$template = 'breadcrumbs.tpl';
if( isset($params['template']) ) $template = trim($params['template']);
$content_obj = $gCms->get_content_object();
if( !$content_obj ) return; //  no current page?

$cache_id = '|nav'.md5(serialize($params));
$tpl = $smarty->createTemplate($this->GetTemplateResource($template),$cache_id,null,$smarty->get_template_parent());
if( !$tpl->isCached() ) {
    //
    // initialization
    //
    $hm = $gCms->GetHierarchyManager();
    $thispageid = $content_obj->Id();
    if( !$thispageid ) return; // no current page?
    $endNode = $hm->GetNodeById($thispageid);
    if( !$endNode ) return; // no current page?
    $deep = 1;
    $stopat = '***DEFAULT_PAGE***';
    $showall = 0;
    $tpl_name = 'breadcrumbs.tpl';
    $usefile = false;

    //
    // get parameters
    //
    if( isset($params['loadprops']) && $params['loadprops'] = 0 ) $deep = 0;
    if( isset($params['show_all']) && $params['show_all'] ) $showall = 1;
    if( isset($params['template']) && $params['template'] != '') $tpl_name = $params['template'];
    if( endswith($tpl_name, '.tpl') ) $usefile = true;
    if( isset($params['root']) ) $stopat = trim($params['root']);

    $pagestack = array();
    $curNode = $endNode;
    $count = 0;
    $prevdepth = 1;

    while( is_object($curNode) && $curNode->get_tag('id') > 0 ) {
        $content = $curNode->getContent($deep,true,true);
        if( !$content ) {
            $curNode = $curNode->get_parent();
            break;
        }

        if( ($content->ShowInMenu() || $showall == 1) && $content->Active() ) {
            $this->FillNode($content,$curNode,$pagestack,$count,$prevdepth,1,$deep,$params);
            $count = 0;
        }
        if( $content->Alias() == $stopat ) break;
        $curNode = $curNode->getParentNode();
    }

    // add the default page if we need to.
    if( !count($pagestack) ) return;
    if( $pagestack[count($pagestack)-1]->alias != $stopat && !isset($pagestack[count($pagestack)-1]->default) ) {
        if( $stopat == '***DEFAULT_PAGE***' ) {
            $curNode = $hm->GetNodeById($gCms->GetContentOperations()->GetDefaultContent());
        }
        else {
            $curNode = $hm->GetNodeByAlias($stopat);
        }
        if( is_object($curNode) ) {
            $content = $curNode->GetContent($deep,true,true);
            $this->FillNode($content,$curNode,$pagestack,$count,$prevdepth,1,$deep,$params);
            $count = 0;
        }
    }

    $separator = $gCms->GetContentOperations()->CreateNewContent('separator');
    $separator->SetName('&raquo;');
    $separator->SetMenuText('&raquo;');

    $pagestack = array_reverse($pagestack);
    $newstack = array();
    $prevdepth = 1;
    $curNode = null;
    for( $i = 0; $i < count($pagestack) - 1; $i++ ) {
        $newstack[] = $pagestack[$i];
        $newstack[count($newstack)-1]->depth = 1;
        $newstack[count($newstack)-1]->prevdepth = 1;
        $this->FillNode($separator,$curNode,$newstack,$gCms,$count,$prevdepth,1,$deep,$params);
    }
    $newstack[] = $pagestack[count($pagestack)-1];
    $newstack[count($newstack)-1]->depth = 1;
    $newstack[count($newstack)-1]->prevdepth = 1;
    unset($pagestack);

    // and get ready to display.
    $tpl->assign('starttext',$this->Lang('youarehere'));
    $tpl->assign('menuparams',$params);
    $tpl->assign('count',count($newstack));
    $tpl->assign('nodelist',$newstack);
}

$tpl->display();
debug_buffer('', 'End of BreadCrumbs Display');
?>
