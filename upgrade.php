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

require_once("include.php");

?>

<html>
<head>
	<title>CMS Made Simple Upgrade</title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>

<body>

<div class="body">

<img src="images/cmsbanner.png" width="400" height="96" alt="CMS Banner Logo" />

<div class="headerish">

<p>Upgrade System</p>

</div>

<div class="main">

<?

$current_version = 1;

$db = new DB($config);

$query = "SELECT version from ".$config->db_prefix."version";
$result = $db->query($query);
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$current_version = $row["version"];
}

mysql_free_result($result);

if (!isset($_GET["doupgrade"])) {

	if ($current_version < $CMS_SCHEMA_VERSION) {
		echo "<p>CMS is in need of an upgrade.<p>You are now running schema version ".$current_version." and you need to be upgraded to version ".$CMS_SCHEMA_VERSION.".</p>Please click <a href=\"upgrade.php?doupgrade=true\">here</a> to complete it.</p>";
	}
	else {
		echo "<p>The CMS database is up to date using schema version ".$current_version.".  Please remove this file when possible.  Click <a href=\"index.php\">here</a> to go to your CMS site.</p>";
	}

}
else {
	while ($current_version < $CMS_SCHEMA_VERSION) {
		$filename = "upgrades/upgrade.".$current_version.".to.".($current_version+1).".php";
		include($filename);
		$current_version++;
	}
	echo "<p>CMS is up to date.  Please click <a href=\"index.php\">here</a> to go to your CMS site.</p>";
}

# vim:ts=4 sw=4 noet
?>

</div>

</div>

</body>
</html>
