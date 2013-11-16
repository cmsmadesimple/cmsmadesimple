<?php

class CmsModuleInfo implements ArrayAccess 
{
  private static $_keys = array('name','version','depends','mincmsversion', 'author', 'authoremail', 'help', 'about', 
				'lazyloadadmin', 'lazyloadfrontend', 'changelog','ver_compatible','dir','writable',
				'description','has_meta');
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
      $config = cmsms()->GetConfig();
      return cms_join_path($config['root_path'],'modules',$this['name']);

    case 'writable':
      return is_directory_writable($this['dir']);

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
      if( !$res ) throw new CmsLogicException('CMSEX_MODULENOTFOUND','',$module_name);
      $config = cmsms()->GetConfig();
    }
  }

  private function _read_from_module_meta($module_name)
  {
    $dir = dirname(dirname(dirname(__FILE__)))."/modules/$module_name";
    $fn = cms_join_path($dir,'moduleinfo.ini');
    if( !file_exists($fn) ) return FALSE;
    $data = parse_ini_file($fn);
    if( $data === FALSE || count($data) == 0 ) return FALSE;

    $this['name'] = isset($data['name'])?trim($data['name']):$module_name;
    $this['version'] = isset($data['version'])?trim($data['version']):'0.0.1';
    $this['description'] = isset($data['description'])?trim($data['description']):'';
    $this['author'] = trim($data['author']);
    $this['authoremail'] = trim($data['authoremail']);
    $this['mincmsversion'] = isset($data['mincmsversion'])?trim($data['mincmsversion']):CMS_VERSION;
    $this['lazyloadadmin'] = cms_to_bool($data['lazyloadadmin']);
    $this['lazyloadfrontend'] = cms_to_bool($data['lazyloadfrontend']);

    $fn = cms_join_path($dir,'changelog.inc');
    if( file_exists($fn) ) $this['changelog'] = file_get_contents($fn);

    $fn = cms_join_path($dir,'help.inc');
    if( file_exists($fn) ) $this['help'] = file_get_contents($fn);
    
    $this['has_meta'] = TRUE;
    return TRUE;
  }

  private function _read_from_module($module_name)
  {
    $mod = ModuleOperations::get_instance()->get_module_instance($module_name,'',TRUE);
    if( !is_object($mod) ) return FALSE;

    $this['name'] = $mod->GetName();
    $this['description'] = $mod->GetDescription();
    if( $this['description'] == '' ) $this['description'] = $mod->GetAdminDescription();
    $this['version'] = $mod->GetVersion();
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
   * @return boolean
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

    $dir = dirname(dirname(dirname(__FILE__)))."/modules/$module_name";
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