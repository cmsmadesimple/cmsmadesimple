<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
#
#$Id: class.user.inc.php 8109 2012-06-24 20:14:56Z calguy1000 $

/**
 * Class and utilities for working with permissions.
 * @package CMS
 * @license GPL
 */

/**
 * Simple class for dealing with a permission.
 *
 * @since 2.0
 * @package CMS
 * @license GPL
 * @author Robert Campbell <calguy1000@gmail.com>
 */
final class CmsPermission
{
	/**
	 * @ignore
	 */
	private static $_keys = array('id','source','name','text','create_date','modified_date');

	/**
	 * @ignore
	 */
	private $_data = array();

	/**
	 * @ignore
	 */
	private static $_perm_map;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_data['source'] = '';
		$this->_data['name'] = '';
		$this->_data['text'] = '';
		$this->_data['create_date'] = '';
		$this->_data['modified_date'] = '';
	}

	/**
	 * @ignore
	 */
	public function __get($key)
	{
		if( !in_array($key,self::$_keys) ) throw new CmsInvalidDataException($key.' is not a valid key for a CmsPermission Object');
		if( isset($this->_data[$key]) ) return $this->_data[$key];
	}

	/**
	 * @ignore
	 */
	public function __set($key,$value)
	{
		if( !in_array($key,self::$_keys) ) throw new CmsInvalidDataException($key.' is not a valid key for a CmsPermission Object');
		if( $key == 'id' ) throw new CmsInvalidDataException($key.' cannot be set this way in a CmsPermission Object');

		$this->_data[$key] = $value;
	}

	/**
	 * Insert a new permission
	 *
	 * @throws CmsSQLErrorException
	 */
	protected function _insert()
	{
		$this->validate();

		$db = CmsApp::get_instance()->GetDb();
		$new_id = $db->GenID(CMS_DB_PREFIX.'permissions_seq');
		if( !$new_id ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

		$now = $db->DbTimeStamp(time());
		$query = 'INSERT INTO '.CMS_DB_PREFIX."permissions
              (permission_id,permission_name,permission_text,permission_source,create_date,
               modified_date) VALUES (?,?,?,?,$now,$now)";
		$dbr = $db->Execute($query,
							array($new_id, $this->_data['name'], $this->_data['text'], $this->_data['source']));
		if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
		$this->_data['id'] = $new_id;
	}

	/**
	 * Validate the exception
	 *
	 * @throws CmsInvalidDataException
	 * @throws CmsLogicException
	 */
	public function validate()
	{
		if( $this->_data['source'] == '' )
			throw new CmsInvalidDataException('Source cannot be empty in a CmsPermission object');
		if( $this->_data['name'] == '' )
			throw new CmsInvalidDataException('Name cannot be empty in a CmsPermission object');
		if( $this->_data['text'] == '' )
			throw new CmsInvalidDataException('Text cannot be empty in a CmsPermission object');

		if( !isset($this->_data['id']) || $this->_data['id'] < 1 ) {
			// Name must be unique
			$db = CmsApp::get_instance()->GetDb();
			$query = 'SElECT permission_id FROM '.CMS_DB_PREFIX.'permissions
                WHERE permission_name = ?';
			$dbr = $db->GetOne($query,array($this->_data['name']));
			if( $dbr > 0 ) throw new CmsInvalidDataException('Permission with name '.$this->_data['name'].' already exists');
		}
	}

	/**
	 * Save the permission to the database
	 *
	 * @throws CmsLogicException
	 */
	public function save()
	{
		if( !isset($this->_data['id']) || $this->_data['id'] < 1 ) return $this->_insert();
		throw new CmsLogicException('Cannot update an existing CmsPermission object');
	}

	/**
	 * Delete this permission
	 *
	 * @throws CmsLogicExceptin
	 */
	public function delete()
	{
		if( !isset($this->_data['id']) || $this->_data['id'] < 1 ) {
			throw new CmsLogicException('Cannnot delete a CmsPermission object that has not been saved');
		}

		$db = CmsApp::get_instance()->GetDb();
		$query = 'DELETE FROM '.CMS_DB_PREFIX.'group_perms WHERE permission_id = ?';
		$dbr = $db->Execute($query,array($this->_data['id']));
		if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

		$query = 'DELETE FROM '.CMS_DB_PREFIX.'permissions WHERE permission_id = ?';
		$dbr = $db->Execute($query,array($this->_data['id']));
		if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
		unset($this->_data['id']);
	}

	/**
	 * Load a permission with the specified name
	 *
	 * @param string $name
	 * @return CmsPermission
	 */
	public static function &load($name)
	{
		if( is_array(self::$_perm_map) ) {
			if( (int)$name <= 0 ) {
				foreach( self::$_perm_map as $perm_id => $perm ) {
					if( $perm->name == $name ) return $perm;
				}
			}
		}

		$db = CmsApp::get_instance()->GetDb();
		$row = null;
		if( (int)$name > 0 ) {
			$query = 'SELECT * FROM '.CMS_DB_PREFIX.'permissions WHERE permission_id = ?';
			$row = $db->GetRow($query,array((int)$name));
		}
		else {
			$query = 'SELECT * FROM '.CMS_DB_PREFIX.'permissions WHERE permission_name = ?';
			$row = $db->GetRow($query,array($name));
		}
		if( !is_array($row) || count($row) == 0 ) {
			throw new CmsInvalidDataException('Could not find permission named '.$name);
		}

		$obj = new CmsPermission();
		$obj->_data['id'] = $row['permission_id'];
		$obj->_data['name'] = $row['permission_name'];
		$obj->_data['text'] = $row['permission_text'];
		$obj->_data['create_date'] = $row['create_date'];
		$obj->_data['modified_date'] = $row['modified_date'];

		if( !is_array(self::$_perm_map) ) self::$_perm_map = array();
		self::$_perm_map[$obj->id] = $obj;
		return $obj;
	}

	/**
	 * Given a permission name, get it's id
	 *
	 * @param string $permname
	 * @return int
	 */
	public static function get_perm_id($permname)
	{
		try {
			$perm = self::load($permname);
			return $perm->id;
		}
		catch( CmsException $e ) {
		}
	}
} // end of class

#
# EOF
#
?>
