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
#$Id: listtemplates.php 5912 2009-08-16 19:41:22Z ronnyk $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
require_once(cms_join_path($dirname,'lib','html_entity_decode_utf8.php'));
require_once("../lib/classes/module/class.cms_module_template.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$userid = get_userid();
$access = check_permission($userid, "Modify Templates");


if (!$access) {
	die('Permission Denied');
return;
}


global $gCms;
$db =& $gCms->GetDb();
$params = $_GET;
$params['select'] = 'all';
$params['page'] = 'listmodtemplates';
include_once("header.php");

if (isset($_GET["message"])) 
{
	$message = preg_replace('/\</','',$_GET['message']);
	echo '<div class="pagemcontainer"><p class="pagemessage">'.$message.'</p></div>';
}



echo '<div class="pagecontainer">';
	echo '<div class="pageoverflow">';
		echo $themeObject->ShowHeader('modules');
	echo '</div>';
	$id = 'm1_';
	$parms = array();
	$parms[CMS_SECURE_PARAM_NAME] = $params[CMS_SECURE_PARAM_NAME];
	$parms['select'] = $params['select'];
	$parms['action'] = 'admin_templates';
	$modulelist = CmsModuleTemplate::get_modules();
	echo '<form name="module_select" method="post" action="moduleinterface.php">';
	echo '<input type=hidden name="id" value="'.$id.'"/>';
	foreach ($parms as $key=>$one)
	{
		echo '<input type=hidden name="'.$key.'" value="'.$one.'"/>';
		echo '<input type=hidden name="'.$id.$key.'" value="'.$one.'"/>';
	}
	echo '<select name="module" onchange="document.module_select.submit();">';
	echo '<option value="">Select a module:</option>';
	foreach ($modulelist as $modulename)
	{
		echo '<option value="'.$modulename.'">'.$modulename.'</option>';
	}
	echo '</select>';
	echo '</form>';

?>
</div>
<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('back')?></a></p>

<?php
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
