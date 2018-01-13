<?php
namespace CMSMS\internal;
use \ModuleOperations;

class module_info implements \ArrayAccess
{
    private static $_keys = array('name','version','depends','mincmsversion', 'author', 'authoremail', 'help', 'about',
                                  'lazyloadadmin', 'lazyloadfrontend', 'changelog','ver_compatible','dir','writable','root_writable',
                                  'description','has_meta','has_custom','notavailable','is_system_module');
    private $_data = array();

    public function OffsetGet($key)
    {
        if( !in_array($key,self::$_keys) ) throw new CmsLogicException('CMSEX_INVALIDMEMBER',null,$key);
        switch( $key ) {
        case 'about':
            break;

        case 'ver_compatible':
            return version_compare($this['mincmsversion'],CMS_VERSION,'<=');

        case 'dir':
            return ModuleOperations::get_instance()->get_module_path( $this->_data['name'] );

        case 'writable':
	    $dir = $this['dir'];
	    if( !$dir || !is_dir( $dir ) ) return false;
            return is_directory_writable($this['dir']);

        case 'root_writable':
            // move this into ModuleManagerModuleInfo
            return is_writable($this['dir']);

        case 'is_system_module':
            return ModuleOperations::get_instance()->IsSystemModule( $this->_data['name'] );

        default:
            if( isset($this->_data[$key]) ) return $this->_data[$key];
            break;
        }
    }

    public function OffsetSet($key,$value)
    {
        if( !in_array($key,self::$_keys) ) throw new CmsLogicException('CMSEX_INVALIDMEMBER',null,$key);
        if( $key == 'about' ) throw new CmsLogicException('CMSEX_INVALIDMEMBERSET',$key);
        if( $key == 'ver_compatible' ) throw new CmsLogicException('CMSEX_INVALIDMEMBERSET',$key);
        if( $key == 'dir' ) throw new CmsLogicException('CMSEX_INVALIDMEMBERSET',$key);
        if( $key == 'writable' ) throw new CmsLogicException('CMSEX_INVALIDMEMBERSET',$key);
        if( $key == 'root_writable' ) throw new CmsLogicException('CMSEX_INVALIDMEMBERSET',$key);
        if( $key == 'has_custom' ) throw new CmsLogicException('CMSEX_INVALIDMEMBERSET',$key);
        $this->_data[$key] = $value;
    }

    public function OffsetExists($key)
    {
        if( !in_array($key,self::$_keys) ) throw new CmsLogicException('CMSEX_INVALIDMEMBER',null,$key);
        return isset($this->_data[$key]);
    }

    public function OffsetUnset($key)
    {
        return; // do nothing
    }

    private function _get_module_meta_file( $module_name )
    {
        $modops = \ModuleOperations::get_instance();
        $path = $modops->get_module_path( $module_name );
        return $path;
    }

    private function _get_module_file( $module_name )
    {
        $modops = \ModuleOperations::get_instance();
        $fn = $modops->get_module_filename( $module_name );
        return $fn;
    }

    public function __construct($module_name,$can_load = TRUE)
    {
        $arr = $arr2 = $fn1 = $fn2 = $ft1 = $ft2 = null;
        $fn1 = $this->_get_module_meta_file( $module_name );
        $fn2 = $this->_get_module_file( $module_name );
        if( is_file($fn1) ) $ft1 = filemtime($fn1);
        if( is_file($fn2) ) $ft2 = filemtime($fn2);
        if( $ft2 > $ft1 && $can_load ) {
            // module file is newer.
            $arr = $this->_read_from_module($module_name);
        }
        else {
            // moduleinfo file is newer.
            $arr = $this->_read_from_module_meta($module_name);
        }
        if( !$arr ) {
            $arr['name'] = $module_name;
            $this->_setData( $arr );
            $this->_data['notavailable'] = true;
        } else {
            $arr2 = $this->_check_modulecustom($module_name);
            $this->_setData( array_merge($arr2, $arr ));
        }
    }

