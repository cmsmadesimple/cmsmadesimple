<?php
# CMS - CMS Made Simple
# (c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA
#
#$Id:$

/**
 * @package             CMS
 */

/**
 * Base module class.
 *
 * All modules should inherit and extend this class with their functionality.
 *
 * @since		2.0
 * @package		CMS
 * @author              Robert Campbell <calguy1000@cmsmadesimple.org>
 */

final class CmsAdminMenuItem 
{
  private static $_keys = array('module','section','title','description','action','url','icon','system','priority');
  private $_data = array();

  public function __construct()
  {
  }

  public function __get($k)
  {
    if( !in_array($k,self::$_keys) ) throw new CmsException('Invalid key: '.$k.' for '.__CLASS__.' object');
    if( isset($this->_data[$k]) ) return $this->_data[$k];
  }

  public function __set($k,$v)
  {
    if( !in_array($k,self::$_keys) ) throw new CmsException('Invalid key: '.$k.' for '.__CLASS__.' object');
    $this->_data[$k] = $v;
  }

  public function __isset($k)
  {
    if( !in_array($k,self::$_keys) ) throw new CmsException('Invalid key: '.$k.' for '.__CLASS__.' object');
    return isset($this->_data[$k]);
  }

  public function __unset($k)
  {
    if( !in_array($k,self::$_keys) ) throw new CmsException('Invalid key: '.$k.' for '.__CLASS__.' object');
    throw new CmsException('Cannot unset data from a CmsAdminMenuItem object');
  }

  public function valid()
  {
    foreach( self::$_keys as $ok ) {
      if( $ok == 'icon'  || $ok == 'system' || $ok == 'priority' ) continue;  // we don't care if this is set.
      if( !isset($this->_data[$ok]) ) {
	return FALSE;
      }
    }
    return TRUE;
  }

  public static function &from_module(CMSModule $mod)
  {
    $obj = null;
    if( $mod->HasAdmin() ) {
      $obj = new CmsAdminMenuItem;
      $obj->module = $mod->GetName();
      $obj->section = $mod->GetAdminSection();
      $obj->title   = $mod->GetFriendlyName();
      $obj->description = $mod->GetAdminDescription();
      $obj->action = 'defaultadmin';
      $obj->url = $mod->create_url('m1_',$obj->action);
    }
    return $obj;
  }
} // end of class

#
# EOF
#
?>