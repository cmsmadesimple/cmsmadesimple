<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: content.functions.php 6863 2011-01-18 02:34:48Z calguy1000 $

/**
 * @package CMS
 */

/**
 * A simple class for handling layout templates as a resource.
 *
 * @package CMS
 * @author Robert Campbell
 * @internal
 * @ignore
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.12
 */
class CmsTemplateCache
{
  static private $_instance;
  private $_cache;
  private $_key;

  public function __construct()
  {
    if( !cmsms()->is_frontend_request() ) {
      throw new CmsLogicException('This class cannot be instantiated on a frontend request');
    }
    if( self::$_instance ) {
      throw new CmsLogicException('Only one instance of this class is permitted');
    }
    self::$_instance = TRUE;

    $this->_key = md5(cmsms()->get_variable('content_id').$_SERVER['REQUEST_URI']);
    $fn = cms_join_path(TMP_CACHE_LOCATION,'template_cache');
    if( file_exists($fn) && filemtime($fn) >= time() - 3600 ) {
      $tmp = file_get_contents($fn);
      if( $tmp ) {
	$this->_cache = unserialize($tmp);
      }
      if( isset($this->_cache[$this->_key]) ) {
	CmsLayoutTemplate::load_bulk($this->_cache[$this->_key]['templates']);
	CmsLayoutTemplateType::load_bulk($this->_cache[$this->_key]['types']);
      }
    }

  }

  public function __destruct()
  {
    // update the cache;
    $dirty = FALSE;
    $t1 = CmsLayoutTemplate::get_loaded_templates();
    $t2 = array();
    if( isset($this->_cache[$this->_key]['templates']) ) {
      $t2 = $this->_cache[$this->_key]['templates'];
    }
    $x = array_diff($t1,$t2);
    if( is_array($x) && count($x) ) {
      $this->_cache[$this->_key]['templates'] = $t1;
      $dirty = TRUE;
    }

    $t1 = CmsLayoutTemplateType::get_loaded_types();
    $t2 = array();
    if( isset($this->_cache[$this->_key]['types']) ) {
      $t2 = $this->_cache[$this->_key]['types'];
    }
    $x = array_diff($t1,$t2);
    if( is_array($x) && count($x) ) {
      $this->_cache[$this->_key]['types'] = $t1;
      $dirty = TRUE;
    }

    if( $dirty ) {
      debug_to_log($this->_cache);
      $fn = cms_join_path(TMP_CACHE_LOCATION,'template_cache');
      file_put_contents($fn,serialize($this->_cache));
    }
  }

  public static function clear_cache()
  {
    $fn = cms_join_path(TMP_CACHE_LOCATION,'template_cache');
    @unlink($fn);
  }
} // end of class

#
# EOF
#
?>