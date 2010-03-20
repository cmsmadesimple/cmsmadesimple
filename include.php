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

//Load necessary global functions
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'cmsms.api.php');

//Where are we?
$dirname = ROOT_DIR;

//Setup a global $gCms.  Even if this goes away,
//we need to instantiate CmsApplication near the
//beginning so that event firing starts right away.
$gCms = cmsms();
$GLOBALS['gCms'] = $gCms;

define('CMS_DEFAULT_VERSIONCHECK_URL','http://dev.cmsmadesimple.org/latest_version.php');
define('CMS_SECURE_PARAM_NAME','sp_');
define('CMS_USER_KEY','cmsuserkey');

//Load stuff that hasn't been moved to static methods yet
require_once(cms_join_path($dirname,'lib','page.functions.php'));
require_once(cms_join_path($dirname,'lib','content.functions.php'));

//Setup the session
CmsSession::setup();

//Do any necessary stuff to the actual request
CmsRequest::setup();

if (isset($starttime))
{
    $gCms->variables['starttime'] = $starttime;
}

#Load the config file (or defaults if it doesn't exist)
require(cms_join_path($dirname,'version.php'));
require(cms_join_path($dirname,'lib','config.functions.php'));

#Grab the current configuration
$config = $gCms->GetConfig();

#Attempt to override the php memory limit
if( isset($config['php_memory_limit']) && !empty($config['php_memory_limit'])  )
{
	ini_set('memory_limit',trim($config['php_memory_limit']));
}

#Hack for changed directory and no way to upgrade config.php
$config['previews_path'] = str_replace('smarty/cms', 'tmp', $config['previews_path']);

if ($config["debug"] == true)
{
	@ini_set('display_errors',1);
	@error_reporting(E_STRICT);
}

/*
debug_buffer('loading smarty');
require(cms_join_path($dirname,'lib','smarty','Smarty.class.php'));
*/
CmsProfiler::get_instance()->mark('loading pageinfo functions');
require_once(cms_join_path($dirname,'lib','classes','class.pageinfo.inc.php'));
if (! isset($CMS_INSTALL_PAGE))
{
	CmsProfiler::get_instance()->mark('loading translation functions');
	require_once(cms_join_path($dirname,'lib','translation.functions.php'));
}
/*
debug_buffer('loading events functions');
require_once(cms_join_path($dirname,'lib','classes','class.events.inc.php'));
debug_buffer('loading php4 entity decode functions');
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'html_entity_decode_php4.php');
*/

CmsProfiler::get_instance()->mark('done loading files');

#Load them into the usual variables.  This'll go away a little later on.
global $DONT_LOAD_DB;
if (!isset($DONT_LOAD_DB))
{
    $cmsdb = cms_db();
}

$smarty = cms_smarty();

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
$global_umask = CmsApplication::get_preference('global_umask','');
if( $global_umask != '' )
{
  @umask( octdec($global_umask) );
}

#Set the locale if it's set
#either in the config, or as a site preference.
$frontendlang = CmsApplication::get_preference('frontendlang','');
if (isset($config['locale']) && $config['locale'] != '')
{
	$frontendlang = $config['locale'];
}
if ($frontendlang != '')
{
	@setlocale(LC_ALL, $frontendlang);
}
if (isset($config['timezone']) && ! empty($config['timezone']) && function_exists('date_default_timezone_set'))
{
	date_default_timezone_set($config['timezone']);
}

$smarty->assign('sitename', CmsApplication::get_preference('sitename', 'CMSMS Site'));
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
}

CmsProfiler::get_instance()->mark('Before module loader');

CmsModuleLoader::load_module_data();
#Load all installed module code
//$modload = $gCms->GetModuleLoader();
//$modload->LoadModules(isset($LOAD_ALL_MODULES), !isset($CMS_ADMIN_PAGE));

CmsProfiler::get_instance()->mark('End of include');

# vim:ts=4 sw=4 noet
?>
