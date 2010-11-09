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
require(cms_join_path($dirname,'lib','classes','class.cms_config.php'));
$gCms = new CmsObject();
if (isset($starttime))
{
  cmsms()->set_variable('starttime',$starttime);
}

#Load the config file (or defaults if it doesn't exist)
require(cms_join_path($dirname,'version.php'));
require(cms_join_path($dirname,'lib','config.functions.php'));

#Grab the current configuration
$config = cmsms()->GetConfig();

#Set the timezone
if( $config['timezone'] != '' )
  {
    @date_default_timezone_set(trim($config['timezone']));
  }

#Adjust the url stuff if we're using HTTPS
if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' )
{
  // adjust the root url
//   if( !isset($config['ssl_url']) )
//   { 
//     $config['ssl_url'] = str_replace('http://','https://',$config['root_url']);
//   }
//   $config['root_url'] = $config['ssl_url'];
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


debug_buffer('loading smarty');
require(cms_join_path($dirname,'lib','smarty','Smarty.class.php'));
debug_buffer('loading adodb');
require(cms_join_path($dirname,'lib','adodb.functions.php'));
load_adodb();
debug_buffer('loading page functions');
require_once(cms_join_path($dirname,'lib','page.functions.php'));
debug_buffer('loading content functions');
require_once(cms_join_path($dirname,'lib','content.functions.php'));
debug_buffer('loading translation functions');
require_once(cms_join_path($dirname,'lib','translation.functions.php'));
debug_buffer('loading events functions');
require_once(cms_join_path($dirname,'lib','classes','class.events.inc.php'));
debug_buffer('loading php4 entity decode functions');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'html_entity_decode_php4.php');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'autoloader.php');

require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.pageinfo.inc.php');
cmsms()->set_variable('pageinfo',new PageInfo());

debug_buffer('done loading files');

#Load them into the usual variables.  This'll go away a little later on.
global $DONT_LOAD_DB;
if (!isset($DONT_LOAD_DB))
{
  cmsms()->GetDb();
}

$smarty = cmsms()->GetSmarty();

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
cmsms()->set_variable('pluginnum',1);
if (isset($page))
{
  cmsms()->set_variable('page',$page);
}

#Set a umask
$global_umask = get_site_preference('global_umask','');
if( $global_umask != '' )
{
  @umask( octdec($global_umask) );
}

#Set the locale if it's set
#either in the config, or as a site preference.
if (isset($config['locale']) && $config['locale'] != '')
{
    @setlocale(LC_ALL, $config['locale']);
}
$frontendlang = get_site_preference('frontendlang');

$smarty->assign('sitename', get_site_preference('sitename', 'CMSMS Site'));
$smarty->assign('lang',$frontendlang);
$smarty->assign('encoding',get_encoding());

if ($config['debug'] == true)
{
	$smarty->debugging = true;
	$smarty->error_reporting = 'E_ALL';
}
if (isset($CMS_ADMIN_PAGE) || isset($CMS_STYLESHEET))
{
	#This will only matter on upgrades now.  All new stuff (0.13 on) will be UTF-8.
	if (is_file(cms_join_path($dirname,'lib','convert','ConvertCharset.class.php')))
	{
		include(cms_join_path($dirname,'lib','convert','ConvertCharset.class.php'));
		cmsms()->set_variable('convertclass',new ConvertCharset());
	}
}

#Setup content routes
if( !isset($CMS_ADMIN_PAGE) && !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE) )
{
  $contentops = cmsms()->GetContentOperations();
  $contentops->register_routes();
}

#Load all installed module code
debug_buffer('','Loading Modules');
if (! isset($CMS_INSTALL_PAGE))
  {
    $modload =& cmsms()->GetModuleLoader();
    $modload->LoadModules(isset($LOAD_ALL_MODULES), !isset($CMS_ADMIN_PAGE));
  }
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
