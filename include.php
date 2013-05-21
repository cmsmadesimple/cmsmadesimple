<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

define('CMS_DEFAULT_VERSIONCHECK_URL','http://www.cmsmadesimple.org/latest_version.php');
define('CMS_SECURE_PARAM_NAME','_sx_');
define('CMS_USER_KEY','_userkey_');

$session_key = substr(md5($dirname), 0, 8);
if( !isset($CMS_INSTALL_PAGE) )
  {
    @session_name('CMSSESSID' . $session_key);
    @ini_set('url_rewriter.tags', '');
    @ini_set('session.use_trans_sid', 0);
  }

#Setup session with different id and start it
if( isset($CMS_ADMIN_PAGE) || isset($CMS_INSTALL_PAGE) )
  {
    // admin pages can't be cached... period, at all.. never.
    @session_cache_limiter('private');
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
  }
else
  {
    @session_cache_limiter('public');
  }

if(!@session_id()) session_start();


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

// minimum stuff to get started (autoloader needs the cmsms() and the config stuff.
//require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.cms_variables.php');
require_once($dirname.DIRECTORY_SEPARATOR.'fileloc.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.CmsException.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.cms_config.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.CmsApp.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'autoloader.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'misc.functions.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'module.functions.php');
require_once($dirname.DIRECTORY_SEPARATOR.'version.php');
debug_buffer('done loading required files');

// sanitize $_GET
array_walk_recursive($_GET, 'sanitize_input_array');
// sanitize $_SERVER
array_walk_recursive($_SERVER,'sanitize_input_array');

if (isset($starttime))
{
  cmsms()->set_variable('starttime',$starttime);
}


if( isset($CMS_ADMIN_PAGE) )
  {
    function cms_admin_sendheaders($content_type = 'text/html',$charset = '')
    {
      if( !$charset ) $charset = get_encoding();

      // Date in the past
      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

      // always modified
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
 
      // HTTP/1.1
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);

      // HTTP/1.0
      header("Pragma: no-cache");
        
      // Language shizzle
      header("Content-Type: $content_type; charset=$charset");
    }

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
	     cms_cookies::set(CMS_SECURE_PARAM_NAME,$key);
	   }
       }
  }


# Create the global
$gCms = cmsms();
#Grab the current configuration
$config = $gCms->GetConfig();

#Set the timezone
if( $config['timezone'] != '' )
  {
    @date_default_timezone_set(trim($config['timezone']));
  }

#Attempt to override the php memory limit
if( isset($config['php_memory_limit']) && !empty($config['php_memory_limit'])  )
  {
    ini_set('memory_limit',trim($config['php_memory_limit']));
  }

#Add users if they exist in the session
cmsms()->set_variable('user_id','');
cmsms()->set_variable('username','');
if (isset($_SESSION['cms_admin_user_id']))
{
  cmsms()->set_variable('user_id',$_SESSION['cms_admin_user_id']);
}
if (isset($_SESSION['cms_admin_username']))
{
  cmsms()->set_variable('username',$_SESSION['cms_admin_username']);
}

if ($config["debug"] == true)
  {
    @ini_set('display_errors',1);
    @error_reporting(E_ALL);
  }


debug_buffer('loading adodb');
require(cms_join_path($dirname,'lib','adodb.functions.php'));
load_adodb();

debug_buffer('loading page functions');
require_once(cms_join_path($dirname,'lib','page.functions.php'));

debug_buffer('loading content functions');
require_once(cms_join_path($dirname,'lib','content.functions.php'));

debug_buffer('loading translation functions');
require_once(cms_join_path($dirname,'lib','translation.functions.php'));

debug_buffer('loading php4 entity decode functions');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'html_entity_decode_php4.php');

debug_buffer('done loading files');

#Load them into the usual variables.  This'll go away a little later on.
global $DONT_LOAD_DB;
if (!isset($DONT_LOAD_DB))
{
  debug_buffer('Initialize Database');
  cmsms()->GetDb();
  debug_buffer('Done Initializing Database');

  if( isset($CMS_ADMIN_PAGE) && !isset($CMS_LOGIN_PAGE) ) {
    $db = cmsms()->GetDb();
    $current_version = $CMS_SCHEMA_VERSION;
    $query = "SELECT version from ".cms_db_prefix()."version";
    $current_version = $db->GetOne($query);
    if ($current_version < $CMS_SCHEMA_VERSION) {
      redirect($config['root_url'] . "/install/upgrade.php");
    }
  }
}

debug_buffer('Initialize Smarty');
$smarty = cmsms()->GetSmarty();
debug_buffer('Done Initialiing Smarty');

/*
// Neccery? -Stikki-
if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', cms_join_path($dirname,'lib','smarty') . DIRECTORY_SEPARATOR);
}
*/

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

#Set a umask
$global_umask = get_site_preference('global_umask','');
if( $global_umask != '' )
{
  @umask( octdec($global_umask) );
}

if ($config['debug'] == true)
{
  $smarty->debugging = true;
  $smarty->error_reporting = 'E_ALL';
}

#Setup content routes
/* not needed
if( !isset($CMS_ADMIN_PAGE) && !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE) )
{
  debug_buffer('','Loading Routes');
  cmsms()->set_variable('pageinfo',new PageInfo());
  $contentops = cmsms()->GetContentOperations();
  $contentops->register_routes();
  debug_buffer('','End of Loading Routes');
}
*/

#Load all installed module code
if (! isset($CMS_INSTALL_PAGE))
  {
    debug_buffer('','Loading Modules');
    $modops = cmsms()->GetModuleOperations();
    $modops->LoadModules(isset($LOAD_ALL_MODULES), !isset($CMS_ADMIN_PAGE));
    debug_buffer('', 'End of Loading Modules');
  }

#Setup language stuff.... will auto-detect languages (Launch only to admin at this point)
if(isset($CMS_ADMIN_PAGE)) 
	CmsNlsOperations::set_language();

$CMS_LAZYLOAD_MODULES = 1; // still used ??

#Do auto task stuff.
if (! isset($CMS_INSTALL_PAGE))
  {
    CmsRegularTaskHandler::handle_tasks();
  }

$smarty->assign('sitename', get_site_preference('sitename', 'CMSMS Site'));

function sanitize_input_array(&$value, $key)
{
  $value = preg_replace('/\<\/?script[^\>]*\>/i', '', $value); //the i makes it caseinsensitive
  $value = preg_replace('/javascript\:/i', '', $value); //the i makes it caseinsensitive
}

# vim:ts=4 sw=4 noet
?>
