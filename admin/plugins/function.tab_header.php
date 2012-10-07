<?php

function smarty_function_tab_header($params,&$template)
{
  $smarty = $template->smarty;

  if( !isset($params['name']) ) return;
  $name = trim($params['name']);
  $label = $name;
  $active = false;
  if( isset($params['label']) ) $label = trim($params['label']);
  if( isset($params['active']) ) $active = (int)trim($params['active']);

  $out = cms_admin_tabs::set_tab_header($name,$label,$active);
  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$out);
    return;
  }
  return $out;
}

?>