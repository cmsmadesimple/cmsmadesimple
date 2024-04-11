<?php

namespace __appbase;

use cms_autoinstaller\smarty_phar_resource;

require_once(\dirname(__FILE__, 2) . '/Smarty/Smarty.class.php');

class cms_smarty extends \Smarty
{
  private static $_instance;
  
  /**
   * @throws \SmartyException
   * @throws \Exception
   */
  public function __construct()
  {
    parent::__construct();
    
    $app     = get_app(); # may not be needed
    $rootdir = app::get_rootdir();
    $tmpdir  = app::get_tmpdir() . '/m' . \md5(__FILE__);
    $appdir  = app::get_appdir();
    $basedir = \dirname(__FILE__, 3);
    
    $this->setTemplateDir($appdir.'/templates');
    $this->setConfigDir($appdir.'/configs');
    $this->setCompileDir($tmpdir.'/templates_c');
    $this->setCacheDir($tmpdir.'/cache');

    $this->registerPlugin('modifier', 'tr', [$this, 'modifier_tr']);
    
    # for debugging purposes
    # $this->registerResource('phar', new smarty_phar_resource());
    $dirs = [$this->compile_dir, $this->cache_dir];
    
    foreach($dirs as $iValue)
    {
      
      if(!@\mkdir($concurrentDirectory = $iValue, 0777, TRUE) && !\is_dir($concurrentDirectory))
      {
        throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
      }
      if( !\is_dir($iValue) )
      {
        throw new \RuntimeException('Required directory ' . $iValue . ' does not exist');
      }
    }
  }

  public static function get_instance()
  {
    if( !\is_object(self::$_instance) ) self::$_instance = new cms_smarty;
    return self::$_instance;
  }

  public function modifier_tr()
  {
    $args = \func_get_args();
    return langtools::get_instance()->translate($args);
  }
}

?>