<?php

namespace cms_autoinstaller;
use \__appbase\utils;

include_once(__DIR__.'/lib/compat.functions.php');
include_once(dirname(dirname(__FILE__)).'/lib/classes/base/class.app.php');

class cms_install extends \__appbase\app
{
    private static $_instance;
    private $_archive;
    private $_dest_version;
    private $_dest_name;
    private $_dest_schema;
    private $_destdir;
    private $_custom_destdir;
    private $_nls;
    private $_orig_tz;
    private $_orig_error_level;
    private $_custom_tmpdir;

    public function get_tmpdir()
    {
        // because phar uses tmpfile() we need to set the TMPDIR environment variable
        // with whatever directory we find.
        $config = $this->get_config();
        return $config['tmpdir'];
    }

    private function fixup_tmpdir_environment()
    {
        // if the system temporary directory is not the same as the config temporary directory
        // then we attempt to putenv the TMPDIR environment variable
        // so that tmpfile() will work as it uses the system temporary directory which can read from environment variables
        $sys_tmpdir = null;
        if( function_exists('sys_get_temp_dir') ) $sys_tmpdir = rtrim(sys_get_temp_dir(),'\\/');
        $config = $this->get_config();
        if( (!$sys_tmpdir || !is_dir($sys_tmpdir) || !is_writable($sys_tmpdir)) && $sys_tmpdir != $config['tmpdir'] ) {
            @putenv('TMPDIR='.$config['tmpdir']);
            $try1 = getenv('TMPDIR');
            if( $try1 != $config['tmpdir'] ) throw new \RuntimeException('Sorry, putenv does not work on this system, and your system temporary directory is not set properly.');
        }
    }

    public function __construct()
    {
        parent::__construct(__FILE__);

        // initialize the session.
        $sess = \__appbase\session::get();
        $junk = $sess[__CLASS__]; // this is junk, but triggers session to start.

        // get the request
        $request = \__appbase\request::get();
        if( isset($request['clear']) ) {
            $sess->reset();
        }

        $config = $this->get_config();

        // setup autoload
        spl_autoload_register(__NAMESPACE__.'\cms_install::autoload');

        $this->fixup_tmpdir_environment();

        // setup smarty
        $smarty = \__appbase\smarty();
        $smarty->assign('APPNAME','cms_installer');
        $smarty->assign('config',$config);
        $smarty->assign('installer_version',$config['installer_version']);

        $fn = $this->get_appdir().'/build.ini';
        $build = null;
        if( file_exists($fn) ) $build = parse_ini_file($fn);
        if( isset($build['build_time']) ) $smarty->assign('build_time',$build['build_time']);

        // handle debug mode
        if( $config['debug'] ) {
            @ini_set('display_errors',1);
            @error_reporting(E_ALL);
        }

        if( $this->in_phar() && !$config['nobase'] ) {
            $base_href = $_SERVER['SCRIPT_NAME'];
            if( \__appbase\endswith($base_href,'.php') ) {
                $base_href = $base_href . '/';
                $smarty->assign('BASE_HREF',$base_href);
            }
        }

        // find our archive, copy it... and rename it securely.
        // we do this because phar data cannot read from a .tar.gz file that is already embedded within a phar
        // (some environments)
        $tmpdir = $this->get_tmpdir().'/m'.md5(__FILE__.session_id());
        $src_archive = (isset($config['archive']))?$config['archive']:'data/data.tar.gz';
        $src_archive = dirname(__DIR__).DIRECTORY_SEPARATOR.$src_archive;
        if( !file_exists($src_archive) ) throw new \Exception('Could not find installation archive at '.$src_archive);
        $dest_archive = $tmpdir.DIRECTORY_SEPARATOR."f".md5($src_archive.session_id()).'.tgz';
        $src_md5 = md5_file($src_archive);

        for( $i = 0; $i < 2; $i++ ) {
            if( !file_exists($dest_archive) ) {
                @mkdir($tmpdir,0777,TRUE);
                @copy($src_archive,$dest_archive);
            }
            $dest_md5 = md5_file($dest_archive);
            if( is_readable($dest_archive) && $src_md5 == $dest_md5 ) break;
            @unlink($dest_archive);
        }
        if( $i == 2 ) throw new \Exception('Checksum of temporary archive does not match... copying/permissions problem');
        $this->_archive = $dest_archive;;

        // get version details (version we are installing)
        // if not in the session, save them there.
        if( isset($sess[__CLASS__.'version']) ) {
            $ver = $sess[__CLASS__.'version'];
            $this->_dest_version = $ver['version'];
            $this->_dest_name = $ver['version_name'];
            $this->_dest_schema = $ver['schema_version'];
        }
        else {
            $verfile = dirname($src_archive).'/version.php';
            if( !is_file($verfile) ) throw new \Exception('Could not find version file');
            include_once($verfile);
            $ver = array('version' => $CMS_VERSION, 'version_name' => $CMS_VERSION_NAME, 'schema_version' => $CMS_SCHEMA_VERSION);
            $sess[__CLASS__.'version'] = $ver;
            $this->_dest_version = $CMS_VERSION;
            $this->_dest_name = $CMS_VERSION_NAME;
            $this->_dest_schema = $CMS_SCHEMA_VERSION;
        }
    }

