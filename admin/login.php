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

$error = "";

if ($_POST["username"] && $_POST["password"]) {

	$username = $_POST["username"];
	$password = $_POST["password"];

	$db = new DB($config);

	$query = "SELECT * FROM ".$config->db_prefix."users WHERE username = '".mysql_escape_string($username)."' and password = '".mysql_escape_string($password)."'";
	$result = $db->query($query);

	$line = mysql_fetch_array($result, MYSQL_ASSOC);

	if (isset($line["user_id"])) {
		setcookie("cms_admin_user_id", $line["user_id"]);
		$_SESSION["cms_admin_user_id"] = $line["user_id"];	
		$_SESSION["cms_admin_username"] = $line["username"];	
		mysql_free_result($result);
		$db->close($link);
		redirect("index.php");
		return;
	}
	else {
		$error .= "<p>Username or Password incorrect!</p>";
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

<?

	if ($error != "") {
		echo "<div class=\"loginerror\">".$error."</div>";
	}

?>

<form method="post" action="login.php" id="login">

<img src="../images/cmslogo.png" border="0" id="loginlogo" alt="CMS Made Simple"/>

<table border=0 id="table">
	<tr>
		<td align="right"><?=GetText::gettext("Username")?>:</td>
		<td><input type="text" id="username" name="username" value="<?echo $_POST["username"]?>" size="15"/></td>
	</tr>
	<tr>
		<td align="right"><?=GetText::gettext("Password")?>:</td>
		<td><input type="password" id="password" name="password" size="15" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="<?=GetText::gettext("Submit")?>" /></td>
	</tr>
</table>
</form>

</div>


</body>
</html>
<?php
# vim:ts=4 sw=4 noet
?>
