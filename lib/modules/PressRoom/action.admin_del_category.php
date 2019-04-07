<?php
namespace PressRoom;
use PressRoom;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission(PressRoom::MANAGE_PERM) ) exit;
$catm = $this->categoriesManager();

try {
    $catid = get_parameter_value( $params, 'catid' );
    if( $catid < 1 ) throw new \LogicException('Invalid catid passed to '.basename(__FILE__));
    $hm = $this->app->get_hook_manager();

    $category = $catm->loadByID( $catid );
    if( !$category ) throw new \LogicException('Invalid catid passed to '.basename(__FILE__));

    // cannot delete the item if it has children
    if( $catm->hasChildren( $catid ) ) throw new \RuntimeException( $this->Lang('err_del_category_children') );

    $hm->emit( 'PressRoom::beforeDeleteCategory', $category );
    $catm->delete( $category );
    $hm->emit( 'PressRoom::afterDeleteCategory', $category );
    $this->SetMessage( $this->Lang('msg_deleted') );
    audit($category->id,$this->GetName(),'Deleted category '.$category->name);
    $this->RedirectToAdminTab('categories',null,'admin_settings');
}
catch( \Exception $e ) {
    $this->SetError( $e->GetMessage() );
    $this->RedirectToAdminTab('categories',null,'admin_settings');
}
