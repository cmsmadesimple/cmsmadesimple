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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

$error = "";

$userplugin_id = "";
if (isset($_POST["userplugin_id"])) $userplugin_id = $_POST["userplugin_id"];
else if (isset($_GET["userplugin_id"])) $userplugin_id = $_GET["userplugin_id"];

$plugin_name= "";
if (isset($_POST["plugin_name"])) $plugin_name = $_POST["plugin_name"];

$code= "";
if (isset($_POST["code"])) $code = $_POST["code"];

if (isset($_POST["cancel"])) {
	redirect("plugins.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Code Blocks');

if ($access) {
	if (isset($_POST["editplugin"])) {

		$validinfo = true;
		if ($plugin_name == "") {
			$error .= "<li>".$gettext->gettext("No plugin name given!")."</li>";
			$validinfo = false;
		}
		if ($code == "") {
			$error .= "<li>".$gettext->gettext("No code entered!")."</li>";
			$validinfo = false;
		}
		srand();
		if (@eval('function testfunction'.rand().'() {'.$code.'}') === FALSE)
		{
			$error .= "<li>".$gettext->gettext("Invalid code entered!  Remember, this is the inside of a function.")."</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			$query = "UPDATE ".cms_db_prefix()."userplugins SET userplugin_name = ".$db->qstr($plugin_name).", code = ".$db->qstr($code).", modified_date = ".$db->DBTimeStamp(time())." WHERE userplugin_id = ".$userplugin_id;
			$result = $db->Execute($query);
			if ($result) {
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $userplugin_id, $plugin_name, 'Edited User Defined Tag');
				redirect("plugins.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error inserting user tag!")."</li>";
			}
		}
	}
	else if ($userplugin_id != -1) {

		$query = "SELECT * from ".cms_db_prefix()."userplugins WHERE userplugin_id = " . $userplugin_id;
		$result = $db->Execute($query);
		
		$row = $result->FetchRow();

		$plugin_name = $row["userplugin_name"];
		$code = $row['code'];
	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".$gettext->gettext("No Access to Add Plugin")."</h3>";
}
else {
	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="edituserplugin.php">

<div class="adminform">

<h3><?=$gettext->gettext("Edit Plugin")?></h3>

<table width="100%" border="0">

	<tr>
		<td width="60">*<?=$gettext->gettext("Name")?>:</td>
		<td><input type="text" name="plugin_name" maxlength="255" value="<?=$plugin_name?>" class="standard" /></td>
	</tr>
	<tr>
		<td>*<?=$gettext->gettext("Code")?></td>
		<td><textarea name="code" cols="50" rows="10"><?=$code?></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="userplugin_id" value="<?=$userplugin_id?>" /><input type="hidden" name="editplugin" value="true" />
		<input type="submit" value="<?=$gettext->gettext("Submit")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		<input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>

</table>

</div>

</form>

<?php
}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
