<?php

require_once("../include.php");

check_login($config);

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
$access = check_permission($config, $userid, 'Modify User');

if ($access) {
	$db = new DB($config);

	if (isset($_POST["cancel"])) {
		redirect("listusers.php");
		return;
	}

	if (isset($_POST["edituser"])) {
		if ($password == $passwordagain) {
			$query = "UPDATE ".$config->db_prefix."users SET username='".mysql_real_escape_string($user)."', ";
			if ($password != "") {
				$query .= "password='".mysql_real_escape_string($password)."', ";
			}
			$query .= "active=$active, modified_date = now() WHERE user_id = $user_id";
			$result = $db->query($query);

			if (mysql_affected_rows() > -1) {
				$db->close();
				redirect("listusers.php");
				return;
			}
			else {
				echo "Error updating user";
			}
		}
		else {
			echo "Passwords do not match";
		}
	}
	else if ($user_id != -1) {

		$query = "SELECT * from ".$config->db_prefix."users WHERE user_id = " . $user_id;
		$result = $db->query($query);
		
		$row = mysql_fetch_array($result, MYSQL_ASSOC);

		$user = $row["username"];
		$active = $row["active"];

		mysql_free_result($result);

	}

	$db->close();
}

include_once("header.php");

if (!$access) {
	print "<h3>No Access to Edit Users</h3>";
}
else {

?>

<form method="post" action="edituser.php">

<div class="adminform">

<h3>Edit User</h3>

<table border="0">

	<tr>
		<td>Name:</td>
		<td><input type="text" name="user" maxlength="25" value="<?=$user?>" /></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" name="password" maxlength="25" value="" /></td>
	</tr>
	<tr>
		<td>Password (again):</td>
		<td><input type="password" name="passwordagain" maxlength="25" value="" /></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="user_id" value="<?=$user_id?>" /><input type="hidden" name="edituser" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></td>
	</tr>

</table>

</div>

</form>

<?php

}

include_once("footer.php");

?>
