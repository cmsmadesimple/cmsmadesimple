<?php

function smarty_function_admin_icon($params,&$template)
{
  $smarty = $template->smarty;
  
  if( !cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) return;

  $icon = null;
  $tagparms = array();
  foreach( $params as $key => $value ) {
    switch( $key ) {
    case 'icon':
      $icon = trim($value);
      break;

    case 'width':
    case 'height':
    case 'alt':
    case 'rel':
    case 'class':
    case 'id':
    case 'name':
    case 'title':
    case 'accesskey':
      $tagparms[$key] = trim($value);
      break;

    case 'assign':
      break;
    }
  }

  if( !$icon ) return;
  $fnd = cms_admin_utils::get_icon($icon);
  if( !$fnd ) return;

  if( !isset($tagparms['alt']) ) {
    $tagparms['alt'] = basename($fnd);
  }


  $out = "<img src=\"{$fnd}\"";
  foreach( $tagparms as $key => $value ) {
    $out .= " $key=\"$value\"";
  }
  $out .= '/>';

  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$out);
    return;
  }
  return $out;
}
?>