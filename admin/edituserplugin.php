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
$CMS_TOP_MENU='extensions';
$CMS_ADMIN_TITLE='editusertag';

require_once("../include.php");

check_login();

$error = "";

$userplugin_id = "";
if (isset($_POST["userplugin_id"])) $userplugin_id = $_POST["userplugin_id"];
else if (isset($_GET["userplugin_id"])) $userplugin_id = $_GET["userplugin_id"];

$plugin_name= "";
if (isset($_POST["plugin_name"])) $plugin_name = $_POST["plugin_name"];

$orig_plugin_name = "";
if (isset($_POST["origpluginname"])) $orig_plugin_name = $_POST["origpluginname"];

$code= "";
if (isset($_POST["code"])) $code = $_POST["code"];

if (isset($_POST["cancel"])) {
	redirect("listusertags.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Code Blocks');

$use_javasyntax = false;
if (get_preference($userid, 'use_javasyntax') == "1")$use_javasyntax = true;

$smarty = new Smarty_CMS($gCms->config);
load_plugins($smarty);

if ($access) {
	if (isset($_POST["editplugin"])) {

		$validinfo = true;
		if ($plugin_name == "") {
			$error .= "<li>".lang('nofieldgiven', array(lang('editusertag')))."</li>";
			$validinfo = false;
		}
		else
		{
			if ($plugin_name != $orig_plugin_name && in_array($plugin_name, $gCms->cmsplugins))
			{
				$error .= "<li>".lang('usertagexists')."</li>";
				$validinfo = false;
			}
		}

		if ($code == "") {
			$error .= "<li>".lang('nofieldgiven', array(lang('code')))."</li>";
			$validinfo = false;
		}
		else if (strrpos($code, '{') !== FALSE)
		{
			$lastopenbrace = strrpos($code, '{');
			$lastclosebrace = strrpos($code, '}');
			if ($lastopenbrace > $lastclosebrace)
			{
				$error .= "<li>".lang('invalidcode')."</li>";
				$validinfo = false;
			}
		}
		
		if ($validinfo)
		{
			srand();
			if (@eval('function testfunction'.rand().'() {'.$code.'}') === FALSE)
			{
				$error .= "<li>".lang('invalidcode')."</li>";
				$validinfo = false;
			}
		}

		if ($validinfo) {
			$query = "UPDATE ".cms_db_prefix()."userplugins SET userplugin_name = ".$db->qstr($plugin_name).", code = ".$db->qstr($code).", modified_date = '".$db->DBTimeStamp(time())."' WHERE userplugin_id = ".$userplugin_id;
			$result = $db->Execute($query);
			if ($result) {
				audit($userplugin_id, $plugin_name, 'Edited User Defined Tag');
				redirect("listusertags.php");
				return;
			}
			else {
				$error .= "<li>".lang('errorupdatingusertag')."</li>";
			}
		}
	}
	else if ($userplugin_id != -1) {

		$query = "SELECT * from ".cms_db_prefix()."userplugins WHERE userplugin_id = " . $userplugin_id;
		$result = $db->Execute($query);
		
		$row = $result->FetchRow();

		$plugin_name = $row["userplugin_name"];
		$orig_plugin_name = $plugin_name;
		$code = $row['code'];
	}
}
if (strlen($plugin_name)>0)
    {
    $CMS_ADMIN_SUBTITLE = $plugin_name;
    }
include_once("header.php");

if (!$access) {
	print "<h3>".lang('noaccessto', array(lang('editusertag')))."</h3>";
}
else {
	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="edituserplugin.php" <?php if($use_javasyntax){echo 'onSubmit="textarea_submit(this, \'code\');"';} ?>>

<div class="adminform">

<h3><?php echo lang('editusertag')?></h3>

<table width="100%" border="0">

	<tr>
		<td width="60">*<?php echo lang('name')?>:</td>
		<td><input type="text" name="plugin_name" maxlength="255" value="<?php echo $plugin_name?>" class="standard" /></td>
	</tr>
	<tr>
		<td>*<?php echo lang('code')?></td>
		<td><?php echo textarea_highlight($use_javasyntax, $code, "code", "syntaxHighlight", "Java") ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="userplugin_id" value="<?php echo $userplugin_id?>" /><input type="hidden" name="origpluginname" value="<?php echo $orig_plugin_name?>" /><input type="hidden" name="editplugin" value="true" />
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>

</table>

</div>

</form>

<?php
}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
