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

$DONT_LOAD_DB=1;
require_once(dirname(__FILE__)."/include.php");

?>

<html>
<head>
	<title>CMS Made Simple Upgrade</title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>

<body>

<div class="body">

<img src="images/cms/cmsbanner.png" width="400" height="96" alt="CMS Banner Logo" />

<div class="headerish">

<p>Upgrade System</p>

</div>

<div class="main">

<?php

if (!isset($_GET["doupgrade"])) {
	echo "<h4>Welcome to the CMS Upgrade System!</h4>";

	echo "<p>In order to upgrade properly, upgrade needs to have write access to your config.php file.  This is so any extra settings that have been introduced in this version can be set to their defaults.</p>";
}

if (!is_writable(dirname(__FILE__)."/config.php"))
{
	?>
	<p><strong>Problem:</strong> config.php is not writable by the web server.  Please fix the permissions and click the button below to check again.</p>

	<p>
	<form action="upgrade.php" method="post">
		<input type="submit" name="submitbutton" value="Try Again" />
	</form>
	</p>
	<?
}
else
{
	echo "<p>Upgrading config.php...";

	if (cms_config_check_old_config())
	{
		cms_config_upgrade();
	}
	$config = cms_config_load(true);
	cms_config_save($config);

	echo "[done]</p>";

	$db = &ADONewConnection($config["dbms"]);
	$db->PConnect($config["db_hostname"],$config["db_username"],$config["db_password"],$config["db_name"]);
	if (!$db) die("Connection failed");
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$gCms->db = &$db;

	$current_version = 1;

	$query = "SELECT version from ".cms_db_prefix()."version";
	$result = $db->Execute($query);
	while($row = $result->FetchRow())
	{
		$current_version = $row["version"];
	}

	if (!isset($_GET["doupgrade"]))
	{
		if ($current_version < $CMS_SCHEMA_VERSION)
		{
			echo "<p>CMS is in need of an upgrade.<p>You are now running schema version ".$current_version." and you need to be upgraded to version ".$CMS_SCHEMA_VERSION.".</p>Please click <a href=\"upgrade.php?doupgrade=true\">here</a> to complete it.</p>";
		}
		else
		{
			echo "<p>Please review config.php,  modify any new settings as necessary and then reset it's permissions back to a locked state.</p>";
			echo "<p>The CMS database is up to date using schema version ".$current_version.".  Please remove this file when possible.  Click <a href=\"index.php\">here</a> to go to your CMS site.</p>";
		}
	}
	else
	{
		while ($current_version < $CMS_SCHEMA_VERSION)
		{
			$filename = "upgrades/upgrade.".$current_version.".to.".($current_version+1).".php";
			include($filename);
			$current_version++;
		}
		echo "<p>Please review config.php,  modify any new settings as necessary and then reset it's permissions back to a locked state.</p>";
		echo "<p>CMS is up to date.  Please click <a href=\"index.php\">here</a> to go to your CMS site.</p>";
	}

}

?>

</div>

</div>

</body>
</html>
<?
# vim:ts=4 sw=4 noet
?>
