<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

final class cms_userprefs
{
  private static $_prefs;

  private function __construct() {}

  private static function _read($userid)
  {
	  if( is_array(self::$_prefs) && isset(self::$_prefs[$userid]) && is_array(self::$_prefs[$userid]) ) return;

	  $db = cmsms()->GetDb();
	  $query = 'SELECT preference,value FROM '.cms_db_prefix().'userprefs WHERE user_id = ?';
	  $dbr = $db->GetArray($query,array($userid));
	  if( is_array($dbr) )
		  {
			  if( !is_array(self::$_prefs) ) self::$_prefs = array();
			  self::$_prefs[$userid] = array();
			  for( $i = 0; $i < count($dbr); $i++ )
				  {
					  $row = $dbr[$i];
					  self::$_prefs[$userid][$row['preference']] = $row['value'];
				  }
		  }
  }

  private static function _userid($value = '')
  {
    return get_userid(false);
  }

  private static function _reset()
  {
    self::$_prefs = null;
  }

  public static function get_for_user($userid,$key,$dflt)
  {
    self::_read($userid);
    if( isset(self::$_prefs[$userid][$key]) )
      {
	return self::$_prefs[$userid][$key];
      }
    return $dflt;
  }

  public static function get($key,$dflt = '')
  {
    return self::get_for_user(self::_userid(),$key,$dflt);
  }

  public static function exists_for_user($userid,$key)
  {
    self::_read($userid);
    if( isset(self::$_prefs[$userid][$key]) ) return TRUE;
    return FALSE;
  }

  public static function exists($key)
  {
    return self::exists_for_user(self::_userid(),$key);
  }

  public static function set_for_user($userid,$key,$value)
  {
    self::_read($userid);
    $db = cmsms()->GetDb();
    if( !self::exists_for_user($userid,$key) )
      {
	$query = 'INSERT INTO '.cms_db_prefix().'userprefs (user_id,preference,value) VALUES (?,?,?)';
	$dbr = $db->Execute($query,array(self::_userid(),$key,$value));
      }
    else
      {
	$query = 'UPDATE '.cms_db_prefix().'userprefs SET value = ? WHERE user_id = ? AND preference = ?';
	$dbr = $db->Execute($query,array($value,$userid,$key));
      }
    self::$_prefs[$userid][$key] = $value;
  }

  public static function set($key,$value)
  {
    return self::set_for_user(self::_userid(),$key,$value);
  }

  public static function remove_for_user($userid,$key,$like = FALSE)
  {
    self::_read($userid);
    $query = 'DELETE FROM '.cms_db_prefix().'userprefs WHERE preference = ? AND user_id = ?';
    if( $like )
      {
	$query = 'DELETE FROM '.cms_db_prefix().'userprefs WHERE preference LIKEn ? AND user_id = ?';
	$key .= '%';
      }
    $db = cmsms()->GetDb();
    $db->Execute($query,array($key,self::_userid()));
    self::_reset();
  }

  public static function remove($key,$like = FALSE)
  {
    return self::remove_for_user(self::_userid(),$key,$like);
  }
}

#
# EOF
#
?>