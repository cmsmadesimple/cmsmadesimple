<?php

class CmsModuleInfo implements ArrayAccess 
{
  private static $_keys = array('name','version','depends','mincmsversion', 'author', 'authoremail', 'help', 'about', 
				'lazyloadadmin', 'lazyloadfrontend', 'changelog','ver_compatible');
  private $_data = array();

  public function OffsetGet($key)
  {
    if( !in_array($key,self::$_keys) ) throw new CmsLogicException('CMSEX_INVALIDMEMBER',null,$key);
    switch( $key ) {
    case 'about':
      break;

    case 'ver_compatible':
      return version_compare($this['mincmsversion'],CMS_VERSION,'<=');

    default:
      if( isset($this->_data[$key]) ) return $this->_data[$key];
      break;
    }
  }

  public function OffsetSet($key,$value)
  {
    if( !in_array($key,self::$_keys) ) throw new CmsLogicException('CMSEX_INVALIDMEMBER',null,$key);
    if( $key == 'about' ) throw new CmsLogicException('CMSEX_INVALIDMEMBERSET',$key);
    if( $key == 'compatible' ) throw new CmsLogicException('CMSEX_INVALIDMEMBERSET',$key);
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

  public function __construct($module_name,$can_load = FALSE)
  {
    $res = $this->_read_from_module_dir($module_name);
    if( !$res && $can_load ) {
      $res = $this->_read_from_module($module_name);
      if( !$res ) throw new CmsLogicException('CMSEX_MODULENOTFOUND','',$module_name);
    }
  }

  private function _read_from_module_dir($module_name)
  {
    $dir = dirname(dirname(dirname(__FILE__)))."/modules/$module_name";
    $fn = cms_join_path($dir,'moduleinfo.ini');
    if( !file_exists($fn) ) return FALSE;
    $data = parse_ini_file($fn);
    if( $data === FALSE || count($data) == 0 ) return FALSE;

    $this['name'] = isset($data['name'])?trim($data['name']):$module_name;
    $this['version'] = isset($data['version'])?trim($data['version']):'0.0.1';
    $this['author'] = trim($data['author']);
    $this['authoremail'] = trim($data['authoremail']);
    $this['mincmsversion'] = isset($data['mincmsversion'])?trim($data['mincmsversion']):CMS_VERSION;
    $this['lazyloadadmin'] = cms_to_bool($data['lazyloadadmin']);
    $this['lazyloadfrontend'] = cms_to_bool($data['lazyloadfrontend']);

    $fn = cms_join_path($dir,'changelog.inc');
    if( file_exists($fn) ) $this['changelog'] = file_get_contents($fn);

    $fn = cms_join_path($dir,'help.inc');
    if( file_exists($fn) ) $this['help'] = file_get_contents($fn);

    return TRUE;
  }

  private function _read_from_module($module_name)
  {
    $mod = ModuleOperations::get_instance()->get_module_instance($module_name,'',TRUE);
    if( !is_object($mod) ) return FALSE;

    $this['name'] = $mod->GetName();
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
}
?>