<?

require_once("../include.php");

if ($_POST["username"] && $_POST["password"]) {

	$username = $_POST["username"];
	$password = $_POST["password"];

	$db = new DB($config);

	$query = "SELECT * FROM ".$config->db_prefix."users WHERE username = '".mysql_escape_string($username)."' and password = '".mysql_escape_string($password)."'";
	$result = $db->query($query);

	$line = mysql_fetch_array($result, MYSQL_ASSOC);

	if (isset($line["user_id"])) {
		$_SESSION["user_id"] = $line["user_id"];	
		redirect("index.php");
	}
	else {
		echo "<p>Username or Password incorrect!</p>";
	}

	mysql_free_result($result);
	$db->close($link);

}

include_once("header.php");

?>
<form method="post" action="login.php" id="login">

<div class="login">

<table border=0>
	<tr>
		<td align="right">Username:</td>
		<td><input type="text" id="username" name="username" value="<?echo $_POST["username"]?>" /></td>
	</tr>
	<tr>
		<td align="right">Password:</td>
		<td><input type="password" id="password" name="password" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Submit" /></td>
	</tr>
</table>

</div>

</form>

<?php
include_once("footer.php");
?>
