<?php

# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: class.content.inc.php 6873 2011-01-26 20:24:56Z calguy1000 $

/**
 * @package CMS
 */

/**
 * A lock object.
 *
 * A class to contain information about a single lock.
 *
 * @since		1.10
 * @package		CMS
 */
class cms_lock implements ArrayAccess
{
  private static $_keys = array('module','record','uid','signature','created','modified');
  private $_data;

  public function __construct(/* variable arguments */)
  {
    $module = $record = $uid = $signature = $created = $modified = '';
    $args = func_get_args();
    if( count($args) == 1 && is_array($args[0]) )
      {
	$args = $args[0];
      }
    
    if( count($args) < 4 )
      {
	throw new Exception('Attempt to create lock object with invalid data');
      }
    if( isset($args['module']) || isset($args['section']) )
      {
	if( isset($args['section']) ) $module = trim($args['section']);
	if( isset($args['module']) ) $module = trim($args['module']);
	if( isset($args['signature']) ) $signature = trim($args['signature']);
	if( isset($args['record'])) $record = (int)$args['record'];
	if( isset($args['uid'])) $uid = (int)$args['uid'];
	if( isset($args['created']) ) $created = $args['created'];
	if( isset($args['modified']) ) $created = $args['modified'];
      }
    else
      {
	$module = trim($args[0]);
	$record = (int)$args[1];
	$uid = (int)$args[2];
	$signature = trim($args[3]);
      }

    if( !$module || !$record || !$uid || !$signature )
      {
	throw new Exception('Attempt to create lock object with invalid data');
      }

    $this->_data = array();
    $this->_data['module'] = $module;
    $this->_data['record'] = (int)$record;
    $this->_data['uid'] = (int)$uid;
    $this->_data['signature'] = $signature;
    $this->_data['created'] = $created;
    $this->_data['modified'] = $created;
  }

  public function offsetGet($k)
  {
    if( !in_array($k,self::$_keys) )
      throw new Exception('Attempt to retrieve invalid data from lock object: '.$k);

    return $this->_data[$k];
  }

  public function offsetSet($k,$v)
  {
    if( !in_array($k,self::$_keys) )
      throw new Exception('Attempt to set invalid data into lock object');

    switch($k)
      {
      case 'module':
      case 'signature':
	$this->_data[$k] = trim($v);
	break;

      case 'record':
      case 'uid':
	$this->_data[$k] = (int)$v;
	break;

      case 'created':
      case 'modified':
	throw new Exception('Attempt to set invalid data into lock object');
	break;
      }
  }

  public function offsetExists($k)
  {
    if( in_array($k,self::$_keys) ) return TRUE;
    return FALSE;
  }

  public function offsetUnset($k)
  {
    throw new Exception('Attempt to erase information from lock object');
  }

  public function touch()
  {
    // touch this lock
    cms_lock_manager::touch($this);
  }

  public function erase()
  {
    cms_lock_manager::erase($this);
  }

  public function from_array($data)
  {
    foreach( $data as $key => $value )
      {
	if( !in_array($key,self::$_keys) )
	  {
	    throw new Exception('Attempt to set invalid data into lock object');
	  }
	$this->_ddata[$key] = $value;
      }
  }
} // end of class

#
# EOF
#
?>