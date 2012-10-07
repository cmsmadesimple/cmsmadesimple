<?php

function smarty_function_page_warning($params,&$template)
{
  $smarty = $template->smarty;
  
  if( !cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) return;
  if( !isset($params['msg']) ) return;

  $out = '<div class="pagewarning">'.trim($params['msg']).'</div>';
  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$out);
    return;
  }
  return $out;
}

?>