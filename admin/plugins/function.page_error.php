<?php

function smarty_function_page_error($params,&$template)
{
  $smarty = $template->smarty;
  
  if( !cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) return;
  if( !isset($params['msg']) ) return;

  $out = '<div class="pageerror">'.trim($params['msg']).'</div>';
  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$out);
    return;
  }
  return $out;
}

?>