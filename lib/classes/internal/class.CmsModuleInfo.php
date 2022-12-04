<?php

class CmsModuleInfo implements ArrayAccess
{
    private static $_keys = array('name','version','depends','mincmsversion', 'author', 'authoremail', 'help', 'about',
                                  'lazyloadadmin', 'lazyloadfrontend', 'changelog','ver_compatible','dir','writable','root_writable',
                                  'description','has_meta','has_custom','notavailable');
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
            return cms_join_path(CMS_ROOT_PATH,'modules',$this['name']);

        case 'writable':
            return is_directory_writable($this['dir']);

        case 'root_writable':
            // move this into ModuleManagerModuleInfo
            return is_writable($this['dir']);

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
        $config = \cms_config::get_instance();
        $fn = $config['root_path']."/modules/$module_name/moduleinfo.ini";
        return $fn;
    }

    private function _get_module_file( $module_name )
    {
        $config = \cms_config::get_instance();
        $fn = $config['root_path']."/modules/$module_name/$module_name.module.php";
        return $fn;
    }

    public function __construct($module_name,$can_load = TRUE)
    {
        $arr = $arr2 = $fn1 = $fn2 = $ft1 = $ft2 = null;
        $fn1 = $this->_get_module_meta_file( $module_name );
        $fn2 = $this->_get_module_file( $module_name );
        if( is_file($fn1) ) $ft1 = filemtime($fn1);
        if( is_file($fn2) ) $ft2 = filemtime($fn2);
        if( $ft2 >= $ft1 && $can_load ) {
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
        $config = \cms_config::get_instance();
        $dir = $config['assets_path']."/module_custom/$module_name";
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
        $config = \cms_config::get_instance();
        $dir = $config['root_path']."/modules/$module_name";
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

    /**
     * @internal
     * @ignore
     * @return bool
     */
    public function write_meta()
    {
        if( !$this['writable'] ) return FALSE;

        $_write_ini = function($input,$filename,$depth = 0) use(&$_write_ini) {
            if( !is_array($input) ) return;

            $res = '';
            foreach($input as $key => $val) {
                if( is_array($val) ) {
                    $res .= "[$key]".PHP_EOL;
                    $res .= $_write_ini($val,'',$depth+1);
                }
                else {
                    if( is_numeric($val) && strpos($val,' ') === FALSE ) {
                        $res .= "$key = $value".PHP_EOL;
                    }
                    else {
                        $res .= "$key = \"$value\"".PHP_EOL;
                    }
                }
            }
            if( $filename ) {
                file_put_contents($filename,$str);
            }
            else {
                return $str;
            }
        }; // _write_ini

        $dir = dirname(dirname(__DIR__))."/modules/$module_name";
        $fn = cms_join_path($dir,'moduleinfo.ini');
        if( !file_exists($fn) ) {
            $out = array();
            $out['name'] = $this['name'];
            $out['version'] = $this['version'];
            $out['description'] = $this['description'];
            $out['author'] = $this['author'];
            $out['authoremail'] = $this['authoremail'];
            $out['mincmsversion'] = $this['mincmsversion'];
            $out['lazyloadadmin'] = $this['lazyloadadmin'];
            $out['lazyloadfrontend'] = $this['lazyloadfrontend'];
            $_write_ini_file($out,$fn);
        }

        $fn = cms_join_path($dir,'changelog.inc');
        if( !file_exists($fn) ) file_put_contents($fn2,$this['changelog']);

        $fn = cms_join_path($dir,'help.inc');
        if( !file_exists($fn) ) file_put_contents($fn2,$this['help']);

        return TRUE;
    }
}

?>
