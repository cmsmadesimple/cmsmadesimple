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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<title></title>

<link rel="stylesheet" type="text/css" href="style.css" />

</head>

<body>

<div class="login">

<form method="post" action="login.php" id="login">

<img src="../cmslogo.png" border="0" id="loginlogo" alt="CMS Made Simple"/>

<table border=0 id="table">
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
</form>

</div>

</body>
</html>