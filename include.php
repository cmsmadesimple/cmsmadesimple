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

/**
 * This file is included in every page.  It does all seutp functions including
 * importing additional functions/classes, setting up sessions and nls, and
 * construction of various important variables like $gCms.
 *
 * @package CMS
 */
#magic_quotes_runtime is a nuisance...  turn it off before it messes something up
set_magic_quotes_runtime(false);

#Setup session with different id and start it
session_name("CMSSESSID");
if(!session_id()) {
	session_start();
}

#Make a new CMS object
require_once(dirname(__FILE__)."/lib/global.functions.php");
$gCms = new CmsObject();

#Load the config file (or defaults if it doesn't exist)
require_once(dirname(__FILE__)."/version.php");
require_once(dirname(__FILE__)."/lib/config.functions.php");

#make a local reference
#if (cms_config_check_old_config()) {
#	cms_config_upgrade();
#}
$config = cms_config_load(true);

#Attach to global object
$gCms->config = &$config;

#Add users if they exist in the session
$gCms->variables["user_id"] = "";
if (isset($_SESSION["cms_admin_user_id"]))
{
	$gCms->variables["user_id"] = $_SESSION["cms_admin_user_id"];
}

$gCms->variables["username"] = "";
if (isset($_SESSION["cms_admin_username"]))
{
	$gCms->variables["username"] = $_SESSION["cms_admin_username"];
}

#Setup db connection
include_once(dirname(__FILE__)."/adodb/adodb.inc.php");

$sql_execs = 0;
$sql_queries = "";
function count_sql_execs($db, $sql, $inputarray)
{
	global $gCms;
	global $sql_execs;
	global $sql_queries;
	if (!is_array($inputarray)) $sql_execs++;
	else if (is_array(reset($inputarray))) $sql_execs += sizeof($inputarray);
	else $sql_execs++;
	if ($gCms->config["debug"] == true)
	{
		$sql_queries .= "<p>$sql</p>\n";
	}
}

if (!isset($DONT_LOAD_DB)) {
	$db = &ADONewConnection($config['dbms']);
	$db->PConnect($config["db_hostname"],$config["db_username"],$config["db_password"],$config["db_name"]);
	if (!$db) die("Connection failed");
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$db->fnExecute = 'count_sql_execs';
	$gCms->db = &$db;
}

require_once(dirname(__FILE__)."/lib/db.functions.php");
require_once(dirname(__FILE__)."/lib/misc.functions.php");
require_once(dirname(__FILE__)."/lib/page.functions.php");
require_once(dirname(__FILE__)."/lib/content.functions.php");
require_once(dirname(__FILE__)."/lib/module.functions.php");
require_once(dirname(__FILE__)."/lib/translation.functions.php");

if (!defined('SMARTY_DIR')) {
	define('SMARTY_DIR', dirname(__FILE__).'/smarty/');
}

#Stupid magic quotes...
if(get_magic_quotes_gpc())
{
	strip_slashes($_GET);
	strip_slashes($_POST);
	strip_slashes($_COOKIE);
	strip_slashes($_SESSIONS);
}

#Setup the object sent to modules
$gCms->variables["pluginnum"] = 1;
if (isset($page)) {
	$gCms->variables["page"] = $page;
}

#Setup hash for storing all modules
$gCms->cmsmodules = array();
$gCms->userplugins = array();
$gCms->cmsplugins = array();
$gCms->siteprefs = array();

#Load all installed module code
load_modules();

#Load all site preferences
load_site_preferences();

#Nice decent default
$current_language = "en_US";

#Only do language stuff for admin pages
if (isset($CMS_ADMIN_PAGE)) {

	#Read in all current languages...
	$dir = dirname(__FILE__)."/lang";
	$ls = dir($dir);
	while (($file = $ls->read()) != "") {
		if (is_file("$dir/$file") && strpos($file, "nls.php") != 0) {
			include("$dir/$file");
		}
	}

	#Check to see if there is already a language in use...
	if (isset($_POST["change_cms_lang"]))
	{
		$current_language = $_POST["change_cms_lang"];
		setcookie("cms_language", $_POST["change_cms_lang"]);
	}
	else if (isset($_COOKIE["cms_language"]))
	{
		$current_language = $_COOKIE["cms_language"];
	}
	else
	{
		#No, take a stab at figuring out the default language...
		#Figure out default language and set it if it exists
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			$svrstring = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
			$alllang = substr($svrstring,0,strpos($svrstring, ";"));
			$langs = explode(",", $alllang);

			foreach ($langs as $onelang) {
				#Check to see if lang exists...
				if (isset($nls['language'][$onelang])) {
					$current_language = $onelang;
					setcookie("cms_language", $onelang);
					break;
				}
				#Check to see if alias exists...
				if (isset($nls['alias'][$onelang])) {
					$alias = $nls['alias'][$onelang];
					if (isset($nls['language'][$alias])) {
						$current_language = $alias;
						setcookie("cms_language", $alias);
						break;
					}
				}
			}
		}
	}
	#Ok, we have a language to load, let's load it already...
	if (isset($nls['file'][$current_language]))
	{
		foreach ($nls['file'][$current_language] as $onefile)
		{
			include($onefile);
		}
	}
}

#Check for HTML_BBCodeParser
if ($config["use_bb_code"] == true) {
	if (include_once(dirname(__FILE__)."/lib/PEAR.php")) {
		if (include_once("HTML/BBCodeParser.php")) {
			$gCms->bbcodeparser = new HTML_BBCodeParser();
		}
	}
}

# vim:ts=4 sw=4 noet
?>
