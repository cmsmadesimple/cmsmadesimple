<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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

function smarty_function_cms_module($params, &$smarty) {

	global $cmsmodules;
	global $modulecmsobj;

	if (isset($params['module'])) {

		if (isset($cmsmodules[$params['module']])) {
			@ob_start();
			#$obj = $cmsmodules[$params['module']]['Instance'];
			#$obj->execute($modulecmsobj,"randstringgoeshere_",$params);
			call_user_func_array(&$cmsmodules[$module]['execute_function'], array($modulecmsobj,"randstringgoeshere_",$params));
			$modoutput = @ob_get_contents();
			@ob_end_clean();
			echo $modoutput;
		}

	}

}

?>
