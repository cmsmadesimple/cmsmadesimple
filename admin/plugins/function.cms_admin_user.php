<?php

function smarty_function_cms_admin_user($params,&$template)
{
  $smarty = $template->smarty;
  $out = null;

  if( cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) {
      $uid = (int)get_parameter_value($params,'uid');
      if( $uid > 0 ) {
          $user = UserOperations::get_instance()->LoadUserByID((int)$params['uid']);
          if( is_object($user) ) {
              $mode = trim(get_parameter_value($params,'mode','username'));
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
      }
  }

  if( isset($params['assign']) ) {
    $smarty->assign($params['assign'],$out);
    return;
  }
  return $out;
}

?>