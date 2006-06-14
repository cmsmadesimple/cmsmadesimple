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
v v v v v v v
#$Id$
^ ^ ^ ^ ^ ^ ^

$dirname = dirname(__FILE__);
require_once($dirname.DIRECTORY_SEPARATOR.'fileloc.php');

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

require_once($dirname.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."misc.functions.php");

#Make a new CMS object
require(cms_join_path($dirname,"lib","classes","class.global.inc.php"));
$gCms = new CmsObject();

#Setup hash for storing all modules and plugins
$gCms->cmsmodules = array();
$gCms->userplugins = array();
$gCms->userpluginfunctions = array();
$gCms->cmsplugins = array();
$gCms->siteprefs = array();

#Load the config file (or defaults if it doesn't exist)
require(cms_join_path($dirname,'version.php'));
require(cms_join_path($dirname,"lib","config.functions.php"));

#make a local reference
#if (cms_config_check_old_config()) {
#	cms_config_upgrade();
#}
#$config = cms_config_load(true);
$config =& $gCms->GetConfig();

#Hack for changed directory and no way to upgrade config.php
$config['previews_path'] = str_replace('smarty/cms', 'tmp', $config['previews_path']); 

v v v v v v v
^ ^ ^ ^ ^ ^ ^
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

/*
function count_sql_execs($db, $sql, $inputarray)
{
	global $gCms;
	global $sql_execs;
	global $sql_queries;
	if (!is_array($inputarray)) $sql_execs++;
	else if (is_array(reset($inputarray))) $sql_execs += sizeof($inputarray);
	else $sql_execs++;
}
*/

debug_buffer('loading smarty');
require(cms_join_path($dirname,'lib','smarty','Smarty.class.php'));
debug_buffer('loading adodb');
if ($config['use_adodb_lite'] == false || (isset($USE_OLD_ADODB) && $USE_OLD_ADODB == 1 && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."adodb".DIRECTORY_SEPARATOR."adodb.inc.php")))
	require(cms_join_path($dirname,"lib",'adodb','adodb.inc.php'));
else
	require(cms_join_path($dirname,"lib","adodb_lite","adodb.inc.php"));
debug_buffer('loading page functions');
require(cms_join_path($dirname,"lib","page.functions.php"));
debug_buffer('loading content functions');
require(cms_join_path($dirname,"lib","content.functions.php"));
debug_buffer('loading pageinfo functions');
require(cms_join_path($dirname,"lib","classes","class.pageinfo.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.content.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.module.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.user.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.htmlblob.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.template.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.stylesheet.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.contentnode.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.contenthierarchymanager.inc.php"));
require(cms_join_path($dirname,"lib","translation.functions.php"));
require(cms_join_path($dirname,"lib","classes","class.bookmark.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.group.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.events.inc.php"));
require(cms_join_path($dirname,"lib","classes","class.usertags.inc.php"));

debug_buffer('done loading files');

#Load them into the usual variables.  This'll go away a little later on.
global $DONT_LOAD_DB;
if (!isset($DONT_LOAD_DB))
{ 
	$db =& $gCms->GetDB();

	#TODO: Move this into debug...  I think
	#$db->fnExecute = 'count_sql_execs';
}

$smarty =& $gCms->GetSmarty();

#Load content types
$dir = $dirname."/lib/contenttypes";
$handle=opendir($dir);
while ($file = readdir ($handle)) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == 'php') include("$dir".DIRECTORY_SEPARATOR."$file");
}
closedir($handle);

if (!defined('SMARTY_DIR')) {
	define('SMARTY_DIR', $dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR);
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

v v v v v v v
#Set the locale if it's set
#either in the config, or as a site preference.
$frontendlang = get_site_preference('frontendlang','');
if (isset($config['locale']) && $config['locale'] != '')
{
  $frontendlang = $config['locale'];
}
if( $frontendlang != '' )
  {
    @setlocale(LC_ALL, $frontendlang);
  }

^ ^ ^ ^ ^ ^ ^
$smarty->assign('sitename', get_site_preference('sitename', 'CMSMS Site'));
v v v v v v v
$smarty->assign('lang',$frontendlang);
$smarty->assign('encoding',get_encoding());
^ ^ ^ ^ ^ ^ ^

if (isset($CMS_ADMIN_PAGE))
{
	include_once($dirname.DIRECTORY_SEPARATOR.$config['admin_dir'].DIRECTORY_SEPARATOR."lang.php");

	require($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'convert'.DIRECTORY_SEPARATOR.'ConvertCharset.class.php');
	$gCms->variables['convertclass'] = new ConvertCharset();

}

#Load all installed module code
if (isset($LOAD_ALL_MODULES))
{
	ModuleOperations::LoadModules(true,!isset($CMS_ADMIN_PAGE));
}
else
{
	ModuleOperations::LoadModules(false,!isset($CMS_ADMIN_PAGE));
}

# vim:ts=4 sw=4 noet
?>
