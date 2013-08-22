<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

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

?>