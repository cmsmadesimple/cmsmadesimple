<?php

function smarty_function_tab_start($params,&$template)
{
  $smarty = $template->smarty;

  if( !isset($params['name']) ) return;

  $parms = array();
  foreach( $params as $key => $value ) {
    switch( $key ) {
    case 'name':
      $name = trim($value);
      break;
    case 'assign':
      break;
    default:
      $parms[$key] = trim($value);
    }
  }

  $out = cms_admin_tabs::start_tab($name,$parms);
  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$out);
    return;
  }
  return $out;
}

?>