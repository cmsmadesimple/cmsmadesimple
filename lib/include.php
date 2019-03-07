<?php
#CMS - CMS Made Simple
#(c)2004-2013 by Ted Kulp (wishy@users.sf.net)
#(c)2011-2016 by The CMSMS Dev Team
#Visit our homepage at: http://www.cmsmadesimple.org
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
namespace CMSMS;
use CMSMS\internal\global_cache;
use CmsApp;
use cms_siteprefs;
use ModuleOperations;
use CmsNlsOperations;

/**
 * This file is included in every page.  It does all setup functions including
 * importing additional functions/classes, setting up sessions and nls, and
 * construction of various important variables like $gCms.
 *
 * This function cannot be included by third party applications to create access to CMSMS API's.  It is intended for
 * and supported for use in CMSMS applications only.
 *
 * @package CMS
 */

/**
 * Special variables that may be set before this file is included which will influence its behavior.
 *
 * DONT_LOAD_DB = Indicates that the database should not be initialized and any database related functions should not be called
 * DONT_LOAD_SMARTY = Indicates that smarty should not be initialized, and no smarty related variables assigned.
 * CMS_INSTALL_PAGE - Indicates that the file was included from the CMSMS Installation/Upgrade process
 * CMS_PHAR_INSTALLER - Indicates that the file was included from the CMSMS PHAR based installer (note: CMS_INSTALL_PAGE will also be set).
 * CMS_ADMIN_PAGE - Indicates that the file was included from an admin side request.
 * CMS_LOGIN_PAGE - Indicates that the file was included from the admin login form.
 */

define('CMS_DEFAULT_VERSIONCHECK_URL','https://www.cmsmadesimple.org/latest_version.php');
define('CMS_SECURE_PARAM_NAME','_k_'); // used for CSRF protection
define('CMS_USER_KEY','_userkey_'); // used for CSRF protection
define('CONFIG_FILE_LOCATION',dirname(__DIR__).'/config.php');
$dirname = __DIR__;
global $CMS_INSTALL_PAGE,$CMS_ADMIN_PAGE,$CMS_LOGIN_PAGE,$DONT_LOAD_DB,$DONT_LOAD_SMARTY;

if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}

if (!isset($CMS_INSTALL_PAGE) && (!file_exists(CONFIG_FILE_LOCATION) || filesize(CONFIG_FILE_LOCATION) < 100)) {
    die ('FATAL ERROR: config.php file not found or invalid');
}

// sanitize $_SERVER and $_GET
$_SERVER = filter_var_array($_SERVER, FILTER_SANITIZE_STRING);
$_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);

