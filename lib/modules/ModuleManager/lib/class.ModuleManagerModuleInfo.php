<?php

class ModuleManagerModuleInfo extends CmsExtendedModuleInfo
{
    private static $_minfo;
    private static $_deprecated = array('CMSMailer','MenuManager');
    private static $_mmkeys = array('e_status','can_install','can_upgrade','can_uninstall','missing_deps','deprecated','needs_upgrade');
    private $_mmdata = array();

    public function __construct($module_name,$can_load = TRUE,$can_check_forge = TRUE)
    {
        parent::__construct($module_name,$can_load);

        // add in some info that only module manager can tell us.
        // like: is there a newer version available
        // extended status (db version newer then file version) / needs upgrade
        if( $this['version'] && $this['installed_version'] ) {
            $tmp = version_compare($this['installed_version'],$this['version']);
            if( $this['installed'] && $tmp < 0 ) {
                $this['e_status'] = 'need_upgrade';
            }
            else if( $tmp > 0 ) {
                $this['e_status'] = 'db_newer';
            }
            else if( $can_check_forge ) {
                try {
                    $rep_info = modulerep_client::get_upgrade_module_info($module_name);
                    if( is_array($rep_info) ) {
                        if( ($res = version_compare($this['version'],$rep_info['version'])) < 0 ) $this['e_status'] = 'newer_available';
                    }
                }
                catch( Exception $e ) {
                    // nothing here.
                }
            }
        }

    }

    private function _get_missing_dependencies()
    {
        $depends = $this['depends'];
        if( is_array($depends) && count($depends) ) {
            $out = array();
            foreach( $depends as $name => $ver ) {
                $rec = self::get_module_info($name);
                if( !is_object($rec) ) {
                    // problem getting module info for it.
                    $out[$name] = $ver;
                    continue;
                }
                if( !$rec['installed'] || !$rec['active'] ) {
                    // dependent is not installed (or not active) so its missing
                    $out[$name] = $ver;
                    continue;
                }
                if( $rec['needs_upgrade'] ) {
                    // dependent needs upgrading, so it's a missing dependency
                    $out[$name] = $ver;
                    continue;
                }
                if( version_compare($rec['version'],$ver) >= 0 ) continue;
                $out[$name] = $ver;
            } // foreach
            if( count($out) ) return $out;
        }
    }

    private function _check_dependencies()
    {
        // check if all module dependants are installed and are of sufficient version.
        $missing = $this->_get_missing_dependencies();
        if( is_array($missing) && count($missing) ) return FALSE;
        return TRUE;
    }

    public function OffsetGet($key)
    {
        if( !in_array($key,self::$_mmkeys) ) return parent::OffsetGet($key);
        if( isset($this->_mmdata[$key]) ) return $this->_mmdata[$key];

        if( $key == 'can_install' ) {
            // can we install this module
            if( $this['installed'] ) return FALSE;
            if( !$this['ver_compatible'] ) return FALSE;
            return $this->_check_dependencies();
        }


        if( $key == 'can_upgrade' ) {
            // see if we can upgrade this module
            return $this->_check_dependencies();
        }

        if( $key == 'needs_upgrade' || $key == 'need_upgrade' ) {
            // test if this module needs an upgrade
            // this only checks with the data we have, does not calculate an extended status.
            if( !$this['version'] || !$this['installed_version'] ) {
                // if it's not installed, it doesn't need an upgrade
                return FALSE;
            }
            $tmp = version_compare($this['installed_version'],$this['version']);
            if( $tmp < 0 ) return TRUE;
            // The e_status field checks the repository... and tells if the db version is newer.
            return FALSE;
        }

        if( $key == 'can_uninstall' ) {
            // check if this module can be uninstalled
            if( !$this['installed'] ) return FALSE;

            // check for installed modules that are dependent upon this one
            $name = $this['name'];
            if( $name == 'ModuleManager' ) return FALSE;

            foreach( self::$_minfo as $mname => $minfo ) {
                if( is_array($minfo['dependants']) && count($minfo['dependants']) ) {
                    if( in_array($name,$minfo['dependants']) ) return FALSE;
                }

            }
            return TRUE;
        }

        if( $key == 'missing_deps' ) {
            // test if this module is missing dependencies
            $out = $this->_get_missing_dependencies();
            return $out;
        }

        if( $key == 'deprecated' ) {
            // test if this module is deprecated
            if( in_array($this['name'],self::$_deprecated) ) return TRUE;
            return FALSE;
        }
    }

    public function OffsetSet($key,$value)
    {
        if( !in_array($key,self::$_mmkeys) ) parent::OffsetSet($key,$value);
        if( $key != 'e_status' && $key != 'deprecated' ) return; // dynamic
        $this->_mmdata[$key] = $value;
    }

    public function OffsetExists($key)
    {
        if( !in_array($key,self::$_mmkeys) ) return parent::OffsetExists($key);
        if( $key != 'e_status' && $key != 'deprecated' ) return; // dynamic
        return isset($this->_mmdata[$key]);
    }

    public static function get_all_module_info($can_check_forge = TRUE)
    {
        if( is_array(self::$_minfo) ) return self::$_minfo;

        $ops = ModuleOperations::get_instance();
        $allknownmodules = $ops->FindAllModules();

        // first pass...
        $out = array();
        foreach( $allknownmodules as $module_name ) {
            try {
                $info = new ModuleManagerModuleInfo($module_name,TRUE,$can_check_forge);
                $out[$module_name] = $info;
            }
            catch( \Exception $e ) {
                debug_display($e->GetMessage(),$module_name);
            }
        }

        self::$_minfo = $out;
        return self::$_minfo;
    }

    public static function &get_module_info($module)
    {
        $tmp = self::get_all_module_info();
        if( isset($tmp[$module]) ) return $tmp[$module];

        $out = null;
        return $out;
    }

} // end of class

?>