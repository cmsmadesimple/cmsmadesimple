<?php

namespace __appbase;

use __appbase\utils;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

require_once __DIR__.'/compat.functions.php';
require_once __DIR__.'/misc.functions.php';
require_once dirname(__DIR__).'/accessor.functions.php';

abstract class app
{
    const CONFIG_ROOT_URL = 'root_url';

    private static $_instance;
    private $_config;
    private $_appdir;

    public function __construct($filename)
    {
        if( is_object(self::$_instance) ) throw new Exception('Cannot create another object of type app');
        self::$_instance = $this;

        spl_autoload_register(__NAMESPACE__.'\app::autoload');

        if( $filename ) {
            $this->_appdir = dirname($filename);
            $config_file = $this->_appdir.'/config.ini';
            if( file_exists($config_file) ) $this->_config = parse_ini_file($config_file);
        }
    }

    public static function &get_instance()
    {
        if( !is_object(self::$_instance) ) throw new Exception('There is no registered app instance');
        return self::$_instance;
    }

    public function get_name()
    {
        return get_class();
    }

    public function get_tmpdir()
    {
        // not modifyiable, ye
        return utils::get_sys_tmpdir();
    }

    public static function get_appdir()
    {
        return self::$_instance->_appdir;
    }

    public static function get_rootdir()
    {
        return dirname(__DIR__,3);
    }

    public static function get_rooturl()
    {
        $config = self::$_instance->config();
        if( $config && isset($config[self::CONFIG_ROOT_URL]) ) return $config[self::CONFIG_ROOT_URL];

        $request = request::get();
        $dir = dirname($request['SCRIPT_FILENAME']);
        return $dir;
    }

    public function get_config()
    {
        return $this->_config;
    }

    public static function clear_cache($do_index_html = TRUE)
    {
        $rdi = new RecursiveDirectoryIterator($this->get_tmpdir());
        $rii = new RecursiveIteratorIterator($rdi);
        foreach( $rii as $file => $info ) {
            if( $info->isFile() ) @unlink($info->getPathInfo());
        }

        if( $do_index_html ) {
            $rdi = new RecursiveDirectoryIterator($this->get_tmpdir());
            $rii = new RecursiveIteratorIterator($rdi);
            foreach( $rii as $file => $info ) {
                if( $info->isFile() ) @touch($info->getPathInfo().'/index.html');
            }
        }
    }

    public static function autoload($classname)
    {
        $dirsuffix = dirname(str_replace('\\','/',$classname));
        $classname = basename(str_replace('\\','/',$classname));
        $dirsuffix = str_replace('__appbase','.',$dirsuffix);
        //if( $dirsuffix == "__appbase" ) $dirsuffix = '.';

        $dirs = array(__DIR__,dirname(__DIR__),dirname(__DIR__).'/tests',dirname(__DIR__).'/base',dirname(__DIR__,2) );
        foreach( $dirs as $dir ) {
            $fn = "$dir/$dirsuffix/class.$classname.php";
            if( file_exists($fn) ) {
                include_once($fn);
                return;
            }
        }
    }

    abstract public function run();

} // end of class

?>
