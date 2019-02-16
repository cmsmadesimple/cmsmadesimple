<?php
if (!isset($gCms)) exit;

$template = null;
if (isset($params['browsecattemplate'])) {
    $template = trim($params['browsecattemplate']);
}
else {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('News::browsecat');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default summary template found');
        return;
    }
    $template = $tpl->get_name();
}

$tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);
$items = news_ops::get_categories($id,$params,$returnid);

// Display template
$tpl_ob->assign('count', count($items));
$tpl_ob->assign('cats', $items);
$tpl_ob->display();
