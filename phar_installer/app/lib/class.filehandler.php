<?php

namespace cms_autoinstaller;

use Exception;
use PharFileInfo;
use function __appbase\get_app;
use function __appbase\lang;

abstract class filehandler
{
  private $_destdir;
  private $_output_fn;
  private $_languages;

  protected function get_config()
  {
    return get_app()->get_config();
  }

  public function set_destdir($destdir)
  {
    if( !is_dir($destdir) ) throw new Exception(lang('error_dirnotvalid',$destdir));
    if( !is_writable($destdir) ) throw new Exception(lang('error_dirnotvalid',$destdir));
    $this->_destdir = $destdir;
  }

  public function get_destdir()
  {
    if( !$this->_destdir ) throw new Exception(lang('error_nodestdir'));
    return $this->_destdir;
  }

  public function set_languages($lang)
  {
    if( !is_array($lang) ) return;
    $this->_languages = $lang;
  }

  public function get_languages()
  {
    return $this->_languages;
  }

  public function set_output_fn($fn)
  {
    if( !is_callable($fn) ) throw new Exception(lang('error_internal',1102));
    $this->_output_fn = $fn;
  }

  public function output_string($txt)
  {
    if( $this->_output_fn ) call_user_func($this->_output_fn,$txt);
  }

  protected function is_excluded($filespec)
  {
    $filespec = trim($filespec);
    if( !$filespec ) throw new Exception(lang('error_internal',1101));
    $config = $this->get_config();
    if( !isset($config['install_excludes']) ) return FALSE;

    $excludes = explode('||',$config['install_excludes']);
    foreach( $excludes as $excl ) {
      if( preg_match($excl,$filespec) ) return TRUE;
    }
    return FALSE;
  }

  protected function dir_exists($filespec)
  {
    $filespec = trim($filespec);
    if( !$filespec ) throw new Exception(lang('error_invalidparam','filespec'));

    $dn = dirname($filespec);
    $tmp = $this->get_destdir()."/$dn";
    return (is_dir($tmp))?TRUE:FALSE;
  }

  protected function create_directory($filespec)
  {
    $filespec = trim($filespec);
    if( !$filespec ) throw new Exception(lang('error_invalidparam','filespec'));

    $dn = dirname($filespec);
    $tmp = $this->get_destdir()."/$dn";
    return @mkdir($tmp,0777,TRUE);
  }

  protected function is_imagefile($filespec)
  {
      // this method uses (ugly) extensions because we cannot rely on finfo_open being available.
      $image_exts = ['bmp','jpg','jpeg','gif','png','svg','webp','ico'];
      $ext = strtolower(substr(strrchr($filespec, '.'), 1));
      return in_array($ext,$image_exts);
  }

  protected function is_langfile($filespec)
  {
    $filespec = trim($filespec);
    if( !$filespec ) throw new Exception(lang('error_invalidparam','filespec'));

    if( $this->is_imagefile($filespec) ) return FALSE;
    $bn = basename($filespec);
    $dn = dirname($filespec);
    $fnmatch = 0;
    $fnmatch = $fnmatch || preg_match('/^[a-zA-Z]{2}_[a-zA-Z]{2}\.php$/',$bn);
    $fnmatch = $fnmatch || preg_match('/^[a-zA-Z]{2}_[a-zA-Z]{2}\.nls\.php$/',$bn);
    if( $fnmatch ) return substr($bn,0,strpos($bn,'.'));

    $nls = get_app()->get_nls();
    if( !is_array($nls) ) return FALSE; // problem

    $bn = substr($bn,0,strpos($bn,'.'));
    $last_dn = basename($dn);
    foreach( $nls['alias'] as $alias => $code ) {
      if( $bn == $alias ) return $code;
    }
    foreach( $nls['htmlarea'] as $code => $short ) {
      if( $bn == $short ) return $code;
    }

    return FALSE;
  }

  protected function is_accepted_lang($filespec)
  {
    $res = $this->is_langfile($filespec);
    if( !$res ) return FALSE;

    $langs = $this->get_languages();
    if( !is_array($langs) || count($langs) == 0 ) return TRUE;

    return in_array($res,$langs);
  }

  abstract public function handle_file($filespec,$srcspec,PharFileInfo $fi);
}

?>
