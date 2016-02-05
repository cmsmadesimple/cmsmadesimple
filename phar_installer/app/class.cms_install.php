<?php

namespace cms_autoinstaller;

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
        try {
            $sess = \__appbase\session::get();

            // check if there is a TMPDIR setting in the request
            $request = \__appbase\request::get();
            if( isset($request['TMPDIR']) && $request['TMPDIR'] != '' ) {
                $path = realpath($request['TMPDIR']);
                if( is_dir($path) && is_writable($path) ) {
                    // store it in the session for later.
                    $sess['tmpdir'] = $path;
                    putenv("TMPDIR=$path");
                    return $path;
                }
            }

            // if there was a TMPDIR setting in the URL, that we stored in the session
            // use it.
            if( isset($sess['tmpdir']) && $sess['tmpdir'] ) {
                $path = $sess['tmpdir'];
                if( is_dir($path) && is_writable($path) ) {
                    putenv("TMPDIR=$path");
                    return $path;
                }
            }

            // try other methods to get the tmpdir
            $path = parent::get_tmpdir();
            putenv("TMPDIR=$path");
            return $path;
        }
        catch( \Exception $e ) {
            // open basedir is probably in effect.
            // we need a place to store our archive, and compiled smarty templates and stuff
            $dir = realpath(getcwd()).'/__m'.md5(session_id());
            if( !@is_dir($dir) && !@mkdir($dir) ) throw $e;
            $txt = 'This is temporary directory created for installing CMSMS in punitively restrictive environments.  You may delete this directory and its files once installation is complete.';
            if( !@file_put_contents($dir.'/__cmsms',$txt) ) throw $e;
            putenv("TMPDIR=$path");
            $this->_custom_tmpdir = $dir;
            return $dir;
        }
    }

    public function __construct()
    {
        // save some timezone info
        $this->_orig_tz = @date_default_timezone_get();
        if( !$this->_orig_tz ) $this->_orig_tz = 'UTC';
        date_default_timezone_set($this->_orig_tz);
        $this->_orig_error_level = error_reporting();

        parent::__construct(__FILE__);

        // initialize the session.
        $sess = \__appbase\session::get();
        $junk = $sess[__CLASS__]; // this is junk, but triggers session to start.

        spl_autoload_register(__NAMESPACE__.'\cms_install::autoload');
        $smarty = \__appbase\smarty();
        $smarty->assign('APPNAME','cms_installer');
        $config = $this->get_config();
        $smarty->assign('config',$config);
        $smarty->assign('installer_version',$config['installer_version']);

        $fn = $this->get_appdir().'/build.ini';
        $build = null;
        if( file_exists($fn) ) $build = parse_ini_file($fn);
        if( isset($build['build_time']) ) $smarty->assign('build_time',$build['build_time']);

        // get the request
        $request = \__appbase\request::get();

        // handle debug mode
        if( isset($request['debug']) && $request['debug'] ) $sess['debug'] = (int)$request['debug'];
        if( isset($sess['debug']) && $sess['debug'] ) {
            @ini_set('display_errors',1);
            @error_reporting(E_ALL);
        }

        // handle base href stuff
        if( isset($request['nobase']) ) $sess['nobase'] = 1;

        if( $this->in_phar() && (!isset($sess['nobase']) || $sess['nobase'] == 0) ) {
            $base_href = $_SERVER['SCRIPT_NAME'];
            if( \__appbase\endswith($base_href,'.php') ) {
                $base_href = $base_href . '/';
                $smarty->assign('BASE_HREF',$base_href);
            }
        }

        // find a source directory
        if( isset($request['dest']) ) {
            if( \__appbase\startswith($request['dest'],'-') ) {
                unset($sess[__CLASS__.'dest']);
            }
            else {
                $dest = trim($request['dest']);
                if( !is_dir($dest) ) throw new \Exception('Invalid source directory specified');
                $dest = realpath($dest);
                if( !is_dir($dest) ) throw new \Exception('Invalid source directory specified');
                $this->set_custom_destdir($dest);
            }
        }
        else if( isset($sess[__CLASS__.'dest']) ) {
            $dest = $sess[__CLASS__.'dest'];
            $this->set_custom_destdir($dest);
        }
        else {
            $dest = getcwd();
            $dest = realpath($dest);
            $this->_destdir = $dest;
        }

        // find our archive, copy it... and rename it securely.
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
            if( !file_exists($verfile) ) throw new \Exception('Could not find version file');
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

    public function get_orig_error_level() { return $this->_orig_error_level; }

    public function get_orig_tz() { return $this->_orig_tz; }

    public function get_destdir() { return $this->_destdir; }

    public function set_custom_destdir($destdir) {
        $this->_destdir = $destdir;
        $this->_custom_destdir = 1;
        $sess = \__appbase\session::get();
        $sess[__CLASS__.'dest'] = $destdir;
    }

    public function has_custom_destdir() { return $this->_custom_destdir; }

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
            if( preg_match('/\.nls\.php$/',$tmp) ) include($file);
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
        $prefix = 'http';
        if( isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' ) $prefix = 'https';
        $prefix .= '://'.$_SERVER['HTTP_HOST'];

        // if we are putting files somewhere else, we cannot determine the root url of the site
        // via the $_SERVER variables.
        if( $this->has_custom_destdir() ) {
            $b = $this->get_destdir();
            if( \__appbase\startswith($b,$_SERVER['DOCUMENT_ROOT']) ) {
                $b = substr($b,strlen($_SERVER['DOCUMENT_ROOT']));
            }
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
            $request = \__appbase\request::get();
            if( isset($request['nofiles']) && $request['nofiles'] ) {
                $wizard->set_data('nofiles',(int)$request['nofiles']);
            } else if( isset($request['no_files']) && $request['no_files'] ) {
                $wizard->set_data('nofiles',(int)$request['nofiles']);
            }

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
            \__appbase\utils::rrmdir($this->_custom_tmpdir);
        }
    }
} // end of class

?>