// include some stuff
require_once($dirname.DIRECTORY_SEPARATOR.'/vendor/autoload.php');
require_once($dirname.DIRECTORY_SEPARATOR.'compat.functions.php');
require_once($dirname.DIRECTORY_SEPARATOR.'misc.functions.php');
require_once($dirname.DIRECTORY_SEPARATOR.'version.php'); // tells us where the config file is and other things.
require_once($dirname.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.CmsException.php');
require_once($dirname.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.HookManager.php');
require_once($dirname.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.cms_config.php');
require_once($dirname.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.CmsApp.php');
require_once($dirname.DIRECTORY_SEPARATOR.'autoloader.php');
require_once($dirname.DIRECTORY_SEPARATOR.'module.functions.php');
require_once($dirname.DIRECTORY_SEPARATOR.'page.functions.php');
require_once($dirname.DIRECTORY_SEPARATOR.'translation.functions.php');
require_once($dirname.DIRECTORY_SEPARATOR.'html_entity_decode_php4.php');

debug_buffer('done loading basic files');

//
// setup our application
//
$_app = new CmsApp;
$config = $_app->GetConfig();
$_app->GetModuleOperations();
$_app->GetContentOperations(); // compatibility
\CMSMS\AuditManager::init();

if ($config["debug"] == true) {
    @ini_set('display_errors',1);
    @error_reporting(E_ALL);
}

// Set the timezone
if( $config['timezone'] != '' ) @date_default_timezone_set(trim($config['timezone']));

if( cms_to_bool(ini_get('register_globals')) ) {
    echo 'FATAL ERROR: For security reasons register_globals must not be enabled for any CMSMS install.  Please adjust your PHP configuration settings to disable this feature.';
    die();
}

if( isset($CMS_ADMIN_PAGE) ) {
    setup_session();

    function cms_admin_sendheaders($content_type = 'text/html',$charset = '') {
        // Language shizzle
        if( !$charset ) $charset = CmsNlsOperations::get_encoding();
        header("Content-Type: $content_type; charset=$charset");
    }
}

require_once($dirname.DIRECTORY_SEPARATOR.'std_hooks.php');

if( isset($_GET['_auth']) && isset($_GET['_op']) ) {
    // todo: put this into a class, as it is a remote control mechanism
    // make this secure, but do not document/advertise it.  it's internal only
    $auth = filter_var($_GET['_auth'],FILTER_SANITIZE_STRING);
    $fn = TMP_CACHE_LOCATION.'/.f'.$auth;
    if( is_file($fn) && is_writable($fn) && is_readable($fn) ) {
        unlink($fn);
        $op = filter_var($_GET['_op'],FILTER_SANITIZE_STRING);
        switch( $op ) {
        case 'clearcache':
            $_app->clear_cached_files();
            die('done');
        }
    }
    header("HTTP/1.0 400 Bad Request");
    header("Status: 400 Bad Request");
    exit();
}

// new for 2.0 ... this creates a mechanism whereby items can be cached automatically, and fetched (or calculated) via the use of a callback
// if the cache is too old, or the cached value has been cleared or not yet been saved.
global_cache::set_driver($_app->get_cache_driver());
$obj = new \CMSMS\internal\global_cachable('schema_version',
                                           function() {
                                                $db = \CmsApp::get_instance()->GetDb();
                                                $query = 'SELECT version FROM '.CmsApp::get_instance()->GetDbPrefix().'version';
                                                return $db->GetOne($query);
                                            });
\CMSMS\internal\global_cache::add_cachable($obj);
$obj = new \CMSMS\internal\global_cachable('latest_content_modification',
                                            function(){
                                                $db = \CmsApp::get_instance()->GetDb();
                                                $query = 'SELECT modified_date FROM '.CmsApp::get_instance()->GetDbPrefix().'content ORDER BY modified_date DESC';
                                                $tmp = $db->GetOne($query);
                                                return $db->UnixTimeStamp($tmp);
                                            });
\CMSMS\internal\global_cache::add_cachable($obj);
$obj = new \CMSMS\internal\global_cachable('default_content',
                                            function(){
                                                $db = \CmsApp::get_instance()->GetDb();
                                                $query = 'SELECT content_id FROM '.CmsApp::get_instance()->GetDbPrefix().'content WHERE default_content = 1';
                                                $tmp = $db->GetOne($query);
                                                return $tmp;
                                            });
\CMSMS\internal\global_cache::add_cachable($obj);
$obj = new \CMSMS\internal\global_cachable('modules',
                                            function(){
                                                $db = \CmsApp::get_instance()->GetDb();
                                                $query = 'SELECT * FROM '.CmsApp::get_instance()->GetDbPrefix().'modules ORDER BY module_name';
                                                $tmp = $db->GetArray($query);
                                                return $tmp;
                                            });
\CMSMS\internal\global_cache::add_cachable($obj);
$obj = new \CMSMS\internal\global_cachable('module_deps',
                                            function(){
                                                $db = \CmsApp::get_instance()->GetDb();
                                                $query = 'SELECT parent_module,child_module,minimum_version FROM '.CmsApp::get_instance()->GetDbPrefix().'module_deps ORDER BY parent_module';
                                                $tmp = $db->GetArray($query);
                                                if( !is_array($tmp) || !count($tmp) ) return '-';  // special value so that we actually return something to cache.
                                                $out = array();
                                                foreach( $tmp as $row ) {
                                                    $out[$row['child_module']][$row['parent_module']] = $row['minimum_version'];
                                                }
                                                return $out;
                                            });
\CMSMS\internal\global_cache::add_cachable($obj);
cms_siteprefs::setup();

// note: the mact encoder reads a preference, so must happen afer siteprefs and global cache are setup.
$_mact_encoder = $_app->get_mact_encoder();
if( !$config['allow_old_mact'] && $_mact_encoder->old_mact_exists() ) {
    // somebody is making a request using an old mact.
    $_mact_encoder->remove_old_mact_params();
}
$_mact_encoder->expand_secure_mact(false);  // expands the secure MACT stuff into $_REQUEST

// Load them into the usual variables.  This'll go away a little later on.
if (!isset($DONT_LOAD_DB)) {
    try {
        debug_buffer('Initialize Database');
        $_app->GetDb();
        debug_buffer('Done Initializing Database');
    }
    catch( \CMSMS\Database\DatabaseConnectionException $e ) {
        die('Sorry, something has gone wrong.  Please contact a site administator. <em>('.get_class($e).')</em>');
    }
}

#Fix for IIS (and others) to make sure REQUEST_URI is filled in
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    if(isset($_SERVER['QUERY_STRING'])) $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
}

if (! isset($CMS_INSTALL_PAGE)) {
    $_app->GetHookMappingManager(); // initialize hook mappings.

    // Set a umask (should prolly go into the CmsApp stuff)
    $global_umask = cms_siteprefs::get('global_umask','');
    if( $global_umask != '' ) umask( octdec($global_umask) );

    // Load all eligible modules
    $modops = $_app->GetModuleOperations();
    $modops->LoadStaticModules();

    // test for cron.
    // we hardcode CmsJobManager here until such a point as we need to abstract it.
    \CMSMS\Async\JobManager::get_instance()->trigger_async_processing();
}

#Setup language stuff.... will auto-detect languages (Launch only to admin at this point)
if(isset($CMS_ADMIN_PAGE)) CmsNlsOperations::set_language();

if( !isset($DONT_LOAD_SMARTY) ) {
    debug_buffer('Initialize Smarty');
    $smarty = $_app->GetSmarty();
    debug_buffer('Done Initializing Smarty');
    if( defined('CMS_DEBUG') && CMS_DEBUG ) $smarty->error_reporting = 'E_ALL';
    $smarty->assignGlobal('sitename', cms_siteprefs::get('sitename', 'CMSMS Site'));
}

if( !isset($CMS_INSTALL_PAGE) ) {
    require_once($dirname.'/classes/internal/class_compatibility.php');
}
