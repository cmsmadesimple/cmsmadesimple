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

$dirname = dirname(__FILE__);
require_once($dirname.DIRECTORY_SEPARATOR.'fileloc.php');

define('CMS_DEFAULT_VERSIONCHECK_URL','http://dev.cmsmadesimple.org/latest_version.php');
define('CMS_SECURE_PARAM_NAME','sp_');
define('CMS_USER_KEY','cmsuserkey');

$session_key = substr(md5($dirname), 0, 8);

#Setup session with different id and start it
@session_name('CMSSESSID' . $session_key);
@ini_set('url_rewriter.tags', '');
@ini_set('session.use_trans_sid', 0);
if(!@session_id()) session_start();

if( isset($CMS_ADMIN_PAGE) )
  {
     if( !isset($_SESSION[CMS_USER_KEY]) )
       {
	 if( isset($_COOKIE[CMS_SECURE_PARAM_NAME]) )
	   {
	     $_SESSION[CMS_USER_KEY] = $_COOKIE[CMS_SECURE_PARAM_NAME];
	   }
	 else
	   {
	     // maybe change this algorithm.
	     $key = substr(str_shuffle(md5($dirname.time().session_id())),-8);
	     $_SESSION[CMS_USER_KEY] = $key;
	     setcookie(CMS_SECURE_PARAM_NAME, $key);
	   }
       }
  }

/**
 * This file is included in every page.  It does all seutp functions including
 * importing additional functions/classes, setting up sessions and nls, and
 * construction of various important variables like $gCms.
 *
 * @package CMS
 */
#magic_quotes_runtime is a nuisance...  turn it off before it messes something up
if (version_compare(phpversion(),"5.3.0","<")) {
  set_magic_quotes_runtime(false);
}

require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'misc.functions.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'module.functions.php');
debug_buffer('', 'Start of include');

# sanitize $_GET
array_walk_recursive($_GET, 'sanitize_get_var'); 

#Make a new CMS object
require(cms_join_path($dirname,'lib','classes','class.global.inc.php'));
$gCms = new CmsObject();
if (isset($starttime))
{
    $gCms->variables['starttime'] = $starttime;
}

#Load the config file (or defaults if it doesn't exist)
require(cms_join_path($dirname,'version.php'));
require(cms_join_path($dirname,'lib','config.functions.php'));

#Grab the current configuration
$config =& $gCms->GetConfig();

#Adjust the url stuff if we're using HTTPS
if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' )
{
  // adjust the root url
  if( !isset($config['ssl_url']) )
  { 
    $config['ssl_url'] = str_replace('http://','https://',$config['root_url']);
  }
  $config['root_url'] = $config['ssl_url'];

  // adjust the uploads url
  if( !isset($config['ssl_uploads_url']) )
  {
    $config['ssl_uploads_url'] = str_replace('http://','https://',$config['ssl_uploads_url']);
  }
  $config['uploads_url'] = $config['ssl_uploads_url'];
}
else if( startswith($config['root_url'],'https://') )
{
  // okay, not using SSL, but the root url is https...
  if( !isset($config['non_ssl_url']) )
    {
      $config['non_ssl_url'] = str_replace('https://','http://',$config['root_url']);
    }
  $config['root_url'] = $config['non_ssl_url'];

  if( !isset($config['non_ssl_uploads_url']) )
    {
      $config['non_ssl_uploads_url'] = str_replace('https://','http://',$config['uploads_url']);
    }
  $config['uploads_url'] = $config['non_ssl_uploads_url'];
}

#Attempt to override the php memory limit
if( isset($config['php_memory_limit']) && !empty($config['php_memory_limit'])  )
  {
    ini_set('memory_limit',trim($config['php_memory_limit']));
  }

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

if ($config["debug"] == true)
  {
    @ini_set('display_errors',1);
    @error_reporting(E_ALL);
  }


debug_buffer('loading smarty');
require(cms_join_path($dirname,'lib','smarty','Smarty.class.php'));
debug_buffer('loading adodb');
require(cms_join_path($dirname,'lib','adodb.functions.php'));
load_adodb();
debug_buffer('loading page functions');
require_once(cms_join_path($dirname,'lib','page.functions.php'));
debug_buffer('loading content functions');
require_once(cms_join_path($dirname,'lib','content.functions.php'));
debug_buffer('loading pageinfo functions');
require_once(cms_join_path($dirname,'lib','classes','class.pageinfo.inc.php'));
if (! isset($CMS_INSTALL_PAGE))
{
	debug_buffer('loading translation functions');
	require_once(cms_join_path($dirname,'lib','translation.functions.php'));
}
debug_buffer('loading events functions');
require_once(cms_join_path($dirname,'lib','classes','class.events.inc.php'));
debug_buffer('loading php4 entity decode functions');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'html_entity_decode_php4.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'autoloader.php');

debug_buffer('done loading files');

#Load them into the usual variables.  This'll go away a little later on.
global $DONT_LOAD_DB;
if (!isset($DONT_LOAD_DB))
{
    $cmsdb =& $gCms->GetDB();
//    $cmsdb->Execute('set names utf8'); // database connection with utf-8
}

$smarty =& $gCms->GetSmarty();
$contenttypes =& $gCms->contenttypes;

#Load content types
$dir = cms_join_path($dirname,'lib','classes','contenttypes');
$handle=opendir($dir);
while ($file = readdir ($handle)) 
{
    $path_parts = pathinfo($file);
    if (isset($path_parts['extension']) && $path_parts['extension'] == 'php')
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
  @umask( octdec($global_umask) );
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
$smarty->assign_by_ref('gCms',$gCms);

if ($config['debug'] == true)
{
	$smarty->debugging = true;
	$smarty->error_reporting = 'E_ALL';
}
if (isset($CMS_ADMIN_PAGE) || isset($CMS_STYLESHEET))
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

#Do auto task stuff.
if (! isset($CMS_INSTALL_PAGE))
  {
    CmsRegularTaskHandler::handle_tasks();
  }


function sanitize_get_var(&$value, $key)
{
  if (version_compare(phpversion(),"5.3.0","<")) {
    $value = eregi_replace('\<\/?script[^\>]*\>', '', $value);
  } else {
    $value = preg_replace('/\<\/?script[^\>]*\>/i', '', $value); //the i makes it caseinsensitive
  }
}

# vim:ts=4 sw=4 noet
?>
