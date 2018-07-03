<?php
namespace cms_autoinstaller;
use \__appbase\utils;
use \__appbase\app;

//require_once(__DIR__.'/compat.functions.php');

class user_aborted extends \Exception {}

abstract class cms_install_base extends app
{
    private $_nls;

    public function get_phar()
    {
        return \Phar::running();
    }

    public function in_phar()
    {
        $x = $this->get_phar();
        if( !$x ) return FALSE;
        return TRUE;
    }

    abstract public function get_archive();

    abstract public function get_destdir();

    abstract public function get_dest_version();

    abstract public function get_dest_name();

    abstract public function get_dest_schema();

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


} // class