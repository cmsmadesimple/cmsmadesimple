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

require_once("../include.php");

check_login($config);

$error = "";

$dropdown = "";

$user = "";
if (isset($_POST["user"])) $user = $_POST["user"];

$password = "";
if (isset($_POST["password"])) $password = $_POST["password"];

$passwordagain = "";
if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["edituser"])) $active = 0;

$user_id = -1;
if (isset($_POST["user_id"])) $user_id = $_POST["user_id"];
else if (isset($_GET["user_id"])) $user_id = $_GET["user_id"];

$userid = get_userid();
$access = check_permission($config, $userid, 'Modify User') || ($userid == $user_id);

if ($access) {

	if (isset($_POST["cancel"])) {
		redirect("listusers.php");
		return;
	}

	$db = new DB($config);

	if (isset($_POST["edituser"])) {
	
		$validinfo = true;

		if ($user == "") {
			$validinfo = false;
			$error .= "<li>".GetText::gettext("No username given!")."</li>";
		}

		if ($password != $passwordagain) {
			$validinfo = false;
			$error .= "<li>".GetText::gettext("Passwords do not match")."</li>";
		}

		if ($validinfo) {
			$query = "UPDATE ".$config->db_prefix."users SET username='".$db->escapestring($user)."', ";
			if ($password != "") {
				$query .= "password='".md5($password)."', ";
			}
			$query .= "active=$active, modified_date = now() WHERE user_id = $user_id";
			$result = $db->query($query);

			if ($db->rowsaffected()) {
				$db->close();
				audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $user_id, $user, 'Edited User');
				redirect("listusers.php");
				return;
			}
			else {
				$error .= "<li>".GetText::gettext("Error updating user")."</li>";
			}
		}
	}
	else if ($user_id != -1) {

		$query = "SELECT * from ".$config->db_prefix."users WHERE user_id = " . $user_id;
		$result = $db->query($query);
		
		$row = $db->getresulthash($result);

		$user = $row["username"];
		$active = $row["active"];

		$db->freeresult($result);

	}

	$db->close();
}

include_once("header.php");

if (!$access) {
	print "<h3>".GetText::gettext("No Access to Edit Users")."</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

?>

<form method="post" action="edituser.php">

<div class="adminform">

<h3><?=GetText::gettext("Edit User")?></h3>

<table border="0">

	<tr>
		<td>*<?=GetText::gettext("Name")?>:</td>
		<td><input type="text" name="user" maxlength="25" value="<?=$user?>" /></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Password<br />(leave blank to keep current password)")?>:</td>
		<td><input type="password" name="password" maxlength="25" value="" /></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Password (again)")?>:</td>
		<td><input type="password" name="passwordagain" maxlength="25" value="" /></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="user_id" value="<?=$user_id?>" /><input type="hidden" name="edituser" value="true" /><input type="submit" value="<?=GetText::gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=GetText::gettext("Cancel")?>" /></td>
	</tr>

</table>

</div>

</form>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
