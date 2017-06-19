<?php

class CmsModuleInfo implements ArrayAccess
{
    private static $_keys = array('name','version','depends','mincmsversion', 'author', 'authoremail', 'help', 'about',
                                  'lazyloadadmin', 'lazyloadfrontend', 'changelog','ver_compatible','dir','writable','root_writable',
                                  'description','has_meta','has_custom');
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

  public function __construct($module_name,$can_load = TRUE)
  {
    $res = $this->_read_from_module_meta($module_name);
    if( !$res ) {
      $res = $this->_read_from_module($module_name);
      if( !$res ) throw new CmsLogicException('CMSEX_MODULENOTFOUND',$module_name);
    }
    $this->_check_modulecustom($module_name);
  }

  private function _check_modulecustom($module_name)
  {
      $config = \cms_config::get_instance();
      $dir = $config['assets_path']."/module_custom/$module_name";
      $files1 = glob($dir."/templates/*.tpl");
      $files2 = glob($dir."/lang/??_??.php");
      $this->_data['has_custom'] = false;
      if( count($files1) || count($files2) ) $this->_data['has_custom'] = TRUE;
  }

  private function _read_from_module_meta($module_name)
  {
    $config = \cms_config::get_instance();
    $fn = $config['root_path']."/modules/$module_name/moduleinfo.ini";
    if( !file_exists($fn) ) return FALSE;
    $inidata = parse_ini_file($fn,TRUE);

    if( $inidata === FALSE || count($inidata) == 0 ) return FALSE;
    if( !isset($inidata['module']) ) return FALSE;

    $data = $inidata['module'];
    $this['name'] = isset($data['name'])?trim($data['name']):$module_name;
    $this['version'] = isset($data['version'])?trim($data['version']):'0.0.1';
    $this['description'] = isset($data['description'])?trim($data['description']):'';
    $this['author'] = trim(get_parameter_value($data,'author',lang('notspecified')));
    $this['authoremail'] = trim(get_parameter_value($data,'authoremail',lang('notspecified')));
    $this['mincmsversion'] = isset($data['mincmsversion'])?trim($data['mincmsversion']):CMS_VERSION;
    $this['lazyloadadmin'] = cms_to_bool(get_parameter_value($data,'lazyloadadmin',FALSE));
    $this['lazyloadfrontend'] = cms_to_bool(get_parameter_value($data,'lazyloadfrontend',FALSE));

    if( isset($inidata['depends']) ) $this['depends'] = $inidata['depends'];

    $fn = cms_join_path($dir,'changelog.inc');
    if( file_exists($fn) ) $this['changelog'] = file_get_contents($fn);
    $fn = cms_join_path($dir,'doc/changelog.inc');
    if( file_exists($fn) ) $this['changelog'] = file_get_contents($fn);

    $fn = cms_join_path($dir,'help.inc');
    if( file_exists($fn) ) $this['help'] = file_get_contents($fn);
    $fn = cms_join_path($dir,'doc/help.inc');
    if( file_exists($fn) ) $this['help'] = file_get_contents($fn);

    $this['has_meta'] = TRUE;
    return TRUE;
  }

  private function _read_from_module($module_name)
  {
      $mod = ModuleOperations::get_instance()->get_module_instance($module_name,'',TRUE);
      if( !is_object($mod) ) {
          $this['name'] = $module_name;
          return TRUE;
      }

      $this['name'] = $mod->GetName();
      $this['description'] = $mod->GetDescription();
      if( $this['description'] == '' ) $this['description'] = $mod->GetAdminDescription();
      $this['version'] = $mod->GetVersion();
      $this['depends'] = $mod->GetDependencies();
      $this['mincmsversion'] = $mod->MinimumCMSVersion();
      $this['author'] = $mod->GetAuthor();
      $this['authoremail'] = $mod->GetAuthor();
      $this['lazyloadadmin'] = $mod->LazyLoadAdmin();
      $this['lazyloadfrontend'] = $mod->LazyLoadAdmin();
      $this['help'] = $mod->GetHelp();
      $this['changelog'] = $mod->GetChangelog();

      return TRUE;
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
	  if( is_numeric($val) ) {
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
