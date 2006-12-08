<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

define('ADODB_OUTP', 'debug_sql');

#magic_quotes_runtime is a nuisance...  turn it off before it messes something up
set_magic_quotes_runtime(false);

#Setup session with different id and start it
@session_name('CMSSESSID');
@ini_set('url_rewriter.tags', '');
@ini_set('session.use_trans_sid', 0);
#if(!@session_id()) {
if(!@session_id() && (isset($_REQUEST[session_name()]) || isset($CMS_ADMIN_PAGE))) 
{
    #Trans SID sucks also...
    @ini_set('url_rewriter.tags', '');
    @ini_set('session.use_trans_sid', 0);
    @session_start();
}

# sanitize $_GET
array_walk_recursive($_GET, 'sanitize_get_var');

$dirname = dirname(__FILE__);
require_once($dirname.DIRECTORY_SEPARATOR.'fileloc.php');

//So we have the camelize, etc functions loaded for __autoload
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'misc.functions.php');
debug_buffer('', 'Start of include');

/**
 * The one and only autoload function for the system.  This basically allows us 
 * to remove a lot of the require_once BS and keep the file loading to as much 
 * of a minimum as possible.
 */
function __autoload($class_name)
{
	$dirname = dirname(__FILE__);

	//We do this in order of importance...  classes first
	if (file_exists(cms_join_path($dirname,'lib','classes','class.' . strtolower($class_name) . '.php')))
	{
		require_once(cms_join_path($dirname,'lib','classes','class.' . strtolower($class_name) . '.php'));
	}
	else if (file_exists(cms_join_path($dirname,'lib','classes','class.' . strtolower($class_name) . '.inc.php')))
	{
		require_once(cms_join_path($dirname,'lib','classes','class.' . strtolower($class_name) . '.inc.php'));
	}
	else if (file_exists(cms_join_path($dirname,'lib','classes','class.' . underscore($class_name) . '.php')))
	{
		require_once(cms_join_path($dirname,'lib','classes','class.' . underscore($class_name) . '.php'));
	}
	else if (file_exists(cms_join_path($dirname,'lib','classes','class.' . underscore($class_name) . '.inc.php')))
	{
		require_once(cms_join_path($dirname,'lib','classes','class.' . underscore($class_name) . '.inc.php'));
	}
	else if (ContentOperations::load_content_type($class_name))
	{
	}
}

#Make a new CMS object
//require_once(cms_join_path($dirname,'lib','classes','class.object.php'));
require_once(cms_join_path($dirname,'lib','classes','class.global.inc.php'));
$gCms = cmsms();
if (isset($starttime))
{
    $gCms->variables['starttime'] = $starttime;
}

#Load the config file (or defaults if it doesn't exist)
require(cms_join_path($dirname,'version.php'));
require(cms_join_path($dirname,'lib','config.functions.php'));

#Grab the current configuration
$config =& $gCms->GetConfig();

#define timezone
if (function_exists('date_default_timezone_set') && isset($config['timezone']) && $config['timezone'] != '') 
{
    date_default_timezone_set($config['timezone']);
}

#Define the CMS_ADODB_DT constant
define('CMS_ADODB_DT', $config['use_adodb_lite'] ? 'DT' : 'T');

#Hack for changed directory and no way to upgrade config.php
$config['previews_path'] = str_replace('smarty/cms', 'tmp', $config['previews_path']);

#Add users if they exist in the session
$gCms->variables['user_id'] = '';
if (isset($_SESSION['cms_admin_user_id']))
{
    $gCms->variables['user_id'] = $_SESSION['cms_admin_user_id'];
}

$gCms->variables['username'] = '';
if (isset($_SESSION['cms_admin_username']))
{
    $gCms->variables['username'] = $_SESSION['cms_admin_username'];
}

debug_buffer('loading smarty');
require(cms_join_path($dirname,'lib','smarty','Smarty.class.php'));
debug_buffer('loading adodb');

