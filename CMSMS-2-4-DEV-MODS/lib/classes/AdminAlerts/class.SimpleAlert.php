<?php
#CMS - CMS Made Simple
#(c)2004-2013 by Ted Kulp (ted@cmsmadesimple.org)
#(c)2016 by the CMSMS Dev Team
#Visit our homepage at: http://www.cmsmadesimple.org
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
 * This file contains the definition for a simple alert class that uses pre-defined values.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 */

namespace CMSMS\AdminAlerts;

/**
 * The SimpleAlert class is a type of alert that allows the developer to create alerts with pre-defined titles, messages, icons, and permissions.
 *
 * @since 2.2
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @prop string[] $perms An array of permission names.  The logged in user must have at least one of these permissions to see the alert.
 * @prop string $icon The complete URL to an icon to associate with this alert
 * @prop string $msg The message to display.  Note: Since alerts are stored in the database, and can be created asynchronously you cannot rely on language strings for the message or title when using this class.
 */
class SimpleAlert extends Alert
{
    /**
     * @ignore
     */
    private $_perms = [];

    /**
     * @ignore
     */
    private $_icon;

    /**
     * @ignore
     */
    private $_title;

    /**
     * @ignore
     */
    private $_msg;

    /**
     * Constructor
     *
     * @param string[] $perms An array of permission names.  Or null.
     */
    public function __construct($perms = null)
    {
        if( $perms && (!is_array($perms) || !count($perms)) ) throw new \InvalidArgumentException('perms must be an array of permission name strings');
        $this->_perms = $perms;
        parent::__construct();
    }

    /**
     * The magic __get method.
     *
     * Get a property from this object, or from the base class.
     *
     * @throws InvalidArgumentException
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        switch( $key ) {
        case 'perms':
            return $this->_perms;
        case 'icon':
            return $this->_icon;
        case 'title':
            return $this->_title;
        case 'msg':
            return $this->_msg;
        default:
            return parent::__get($key);
        }
    }

    /**
     * The magic __set method.
     *
     * Set a property for this object, or for the base Alert class.
     *
     * @param string $key
     * @param mixed $val
     */
    public function __set($key,$val)
    {
        switch( $key ) {
        case 'icon':
            $this->_icon = trim($val);
            break;
        case 'title':
            $this->_title = trim($val);
            break;
        case 'msg':
            $this->_msg = trim($val);
            break;
        case 'perms':
            if( !is_array($val) || !count($val) ) throw new \InvalidArgumentException('perms must be an array of permission name strings');
            $tmp = [];
            foreach( $val as $one ) {
                $one = trim($one);
                if( !$one ) continue;
                if( !in_array($one,$tmp) ) $tmp[] = $one;
            }
            if( !count($tmp) ) throw new \InvalidArgumentException('perms must be an array of permission name strings');
            $this->_perms = $tmp;
            break;

        default:
            return parent::__set($key,$val);
        }
    }

    /**
     * Givent he admin_uid, check if the specified uid has at least one of the permissions specified in the perms array.
     *
     * @param int $admin_uid
     * @return bool;
     */
    protected function is_for($admin_uid)
    {
        $admin_uid = (int) $admin_uid;
        if( !count($this->_perms) ) return FALSE;
        $userops = \UserOperations::get_instance();
        $perms = $this->_perms;
        if( !is_array($this->_perms) ) $perms = array($this->_perms);
        foreach( $perms as $permname ) {
            if( $userops->CheckPermission($admin_uid,$permname) ) return TRUE;
        }
        return FALSE;
    }

    /**
     * Return the alert title.
     *
     * @return string
     */
    public function get_title()
    {
        return $this->_title;
    }

    /**
     * Return the alert message
     *
     * @return string
     */
    public function get_message()
    {
        return $this->_msg;
    }

    /**
     * Return the alert icon URL (if any)
     *
     * @return string
     */
    public function get_icon()
    {
        return $this->_icon;
    }

} // end of class