    static public function autoload($classname)
    {
        if( \__appbase\startswith($classname, 'cms_autoinstaller\\') ) $classname = substr($classname,strlen('cms_autoinstaller\\'));

        $dirs = array(__DIR__,__DIR__.'/base',__DIR__.'/lib',__DIR__.'/wizard');
        foreach( $dirs as $dir ) {
            $fn = $dir."/class.$classname.php";
            if( file_exists($fn) ) {
                include_once($fn);
                return;
            }
        }
    }

    protected function set_config_defaults()
    {
        $tmp = [ 'timezone' => null, 'tmpdir' => null, 'dest' => null, 'debug' => false, 'nofiles' => false, 'nobase' => false, 'lang' => null, 'verbose' => false ];
        $config = array_merge(parent::get_config(), $tmp);
        $this->_orig_tz = $config['timezone'] = @date_default_timezone_get();
        if( !$this->_orig_tz ) $this->_orig_tz = $config['timezone'] = 'UTC';
        $config['dest'] = realpath(getcwd());
        return $config;
    }

    protected function load_config()
    {
        // setup some defaults
        $config = $this->set_config_defaults();

        // override default config with config file
        $config_file = realpath(getcwd()).'/custom_config.ini';
        if( is_file($config_file) && is_readable($config_file) ) {
            $tmp = parse_ini_file($config_file);
            if( is_array($tmp) && count($tmp) ) {
                $config = array_merge($config,$tmp);
                if( isset($tmp['dest']) ) $this->_custom_destdir = $tmp['dest'];
            }
        }

        // override current config with url params
        $request = \__appbase\request::get();
        $list = [ 'TMPDIR', 'tmpdir', 'timezone', 'tz', 'dest', 'destdir', 'debug', 'nofiles', 'no_files', 'nobase' ];
        foreach( $list as $key ) {
	    if( !isset($request[$key]) ) continue;
            $val = $request[$key];
            switch( $key ) {
            case 'TMPDIR':
            case 'tmpdir':
                $config['tmpdir'] = trim($val);
                break;
            case 'timezone':
            case 'tz':
                $config['timezone'] = trim($val);
                break;
            case 'dest':
            case 'destdir':
                $this->_custom_destdir = $config['dest'] = trim($val);
                break;
            case 'debug':
                $config['debug'] = utils::to_bool($val);
                break;
            case 'nobase':
                $config['nobase'] = utils::to_bool($val);
                break;
            case 'nofiles':
            case 'no_files':
                $config['nofiles'] = utils::to_bool($val);
                break;
            }
        }
        return $config;
    }

    protected function check_config($config)
    {
        foreach( $config as $key => $val ) {
            switch( $key ) {
            case 'timezone':
                // do nothing
                break;
            case 'tmpdir':
                if( !$val ) {
                    // no tmpdir set... gotta find or create one.
                    $val = parent::get_tmpdir();
                }
                if( !is_dir($val) || !is_writable($val) ) {
                    // could not find a valid system temporary directory, or none specified. gotta make one
                    $dir = realpath(getcwd()).'/__m'.md5(session_id());
                    if( !@is_dir($dir) && !@mkdir($dir) ) throw new \RuntimeException('Sorry, problem determining a temporary directory, non specified, and we could not create one.');
                    $txt = 'This is temporary directory created for installing CMSMS in punitively restrictive environments.  You may delete this directory and its files once installation is complete.';
                    if( !@file_put_contents($dir.'/__cmsms',$txt) ) throw new \RuntimeException('We could not create a file in the temporary directory we just created (is safe mode on?).');
                    $config[$key] = $dir;
                    $this->_custom_tmpdir = $dir;
                    $val = $dir;
                }
                $config[$key] = $val;
                break;
            case 'dest':
                if( !is_dir($val) || !is_writable($val) ) {
                    throw new \RuntimeException('Invalid config value for '.$key.' - not a directory, or not writable');
                }
                break;
            case 'debug':
            case 'nofiles':
            case 'nobase':
                // do nothing
                break;
            }
        }
        return $config;
    }

    public function get_config()
    {
        $sess = \__appbase\session::get();
        if( isset($sess['config']) ) {
            // already set once... so you must close and re-open the browser to reset it.
            return $sess['config'];
        }

        // gotta load the config, then store it in the session
        $config = $this->load_config();
        $config = $this->check_config($config);
        $sess['config'] = $config;
        return $config;
    }

    private function set_config_val($key,$val)
    {
        $config = $this->get_config();
        $config[trim($key)] = $val;

        $sess = \__appbase\session::get();
        $sess['config'] = $config;
    }


    public function get_orig_error_level() { return $this->_orig_error_level; }