$loaded_adodb = false;

if ($config['use_adodb_lite'] == false || (isset($USE_OLD_ADODB) && $USE_OLD_ADODB == 1))
{
# CMSMS is configured to use full ADOdb
    $full_adodb = cms_join_path(dirname(__FILE__),'lib','adodb','adodb.inc.php');
    if (! file_exists($full_adodb))
    {
        # Full ADOdb cannot be found, show a debug error message
        $gCms->errors[] = 'CMS Made Simple is configured to use the full ADOdb Database Abstraction library, but it\'s not in the lib' .DIRECTORY_SEPARATOR. 'adodb directory. Switched back to ADOdb Lite.';
    }
    else
    {
        # Load (full) ADOdb
        require($full_adodb);
        $loaded_adodb = true;
    }
}
if (!$loaded_adodb)
{
    $adodb_light = cms_join_path(dirname(__FILE__),'lib','adodb_lite','adodb.inc.php');
    # The ADOdb library is not yet included, try ADOdb Lite
    if (file_exists($adodb_light))
    {
        # Load ADOdb Lite
        require($adodb_light);
    }
    else
    {
        # ADOdb cannot be found, show a message and stop the script execution
        echo "The ADOdb Lite database abstraction library cannot be found, CMS Made Simple cannot load.";
        die();
    }
}

debug_buffer('loading page functions');
require_once(cms_join_path($dirname,'lib','page.functions.php'));
debug_buffer('loading content functions');
require_once(cms_join_path($dirname,'lib','content.functions.php'));
debug_buffer('loading pageinfo functions');
require_once(cms_join_path($dirname,'lib','classes','class.pageinfo.inc.php'));
debug_buffer('loading translation functions');
require_once(cms_join_path($dirname,'lib','translation.functions.php'));
debug_buffer('loading events functions');
//require_once(cms_join_path($dirname,'lib','classes','class.events.inc.php'));
//require_once(cms_join_path($dirname,'lib','classes','class.cms_object_relational_mapping.php'));

if (isset($config['backwards_compatible']) && $config['backwards_compatible'] == true)
{
#	load_backwards_compatibility();
}

debug_buffer('done loading files');

#Load them into the usual variables.  This'll go away a little later on.
global $DONT_LOAD_DB;
if (!isset($DONT_LOAD_DB))
{
    $db =& $gCms->GetDB();
    if($config['dbms'] == 'sqlite')
    {
        sqlite_create_function($db->_connectionID,'now','time',0);
    }
}

$smarty = $gCms->GetSmarty();
$contenttypes =& $gCms->contenttypes;

#Load content types
$dir = cms_join_path($dirname,'lib','classes','contenttypes');
$handle=opendir($dir);
while ($file = readdir ($handle)) 
{
    $path_parts = pathinfo($file);
    if ($path_parts['extension'] == 'php')
    {
		$obj = new CmsContentTypePlaceholder();
		$obj->type = strtolower(basename($file, '.inc.php'));
		$obj->filename = cms_join_path($dir,$file);
		$obj->loaded = false;
		$obj->friendlyname = basename($file, '.inc.php');
		$contenttypes[strtolower(basename($file, '.inc.php'))] = $obj;
    }
}
closedir($handle);

/*
$blocktypes =& $gCms->blocktypes;

#Load block types
$dir = cms_join_path($dirname,'lib','classes','blocktypes');
$handle=opendir($dir);
while ($file = readdir ($handle)) 
{
    $path_parts = pathinfo($file);
    if ($path_parts['extension'] == 'php')
    {
		$obj = new CmsBlockTypePlaceholder();
		$obj->type = str_replace('block.', '', strtolower(basename($file, '.inc.php')));
		$obj->filename = cms_join_path($dir, $file);
		$obj->loaded = false;
		$obj->friendlyname = str_replace('block.', '', basename($file, '.inc.php'));
		$blocktypes[str_replace('block.', '', strtolower(basename($file, '.inc.php')))] = $obj;
    }
}
closedir($handle);
*/

