<?php

require_once("../include.php");

check_login($config);

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
	if ($password != "") {
		if ($password == $passwordagain) {
			$query = "INSERT INTO ".$config->db_prefix."users (username, password, active, create_date, modified_date) VALUES ('".mysql_real_escape_string($user)."', '".mysql_real_escape_string($password)."', $active, now(), now())";
			$result = $db->query($query);
			if (mysql_affected_rows() > -1) {
				$db->close();
				redirect("listusers.php");
				return;
			}
			else {
				echo "Error inserting user";
			}
		}
		else {
			echo "Passwords do not match";
		}
	}
	else {
		echo "Password not given";
	}
}

$db->close();

?>

<form method="post" action="adduser.php">

<div class="adminform">

<h3>Add User</h3>

<table border="0">

	<tr>
		<td>Name:</td>
		<td><input type="text" name="user" maxlength="255" value="<?=$user?>" /></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" name="password" maxlength="255" value="" /></td>
	</tr>
	<tr>
		<td>Password (again):</td>
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
