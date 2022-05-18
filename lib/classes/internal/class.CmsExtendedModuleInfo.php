<?php

class CmsExtendedModuleInfo extends CmsModuleInfo
{
    private static $_ekeys = array('installed','status','active','system_module','installed_version','admin_only',
                                   'active','allow_fe_lazyload','allow_admin_lazyload','dependants','can_deactivate',
                                   'can_uninstall','missingdeps');
    private $_edata = array();

    public function __construct($module_name,$can_load = false)
    {
        parent::__construct($module_name,$can_load);
        $ops = ModuleOperations::get_instance();

        $minfo = $ops->GetInstalledModuleInfo();
        $this['installed'] = false;
        $this['system_module'] = $ops->IsSystemModule($module_name);
        if( isset($minfo[$module_name]) ) {
            $this['installed'] = true;
            $this['status'] = $minfo[$module_name]['status'];
            $this['installed_version'] = $minfo[$module_name]['version'];
            $this['admin_only'] = isset($minfo[$module_name]['admin_only'])?$minfo[$module_name]['admin_only']:0;
            $this['active'] = $minfo[$module_name]['active'];
            $this['allow_fe_lazyload'] = $minfo[$module_name]['allow_fe_lazyload'];
            $this['allow_admin_lazyload'] = $minfo[$module_name]['allow_admin_lazyload'];

            $this->_edata['can_deactivate'] = ($this['name'] == 'ModuleManager') ? FALSE : TRUE;
            $this->_edata['can_uninstall'] = ($this['name'] == 'ModuleManager') ? FALSE : TRUE;

            // dependants is the list of modules that use this module (i.e:  CGBlog uses CGExtensions)
            if( isset($minfo[$module_name]['dependants']) ) $this['dependants'] = $minfo[$module_name]['dependants'];
        }
    }
    
    #[\ReturnTypeWillChange]
    public function OffsetGet($key)
    {
        if( !in_array($key,self::$_ekeys) ) return parent::OffsetGet($key);
        if( isset($this->_edata[$key]) ) return $this->_edata[$key];
        if( $key == 'missingdeps' ) {
            $out = null;
            $deps = $this['depends'];
            if( is_array($deps) && count($deps) ) {
                foreach( $deps as $onedepname => $onedepversion ) {
                    $depinfo = new CmsExtendedModuleInfo($onedepname);
                    if( !$depinfo['installed'] || version_compare($depinfo['version'],$onedepversion) < 0 ) $out[$onedepname] = $onedepversion;
                }
            }
            return $out;
        }
    }

    public function OffsetSet($key,$value) : void
    {
        if( !in_array($key,self::$_ekeys) ) parent::OffsetSet($key,$value);
        if( $key == 'can_deactivate' ) throw new CmsLogicException('CMSEX_INVALIDMEMBER',null,$key);
        if( $key == 'can_uninstall' ) throw new CmsLogicException('CMSEX_INVALIDMEMBER',null,$key);
        if( $key == 'missingdeps' ) throw new CmsLogicException('CMSEX_INVALIDMEMBER',null,$key);
        $this->_edata[$key] = $value;
    }

    public function OffsetExists($key) : bool
    {
        if( !in_array($key,self::$_ekeys) ) return parent::OffsetExists($key);
        return isset($this->_edata[$key]);
    }
} // end of class

?>
