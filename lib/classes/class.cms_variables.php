<?php
// -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_tree (c) 2010 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A simple php tree class.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
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

/**
 * @package CMS
 */

/**
 * A singleton class to manage internal CMSMS Variables.
 *
 * This is an internal class.  Using this class to read or adjust internal variables from third party code is not supported
 * and constites a violation of forge rules.  This class is slowly being removed.
 *
 * @package CMS
 * @deprecated
 * @internal
 * @ignore
 * @author Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 */
class cms_variables implements ArrayAccess
{
  private $_allowed_variables = 
    array('content_obj','content_id','page','page_id','page_name','position','friendly_position','starttime','user_id','username','pageinfo',
	  'user_in_group','content-type','modulenum','error','formcount','mid_cache','userperms','ownerpages','authorpages','bulkcontent','authorblobs',
	  'template','last_content_modification');

  private static $_instance;
  private $_data = array();

  // this is a singleton.
  private function __construct()
  {
    $this->_data['content-type'] = 'text/html';
  }


  /**
   * Return the single allowed instance of this class.  The instance will be created if necessary.
   *
   * @returns object.
   */
  public static function get_instance()
  {
    if (!isset(self::$_instance)) {
      $c = __CLASS__;
      self::$_instance = new $c;
    }

    return self::$_instance;
  }

  public function offsetExists($key)
  {
    if( $key == 'admintheme' ) return TRUE; // special case
    return isset($this->_data[$key]);
  }

  public function offsetGet($key)
  {
    if( $key == 'admintheme' )
      {
	// special case, for compatibility.
	return cms_utils::get_theme_object();
      }

    if( !in_array($key,$this->_allowed_variables) )
      {
	trigger_error('Retrival of unauthorized internal variables is deprecated: '.$key,E_USER_NOTICE);
	return;
      }
    if( isset($this->_data[$key]) )
      {
	return $this->_data[$key];
      }
    switch( $key )
      {
      case 'convertclass':
	$this->_data[$key] = new ConvertClass();
	return $this->_data[$key];
      }
  }

  public function offsetSet($key,$value)
  {
    // could do a friend thing here... or other limiting things.
    if( !in_array($key,$this->_allowed_variables) )
      {
	trigger_error('Modification of internal data is deprecated: '.$key,E_USER_NOTICE);
      }
    $this->_data[$key] = $value;
  }

  public function offsetUnset($key)
  {
    if( !in_array($key,$this->_allowed_variables) )
      {
	trigger_error('Unsetting internal variable '.$key.' is invalid',E_USER_ERROR);
      }
    if( isset($this->_data[$key]) )
      {
	unset($this->_data[$key]);
      }
  }

} // end of class

?>