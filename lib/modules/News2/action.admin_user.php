<?php
namespace News2;
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

$uid = (int) get_parameter_value( $params, 'uid' );
if( $uid < 1 ) return;

$user = cmsms()->GetUserOperations()->LoadUserByID($uid);
if( !$user ) return;

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_user.tpl'), null, null, $smarty );
$tpl->assign('user',$user);
$tpl->display();
