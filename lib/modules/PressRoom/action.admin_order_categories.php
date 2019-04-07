<?php
namespace PressRoom;
use PressRoom;

if( !$gCms ) exit;
if( !$this->CheckPermission( PressRoom::MANAGE_PERM ) ) exit;
$catm = $this->categoriesManager();

try {
    if( !empty($_POST) ) {
        if( isset($_POST['cancel']) ) {
            $this->RedirectToAdminTab('categories','','admin_settings');
        }

        $submit_data = get_parameter_value( $_POST, 'submit_data' );
        if( !$submit_data ) throw new \RuntimeException( $this->Lang('err_missing_param') );
        $data = json_decode( $submit_data, TRUE );
        if( !$data ) throw new \RuntimeException( $this->Lang('err_missing_param') );

        $flat_list = $catm->loadAllArray();
        $flat_hash = null;
        foreach( $flat_list as $row ) {
            $flat_hash[$row['id']] = $row;
        }
        unset($flat_list);

        $update_hash = function($data,&$flat_hash,$parent = -1) use (&$update_hash) {
            $item_order = 0;
            foreach( $data as $key => $val ) {
                $id = (int) substr($key,4);
                $item_order++;
                $flat_hash[$id]['parent_id'] = $parent;
                $flat_hash[$id]['item_order'] = $item_order;
                if( is_array($val) ) {
                    $update_hash($val, $flat_hash, $id);
                }
            }
        };

        $update_hash( $data, $flat_hash );
        usort($flat_hash, function($a,$b){
                $t1 = $a['parent_id'] - $b['parent_id'];
                $t2 = $a['item_order'] - $b['item_order'];
                if( $t1 != 0 ) return $t1;
                return $t2;
        });   // converts to flat list.

        $db->StartTrans();
        foreach( $flat_hash as $row ) {
            $cat = Category::from_row( $row );
            $catm->save( $cat );
        }
        $catm->updateHierarchyPositions();
        $db->CompleteTrans();

        audit('',$this->GetName(),'Reordered categories');
        $this->SetMessage( $this->Lang('msg_saved') );
        $this->RedirectToAdminTab('categories','','admin_settings');
    }

    $categories = $catm->loadTree();
    $tpl = $smarty->CreateTemplate( $this->GetTemplateResource( 'admin_order_categories.tpl' ), null, null, $smarty );
    $tpl->assign('category_tree',$categories);
    $tpl->display();
}
catch( \Exception $e ) {
    $this->SetError( $e->GetMessage() );
    $this->RedirectToAdminTab('categories','','admin_settings');
}
