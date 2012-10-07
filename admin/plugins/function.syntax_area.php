<?php

function smarty_function_syntax_area($params, &$template)
{
  $smarty   = $template->smarty;

  if( !isset($params['name']) )
    throw new CmsInvalidDataExtension('syntax_area misssing parameter: name');

  $name = trim($params['name']);
  if( isset($params['prefix']) ) {
    $name = trim($params['prefix']).$name;
  }

  $value = '';
  if( isset($params['value']) ) {
    $value = trim($params['value']);
  }

  $id = '';
  if( isset($params['id']) ) {
    $id = trim($params['id']);
  }

  $class = '';
  if( isset($params['class']) ) {
    $class = trim($params['class']);
  }

  $type = 'html';
  if( isset($params['type']) ) {
    $type = trim($params['type']);
  }

  $rows = 15;
  $cols = 80;
  if( isset($params['rows']) ) {
    $rows = (int)$params['rows'];
  }
  if( isset($params['cols']) ) {
    $rows = (int)$params['cols'];
  }

  $out = create_textarea(FALSE,$value,$name,$class,$id,'','',$width,$height,'',$type);
  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$out);
    return;
  }
  return $out;
}

#
# EOF
#
?>