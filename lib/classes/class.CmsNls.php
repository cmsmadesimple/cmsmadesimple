<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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
#$Id: translation.functions.php 7867 2012-04-13 18:00:14Z calguy1000 $

/**
 * Translation functions/classes
 *
 * @package CMS
 */

/**
 * A class to provide data and methods for encapsulating a single language
 *
 * @author Robert Campbell
 * @since 1.11
 * @package CMS
 * @license GPL
 */
class CmsNls
{
  protected $_isocode;
  protected $_locale;
  protected $_fullname;
  protected $_encoding;
  protected $_aliases;
  protected $_display;
  protected $_key;
  protected $_direction;
  protected $_htmlarea;

  public function __construct() {}

  public function matches($str)
  {
    if( $str == $this->name() ) return TRUE;
    if( $str == $this->isocode() ) return TRUE;
    if( $str == $this->fullname() ) return TRUE;
    $aliases = $this->aliases();
    if( !is_array($aliases) ) $aliases = explode(',',$aliases);
    if( is_array($aliases) && count($aliases) )
      {
	for( $i = 0; $i < count($aliases); $i++ )
	  {
	    if( strtolower($aliases[$i]) == strtolower($str) ) return TRUE;
	  }
      }
    return FALSE;
  }

  public function name()
  {
    return $this->_key;
//     $name = get_class();
//     if( endswith($name,'_nls') )
//       {
// 	$name = substr($name,0,strlen($name)-4);
//       }
//     return $name;
  }

  public function isocode()
  {
    if( !$this->_isocode )
      {
	return substr($this->_fullname,0,2);
      }
    return $this->_isocode;
  }

  public function display()
  {
    if( $this->_display )
      {
	return $this->_display;
      }
  }

  public function locale()
  {
//     if( !$this->_locale ) {
//       debug_display($this);
//       return $this->name();
//     }
    return $this->_locale;
  }

  public function encoding()
  {
    if( !$this->_encoding )
      return 'UTF-8';
    return $this->_encoding;
  }

  public function fullname()
  {
    if( $this->_fullname )
      return $this->_fullname;
  }

  public function aliases()
  {
    if( $this->_aliases )
      {
	if( is_array($this->_aliases) )
	  return $this->_aliases;
	return explode(',',$this->_aliases);
      }
  }

  public function key()
  {
    return $this->_key;
  }

  public function direction()
  {
    if( $this->_direction ) return $this->_direction;
    return 'ltr';
  }

  public function htmlarea()
  {
    if( $this->_htmlarea )
      return $this->_htmlarea;
    return substr($this->_fullname,0,2);
  }

  public static function &from_array($data)
  {
    $obj = new CmsNls();

    // name and key
    if( isset($data['englishlang']) )
      {
	foreach( $data['englishlang'] as $k => $v )
	  {
	    $obj->_fullname = $v;
	    $obj->_key = $k;
	    break;
	  }
      }

    // get the display value
    if( isset($data['language'][$obj->_key]) )
      {
	$obj->_display = $data['language'][$obj->_key];
      }

    // get the isocode?
    if( isset($data['isocode'][$obj->_key]) )
      {
	$obj->_isocode = $data['isocode'][$obj->_key];
      }
    else
      {
	$t = explode('_',$obj->_key);
	if( is_array($t) && count($t) )
	  {
	    $obj->_isocode = $t[0];
	  }
      }

    // get the locale
    if( isset($data['locale'][$obj->_key]) )
      {
	$obj->_locale = $data['locale'][$obj->_key];
      }

    // get the encoding
    if( isset($data['encoding'][$obj->_key]) )
      {
	$obj->_encoding = $data['encoding'][$obj->_key];
      }

    if( isset($data['htmlarea'][$obj->_key]) )
      {
	$obj->_htmlarea = $data['htmlarea'][$obj->_key];
      }

    // get the direction
    if( isset($data['direction'][$obj->_key]) )
      {
	$obj->_direction = $data['direction'][$obj->_key];
      }

    // get aliases
    if( isset($data['alias']) )
      {
	$obj->_aliases= array_keys($data['alias']);
      }

    if( $obj->_key == '' )
      {
	debug_display($data);
	debug_display($obj); die();
      }
    return $obj;
  }
} // end of class

#
# EOF
#
?>