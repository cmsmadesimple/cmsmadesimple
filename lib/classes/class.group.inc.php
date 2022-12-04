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
#$Id$

/**
 * Classes and functions related to CMSMS admin groups.
 * @package CMS
 * @license GPL
 */

/**
 * Generic group class. This can be used for any logged in group or group related function.
 *
 * @property-read int $id The group id
 * @property string $name The group name
 * @property string $description The group description
 * @property bool $active Indicates active status of this group.
 * @since 0.9
 * @package CMS
 * @license GPL
 */
class Group
{
    /**
     * @ignore
     */
    private $_data = array('id'=>-1,'name'=>null,'description'=>null,'active'=>false);

    /**
     * @ignore
     */
    public function __get($key)
    {
        if( !array_key_exists($key,$this->_data) ) throw new \LogicException($key.' is not a member of '.__CLASS__);
        return $this->_data[$key];
    }

    /**
     * @ignore
     */
    public function __set($key,$val)
    {
        switch( $key ) {
        case 'id':
            throw new \LogicException($key.' is not a settable member of '.__CLASS__);
            break;

        case 'name':
        case 'description':
            $this->_data[$key] = trim($val);
            break;

        case 'active':
            $this->_data[$key] = cms_to_bool($val);
            break;

        default:
            throw new \LogicException($key.' is not a member of '.__CLASS__);
        }
    }

    /**
     * Validate this object.
     *
     * @throws \LogicException
     * @throws \CmsInvalidDataException
     */
    public function validate()
    {
        if( !$this->name ) throw new \LogicException('No name specified for this group');
        $db = CmsApp::get_instance()->GetDb();
        $sql = 'SELECT group_id FROM `'.CMS_DB_PREFIX.'groups` WHERE group_name = ? AND group_id != ?';
        $tmp = $db->GetOne($sql,array($this->name,$this->id));
        if( $tmp ) throw new \CmsInvalidDataException(lang('errorgroupexists'));
    }

    /**
     * @ignore
     */
    protected function update()
    {
        $db = CmsApp::get_instance()->GetDb();
        $sql = 'UPDATE `'.CMS_DB_PREFIX.'groups` SET group_name = ?, group_desc = ?, active = ?, modified_date = NOW() WHERE group_id = ?';
        $dbresult = $db->Execute($sql,array($this->name,$this->description,$this->active,$this->id));
        if( $dbresult !== false ) return TRUE;
        return FALSE;
    }

    /**
     * @ignore
     */
    protected function insert()
    {
        $db = CmsApp::get_instance()->GetDb();
        $this->_data['id'] = $db->GenID(CMS_DB_PREFIX."groups_seq");
        $time = $db->DBTimeStamp(time());
        $query = "INSERT INTO `".CMS_DB_PREFIX."groups` (group_id, group_name, group_desc, active, create_date, modified_date)
                  VALUES (?,?,?,?,".$time.", ".$time.")";
        $dbresult = $db->Execute($query, array($this->id, $this->name, $this->description, $this->active));
        return $dbresult;
    }

    /**
     * Persists the group to the database.
     *
     * @return bool true if the save was successful, false if not.
     */
    function Save()
    {
        $this->validate();
        if( $this->id > 0 ) {
            return $this->update();
        }
        else {
            return $this->insert();
        }
    }

    /**
     * Deletes the group from the database
     *
     * @throws LogicException
     * @return bool True if the delete was successful, false if not.
     */
    function Delete()
    {
        if( $this->id < 1 ) return FALSE;
        if( $this->id == 1 ) throw new \LogicException(lang('error_deletespecialgroup'));
        $db = CmsApp::get_instance()->GetDb();
        $query = 'DELETE FROM '.CMS_DB_PREFIX.'user_groups where group_id = ?';
        $dbresult = $db->Execute($query, array($this->id));
        $query = "DELETE FROM ".CMS_DB_PREFIX."group_perms where group_id = ?";
        $dbresult = $db->Execute($query, array($this->id));
        $query = "DELETE FROM `".CMS_DB_PREFIX."groups` where group_id = ?";
        $dbresult = $db->Execute($query, array($this->id));
        $this->_data['id'] = -1;
        return TRUE;
    }

    /**
     * Load a Group given it's id.
     *
     * @param int $id
     * @return Group
     * @throws CmsInvalidDataException
     */
    public static function &load($id)
    {
        $id = (int) $id;
        if( $id < 1 ) throw new \CmsInvalidDataException(lang('missingparams'));

        $db = CmsApp::get_instance()->GetDb();
        $query = "SELECT group_id, group_name, group_desc, active FROM `".CMS_DB_PREFIX."groups` WHERE group_id = ? ORDER BY group_id";
        $row = $db->GetRow($query, array($id));

        $obj = new self();
        $obj->_data['id'] = $row['group_id'];
        $obj->name = $row['group_name'];
        $obj->description = $row['group_desc'];
        $obj->active = $row['active'];
        return $obj;
    }

    /**
     * Load all groups
     *
     * @return array Array of group records.
     */
    public static function load_all()
    {
        $db = CmsApp::get_instance()->GetDb();
        $result = array();
        $query = "SELECT group_id, group_name, group_desc, active FROM `".CMS_DB_PREFIX."groups` ORDER BY group_id";
        $list = $db->GetArray($query);
        $out = array();
        for( $i = 0, $n = count($list); $i < $n; $i++ ) {
            $row = $list[$i];
            $obj = new self();
            $obj->_data['id'] = (int) $row['group_id'];
            $obj->name = $row['group_name'];
            $obj->description = $row['group_desc'];
            $obj->active = $row['active'];
            $out[] = $obj;
        }
        if( count($out) ) return $out;
    }

    /**
     * Check if the group has the specified permission.
     *
     * @since 1.11
     * @author Robert Campbell
     * @internal
     * @access private
     * @ignore
     * @param mixed $perm Either the permission id, or permission name to test.
     * @return bool True if the group has the specified permission, false otherwise.
     */
    public function HasPermission($perm)
    {
        if( $this->id <= 0 ) return FALSE;
        $groupops = GroupOperations::get_instance();
        return $groupops->CheckPermission($this->id,$perm);
    }

    /**
     * Ensure this group has the specified permission.
     *
     * @since 1.11
     * @author Robert Campbell
     * @internal
     * @access private
     * @ignore
     * @param mixed $perm Either the permission id, or permission name to test.
     */
    public function GrantPermission($perm)
    {
        if( $this->id < 1 ) return;
        if( $this->HasPermission($perm) ) return;
        $groupops = GroupOperations::get_instance();
        return $groupops->GrantPermission($this->id,$perm);
    }

    /**
     * Ensure this group does not have the specified permission.
     *
     * @since 1.11
     * @author Robert Campbell
     * @internal
     * @access private
     * @ignore
     * @param mixed $perm Either the permission id, or permission name to test.
     */
    public function RemovePermission($perm)
    {
        if( $this->id <= 0 ) return;
        if( !$this->HasPermission($perm) ) return;
        $groupops = GroupOperations::get_instance();
        return $groupops->RemovPermission($this->id,$perm);
    }

}

?>
