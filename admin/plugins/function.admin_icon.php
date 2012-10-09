<?php

function smarty_function_admin_icon($params,&$template)
{
  $smarty = $template->smarty;
  
  if( !cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) return;
  $theme = cms_utils::get_theme_object();
  if( !is_object($theme) ) return;

  $module = $smarty->get_template_vars('actionmodule');

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

  $dirs = array();
  if( $module ) {
    $obj = cms_utils::get_module($module);
    if( is_object($obj) ) {
      $img = basename($icon);
      $dirs[] = array(cms_join_path($obj->GetModulePath(),'icons',"{$img}"),
		      $obj->GetModuleURLPath()."/icons/{$img}");
      $dirs[] = array(cms_join_path($obj->GetModulePath(),'images',"{$img}"),
		      $obj->GetModuleURLPath()."/images/{$img}");
    }
  }
  if( basename($icon) == $icon ) {
    $icon = "icons/system/{$icon}";
  }
  $config = cmsms()->GetConfig();
  $dirs[] = array(cms_join_path($config['root_path'],$config['admin_dir'],"themes/{$theme->themeName}/images/{$icon}"),
		  $config['admin_url']."/themes/{$theme->themeName}/images/{$icon}");

  $fnd = null;
  foreach( $dirs as $one ) {
    if( file_exists($one[0]) ) {
      $fnd = $one[1];
    }
  }
  if( !$fnd ) return;

  $out = "<img src=\"{$fnd}\"";
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