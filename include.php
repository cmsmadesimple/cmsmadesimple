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
#
#$Id$

require_once(dirname(__FILE__).'/fileloc.php');

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
@session_name("CMSSESSID");
@ini_set('url_rewriter.tags', '');
@ini_set('session.use_trans_sid', 0);
#if(!@session_id()) {
if(!@session_id() && (isset($_REQUEST[session_name()]) || isset($CMS_ADMIN_PAGE))) {
	#Trans SID sucks also...
	@ini_set('url_rewriter.tags', '');
	@ini_set('session.use_trans_sid', 0);
	@session_start();
}

require_once(dirname(__FILE__)."/lib/misc.functions.php");

#Make a new CMS object
require(dirname(__FILE__)."/lib/classes/class.global.inc.php");
$gCms = new CmsObject();

#Setup hash for storing all modules
$gCms->cmsmodules = array();
$gCms->userplugins = array();
$gCms->cmsplugins = array();
$gCms->siteprefs = array();

#Load the config file (or defaults if it doesn't exist)
require(dirname(__FILE__)."/version.php");
require(dirname(__FILE__)."/lib/config.functions.php");

#make a local reference
#if (cms_config_check_old_config()) {
#	cms_config_upgrade();
#}
#$config = cms_config_load(true);
$config =& $gCms->GetConfig();

#Hack for changed directory and no way to upgrade config.php
$config['previews_path'] = str_replace('smarty/cms', 'tmp', $config['previews_path']); 

#Set the locale if it's set
if (isset($config['locale']) && $config['locale'] != '')
{
	@setlocale(LC_ALL, $config['locale']);
}

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
}

showmem('including smarty');
require(dirname(__FILE__).'/lib/smarty/Smarty.class.php');
showmem('including adodb');
require(dirname(__FILE__)."/lib/adodb_lite/adodb.inc.php");
showmem('including page.functions.php');
require(dirname(__FILE__)."/lib/page.functions.php");
showmem('including content.functions.php');
require(dirname(__FILE__)."/lib/content.functions.php");
showmem('including class.pageinfo.inc.php');
require(dirname(__FILE__)."/lib/classes/class.pageinfo.inc.php");
showmem('including class.content.inc.php');
require(dirname(__FILE__)."/lib/classes/class.content.inc.php");
showmem('including class.module.inc.php');
require(dirname(__FILE__)."/lib/classes/class.module.inc.php");
showmem('including class.pageinfo.inc.php');
require(dirname(__FILE__)."/lib/classes/class.user.inc.php");
showmem('including class.htmlblob.inc.php');
require(dirname(__FILE__).'/lib/classes/class.htmlblob.inc.php');
showmem('including class.template.inc.php');
require(dirname(__FILE__).'/lib/classes/class.template.inc.php');
showmem('including class.stylesheet.inc.php');
require(dirname(__FILE__).'/lib/classes/class.stylesheet.inc.php');
showmem('including class.contentnode.inc.php');
require(dirname(__FILE__).'/lib/classes/class.contentnode.inc.php');
showmem('including class.contenthierarchymamager.inc.php');
require(dirname(__FILE__).'/lib/classes/class.contenthierarchymanager.inc.php');
showmem('including translation.functions.php');
require(dirname(__FILE__)."/lib/translation.functions.php");
showmem('including class.bookmark.inc.php');
require(dirname(__FILE__)."/lib/classes/class.bookmark.inc.php");
showmem('including class.group.inc.php');
require(dirname(__FILE__)."/lib/classes/class.group.inc.php");

#Load them into the usual variables.  This'll go away a little later on.
if (!isset($DONT_LOAD_DB))
{ 
	$db =& $gCms->GetDB();

	#TODO: Move this into debug...  I think
	$db->fnExecute = 'count_sql_execs';
}

showmem('before 2nd smarty');
$smarty =& $gCms->GetSmarty();
showmem('after 2nd smarty');

#Load content types
$dir = dirname(__FILE__)."/lib/contenttypes";
$ls = dir($dir);
while (($file = $ls->read()) != "")
{
	if (!is_dir("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0))
	{
		if (substr($file,strlen($file)-4,4) == ".php")
		{
			include("$dir/$file");
		}
	}
}

if (!defined('SMARTY_DIR')) {
	define('SMARTY_DIR', dirname(__FILE__).'/lib/smarty/');
}

#Setup global smarty object
#$smarty = new Smarty_CMS($config);
#$gCms->smarty = &$smarty;

#Stupid magic quotes...
if(get_magic_quotes_gpc())
{
	strip_slashes($_GET);
	strip_slashes($_POST);
	strip_slashes($_REQUEST);
	strip_slashes($_COOKIE);
	strip_slashes($_SESSIONS);
}

#Fix for IIS (and others) to make sure REQUEST_URI is filled in
if (!isset($_SERVER['REQUEST_URI']))
{
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
	if(isset($_SERVER['QUERY_STRING']))
		$_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
}

#Setup the object sent to modules
$gCms->variables["pluginnum"] = 1;
if (isset($page))
{
	$gCms->variables["page"] = $page;
}

#Load all site preferences
load_site_preferences();

if (isset($CMS_ADMIN_PAGE))
{
	include_once(dirname(__FILE__)."/".$config['admin_dir']."/lang.php");

	require(dirname(__FILE__).'/lib/convert/ConvertCharset.class.php');
	$gCms->variables['convertclass'] = new ConvertCharset();

}

#Load all installed module code
showmem('before load modules');
if (isset($LOAD_ALL_MODULES))
{
	ModuleOperations::LoadModules(true);
}
else
{
	ModuleOperations::LoadModules(false);
}
showmem('after load modules');

# vim:ts=4 sw=4 noet
?>
