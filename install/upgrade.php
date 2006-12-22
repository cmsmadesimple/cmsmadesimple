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

#$DONT_LOAD_DB=1;

// check PHP version
$version = explode('.', phpversion());
if ((int) $version[0] < 5)
{
    echo 'Unable to upgrade. This version of CMS Made Simple requires at least PHP5 and this server is using an older version ('. phpversion() . ').'; die();
}

$LOAD_ALL_MODULES=1;
$USE_OLD_ADODB=1;

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."fileloc.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."include.php");

//load_backwards_compatibility();

//Do module autoupgrades 
function module_autoupgrade()
{
	global $gCms;
	$db =& $gCms->GetDB();

	foreach ($gCms->modules as $modulename=>$value)
	{
		if ($gCms->modules[$modulename]['object']->AllowAutoUpgrade() == true)
		{
			//Check to see what version we currently have in the database (if it's installed)
			$module_version = false;

			$query = "SELECT version from ".cms_db_prefix()."modules WHERE module_name = ?";
			$result = $db->Execute($query, array($modulename));
			while ($row = $result->FetchRow())
			{
				$module_version = $row['version'];
			}

			//Check to see what version we have in the file system
			$file_version = $gCms->modules[$modulename]['object']->GetVersion();

			if ($module_version != false)
			{
				if (version_compare($file_version, $module_version) == 1)
				{
					echo "<p>Upgrading $modulename module from $module_version to $file_version...";
					$gCms->modules[$modulename]['object']->Upgrade($module_version, $file_version);
					$query = "UPDATE ".cms_db_prefix()."modules SET version = ?, admin_only = ? WHERE module_name = ?";
					$result = $db->Execute($query, array($file_version, ($gCms->modules[$modulename]['object']->IsAdminOnly()==true?1:0), $modulename));
					Events::SendEvent('Core', 'ModuleUpgraded', array('name' => $modulename, 'oldversion' => $module_version, 'newversion' => $file_version));
					echo "[Done]</p>";
				}
			}
		}
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>CMS Made Simple Upgrade</title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>

<body>

<div class="body">

<img src="../images/cms/cmsbanner.jpg" width="800" height="100" alt="CMS Banner Logo" />

<div class="headerish">

<h1>Upgrade System</h1>

</div>

<div class="main">

<?php

clearstatcache();
if (!is_writable(TMP_TEMPLATES_C_LOCATION) || !is_writable(TMP_CACHE_LOCATION))
{
	echo '<p>The following directories must be writable by the web server:<br />';
	echo 'tmp/cache<br />';
	echo 'tmp/templates_c<br /></p>';
	echo '<p>Please correct by executing:<br /><em>chmod 777 tmp/cache<br />chmod 777 tmp/templates_c</em><br />or the equivilent for your platform before continuing.</p>';
	echo '</div></div>';
	echo '</body></html>';
	exit;
}

if (!isset($_GET["doupgrade"])) {
	echo "<h3>Welcome to the CMS Upgrade System!</h3>";

	echo "<p>In order to upgrade properly, upgrade needs to have write access to your config.php file.  This is so any extra settings that have been introduced in this version can be set to their defaults.</p>";
}

if (!is_writable(CONFIG_FILE_LOCATION))
{
	?>
	<p><strong>Problem:</strong> config.php is not writable by the web server.  Please fix the permissions and click the button below to check again.</p>

	<p>
	<form action="upgrade.php" method="post">
		<input type="submit" name="submitbutton" value="Try Again" />
	</form>
	</p>
	<?php
}
else
{
	echo "<p>Upgrading config.php...";

	$config = CmsConfig::get_instance();
	$config->load(true, true);
	$config->save();

	echo "[done]</p>";

	echo "<p>Cleaning cache dirs...";

	@clearstatcache();

	//Clear cache dirs
	$cpath = TMP_CACHE_LOCATION;
	$handle=opendir($cpath);
	while ($cfile = readdir($handle)) {
		#echo $cpath.$cfile . '--' . var_export(is_file($cpath.DIRECTORY_SEPARATOR.$cfile), true) . "<br />";
		if ($cfile != "." && $cfile != ".." && is_file($cpath.DIRECTORY_SEPARATOR.$cfile)) {
			unlink($cpath.DIRECTORY_SEPARATOR.$cfile);
		}
	}
	$cpath = TMP_TEMPLATES_C_LOCATION;
	$handle=opendir($cpath);
	while ($cfile = readdir($handle)) {
		#echo $cpath.$cfile . '--' . var_export(is_file($cpath.DIRECTORY_SEPARATOR.$cfile), true) . "<br />";
		if ($cfile != "." && $cfile != ".." && is_file($cpath.DIRECTORY_SEPARATOR.$cfile)) {
			unlink($cpath.DIRECTORY_SEPARATOR.$cfile);
		}
	}

	echo "[done]</p>";

	/*
	$db = &ADONewConnection($config["dbms"], 'pear:date:extend:transaction');
	$db->Connect($config["db_hostname"],$config["db_username"],$config["db_password"],$config["db_name"]);
	if (!$db) die("Connection failed");
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	global $gCms;
	$gCms->db = $db;
	*/
	
	$db = cms_db();

	$current_version = 1;

	$query = "SELECT version from ".cms_db_prefix()."version";
	$result = $db->Execute($query);
	while($result && $row = $result->FetchRow())
	{
		$current_version = $row["version"];
	}

	if (!isset($_GET["doupgrade"]))
	{
		if ($current_version < $CMS_SCHEMA_VERSION)
		{
			echo "<p>CMS is in need of an upgrade.</p><p>You are now running schema version ".$current_version." and you need to be upgraded to version ".$CMS_SCHEMA_VERSION.'.</p><p class="important">Please click <a href="upgrade.php?doupgrade=true">here</a> to complete the upgrade.</p>';
		}
		else
		{
			module_autoupgrade();

			echo "<p>Please review config.php, modify any new settings as necessary and then reset it's permissions back to a locked state. You should also check that all of your modules are up to date, by going to the Extensions -> Modules page and looking for any listed as 'Needs Upgrade'.</p>";
			echo "<p>The CMS database is up to date using schema version ".$current_version.". Please remove this file when possible. Click <a href=\"../index.php\">here</a> to go to your CMS site.</p><br />";
		}

	}
	else
	{
		while ($current_version < $CMS_SCHEMA_VERSION)
		{
			$filename = "upgrades".DIRECTORY_SEPARATOR."upgrade.".$current_version.".to.".($current_version+1).".php";
			include($filename);
			$current_version++;
		}

		module_autoupgrade();


		echo "<p>Please review config.php, modify any new settings as necessary and then reset it's permissions back to a locked state.</p>";
		echo "<p>CMS is up to date. Please click <a href=\"../index.php\">here</a> to go to your CMS site or you can <a href=\"../".$config["admin_dir"]."/\">go to the Admin Panel</a>.</p>";

	}

	if (file_exists(TMP_CACHE_LOCATION.DIRECTORY_SEPARATOR."SITEDOWN"))
	{
		if (!unlink(TMP_CACHE_LOCATION.DIRECTORY_SEPARATOR."SITEDOWN"))
		{
			echo "<p class=\"error\"><strong>Error:</strong> Could not remove the tmp/cache/SITEDOWN file.  Please remove manually or you will continue to show a \"Site Down for Maintainence\" message on your site.</p>";
		}
	}
}

?>

</div>

</div>

</body>
</html>
<?php
# vim:ts=4 sw=4 noet
?>
