<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CmsTemplateCache (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A simple class to handle remembering and preloading template data for
#  frotnend requests.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
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
 * @package CMS
 */

/**
 *  A simple class to handle remembering and preloading template data for
 *  frotnend requests.
 *
 * @package CMS
 * @author Robert Campbell
 * @internal
 * @ignore
 * @copyright Copyright (c) 2013, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.0
 */
class CmsTemplateCache
{
  static private $_instance;
  private $_cache;
  private $_key;

  public function __construct()
  {
	  if( !CmsApp::get_instance()->is_frontend_request() ) throw new CmsLogicException('This class can only be instantiated on a frontend request');
	  if( self::$_instance ) throw new CmsLogicException('Only one instance of this class is permitted');
	  self::$_instance = TRUE;

	  $this->_key = md5($_SERVER['REQUEST_URI'].serialize($_GET));
	  if( ($tmp = cms_cache_handler::get_instance()->get('template_cache')) ) {
		  $this->_cache = unserialize($tmp);
		  if( isset($this->_cache[$this->_key]) ) {
			  CmsLayoutTemplate::load_bulk($this->_cache[$this->_key]['templates']);
			  if( isset($this->_cache[$this->_key]['types']) ) CmsLayoutTemplateType::load_bulk($this->_cache[$this->_key]['types']);
		  }
	  }
  }

  public function __destruct()
  {
      // update the cache;
      if( !CmsApp::get_instance()->is_frontend_request() ) return;

      $dirty = FALSE;
      $t1 = CmsLayoutTemplate::get_loaded_templates();
      if( is_array($t1) ) {
          $t2 = array();
          if( isset($this->_cache[$this->_key]['templates']) ) $t2 = $this->_cache[$this->_key]['templates'];
          $x = array_diff($t1,$t2);
          if( is_array($x) && count($x) ) {
              $this->_cache[$this->_key]['templates'] = $t1;
              $dirty = TRUE;
          }
      }

      $t1 = CmsLayoutTemplateType::get_loaded_types();
      if( is_array($t1) ) {
          $t2 = array();
          if( isset($this->_cache[$this->_key]['types']) ) $t2 = $this->_cache[$this->_key]['types'];
          $x = array_diff($t1,$t2);
          if( is_array($x) && count($x) ) {
              $this->_cache[$this->_key]['types'] = $t1;
              $dirty = TRUE;
          }
      }

      if( $dirty ) cms_cache_handler::get_instance()->set('template_cache',serialize($this->_cache));
  }

  public static function clear_cache()
  {
	  if( !defined('TMP_CACHE_LOCATION') ) return;
	  $fn = cms_join_path(TMP_CACHE_LOCATION,'template_cache');
	  @unlink($fn);
  }
} // end of class

#
# EOF
#
?>