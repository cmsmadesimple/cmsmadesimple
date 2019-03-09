<?php
namespace News2;
use News2;
use CMSMS\HookManager;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission(News2::MANAGE_PERM) ) exit;
$catm = $this->categoriesManager();

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
        if( $category->alias && !preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*/', $category->alias) ) {
            throw new \RuntimeException( $this->Lang('err_catname') );
        }

        $category = HookManager::do_hook( 'News2::beforeEditCategory', $category );
        $catm->save( $category );
        $category = HookManager::do_hook( 'News2::afterEditCategory', $category );
        audit($category->id,$this->GetName(),'Edited category '.$category->name);
        $this->RedirectToAdminTab('categories',null,'admin_settings');
    }
    catch( \Exception $e ) {
        echo $this->ShowErrors( $e->GetMessage() );
    }
}


$category_tree_list = $catm->getCategoryList( [-1 => $this->Lang('none')] );
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_edit_category.tpl'), null, null, $smarty );
$tpl->assign('obj',$category);
$tpl->assign('category_tree_list',$category_tree_list);
$tpl->display();
