<?php

function smarty_function_cms_textarea($params, &$smarty)
{
  $wysiwyg = FALSE;
  $content = '';
  $name = '';
  $id = '';
  $rows = 15;
  $cols = 80;
  $syntax = '';
  $class = '';

  foreach( $params as $key => $value ) {
    switch( $key ) {
    case 'wysiwyg':
      $$key = (bool)$value;
      break;

    case 'syntax':
      $$key = (bool)$value;
      break;

    case 'id':
    case 'name':
    case 'content':
    case 'class':
      $$key = trim($value);
      break;

    case 'cols':
    case 'rows':
      $$key = (int)$value;
      break;
    }
  }

  $addtext = '';
  if( $class ) $addtext = 'class="'.$class.'"';

  $res = null;
  if( $syntax ) {
    $userid = get_userid(FALSE);
    $syntaxhighlighter = get_preference($userid, 'syntaxhighlighter');
    if( $syntaxhighlighter ) {
      $mod = cms_utils::get_module($syntaxhighlighter);
      if( $mod ) {
	// we want a syntax highlighter, we have a preferred one, and could find the module
	$res = $mod->SyntaxTextArea($name,'php',$cols,$rows,'',$content,'',$addtext);
      }
    }
  }

  if( !$res ) {
    $res = create_textarea($wysiwyg,$content,$name,'',$id,'','',$cols,$rows,'',
			   $syntaxhighlighter,$addtext);
  }

  if( isset($params['assign']) ) {
    $smarty->assign(trim($params['assign']),$res);
    return;
  }
  return $res;
}

#
# EOF
#
?>