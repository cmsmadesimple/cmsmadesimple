<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Class: CmsLockOperations (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A class for managing locks on various objects.
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
 * Classes and utilities for managing locks.
 * @package CMS
 * @license GPL
 */

/**
 * A singleton class providing utilities for interacting with locks.
 *
 * @package CMS
 * @licsense GPL
 * @since 2.0
 */
final class CmsLockOperations
{
  /**
   * @ignore
   */
  private function __construct($type,$id) {}

  /**
   * Touch any lock of the specified type, and id that matches the currently logged in UID
   *
   * @param int $lock_id The lock identifier
   * @param string $type The type of object being locked
   * @param int $oid The object identifier
   * @return int The expiry timestamp of the lock.
   */
  public static function touch($lock_id,$type,$oid)
  {
    $uid = get_userid(FALSE);
    $lock = CmsLock::load_by_id($lock_id,$type,$oid,$uid);
    $lock->save();
    return $lock['expires'];
  }

  /**
   * Delete any lock of the specified type, and id that matches the currently logged in UID
   *
   * @param int $lock_id The lock identifier
   * @param string $type The type of object being locked
   * @param int $oid The object identifier
   */
  public static function delete($lock_id,$type,$oid)
  {
    self::unlock($lock_id,$type,$oid);
  }

  /**
   * Delete any lock of the specified type, and id that matches the currently logged in UID
   *
   * @param int $lock_id The lock identifier
   * @param string $type The type of object being locked
   * @param int $oid The object identifier
   */
  public static function unlock($lock_id,$type,$oid)
  {
      if( $lock_id ) {
          $uid = get_userid(FALSE);
          $lock = CmsLock::load_by_id($lock_id,$type,$oid);
          $lock->delete();
      }
  }

  /**
   * test for any lock of the specified type, and id
   *
   * @param string $type The type of object being locked
   * @param int $oid The object identifier
   * @return bool
   */
  public static function is_locked($type,$oid)
  {
      try {
          $lock = CmsLock::load($type,$oid);
          sleep(1); // wait for potential asynhronous requests to complete.
          $lock = CmsLock::load($type,$oid);
          return $lock['id'];
      }
      catch( CmsNoLockException $e ) {
          return FALSE;
      }
  }

  /**
   * Delete any locks that have expired.
   *
   * @param int $expires Delete locks older than this date (if not specified current time will be used).
   * @param string $type The type of locks to delete.  If not specified any locks can be deleted.
   */
  private static function delete_expired($expires = null,$type = null)
  {
    if( $expires == null ) $expires == time();
    $db = CmsApp::get_instance()->GetDb();
    $query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLock::LOCK_TABLE.' WHERE expires < ?';
    $parms = array($expires);
    if( $type ) {
      $query .= ' AND type = ?';
      $parms[] = $type;
    }
    $dbr = $db->Execute($query,$parms);
  }

  /**
   * Get all locks of a specific type
   *
   * @param string $type The lock type
   */
  public static function get_locks($type)
  {
    $db = CmsApp::get_instance()->GetDb();
    $query = 'SELECT * FROM '.CMS_DB_PREFIX.CmsLock::LOCK_TABLE.' WHERE type = ?';
    $tmp = $db->GetArray($query,array($type));
    if( !is_array($tmp) || count($tmp) == 0 ) return;

    $locks = array();
    foreach( $tmp as $row ) {
      $obj = CmsLock::from_row($row);
      $locks[] = $obj;
    }
    return $locks;
  }

  /**
   * Delete all the locks for the current user
   *
   * @param string $type An optional type name.
   */
  public static function delete_for_user($type = null)
  {
    $uid = get_userid(FALSE);
    $db = CmsApp::get_instance()->GetDb();
    $parms = array($uid);
    $query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLock::LOCK_TABLE.' WHERE uid = ?';
    if( $type ) {
        $query .= ' AND type = ?';
        $parms[] = trim($type);
    }
    $db->Execute($query,$parms);
  }

} // end of class

#
# EOF
#
?>
