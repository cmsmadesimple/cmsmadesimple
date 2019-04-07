<?php
namespace PressRoom;
use PressRoom;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission(PressRoom::MANAGE_PERM) ) exit;
$catm = $this->categoriesManager();
$hm = $this->cms->get_hook_manager();

$category = $catm->createNew();
$catid = get_parameter_value( $params, 'catid');
if( $catid > 0 ) {
    $category = $catm->loadByID( $catid );
}

if( !empty($_POST) ) {
    if( isset($_POST['cancel']) ) {
        $this->SetMessage( $this->Lang('msg_cancelled') );
        $this->RedirectToAdminTab('categories',null,'admin_settings');
        return;
    }

    try {
        // it is a submission
        $category->name = filter_var( $_POST['name'], FILTER_SANITIZE_STRING );
        $category->alias = filter_var( $_POST['alias'], FILTER_SANITIZE_STRING );
        $category->image_url = filter_var( $_POST['image_url'], FILTER_SANITIZE_STRING );
        $category->parent_id = (isset($_POST['parent'])) ? (int) $_POST['parent'] : -1;
        $category->detailpage = (isset($_POST['detailpage']) ? (int) $_POST['detailpage'] : 0 );
        if( $category->alias && !preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*/', $category->alias) ) {
            throw new \RuntimeException( $this->Lang('err_catname') );
        }

        $category = $hm->emit( 'PressRoom::beforeEditCategory', $category );
        $catm->save( $category );
        $category = $hm->emit( 'PressRoom::afterEditCategory', $category );
        audit($category->id,$this->GetName(),'Edited category '.$category->name);
        $this->RedirectToAdminTab('categories',null,'admin_settings');
    }
    catch( \Exception $e ) {
        echo $this->ShowErrors( $e->GetMessage() );
    }
}


$eff_detailpage = $eff_detailpage_str = null;
if( $category->id > 0 ) {
    $eff_detailpage_id = $catm->get_detailpage_for_category( $category->id );
    if( $eff_detailpage_id ) {
        $contentobj = $this->cms->GetContentOperations()->LoadContentFromID( $eff_detailpage_id );
        $eff_detailpage = $contentobj->Alias();
        $eff_detailpage_str = $contentobj->HierarchyPath();
    }
}
$category_tree_list = $catm->getCategoryList( [-1 => $this->Lang('none')] );
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_edit_category.tpl'), null, null, $smarty );
$tpl->assign('obj',$category);
$tpl->assign('category_tree_list',$category_tree_list);
$tpl->assign('effective_detailpage',$eff_detailpage);
$tpl->assign('effective_detailpage_str',$eff_detailpage_str);
$tpl->display();
