<?php
namespace News2;
if( !isset($gCms) ) exit;
$artm = $this->articleManager();

$start_id = $start_category = $output_list = null;
$catm = $this->categoriesManager();
$template = trim(get_parameter_value($params, 'categorytemplate', 'showcategories.tpl'));
$alias = trim(get_parameter_value($params, 'alias'));
$start_id = (int) get_parameter_value($params, 'from');
$maxdepth = (int) get_parameter_value($params, 'maxdepth', 1000000);

if( $alias ) {
    $start_category = $catm->loadByAlias( $alias );
    if( !$start_category ) throw new \CmsError404Exception('Category with alias '.$alias.'not found');
    $output_list = $catm->loadTree( $start_category->id );
} else if( $start_id > 0 ) {
    $start_category = $catm->loadByID( $start_id );
    if( !$start_category ) throw new \CmsError404Exception('Category with id '.$start.'not found');
    $output_list = $catm->loadTree( $start_id );
} else {
    // start at the top
    $output_list = $catm->loadTree();
}

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource( $template ), null, null, $smarty );
$tpl->assign('categories', $output_list);
$tpl->assign('maxdepth', $maxdepth);
$tpl->display();