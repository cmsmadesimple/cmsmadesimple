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

$user= "";
if (isset($_POST["user"])) $user = $_POST["user"];

$password= "";
if (isset($_POST["password"])) $password = $_POST["password"];

$passwordagain= "";
if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["adduser"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listusers.php");
	return;
}

$db = new DB($config);

if (isset($_POST["adduser"])) {

	$validinfo = true;

	if ($user == "") {
		$validinfo = false;
		$error .= "<li>No username given!</li>";
	}

	if ($password == "") {
		$validinfo = false;
		$error .= "<li>No password given!</li>";
	}
	else if ($password != $passwordagain) {
		#We don't want to see this if no password was given
		$validinfo = false;
		$error .= "<li>Passwords do not match</li>";
	}

	if ($validinfo) {
		$query = "INSERT INTO ".$config->db_prefix."users (username, password, active, create_date, modified_date) VALUES ('".mysql_real_escape_string($user)."', '".mysql_real_escape_string($password)."', $active, now(), now())";
		$result = $db->query($query);
		if (mysql_affected_rows() > -1) {
			$db->close();
			redirect("listusers.php");
			return;
		}
		else {
			$error .= "<li>Error inserting user</li>";
		}
	}
}

$db->close();

include_once("header.php");

if ($error != "") {
	echo "<ul class=\"error\">".$error."</ul>";
}

?>

<form method="post" action="adduser.php">

<div class="adminform">

<h3>Add User</h3>

<table border="0">

	<tr>
		<td>*Name:</td>
		<td><input type="text" name="user" maxlength="255" value="<?=$user?>" /></td>
	</tr>
	<tr>
		<td>*Password:</td>
		<td><input type="password" name="password" maxlength="255" value="" /></td>
	</tr>
	<tr>
		<td>*Password (again):</td>
		<td><input type="password" name="passwordagain" maxlength="255" value="" /></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="adduser" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></td>
	</tr>

</table>

</div>

</form>

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