    public function get_orig_tz() { return $this->_orig_tz; }

    public function get_destdir() {
        $config = $this->get_config();
        return $config['dest'];
    }

    public function set_destdir($destdir) {
        $this->set_config_val('dest',$destdir);
    }

    public function has_custom_destdir() {
        $p1 = realpath(getcwd());
        $p2 = realpath($this->_custom_destdir);
        return ($p1 != $p2);
    }

    public function get_archive() { return $this->_archive; }

    public function get_dest_version() { return $this->_dest_version; }

    public function get_dest_name() { return $this->_dest_name; }

    public function get_dest_schema() { return $this->_dest_schema; }

    public function get_phar()
    {
        return \Phar::running();
    }

    public function in_phar() {
        $x = $this->get_phar();
        if( !$x ) return FALSE;
        return TRUE;
    }

    public function get_nls()
    {
        if( is_array($this->_nls) ) return $this->_nls;

        $archive = $this->get_archive();
        $archive = str_replace('\\','/',$archive); // stupid windoze
        if( !file_exists($archive) ) throw new \Exception(\__appbase\lang('error_noarchive'));

        $phardata = new \PharData($archive);
        $nls = array();
        $found = false;
        $pharprefix = "phar://".$archive;
        foreach( new \RecursiveIteratorIterator($phardata) as $file => $it ) {
            if( ($p = strpos($file,'/lib/nls')) === FALSE ) continue;
            $tmp = substr($file,$p);
            if( !\__appbase\endswith($tmp,'.php') ) continue;
            $found = true;
            if( preg_match('/\.nls\.php$/',$tmp) ) {
               $tmpdir = $this->get_tmpdir();
               $fn = "$tmpdir/tmp_".basename($file);
               @copy($file,$fn);
               include($fn);
               unlink($fn);
            }
        }
        if( !$found ) throw new \Exception(\__appbase\lang('error_nlsnotfound'));
        $this->_nls = $nls;
        return $nls;
    }

    public function get_language_list()
    {
        $this->get_nls();
        return $this->_nls['language'];
    }

    public function get_root_url()
    {
        $prefix = null;
        //if( isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' ) $prefix = 'https';
        $prefix .= '//'.$_SERVER['HTTP_HOST'];

        // if we are putting files somewhere else, we cannot determine the root url of the site
        // via the $_SERVER variables.
        $b = $this->get_destdir();
        if( $b != getcwd() ) {
            if( \__appbase\startswith($b,$_SERVER['DOCUMENT_ROOT']) ) $b = substr($b,strlen($_SERVER['DOCUMENT_ROOT']));
            $b = str_replace('\\','/',$b); // cuz windows blows
            if( !\__appbase\endswith($prefix,'/') && !\__appbase\startswith($b,'/') ) $prefix .= '/';
            return $prefix.$b;
        }

        $b = dirname($_SERVER['PHP_SELF']);
        if( $this->in_phar() ) {
            $tmp = basename($_SERVER['SCRIPT_NAME']);
            if( ($p = strpos($b,$tmp)) !== FALSE ) $b = substr($b,0,$p);
        }

        $b = str_replace('\\','/',$b); // cuz windows blows.
        if( !\__appbase\endswith($prefix,'/') && !\__appbase\startswith($b,'/') ) $prefix .= '/';
        return $prefix.$b;
    }

    public function run()
    {
        // set the languages we're going to support.
        $list = \__appbase\nls()->get_list();
        foreach( $list as &$one ) $one = substr($one,0,-4);
        \__appbase\translator()->set_allowed_languages($list);

        // the default language.
        \__appbase\translator()->set_default_language('en_US');

        // get the language preferred by the user (either in the request, in a cookie, or in the session)
        $lang = \__appbase\translator()->get_selected_language();

        if( !$lang ) $lang = \__appbase\translator()->get_default_language(); // get a preferred language

        // set our selected language...
        \__appbase\translator()->set_selected_language($lang);

        // for every request we're gonna make sure it's not cached.
        session_cache_limiter('private');

        // and make sure we are in UTF-8
        header('Content-Type:text/html; charset=UTF-8');

        // and do our stuff.
        try {
            $tmp = 'm'.substr(md5(realpath(getcwd()).session_id()),0,8);
            $wizard = \__appbase\wizard::get_instance(__DIR__.'/wizard','\cms_autoinstaller');
            // this sets a custom step variable for each instance
            // which is just one more security measure.
            // nobody can guess an installer URL and jump to a specific step to
            // nuke anything (even though database creds are stored in the session
            // so are all the other parameters.
            $wizard->set_step_var($tmp);
            $res = $wizard->process();
        }
        catch( \Exception $e ) {
            $smarty = \__appbase\smarty();
            $smarty->assign('error',$e->GetMessage());
            $smarty->display('error.tpl');
        }
    }

    public function cleanup()
    {
        if( $this->_custom_tmpdir ) {
            utils::rrmdir($this->_custom_tmpdir);
        }
    }
} // end of class
