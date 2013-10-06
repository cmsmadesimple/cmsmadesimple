<?php

function smarty_function_cms_admin_user($params,&$template)
{
  $smarty = $template->smarty;
  
  if( !cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) return;
  if( !isset($params['uid']) ) return;

  $out = null;
  $mode = 'username';
  $user = UserOperations::get_instance()->LoadUserByID((int)$params['uid']);
  if( is_object($user) ) {
    if( isset($params['mode']) ) $mode = trim($params['mode']);
    switch( $mode ) {
    case 'username':
      $out = $user->username;
      break;
    case 'email':
      $out = $user->email;
      break;
    case 'firstname':
      $out = $user->firstname;
      break;
    case 'lastname':
      $out = $user->lastname;
      break;
    case 'fullname':
      $out = "{$user->firstname} {$user->lastname}";
      break;
    }
  }

  if( isset($params['assign']) ) {
    $smarty->assign($params['assign'],$out);
    return;
  }
  return $out;
}

?>