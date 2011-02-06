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
 * Lock Management Class
 *
 * A class to manage locks generically for any object within CMSMS.
 *
 * @since		1.10
 * @package		CMS
 */
class cms_lock_manager
{
  const STATUS_LOCKED = 'locked';
  const STATUS_UNLOCKED = 'unlocked';
  const STATUS_STALE = 'stale';

  private static $_locks;

  private static function get_locks($module)
  {
    if( !$module ) throw new Exception('Attempt to Get Locks without specifying a section or module');
    if( !is_array(self::$_locks) ) self::$_locks = array();
    if( isset(self::$_locks[$module] ) ) return;

    // now we gotta load em.
    $db = cmsms()->GetDb();
    $query = 'SELECT * FROM '.cms_db_prefix().'locks WHERE section = ? ORDER BY created DESC';
    $tmp = $db->GetArray($query,array($module));
    if( is_array($tmp) )
      {
	self::$_locks[$module] = $tmp;
      }
  }

  /**
   * Test if something is locked
   *
   * @param string The module (or section) of object that is locked.  i.e: Core::Content
   * @param int    The id of the record to check for
   * @return boolean
   */
  public static function is_locked($module,$record_id)
  {
    $res = self::get_lock($module,$record_id);
    if( is_object($res) ) return TRUE;
    return FALSE;
  }


  /**
   * Test if something is locked
   *
   * @param string The module (or section) of object that is locked.  i.e: Core::Content
   * @param int    The id of the record to check for
   * @return object a cms_lock object representing the lock, or null.
   */
  public static function get_lock($module,$record_id)
  {
    self::get_locks($module);

    if( !isset(self::$_locks[$module]) ) return;
    foreach( self::$_locks[$module] as $lock_record )
      {
	if( $lock_record['record'] == $record_id ) 
	  {
	    return new cms_lock($lock_record);
	  }
      }
    return;
  }

 
  static public function lock($module,$record,$userid = '',$signature = '')
  {
    if( !$module ) throw new Exception('Cannot create lock with no module specified');
    if( !$userid ) $userid = get_userid();
    if( !$signature ) $signature = md5(serialize($_SERVER).serialize($_GET).session_id());

    $tmp = self::get_lock($module,$record);
    if( is_object($tmp) ) 
      {
	if( $tmp['uid'] == $userid ) return TRUE; // already locked by me.
	return FALSE; // already locked by somebody else.
      }

    $db = cmsms()->GetDb();
    $query = 'INSERT INTO '.cms_db_prefix().'locks (section,record,uid,signature,created,modified)
              VALUES (?,?,?,?,NOW(),NOW())';
    $dbr = $db->Execute($query,array($module,$record,$userid,$signature));
    if( !$dbr ) return FALSE;
    return TRUE;
  }


  static public function unlock($module,$record,$userid = '',$signature = '')
  {
    if( !$module ) throw new Exception('Cannot destroy lock with no module specified');
    if( !$userid ) $userid = get_userid();
    if( !$signature ) $signature = md5(serialize($_SERVER).serialize($_GET).session_id());

    $lock = self::get_lock($module,$record);
    if( is_object($lock) )
      {
	if( $lock['uid'] != $userid )
	  throw new Exception('Cannot unlock something owned by somebody else');

	$db = cmsms()->GetDb();
	$query = 'DELETE FROM '.cms_db_prefix().'locks WHERE section = ? AND record = ? AND uid = ? AND signature = ?';
	$dbr = $db->Execute($query,array($module,$record,$userid,$signature));
	if( !$dbr )
	  {
	    throw new Exception('Cannot Remove Lock: '.$db->sql.' -- '.$db->ErrorMsg());
	  }
      }
    return TRUE;
  }


  static public function touch($module,$record,$userid = '',$signature = '')
  {
    $db = cmsms()->GetDb();
    if( !$module ) throw new Exception('Cannot touch lock with no module specified');
    if( !$userid ) $userid = get_userid();
    if( !$signature ) $signature = md5(serialize($_SERVER).serialize($_GET).session_id());

    $lock = self::get_lock($module,$record);
    if( is_object($lock) )
      {
	if( $lock['uid'] != $userid ) return FALSE;
      }

    $query = 'UPDATE '.cms_db_prefix().'locks SET modified = NOW() 
              WHERE section = ? AND record = ? AND uid = ? AND signature = ?';
    $tmp = $db->Execute($query,array($lock['module'],$lock['record'],$lock['uid'],$lock['signature']));

    if( !$tmp ) return FALSE;
    return TRUE;
  }


  static public function delete($module,$record,$userid)
  {
    if( !$module || !$record || !$userid )
      {
	throw new Exception('Cannot delete a lock without valid information');
      }

    $lock = self::get_lock($module,$record);
    if( !is_object($lock) )
      {
	throw new Exception('Could not find the proper lock to steal');
      }
    if( $lock['uid'] != $userid )
      {
	throw new Exception('Could not find the proper lock to steal');
      }

    $db = cmsms()->GetDb();
    $query = 'DELETE FROM '.cms_db_prefix().'locks WHERE section = ? AND record = ? AND uid = ?';
    $dbr = $db->Execute($query,array($module,$record,$userid));
    audit('',lang('locking'),lang('lock_deleted',get_userid(false),$module,$record,$userid));
  }
}

#
# EOF
#
?>