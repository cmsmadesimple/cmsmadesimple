<?php

namespace __appbase;

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

    $app = get_app();
    $rootdir = $app->get_rootdir();
    $tmpdir = $app->get_tmpdir().'/m' . \md5(__FILE__);
    $appdir = $app->get_appdir();
    $basedir = \dirname(__FILE__, 3);
    
    $this->setTemplateDir($appdir.'/templates');
    $this->setConfigDir($appdir.'/configs');
    $this->setCompileDir($tmpdir.'/templates_c');
    $this->setCacheDir($tmpdir.'/cache');

    $this->registerPlugin('modifier','tr',array($this,'modifier_tr'));
    $dirs = [$this->compile_dir, $this->cache_dir];
    
    for($i = 0, $iMax = count($dirs); $i < $iMax; $i++ ) {
      
      if(!@\mkdir($concurrentDirectory = $dirs[$i], 0777, TRUE) && !\is_dir($concurrentDirectory))
      {
        throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
      }
      if( !\is_dir($dirs[$i]) ) throw new \RuntimeException('Required directory ' . $dirs[$i] . ' does not exist');
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