    private function _setData( array $in )
    {
        foreach( $in as $key => $value ) {
            if( in_array( $key, self::$_keys ) ) $this->_data[$key] = $value;
        }
    }

    private function _check_modulecustom($module_name)
    {
        $dir = CMS_ASSETS_PATH."/module_custom/$module_name";
        $files1 = glob($dir."/templates/*.tpl");
        $files2 = glob($dir."/lang/??_??.php");

        $tmp = ['has_custom' => FALSE ];
        if( count($files1) || count($files2) ) $this->_tmp['has_custom'] = TRUE;
        return $tmp;
    }

    private function _remove_module_meta( $module_name )
    {
        $fn = $this->_get_module_meta_file( $module_name );
        if( is_file($fn) && is_writable($fn) ) unlink($fn);
    }

    private function _read_from_module_meta($module_name)
    {
        $dir = \ModuleOperations::get_instance()->get_module_path( $module_name );
        $fn = $this->_get_module_meta_file( $module_name );
        if( !is_file($fn) ) return;
        $inidata = @parse_ini_file($fn,TRUE);
        if( $inidata === FALSE || count($inidata) == 0 ) return;
        if( !isset($inidata['module']) ) return;

        $data = $inidata['module'];
        $arr = [];
        $arr['name'] = isset($data['name'])?trim($data['name']):$module_name;
        $arr['version'] = isset($data['version'])?trim($data['version']):'0.0.1';
        $arr['description'] = isset($data['description'])?trim($data['description']):'';
        $arr['author'] = trim(get_parameter_value($data,'author',lang('notspecified')));
        $arr['authoremail'] = trim(get_parameter_value($data,'authoremail',lang('notspecified')));
        $arr['mincmsversion'] = isset($data['mincmsversion'])?trim($data['mincmsversion']):CMS_VERSION;
        $arr['lazyloadadmin'] = cms_to_bool(get_parameter_value($data,'lazyloadadmin',FALSE));
        $arr['lazyloadfrontend'] = cms_to_bool(get_parameter_value($data,'lazyloadfrontend',FALSE));

        if( isset($inidata['depends']) ) $arr['depends'] = $inidata['depends'];

        $fn = cms_join_path($dir,'changelog.inc');
        if( file_exists($fn) ) $arr['changelog'] = file_get_contents($fn);
        $fn = cms_join_path($dir,'doc/changelog.inc');
        if( file_exists($fn) ) $arr['changelog'] = file_get_contents($fn);

        $fn = cms_join_path($dir,'help.inc');
        if( file_exists($fn) ) $arr['help'] = file_get_contents($fn);
        $fn = cms_join_path($dir,'doc/help.inc');
        if( file_exists($fn) ) $arr['help'] = file_get_contents($fn);

        $arr['has_meta'] = TRUE;
        return $arr;
    }

    private function _read_from_module($module_name)
    {
        // load the module... this is more likely to result in fatal errors than exceptions
        // so we don't bother to read
        $mod = ModuleOperations::get_instance()->get_module_instance($module_name,'',TRUE);
        if( !is_object($mod) ) return;

        $arr = [];
        $arr['name'] = $mod->GetName();
        $arr['description'] = $mod->GetDescription();
        if( $arr['description'] == '' ) $arr['description'] = $mod->GetAdminDescription();
        $arr['version'] = $mod->GetVersion();
        $arr['depends'] = $mod->GetDependencies();
        $arr['mincmsversion'] = $mod->MinimumCMSVersion();
        $arr['author'] = $mod->GetAuthor();
        $arr['authoremail'] = $mod->GetAuthor();
        $arr['lazyloadadmin'] = $mod->LazyLoadAdmin();
        $arr['lazyloadfrontend'] = $mod->LazyLoadAdmin();
        $arr['help'] = $mod->GetHelp();
        $arr['changelog'] = $mod->GetChangelog();
        return $arr;
    }

} // end of class
