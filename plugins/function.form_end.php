<?php

function smarty_function_form_end($params, &$template)
{
  $smarty = $template->smarty;

  $out .= '</form>';
  if( isset($params['assign']) ) {
    $smarty->assign($params['assign'],$out);
    return;
  }
  return $out;
}


?>