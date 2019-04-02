<?php
namespace News2;
use News2;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) exit;
$fdm = $this->fielddefManager();
$hm = $this->cms->get_hook_manager();

$fdid = get_parameter_value( $params, 'fdid' );
if( $fdid < 1 ) throw new \LogicException('Invalid fdid passed to '.basename(__FILE__));

$fielddef = $fdm->loadByID( $fdid );
if( !$fielddef ) throw new \LogicException('Invalid fdid passed to '.basename(__FILE__));

$hm->emit( 'News2::beforeDeleteFielddef', $fielddef );
$fdm->delete( $fielddef );
$hm->emit( 'News2::afterDeleteFielddef', $fielddef );
$this->SetMessage( $this->Lang('msg_deleted') );
audit('',$this->GetName(),'Deleted fielddef '.$fielddef->name);
$this->RedirectToAdminTab('fielddefs',null,'admin_settings');
