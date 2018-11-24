<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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

use \CMSMS\internal\module_meta;

/**
 * Classes and utilities for operationg on and with modules
 *
 * @package CMS
 * @license GPL
 */

/**
 * A singleton utility class to allow for working with modules.
 *
 * @since		0.9
 * @package		CMS
 * @license GPL
 */
final class ModuleOperations
{
	/**
	 * System Modules - a list (hardcoded) of all system modules
	 *
	 * @access private
	 * @internal
	 */
	protected $cmssystemmodules =  [ 'AdminLog', 'AdminSearch', 'DesignManager', 'CMSContentManager', 'FileManager', 'ModuleManager', 'Search','News', 'MicroTiny',
                                     'Navigator', 'CmsJobManager', 'FilePicker', 'CoreAdminLogin' ];

	/**
	 * @ignore
	 */
	static private $_instance = null;

    /**
     * @ignore
     */
    const STD_AUTH_MODULE = 'CoreAdminLogin';

    /**
     * @ignore
     */
    private $_auth_module = null;

    /**
     * @ignore
     */
    const CLASSMAP_PREF = 'module_classmap';

    /**
     * @ignore
     */
    static private $_classmap = null;

	/**
	 * @ignore
	 */
	private $_modules = null;

	/**
	 * @ignore
	 */
	private $_moduleinfo;

    /**
     * @ignore
     */
    private function __construct() {}


    /**
     * @ignore
     */
    private $_module_class_map;

    /**
     * Get the only permitted instance of this object.  It will be created if necessary
     *
     * @return ModuleOperations
     */
    public static function &get_instance()
    {
        if( !isset(self::$_instance) ) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }

    /**
     * @ignore
     */
    protected function get_module_classmap()
    {
        if( !is_array(self::$_classmap) ) {
            self::$_classmap = [];
            $tmp = \cms_siteprefs::get(self::CLASSMAP_PREF);
            if( $tmp ) self::$_classmap = unserialize($tmp);
        }
        return self::$_classmap;
    }

    /**
     * @ignore
     */
    protected function get_module_classname(string $module)
    {
        $module = trim($module);
        if( !$module ) return;
        $map = $this->get_module_classmap();
        if( isset($map[$module]) ) return $map[$module];
        return $module;
    }


    /**
     * @ignore
     */
    public function get_module_filename(string $module)
    {
        $module = trim($module);
        if( !$module ) return;
        $config = \cms_config::get_instance();
        $path = CMS_ROOT_PATH.'/lib/modules';
        if( !self::get_instance()->IsSystemModule( $module ) ) $path = CMS_ASSETS_PATH.'/modules';
        $fn = $path."/$module/$module.module.php";
        if( is_file($fn) ) return $fn;
    }

    /**
     * @ignore
     */
    public function get_module_path( string $module )
    {
        $fn = $this->get_module_filename( $module );
        if( $fn ) return dirname( $fn );
    }

    /**
     * Allow setting the classname for a module... useful when the module class file itself is within a namespace.
     *
     * @param string $module The module name
     * @param string $classname The class name.
     */
    public function set_module_classname(string $module,string $classname)
    {
        $module = trim($module);
        $classname = trim($classname);
        if( !$module || !$classname ) return;

        $this->get_module_classmap();
        $this->_classmap[$module] = $classname;
        \cms_siteprefs::set(self::CLASSMAP_PREF, serialize(self::$_classmap));
    }

