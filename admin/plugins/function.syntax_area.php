<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_function_syntax_area($params, &$template)
{
	$smarty = $template->smarty;

	if( !isset($params['name']) )
		throw new CmsInvalidDataExtension('syntax_area misssing parameter: name');

	$name = trim($params['name']);
	if( isset($params['prefix']) )
	{
		$name = trim($params['prefix']).$name;
	}

	$value = '';
	if( isset($params['value']) )
	{
		$value = trim($params['value']);
	}

	$id = '';
	if( isset($params['id']) )
	{
		$id = trim($params['id']);
	}

	$class = '';
	if( isset($params['class']) )
	{
		$class = trim($params['class']);
	}

	$type = 'html';
	if( isset($params['type']) )
	{
		$type = trim($params['type']);
	}

	$rows = 15;
	$cols = 80;
	if( isset($params['rows']) )
	{
		$rows = (int)$params['rows'];
	}
	if( isset($params['cols']) )
	{
		$rows = (int)$params['cols'];
	}
	
	$out = null;
	$userid = get_userid(FALSE);
	$syntaxhighlighter = get_preference($userid, 'syntaxhighlighter');
	if( $syntaxhighlighter )
	{
		$mod = cms_utils::get_module($syntaxhighlighter);
		if( $mod )
		{
			// we want a syntax highlighter, we have a preferred one and could find the module
			$out = $mod->SyntaxTextArea($name,$type,$cols,$rows,'',$value);
		}
	}

	if( !$out )
	{
		$out = '<textarea name="'.$name.'"';
		if( $id ) $out .= ' id="'.$id.'"';
		if( $class ) $out .= ' class="'.$class.'"';
		if( $rows ) $out .= ' rows="'.$rows.'"';
		if( $cols ) $out .= ' cols="'.$cols.'"';
		$out .= '>'.$value.'</textarea>';
	}

	if( isset($params['assign']) )
	{
		$smarty->assign(trim($params['assign']),$out);
		return;
	}
	return $out;
}
#
# EOF
#
?>