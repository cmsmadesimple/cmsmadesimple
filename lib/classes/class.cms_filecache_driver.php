<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Class: cms_filecache_driver (c) 2013 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A class for caching data in files for CMSMS.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE

class cms_filecache_driver extends cms_cache_driver
{
  const LOCK_READ   = '_read';
  const LOCK_WRITE  = '_write';
  const LOCK_UNLOCK = '_unlock';
  const KEY_SERIALIZED = '__SERIALIZED__';

  private $_lifetime = 3600;
  private $_locking = false;
  private $_blocking = false;
  private $_cache_dir = TMP_CACHE_LOCATION;
  private $_auto_cleaning = 0;
  private $_group = 'default';

  public function __construct($opts)
  {
    $_keys = array('lifetime','locking','cache_dir','auto_cleaning','blocking','group');
    if( is_array($opts) ) {
      foreach( $opts as $key => $value ) {
	if( in_array($key,$_keys) ) {
	  $tmp = '_'.$key;
	  $this->$tmp = $value;
	}
      }
    }
  }

  public function get($key,$group = '')
  {
    if( !$group ) $group = $this->_group;

    $this->_auto_clean_files();
    $fn = $this->_get_filename($key,$group);
    $data = $this->_read_cache_file($fn);
    return $data;
  }


  public function clear($group = '')
  {
    return $this->_clean_dir($this->_cache_dir,$group,false);
  }


  public function exists($key,$group = '')
  {
    if( !$group ) $group = $this->_group;

    $this->_auto_clean_files();
    $fn = $this->_get_filename($key,$group);
    clearstatcache();
    if( @file_exists($fn) ) return TRUE;
    return FALSE;
  }


  public function erase($key,$group = '')
  {
    if( !$group ) $group = $this->_group;

    $fn = $this->_get_filename($key,$group);
    if( @file_exists($fn) ) {
      @unlink($fn);
      return TRUE;
    }
    return FALSE;
  }


  public function set($key,$value,$group = '')
  {
    if( !$group ) $group = $this->_group;

    $fn = $this->_get_filename($key,$group);
    $res = $this->_write_cache_file($fn,$value);
    return $res;
  }


  public function set_group($group)
  {
    if( $group ) $this->_group = trim($group);
  }

  private function _get_filename($key,$group)
  {
    $fn = $this->_cache_dir . '/cache_'.md5(__DIR__.$group).'_'.md5($key.__DIR__).'.cms';
    return $fn;
  }


  private function _flock($res,$flag)
  {
    if( !$this->_locking ) return TRUE;

    $mode = '';
    switch( strtolower($flag) ) {
    case self::LOCK_READ:
      $mode = LOCK_SH;
      break;

    case self::LOCK_WRITE:
      $mode = LOCK_EX;
      break;

    case self::LOCK_UNLOCK:
      $mode = LOCK_UN;
    }
      
    if( $this->_blocking ) {
      return flock($res,$mode);
    }

    // non blocking lock
    $mode = $mode | LOCK_NB;
    for( $n = 0; $n < 5; $n++ ) {
      $res = flock($res,$mode);
      if( $res ) return TRUE;
      $tl = rand(1,300);
      usleep($tl);
    }
    return FALSE;
  }

  private function _read_cache_file($fn)
  {
    $this->_cleanup($fn);
    $data = null;
    if( @file_exists($fn) ) {
      clearstatcache();
      $fp = @fopen($fn,'rb');
      if( $fp ) {
	if( $this->_flock($fp,self::LOCK_READ) ) {
	  $len = @filesize($fn);
	  if( $len > 0 ) $data = fread($fp,$len);
	  $this->_flock($fp,self::LOCK_UNLOCK);
	}
	@fclose($fp);

	if( startswith($data,self::KEY_SERIALIZED) ) {
	  $data = unserialize(substr($data,strlen(self::KEY_SERIALIZED)));
	}
	return $data;
      }
    }
  }


  private function _cleanup($fn)
  {
    if( is_null($this->_lifetime) ) return;
    clearstatcache();
    $filemtime = @filemtime($fn);
    if( $filemtime < time() - $this->_lifetime ) {
      @unlink($fn);
    }
  }


  private function _write_cache_file($fn,$data)
  {
    @touch($fn);
    $fp = @fopen($fn,'r+');
    if( $fp ) {
      if( !$this->_flock($fp,self::LOCK_WRITE) ) {
	@fclose($fp);
	@unlink($fn);
	return FALSE;
      }
      else {
	if( is_array($data) || is_object($data) ) {
	  $data = self::KEY_SERIALIZED.serialize($data);
	}
	@fwrite($fp,$data);
	$this->_flock($fp,self::LOCK_UNLOCK);
      }
      @fclose($fp);
      return TRUE;
    }
    return FALSE;
  }


  private function _auto_clean_files()
  {
    if( $this->_auto_cleaning > 0 ) {
      // only clean files once per request.
      static $_have_cleaned = FALSE;
      if( !$_have_cleaned ) {
	$res = $this->_clean_dir($this->_cache_dir);
	if( $res ) $_have_cleaned = TRUE;
	return $res;
      }
    }
    return 0;
  }


  private function _clean_dir($dir,$group = '',$old = true)
  {
    if( !$group ) $group = $this->_group;

    $mask = $dir.'/dcache_*_*.cg';
    if( $group ) $mask = $dir.'/dcache_'.md5($group).'_*.cg';
    
    $files = glob($mask);
    if( !is_array($files) ) return 0;

    $nremoved = 0;
    foreach( $files as $file ) {
      if( is_file($file) ) {
	$del = false; 
	if( $old == true ) {
	  if( !is_null($this->_lifetime) ) {
	    if( (time() - @filemtime($file)) > $this->_lifetime ) {
	      @unlink($file);
	      $nremoved++;
	    }
	  }
	}
	else {
	  // clean all files... 
	  @unlink($file);
	  $nremoved++;
	}
      }
    }

    return $nremoved;
  }
}

?>