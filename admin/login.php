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

if ($_POST["username"] && $_POST["password"]) {

	$username = $_POST["username"];
	$password = $_POST["password"];

	$db = new DB($config);

	$query = "SELECT * FROM ".$config->db_prefix."users WHERE username = '".mysql_escape_string($username)."' and password = '".mysql_escape_string($password)."'";
	$result = $db->query($query);

	$line = mysql_fetch_array($result, MYSQL_ASSOC);

	if (isset($line["user_id"])) {
		$_SESSION["user_id"] = $line["user_id"];	
		$_SESSION["username"] = $line["username"];	
		mysql_free_result($result);
		$db->close($link);
		redirect("index.php");
		return;
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
