<?php
namespace News2;
use News2;
use CMSMS\HookManager;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) exit;
$fdm = $this->fielddefManager();

$new_order_str = get_parameter_value( $_POST, 'data' );
if( !$new_order_str ) {
    $this->RedirectToAdminTab('fielddefs',null,'admin_settings');
}
$new_order_list = array_filter(explode(',',$new_order_str), function($item) {
        $item = (int) $item;
        return $item > 0;
});
array_unique( $new_order_list );
$all = $fdm->loadAll();
if( count($all) != count($new_order_list) ) {
    $this->RedirectToAdminTab('fielddefs', null, 'admin_settings');
}
$hash = null;
for( $i = 0; $i < count($all); $i++ ) {
    $item = $all[$i];
    $hash[$item->id] = $item;
}
unset($all,$new_order_str);

$out = null;
for( $i = 0; $i < count($new_order_list); $i++ ) {
    $item_id = $new_order_list[$i];
    $item = $hash[$item_id];
    $item->item_order = $i+1;
    $fdm->save($item);
}

HookManager::do_hook('News2::onReorderFielddefs');
$this->SetMessage( $this->Lang( 'msg_saved' ) );
$this->RedirectToAdminTab('fielddefs', null, 'admin_settings');