    /**
     * Generate a moduleinfo.ini file for a module.
     *
     * @since 2.3
     * @param CMSModule $modinstance;
     * @return string
     */
    public function generate_moduleinfo( CMSModule $modinstance )
    {
        $dir = $this->get_module_path( $modinstance->GetName() );
        if( !is_writable( $dir ) ) throw new CmsFileSystemException(lang('errordirectorynotwritable'));

        $to_string = function( $key, $value ) {
            $res = null;
            if( is_numeric($value) && strpos($value,' ') === FALSE ) {
                $res .= "$key = $value".PHP_EOL;
            } else {
                $res .= "$key = \"{$value}\"".PHP_EOL;
            }
            return $res;
        };

        $fh = fopen($dir."/moduleinfo.ini",'w');
        fputs($fh,"[module]\n");
        fputs($fh,$to_string('name',$modinstance->GetName()));
        fputs($fh,$to_string('version',$modinstance->GetVersion()));
        //fputs($fh,$to_string('description',$modinstance->GetAdminDescription())); // language sensitive.
        fputs($fh,$to_string('author',$modinstance->GetAuthor()));
        fputs($fh,$to_string('authoremail',$modinstance->GetAuthorEmail()));
        fputs($fh,$to_string('mincmsversion',$modinstance->MinimumCMSVersion()));
        fputs($fh,$to_string('lazyloadadmin',($modinstance->LazyLoadAdmin())?'1':'0'));
        fputs($fh,$to_string('lazyloadfrontend',($modinstance->LazyLoadFrontend())?'1':'0'));
        $depends = $modinstance->GetDependencies();
        if( is_array($depends) && count($depends) ) {
            fputs($fh,"[depends]\n");
            foreach( $depends as $key => $val ) {
                fputs($fh,$to_string($key,$val));
            }
        }
        fputs($fh,"[meta]\n");
        fputs($fh,"generated = ".time()."\n");
        fputs($fh,"cms_ver = ".CMS_VERSION."\n");
        fclose($fh);
    }

    /**
     * @ignore
     */
    private function _install_module(CmsModule& $module_obj)
    {
        debug_buffer('install_module '.$module_obj->GetName());

        $gCms = CmsApp::get_instance(); // preserve the global.
        $db = $gCms->GetDb();

        $result = $module_obj->Install();
        if( !isset($result) || $result === FALSE) {
            // install returned nothing, or FALSE, a successful installation
            $query = 'DELETE FROM '.CMS_DB_PREFIX.'modules WHERE module_name = ?';
            $dbr = $db->Execute($query,array($module_obj->GetName()));
            $query = 'DELETE FROM '.CMS_DB_PREFIX.'module_deps WHERE child_module = ?';
            $dbr = $db->Execute($query,array($module_obj->GetName()));

            $lazyload_fe    = (method_exists($module_obj,'LazyLoadFrontend') && $module_obj->LazyLoadFrontend())?1:0;
            $lazyload_admin = (method_exists($module_obj,'LazyLoadAdmin') && $module_obj->LazyLoadAdmin())?1:0;
            $query = 'INSERT INTO '.CMS_DB_PREFIX.'modules
                      (module_name,version,status,admin_only,active,allow_fe_lazyload,allow_admin_lazyload)
                      VALUES (?,?,?,?,?,?,?)';
            $dbr = $db->Execute($query,array($module_obj->GetName(),$module_obj->GetVersion(),'installed',
                                             ($module_obj->IsAdminOnly()==true)?1:0,
                                             1,$lazyload_fe,$lazyload_admin));

            $deps = $module_obj->GetDependencies();
            if( is_array($deps) && count($deps) ) {
                $query = 'INSERT INTO '.CMS_DB_PREFIX.'module_deps (parent_module,child_module,minimum_version,create_date,modified_date)
                          VALUES (?,?,?,NOW(),NOW())';
                foreach( $deps as $depname => $depversion ) {
                    if( !$depname || !$depversion ) continue;
                    $dbr = $db->Execute($query,array($depname,$module_obj->GetName(),$depversion));
                }
            }
            $this->generate_moduleinfo( $module_obj );
            $this->_moduleinfo = array();
            $gCms->clear_cached_files();

            cms_notice('Installed module '.$module_obj->GetName().' version '.$module_obj->GetVersion());
            \CMSMS\HookManager::do_hook('Core::ModuleInstalled', [ 'name' => $module_obj->GetName(), 'version' => $module_obj->GetVersion() ] );
            return array(TRUE,$module_obj->InstallPostMessage());
        }

        // install returned something.
        return array(FALSE,$result);
    }


    /**
     * Install a module into the database
     *
     * @param string $module The name of the module to install
     * @return array Returns a tuple of whether the install was successful and a message if applicable
     */
    public function InstallModule(string $module)
    {
        // get an instance of the object (force it).
        $modinstance = $this->get_module_instance($module,'',TRUE);
        if( !$modinstance ) return array(FALSE,lang('errormodulenotloaded'));

        // check for dependencies
        $deps = $modinstance->GetDependencies();
        if( is_array($deps) && count($deps) ) {
            foreach( $deps as $mname => $mversion ) {
                if( $mname == '' || $mversion == '' ) continue; // invalid entry.
                $newmod = $this->get_module_instance($mname);
                if( !is_object($newmod) || version_compare($newmod->GetVersion(),$mversion) < 0 ) {
                    return array(FALSE,lang('missingdependency').': '.$mname);
                }
            }
        }

        // do the actual installation stuff.
        $res = $this->_install_module($modinstance);
        if( $res[0] == FALSE && $res[1] == '') {
            $res[1] = lang('errorinstallfailed');
            // put mention into the admin log
            cms_error('Installation of module '.$module.' failed');
        }
        return $res;
    }


