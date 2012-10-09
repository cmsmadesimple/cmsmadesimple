<?php

function smarty_function_admin_icon($params,&$template)
{
  $smarty = $template->smarty;
  
  if( !cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) return;
  $theme = cms_utils::get_theme_object();
  if( !is_object($theme) ) return;

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
      $tagparms[$key] = trim($value);
      break;

    case 'assign':
      break;
    }
  }

  if( basename($icon) == $icon ) {
    $icon = "icons/system/{$icon}";
  }
  $config = cmsms()->GetConfig();
  $icon = "themes/{$theme->themeName}/images/{$icon}";
  $out = "<img src=\"{$icon}\"";
  foreach( $tagparms as $key => $value ) {
    $out .= " $key=\"$value\"";
  }
  $out .= '>';

  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$out);
    return;
  }
  return $out;
}
?>