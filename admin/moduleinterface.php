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
#
#$Id$

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login($config);

$module = "";
if (isset($_REQUEST["module"])) $module = $_REQUEST["module"];

if (isset($gCms->modules[$module]) && $gCms->modules[$module]['object']->IsWYSIWYG())
{
	$userid = get_userid();
	if (get_preference($userid, 'use_wysiwyg') == "1") {
		$htmlarea_flag = "true";
		$htmlarea_replaceall = true;
	}
}

include_once("header.php");

if (count($gCms->modules) > 0)
{

?>

	<h3><?php echo lang('moduleinterface', array($module))?></h3>

<?php

	if (isset($gCms->modules[$module]))
	{
		@ob_start();
		#call_user_func_array($gCms->modules[$module]['execute_admin_function'], array($gCms,"module_".$module."_"));
		$id = 'm1_';
		$params = array();
		foreach ($_REQUEST as $key=>$value)
		{
			if (strpos($key, $id) !== FALSE && strpos($key, $id) == 0)
			{
				$key = str_replace($id, '', $key);
				$params[$key] = $value;
			}
		}
		echo $gCms->modules[$module]['object']->DoAction((isset($_REQUEST[$id.'action'])?$_REQUEST[$id.'action']:'defaultadmin'), $id, $params);
		$content = @ob_get_contents();
		@ob_end_clean();
		echo $content;
	}
	else
	{
		redirect("index.php");
	}
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