if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', cms_join_path($dirname,'lib','smarty') . DIRECTORY_SEPARATOR);
}

#Stupid magic quotes...
if(get_magic_quotes_gpc())
{
    stripslashes_deep($_GET);
    stripslashes_deep($_POST);
    stripslashes_deep($_REQUEST);
    stripslashes_deep($_COOKIE);
    stripslashes_deep($_SESSION);
}

#Fix for IIS (and others) to make sure REQUEST_URI is filled in
if (!isset($_SERVER['REQUEST_URI']))
{
    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    if(isset($_SERVER['QUERY_STRING']))
    {
        $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
    }
}

#Setup the object sent to modules
$gCms->variables['pluginnum'] = 1;
if (isset($page))
{
	$gCms->variables['page'] = $page;
}

#Set a umask
$global_umask = get_site_preference('global_umask','');
if( $global_umask != '' )
{
    @umask( $global_umask );
}

#Set the locale if it's set
#either in the config, or as a site preference.
$frontendlang = get_site_preference('frontendlang','');
if (isset($config['locale']) && $config['locale'] != '')
{
    $frontendlang = $config['locale'];
}
if ($frontendlang != '')
{
    @setlocale(LC_ALL, $frontendlang);
}

$smarty->assign('sitename', get_site_preference('sitename', 'CMSMS Site'));
$smarty->assign('lang',$frontendlang);
$smarty->assign('encoding',get_encoding());

if (isset($CMS_ADMIN_PAGE))
{
    include_once(cms_join_path($dirname,$config['admin_dir'],'lang.php'));

	#This will only matter on upgrades now.  All new stuff (0.13 on) will be UTF-8.
	if (is_file(cms_join_path($dirname,'lib','convert','ConvertCharset.class.php')))
	{
		include(cms_join_path($dirname,'lib','convert','ConvertCharset.class.php'));
		$gCms->variables['convertclass'] = new ConvertCharset();
	}
}

#Load all installed module code
$modload =& $gCms->GetModuleLoader();
$modload->LoadModules(isset($LOAD_ALL_MODULES), !isset($CMS_ADMIN_PAGE));

debug_buffer('', 'End of include');

function sanitize_get_var(&$value, $key)
{
    $value = eregi_replace('\<\/?script[^\>]*\>', '', $value);
}

function load_backwards_compatibility()
{
	$dirname = dirname(__FILE__);
	debug_buffer('loading template functions');
	require_once(cms_join_path($dirname,'lib','classes','class.templateoperations.inc.php'));
	debug_buffer('loading gcb functions');
	require_once(cms_join_path($dirname,'lib','classes','class.globalcontentoperations.inc.php'));
	debug_buffer('loading module class');
	require_once(cms_join_path($dirname,'lib','classes','class.moduleoperations.inc.php'));
	debug_buffer('loading bookmark functions');
	require_once(cms_join_path($dirname,'lib','classes','class.bookmarkoperations.inc.php'));
	debug_buffer('loading content class');
	require_once(cms_join_path($dirname,'lib','classes','class.contentoperations.inc.php'));
	debug_buffer('loading user functions');
	require_once(cms_join_path($dirname,'lib','classes','class.useroperations.inc.php'));
	debug_buffer('loading group functions');
	require_once(cms_join_path($dirname,'lib','classes','class.groupoperations.inc.php'));
	debug_buffer('loading stylesheet functions');
	require_once(cms_join_path($dirname,'lib','classes','class.stylesheetoperations.inc.php'));
	debug_buffer('loading user tags functions');
	require_once(cms_join_path($dirname,'lib','classes','class.usertagoperations.inc.php'));
}

# vim:ts=4 sw=4 noet
?>