    /**
     * @ignore
     */
    private function _get_module_info()
    {
        if( !is_array($this->_moduleinfo) || count($this->_moduleinfo) == 0 ) {
            $tmp = \CMSMS\internal\global_cache::get('modules');
            if( is_array($tmp) ) {
                $this->_moduleinfo = [];
                for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
                    $name = $tmp[$i]['module_name'];
                    $filename = $this->get_module_filename($name);
                    if( is_file($filename) ) {
                        if( !isset($this->_moduleinfo[$name]) ) $this->_moduleinfo[$name] = $tmp[$i];
                    }
                }

                $all_deps = $this->_get_all_module_dependencies();
                if( $all_deps && count($all_deps) ) {
                    foreach( $all_deps as $mname => $deps ) {
                        if( is_array($deps) && count($deps) && isset($this->_moduleinfo[$mname]) ) {
                            $minfo =& $this->_moduleinfo[$mname];
                            $minfo['dependants'] = array_keys($deps);
                        }
                    }
                }
            }
        }

        return $this->_moduleinfo;
    }


    /**
     * @ignore
     */
    private function _load_module(string $module_name,bool $force_load = FALSE,bool $dependents = TRUE)
    {
        $gCms = CmsApp::get_instance(); // backwards compatibility... set the global.

        $info = $this->_get_module_info();
        if( !isset($info[$module_name]) && !$force_load ) {
            cms_warning("Nothing is known about $module_name... cant load it");
            return FALSE;
        }

        global $CMS_ADMIN_PAGE, $CMS_STYLESHEET, $CMS_INSTALL_PAGE;

        // okay, lessee if we can load the dependants
        if( $dependents ) {
            $deps = $this->get_module_dependencies($module_name);
            if( is_array($deps) && count($deps) ) {
                foreach( $deps as $name => $ver ) {
                    if( $name == $module_name ) continue; // a module cannot depend on itself.
                    // this is the start of a recursive routine. get_module_instance() may call _load_module
                    $obj2 = $this->get_module_instance($name,$ver);
                    if( !is_object($obj2) ) {
                        cms_warning("Cannot load module $module_name ... Problem loading dependent module $name version $ver");
                        return FALSE;
                    }
                }
            }
        }

        // now load the module itself... recurses into the autoloader if possible.
        $class_name = $this->get_module_classname($module_name);
        if( !class_exists($class_name,true) ) {
            $fname = $this->get_module_filename( $module_name);
            if( !is_file($fname) ) {
                cms_warning("Cannot load $module_name because the module file does not exist");
                return FALSE;
            }

            debug_buffer('including source for module '.$module_name);
            require_once($fname);
        }

        if( !class_exists($class_name,FALSE) ) {
            debug_buffer("Cannot load $module_name because the class still does not exist");
            return FALSE;
        }

        global $CMS_FORCELOAD;
        $CMS_FORCELOAD = $force_load;
        $obj = new $class_name;
        unset($CMS_FORCELOAD);
        if( !is_object($obj) || ! $obj instanceof \CMSModule ) {
            // oops, some problem loading.
            cms_error("Cannot load module $module_name ... some problem instantiating the class");
            return FALSE;
        }

        if (version_compare($obj->MinimumCMSVersion(),CMS_VERSION) == 1 ) {
            // oops, not compatible.... can't load.
            cms_error('Cannot load module '.$module_name.' it is not compatible wth this version of CMSMS');
            unset($obj);
            return FALSE;
        }

        $this->_modules[$module_name] = $obj;

        $tmp = $gCms->get_installed_schema_version();
        if( $tmp == CMS_SCHEMA_VERSION && isset($CMS_INSTALL_PAGE) && in_array($module_name, $this->cmssystemmodules) ) {
            // during the phar installer, we can use get_module_instance() to install or upgrade core modules
            if( !isset($info[$module_name]) || $info[$module_name]['status'] != 'installed' ) {
                $res = $this->_install_module($obj);
                if( $res[0] == FALSE ) {
                    // nope, can't auto install...
                    unset($obj,$this->_modules[$module_name]);
                    return FALSE;
                }
            }

            // can't auto upgrade modules if cmsms schema versions don't match.
            // check to see if an upgrade is needed.
            if( isset($info[$module_name]) && $info[$module_name]['status'] == 'installed' ) {
                $dbversion = $info[$module_name]['version'];
                if( version_compare($dbversion, $obj->GetVersion()) == -1 ) {
                    // looks like upgrade is needed
                    $res = $this->_upgrade_module($obj);
                    if( !$res ) {
                        // upgrade failed
                        allow_admin_lang(FALSE); // isn't this ugly.
                        debug_buffer("Automatic upgrade of $module_name failed");
                        unset($obj,$this->_modules[$module_name]);
                        return FALSE;
                    }
                }
            }
        }

        if( !$force_load && (!isset($info[$module_name]['status']) || $info[$module_name]['status'] != 'installed') ) {
            debug_buffer('Cannot load an uninstalled module');
            unset($obj,$this->_modules[$module_name]);
            return false;
        }

        if( !isset($CMS_INSTALL_PAGE) && !isset($CMS_STYLESHEET) && !$force_load ) {
            if( isset($CMS_ADMIN_PAGE) ) {
                $obj->InitializeAdmin();
            } else if( $gCms->is_frontend_request() ) {
                $obj->InitializeFrontend();
            }
        }

        // we're all done.
        \CMSMS\HookManager::do_hook('Core::ModuleLoaded', [ 'name' => $module_name ] );
        return TRUE;
    }


    /**
     * A function to return a list of all modules that appear to exist properly in the modules directory.
     *
     * @return array of module names for all modules that exist in the module directory.
     */
    public function FindAllModules()
    {
        $result = [];
        $paths = [ CMS_ASSETS_PATH.'/modules', CMS_ROOT_PATH.'/lib/modules' ];
        foreach( $paths as $dir ) {
            if( !is_dir($dir) ) continue;
            if( is_dir($dir) && $handle = @opendir($dir) ) {
                while( ($file = readdir($handle)) !== false ) {
                    if( $file == '..' || $file == '.' ) continue;
                    $fn = "$dir/$file/$file.module.php";
                    if( @is_file($fn) && !in_array($file,$result) ) $result[] = $file;
                }
            }
        }

        sort($result);
        return $result;
    }


    /**
     * Return the information stored in the database about all installed modules.
     *
     * @since 2.0
     * @return array
     */
    public function GetInstalledModuleInfo()
    {
        return $this->_get_module_info();
    }


    /**
     * Finds all modules that cannot be lazy loaded and loads them.
     * Handles admin and frontend requests.  but does not load modules for stylesheet requests.
     *
     * @access public
     * @internal
     * @param noadmin boolean indicates that modules marked as admin_only in the database should not be loaded, default is false
     */
    public function LoadStaticModules()
    {
        global $CMS_ADMIN_PAGE;
        global $CMS_STYLESHEET;
        if( isset($CMS_STYLESHEET) ) return;

        $config = \cms_config::get_instance();
        debug_buffer('Loading Modules');
        $allinfo = $this->_get_module_info();
        if( !is_array($allinfo) ) return; // no modules installed, probably an empty database... edge case.

        foreach( $allinfo as $module_name => $info ) {
            if( $info['status'] != 'installed' ) continue;
            if( !$info['active'] ) continue;
            if( isset($CMS_ADMIN_PAGE) ) {
                // admin request
                if( isset($info['allow_admin_lazyload']) && $info['allow_admin_lazyload'] ) continue;
            } else {
                // frontend request
                if( isset($info['allow_admin_lazyload']) && $info['allow_admin_lazyload'] ) continue;
            }
            $this->get_module_instance($module_name);
        }
        debug_buffer('End of Loading Modules');
    }

    /**
     * @ignore
     */
    private function _upgrade_module( \CMSModule &$module_obj, string $to_version = '' )
    {
        // we can't upgrade a module if the schema is not up to date.
        $gCms = CmsApp::get_instance();
        $tmp = $gCms->get_installed_schema_version();
        if( $tmp && $tmp < CMS_SCHEMA_VERSION ) return array(FALSE,lang('error_coreupgradeneeded'));

        $info = $this->_get_module_info();
        $module_name = $module_obj->GetName();
        $dbversion = $info[$module_name]['version'];
        if( $to_version == '' ) $to_version = $module_obj->GetVersion();
        $dbversion = $info[$module_name]['version'];
        if( version_compare($dbversion, $to_version) != -1 ) {
            return array(TRUE); // nothing to do.
        }

        $db = $gCms->GetDb();
        $result = $module_obj->Upgrade($dbversion,$to_version);
        if( !isset($result) || $result === FALSE ) {
            $lazyload_fe    = (method_exists($module_obj,'LazyLoadFrontend') && $module_obj->LazyLoadFrontend())?1:0;
            $lazyload_admin = (method_exists($module_obj,'LazyLoadAdmin') && $module_obj->LazyLoadAdmin())?1:0;
            $admin_only = ($module_obj->IsAdminOnly())?1:0;

            $query = 'UPDATE '.CMS_DB_PREFIX.'modules SET version = ?, active = 1, allow_fe_lazyload = ?, allow_admin_lazyload = ?, admin_only = ?
                      WHERE module_name = ?';
            $dbr = $db->Execute($query,array($to_version,$lazyload_fe,$lazyload_admin,$admin_only,$module_obj->GetName()));

            // upgrade dependencies
            $query = 'DELETE FROM '.CMS_DB_PREFIX.'module_deps WHERE child_module = ?';
            $dbr = $db->Execute($query,array($module_obj->GetName()));

            $deps = $module_obj->GetDependencies();
            if( is_array($deps) && count($deps) ) {
                $query = 'INSERT INTO '.CMS_DB_PREFIX.'module_deps (parent_module,child_module,minimum_version,create_date,modified_date)
                          VALUES (?,?,?,NOW(),NOW())';
                foreach( $deps as $depname => $depversion ) {
                    if( !$depname || !$depversion ) continue;
                    $dbr = $db->Execute($query,array($depname,$module_obj->GetName(),$depversion));
                }
            }
            $this->generate_moduleinfo( $module_obj );
            $this->_moduleinfo = array();
            $gCms->clear_cached_files();
            cms_notice('Upgraded module '.$module_obj->GetName().' to version '.$module_obj->GetVersion());
            \CMSMS\HookManager::do_hook('Core::ModuleUpgraded', [ 'name' => $module_obj->GetName(), 'oldversion' => $dbversion, 'newversion' => $module_obj->GetVersion() ] );
            return array(TRUE);
        }

        cms_error('Upgrade failed for module '.$module_obj->GetName());
        return array(FALSE,$result);
    }


    /**
     * Upgrade a module
     *
     * This is an internal method, subject to change in later releases.  It should never be called for upgrading arbitrary modules.
     * Any use of this function by third party code will not be supported.  Use at your own risk and do not report bugs or issues
     * related to your use of this module.
     *
     * @internal
     * @param string $module_name The name of the module to upgrade
     * @param string $to_version The destination version
     * @return bool Whether or not the upgrade was successful
     */
    public function UpgradeModule( string $module_name, string $to_version = '')
    {
        $module_obj = $this->get_module_instance($module_name,'',TRUE);
        if( !is_object($module_obj) ) return array(FALSE,lang('errormodulenotloaded'));
        return $this->_upgrade_module($module_obj,$to_version);
    }


    /**
     * Uninstall a module
     *
     * @internal
     * @param string $module The name of the module to upgrade
     * @return array Returns a tuple of whether the install was successful and a message if applicable
     */
    public function UninstallModule( string $module )
    {
        $gCms = CmsApp::get_instance();
        $db = $gCms->GetDb();

        $modinstance = cms_utils::get_module($module);
        if( !$modinstance ) return array(FALSE,lang('errormodulenotloaded'));

        $cleanup = $modinstance->AllowUninstallCleanup();
        $result = $modinstance->Uninstall();

        if (!isset($result) || $result === FALSE) {
            // now delete the record
            $query = "DELETE FROM ".CMS_DB_PREFIX."modules WHERE module_name = ?";
            $db->Execute($query, array($module));

            // delete any dependencies
            $query = "DELETE FROM ".CMS_DB_PREFIX."module_deps WHERE child_module = ?";
            $db->Execute($query, array($module));

            // clean up, if permitted
            if ($cleanup) {
                // deprecated
                $db->Execute('DELETE FROM '.CMS_DB_PREFIX.'module_templates where module_name=?',array($module));
                $db->Execute('DELETE FROM '.CMS_DB_PREFIX.'event_handlers where module_name=?',array($module));
                $db->Execute('DELETE FROM '.CMS_DB_PREFIX.'events where originator=?',array($module));

                $types = CmsLayoutTemplateType::load_all_by_originator($module);
                if( is_array($types) && count($types) ) {
                    foreach( $types as $type ) {
                        $tpls = CmsLayoutTemplate::template_query(array('t:'.$type->get_id()));
                        if( is_array($tpls) && count($tpls) ) {
                            foreach( $tpls as $tpl ) {
                                $tpl->delete();
                            }
                        }
                        $type->delete();
                    }
                }

                $alerts = \CMSMS\AdminAlerts\Alert::load_all();
                if( count($alerts) ) {
                    foreach( $alerts as $alert ) {
                        if( $alert->module == $module ) $alert->delete();
                    }
                }

                $jobmgr = \ModuleOperations::get_instance()->get_module_instance('CmsJobManager');
                if( $jobmgr ) $jobmgr->delete_jobs_by_module( $module );

                $db->Execute('DELETE FROM '.CMS_DB_PREFIX.'module_smarty_plugins where module=?',array($module));
                $db->Execute('DELETE FROM '.CMS_DB_PREFIX."siteprefs WHERE sitepref_name LIKE '". str_replace("'",'',$db->qstr($module))."_mapi_pref%'");
                $db->Execute('DELETE FROM '.CMS_DB_PREFIX.'routes WHERE key1 = ?',array($module));
                $db->Execute('DELETE FROM '.CMS_DB_PREFIX.'module_smarty_plugins WHERE module = ?',array($module));
            }

            // clear the cache.
            $gCms->clear_cached_files();

            // Removing module from info
            $this->_moduleinfo = array();

            cms_notice('Uninstalled module '.$module);
            \CMSMS\HookManager::do_hook('Core::ModuleUninstalled', [ 'name' => $module ] );
            return array(TRUE);
        }

        cms_error('Uninstall failed: '.$module);
        return array(FALSE,$result);
    }


    /**
     * Test if a module is active
     *
     * @param string $module_name
     * @return bool
     */
    public function IsModuleActive(string $module_name)
    {
        if( !$module_name ) return FALSE;
        $info = $this->_get_module_info();
        if( !isset($info[$module_name]) ) return FALSE;

        return (bool)$info[$module_name]['active'];
    }


    /**
     * Activate a module
     *
     * @param string $module_name
     * @param bool $activate flag indicating wether to activate or deactivate the module
     * @return bool
     */
    public function ActivateModule(string $module_name,bool $activate = true)
    {
        if( !$module_name ) return FALSE;
        $info = $this->_get_module_info();
        if( !isset($info[$module_name]) ) return FALSE;

        $o_state = $info[$module_name]['active'];
        if( $activate ) {
            $info[$module_name]['active'] = 1;
        }
        else {
            $info[$module_name]['active'] = 0;
        }
        if( $info[$module_name]['active'] != $o_state ) {
            \CMSMS\HookManager::do_hook( 'Core::BeforeModuleActivated', [ 'name'=>$module_name, 'activated'=>$activate ] );
            $db = CmsApp::get_instance()->GetDb();
            $query = 'UPDATE '.CMS_DB_PREFIX.'modules SET active = ? WHERE module_name = ?';
            $dbr = $db->Execute($query,array($info[$module_name]['active'],$module_name));
            $this->_moduleinfo = array();
            cmsms()->clear_cached_files();
            \CMSMS\HookManager::do_hook( 'Core::AfterModuleActivated', [ 'name'=>$module_name, 'activated'=>$activate ] );
            if( $activate ) {
                cms_notice("Module $module_name activated");
            }
            else {
                cms_notice("Module $module_name deactivated");
            }
        }
        return TRUE;
    }


    /**
     * Returns a hash of all loaded modules.  This will include all
     * modules loaded into memory at the current time
     *
     * @return array The hash of all loaded modules
     */
    public function GetLoadedModules()
    {
        return $this->_modules;
    }


    /**
     * @internal
     */
    public function is_module_loaded(string $module_name)
    {
        $module_name = trim( $module_name );
        return isset( $this->_modules[$module_name] );
    }

    /**
     * Return an array of the names of all modules that we currently know about
     *
     * @return array
     */
    public function GetAllModuleNames()
    {
        return array_keys($this->_get_module_info());
    }

    /**
     * Return all of the information we know about modules.
     *
     * @return array
     */
    public function GetAllModuleInfo()
    {
        return $this->_get_module_info();
    }

    /**
     * Returns an array of the names of all installed modules.
     *
     * @param bool $include_all Include even inactive modules
     * @return array
     */
    public function GetInstalledModules(bool $include_all = FALSE)
    {
        $result = array();
        $info = $this->_get_module_info();
        if( is_array($info) ) {
            foreach( $info as $name => $rec ) {
                if( $rec['status'] != 'installed' ) continue;
                if( !$rec['active'] && !$include_all ) continue;
                $result[] = $name;
            }
        }
        return $result;
    }


    /**
     * Returns an array of installed modules that have a certain capabilies
     * This method will force the loading of all modules regardless of the module settings.
     *
     * @param string $capability The capability name
     * @param mixed $args Capability arguments
     * @return array List of all the module objects with that capability
     */
    public static function get_modules_with_capability(string $capability, $args = null )
    {
        if( !is_array($args) ) {
            if( !empty($args) ) {
                $args = [ $args ];;
            }
            else {
                $args = [];
            }
        }
        return module_meta::get_instance()->module_list_by_capability($capability,$args);
    }

    /**
     * @ignore
     */
    private function _get_all_module_dependencies()
    {
        $out = \CMSMS\internal\global_cache::get('module_deps');
        if( $out === '-' ) return;
        return $out;
    }

    /**
     * A function to return a list of dependencies from a module.
     * this method works by reading the dependencies from the database.
     *
     * @since 1.11.8
     * @author Robert Campbell
     * @param string $module_name The module name
     * @return array Hash of module names and dependencies
     */
    public function get_module_dependencies(string $module_name)
    {
        if( !$module_name ) return;

        $deps = $this->_get_all_module_dependencies();
        if( isset($deps[$module_name]) ) return $deps[$module_name];
    }

    /**
     * A function to return the object reference to the module object
     * if the module is not already loaded, it will be loaded.  Version checks are done
     * with the module to allow only loading versions of modules that are greater than the
     * specified value.
     *
     * @param string $module_name The module name
     * @param string $version an optional version string.
     * @param bool $force an optional flag to indicate wether the module should be force loaded if necesary.
     * @return CMSModule
     */
    public function &get_module_instance(string $module_name,string $version = '',bool $force = FALSE)
    {
        if( empty($module_name) && isset($this->variables['module'])) $module_name = $this->variables['module'];

        $obj = null;
        if( isset($this->_modules[$module_name]) ) {
            if( $force ) {
                unset($this->_modules[$module_name]);
            }
            else {
                $obj =& $this->_modules[$module_name];
            }
        }
        if( !is_object($obj) ) {
            // gotta load it.
            $res = $this->_load_module($module_name,$force);
            if( $res ) $obj =& $this->_modules[$module_name];
        }

        if( is_object($obj) && !empty($version) ) {
            $res = version_compare($obj->GetVersion(),$version);
            if( $res < 0 OR $res === FALSE ) $obj = null;
        }

        return $obj;
    }


    /**
     * Test if the specified module name is a system module
     *
     * @param string $module_name The module name
     * @return bool
     */
    public function IsSystemModule(string $module_name)
    {
        return in_array($module_name,$this->cmssystemmodules);
    }


    public function RegisterAdminAuthenticationModule( \CMSModule $mod )
    {
        if( $this->_auth_module ) throw new \LogicException( 'Sorry, only one non standard auth module is supported' );
        if( ! $mod instanceof \CMSMS\IAuthModuleInterface ) throw new \LogicException('Sorry. '.$mod->GetName().' is not a valid authentication module');
        $this->_auth_module = $mod;
    }

    public function &GetAdminLoginModule()
    {
        if( $this->_auth_module ) return $this->_auth_module;
        return $this->get_module_instance( self::STD_AUTH_MODULE, '', TRUE );
    }

    /**
     * Return the current syntax highlighter module object
     *
     * This method retrieves the specified syntax highlighter module, or uses the current current user preference for the syntax hightlighter module
     * for a name.
     *
     * @param string $module_name allows bypassing the automatic detection process and specifying a wysiwyg module.
     * @return CMSModule
     * @since 1.10
     */
    public function &GetSyntaxHighlighter(string $module_name = null)
    {
        $obj = null;
        if( !$module_name ) {
            global $CMS_ADMIN_PAGE;
            if( isset($CMS_ADMIN_PAGE) ) $module_name = cms_userprefs::get_for_user(get_userid(FALSE),'syntaxhighlighter');
            if( $module_name ) $module_name = html_entity_decode( $module_name ); // for some reason entities may have gotten in there.
        }

        if( !$module_name || $module_name == -1 ) return $obj;
        $obj = $this->get_module_instance($module_name);
        if( !$obj ) return $obj;
        if( $obj->HasCapability(CmsCoreCapabilities::SYNTAX_MODULE) ) return $obj;

        $obj = null;
        return $obj;
    }


    /**
     * Return the current wysiwyg module object
     *
     * This method makes an attempt to find the appropriate wysiwyg module given the current request context
     * and admin user preference.
     *
     * @param string $module_name allows bypassing the automatic detection process and specifying a wysiwyg module.
     * @return CMSModule
     * @since 1.10
     * @deprecated
     */
    public function &GetWYSIWYGModule(string $module_name = null)
    {
        $obj = null;
        if( !$module_name ) {
            if( CmsApp::get_instance()->is_frontend_request() ) {
                $module_name = cms_siteprefs::get('frontendwysiwyg');
            }
            else {
                $module_name = cms_userprefs::get_for_user(get_userid(FALSE),'wysiwyg');
            }
            if( $module_name ) $module_name = html_entity_decode( $module_name );
        }

        if( !$module_name || $module_name == -1 ) return $obj;
        $obj = $this->get_module_instance($module_name);
        if( !$obj ) return $obj;
        if( $obj->HasCapability(CmsCoreCapabilities::WYSIWYG_MODULE) ) return $obj;

        $obj = null;
        return $obj;
    }


    /**
     * Return the current search module object
     *
     * This method returns module object for the currently selected search module.
     *
     * @return CMSModule
     * @since 1.10
     */
    public function &GetSearchModule()
    {
        $obj = null;
        $module_name = cms_siteprefs::get('searchmodule','Search');
        if( $module_name && $module_name != 'none' && $module_name != '-1' ) $obj = $this->get_module_instance($module_name);
        return $obj;
    }


    /**
     * Return the current filepicker module object.
     *
     * This method returns module object for the currently selected search module.
     *
     * @return \CMSMS\FilePickerInterface
     * @since 2.2
     */
    public function &GetFilePickerModule()
    {
        $obj = null;
        $module_name = cms_siteprefs::get('filepickermodule','FilePicker');
        if( $module_name && $module_name != 'none' && $module_name != '-1' ) $obj = $this->get_module_instance($module_name);
        return $obj;
    }


    /**
     * Alias for the GetSyntaxHiglighter method.
     *
     * @see ModuleOperations::GetSyntaxHighlighter
     * @deprecated
     * @since 1.10
     * @param string $module_name
     * @return CMSModule
     */
    public function &GetSyntaxModule(string $module_name = null)
    {
        return $this->GetSyntaxHighlighter($module_name);
    }


    /**
     * Unload a module from memory
     *
     * @internal
     * @since 1.10
     * @param string $module_name
     */
    public function unload_module(string $module_name)
    {
        if( !isset($this->_modules[$module_name]) || !is_object($this->_modules[$module_name]) )  return;
        unset($this->_modules[$module_name]);
    }

    /**
     * Given a request and an 'id' return the parameters for the module call
     *
     * @internal
     * @param string $id
     * @return array
     */
    public function GetModuleParameters(string $id)
    {
        $params = [];

        if( $id ) {
            foreach ($_REQUEST as $key=>$value) {
                if( startswith($key,$id) ) {
                    $key = substr($key,strlen($id));
                    if( $key == 'id' || $key == 'returnid' || $key == 'action' ) continue;
                    $params[$key] = $value;
                }
            }
        }

        return $params;
    }
} // end of class
