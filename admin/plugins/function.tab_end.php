<?php

function smarty_function_tab_end($params,&$template)
{
  $smarty = $template->smarty;

  $out = cms_admin_tabs::end_tab_content();
  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$out);
    return;
  }
  return $out;
}

?>