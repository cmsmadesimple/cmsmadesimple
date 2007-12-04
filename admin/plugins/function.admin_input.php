<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

function call_input_method(&$params, &$smarty)
{
	require_once 'function.html_' . $params['type'] . '.php';
	$function_name = 'smarty_function_html_' . $params['type'];
	
	ob_start();
		call_user_func_array($function_name, array(&$params, &$smarty));
		$output = ob_get_contents();
	ob_end_clean();
	
	return $output;
}


function smarty_function_admin_input($params, &$smarty)
{
	// Give a default value of text if no type is specified
	if(!isset($params['type']))
		$params['type'] = 'input';

	// Set the label to the name if we didn't pass the label and the name is set
	if((!isset($params['label'])) && (isset($params['name'])))
		$params['label'] = $params['name'];

	// Use the following switch statement to initialize any values for input types that weren't passed.
	// I didn't want to have to change any of the individual methods yet, but we can move these later.
	switch($params['type'])
	{
		case "input":
		break;
		case "checkbox":
		break;		
		case "submit":
			if(!isset($params['class']))
			{
				$params['class'] = "pagebutton";
				$params['onmouseover'] = "this.className='pagebuttonhover'";
				$params['onmouseout'] = "this.className='pagebutton'";
			}
		break;
	}

	$smarty->assign('input_html', call_input_method($params, $smarty));
	$smarty->assign('params', $params);

	$smarty->display('elements/admin-input.tpl');
}
?>