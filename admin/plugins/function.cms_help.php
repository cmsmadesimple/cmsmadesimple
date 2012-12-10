<?php

function smarty_function_cms_help($params,&$template)
{
  $smarty = $template->smarty;

  if( !cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) return;
  $theme = cms_utils::get_theme_object();
  if( !is_object($theme) ) return;

  $key1 = '';
  $key2 = '';
  $helptext = '';
  foreach( $params as $key => $value ) {
    switch( $key ) {
    case 'key1':
    case 'key2':
      $$key = trim($value);
      break;

    case 'value':
      $helptext = $value;
    }
  }

  if( !$key1 ) return;
  $key = $key1;
  if( $key2 ) $key .- '__'.$key2;

  $icon = cms_admin_utils::get_icon('info.gif');
  if( !$icon ) return;

  if( !$helptext ) {
    $out = '<div class="cms_help"><img class="cms_helpicon" src="'.$icon.'" alt="'.$icon.'"/><span class="cms_helpkey" style="display: none;">'.$key.'</span></div>';
  }
  else {
    $out = '<div class="cms_helptext title="'.lang('help').'" id="cmshelp_'.$key.'" style="display: none;">'.$helptext.'</div>';
  }
  
  if( isset($params['assign']) ) {
    $smarty->assign($params['assign'],$out);
  }
  else {
    return $out;
  }
}

?>