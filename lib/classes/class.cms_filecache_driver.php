<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Class: cms_filecache_driver (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A class for caching data in files for CMSMS.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
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

/**
 * A class to cache data into the TMP_CACHE directory.
 * @package CMS
 * @license GPL
 */

/**
 * A cache driver to cache files using the filesystem.
 *
 * This driver stores files in the CMSMS TMP_CACHE location.  Supports read, and write locking
 * a settable cache lifetime, md5 encoded keys and groups so that filenames cannot be easily
 * determined, and automatic cleaning.
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell
 */
class cms_filecache_driver extends cms_cache_driver
{
    /**
     * @ignore
     */
    const LOCK_READ   = '_read';

    /**
     * @ignore
     */
    const LOCK_WRITE  = '_write';

    /**
     * @ignore
     */
    const LOCK_UNLOCK = '_unlock';

    /**
     * @ignore
     */
    const KEY_SERIALIZED = '__SERIALIZED__';

    /**
     * @ignore
     */
    private $_lifetime = 7200;

    /**
     * @ignore
     */
    private $_locking = true;

    /**
     * @ignore
     */
    private $_blocking = false;

    /**
     * @ignore
     */
    private $_cache_dir = TMP_CACHE_LOCATION;

    /**
     * @ignore
     */
    private $_auto_cleaning = 0;

    /**
     * @ignore
     */
    private $_group = 'default';

    /**
     * Constructor
     *
     * Accepts an associative array of options as follows:
     *   lifetime  => seconds (default 3600)
     *   locking   => boolean (default false)
     *   cache_dir => string (default TMP_CACHE_LOCATION)
     *   auto_cleaning => boolean (default false)
     *   blocking => boolean (default false)
     *   grouop => string (no default)
     * @param string $opts
     */
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


    /**
     * Get a cached value
     * if the $group parameter is not specified the current group will be used
     *
     * @see cms_filecache_driver::set_group
     * @param string $key
     * @param string $group
     */
    public function get($key,$group = '')
    {
        if( !$group ) $group = $this->_group;

        $this->_auto_clean_files();
        $fn = $this->_get_filename($key,$group);
        $data = $this->_read_cache_file($fn);
        return $data;
    }


    /**
     * Clear all cached values from a group
     * if the $group parameter is not specified the current group will be used
     *
     * @see cms_filecache_driver::set_group
     * @param string $group
     */
    public function clear($group = '')
    {
        return $this->_clean_dir($this->_cache_dir,$group,false);
    }


    /**
     * Test if a cached value exists.
     * if the $group parameter is not specified the current group will be used
     *
     * @see cms_filecache_driver::set_group
     * @param string $key
     * @param string $group
     */
    public function exists($key,$group = '')
    {
        if( !$group ) $group = $this->_group;

        $this->_auto_clean_files();
        $fn = $this->_get_filename($key,$group);
        clearstatcache(false,$fn);
        if( is_file($fn) ) return TRUE;
        return FALSE;
    }


    /**
     * Erase a cached value
     * if the $group parameter is not specified the current group will be used
     *
     * @see cms_filecache_driver::set_group
     * @param string $key
     * @param string $group
     */
    public function erase($key,$group = '')
    {
        if( !$group ) $group = $this->_group;

        $fn = $this->_get_filename($key,$group);
        if( is_file($fn) ) {
            @unlink($fn);
            return TRUE;
        }
        return FALSE;
    }


    /**
     * Set a cached value
     * if the $group parameter is not specified the current group will be used
     *
     * @see cms_filecache_driver::set_group
     * @param string $key
     * @param mixed $value
     * @param string $group
     */
    public function set($key,$value,$group = '')
    {
        if( !$group ) $group = $this->_group;

        $fn = $this->_get_filename($key,$group);
        $res = $this->_write_cache_file($fn,$value);
        return $res;
    }


    /**
     * Set the current group
     *
     * @param string $group
     */
    public function set_group($group)
    {
        if( $group ) $this->_group = trim($group);
    }


    /**
     * @ignore
     */
    private function _get_filename($key,$group)
    {
        $fn = $this->_cache_dir . '/cache_'.md5(__DIR__.$group).'_'.md5($key.__DIR__).'.cms';
        return $fn;
    }


    /**
     * @ignore
     */
    private function _flock($res,$flag)
    {
        if( !$this->_locking ) return TRUE;
	if( !$res ) return FALSE;

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

        if( $this->_blocking ) return flock($res,$mode);

        // non blocking lock
        $mode = $mode | LOCK_NB;
        for( $n = 0; $n < 5; $n++ ) {
            $res2 = flock($res,$mode);
            if( $res2 ) return TRUE;
            $tl = rand(5,300);
            usleep($tl);
        }
        return FALSE;
    }


    /**
     * @ignore
     */
    private function _read_cache_file($fn)
    {
        $this->_cleanup($fn);
        $data = null;
        if( is_file($fn) ) {
            clearstatcache();
            $fp = @fopen($fn,'rb');
            if( $fp ) {
                if( $this->_flock($fp,self::LOCK_READ) ) {
                    $len = @filesize($fn);
                    if( $len > 0 ) $data = fread($fp,$len);
                    $this->_flock($fp,self::LOCK_UNLOCK);
                }
                @fclose($fp);

                if( $data && startswith($data,self::KEY_SERIALIZED) ) {
                    $data = unserialize(substr($data,strlen(self::KEY_SERIALIZED)));
                }
                return $data;
            }
        }
    }


    /**
     * @ignore
     */
    private function _cleanup($fn)
    {
        if( is_null($this->_lifetime) ) return;
        clearstatcache();
        $filemtime = @filemtime($fn);
        if( $filemtime < time() - $this->_lifetime ) @unlink($fn);
    }


    /**
     * @ignore
     */
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


    /**
     * @ignore
     */
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


    /**
     * @ignore
     */
    private function _clean_dir($dir,$group = '',$old = true)
    {
        if( !$group ) $group = $this->_group;

        $mask = $dir.'/cache_*_*.cms';
        if( $group ) $mask = $dir.'/cache_'.md5(__DIR__.$group).'_*.cms';

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

} // end of class

?>
