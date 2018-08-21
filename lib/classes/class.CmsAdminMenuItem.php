<?php
# CMS - CMS Made Simple
# (c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
# Visit our homepage at: http://cmsmadesimple.org
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
 * Classes and utilities to provide menu items in the CMSMS admin navigation
 * @package CMS
 * @license     GPL
 */

/**
 * Base module class.
 *
 * All modules should inherit and extend this class with their functionality.
 *
 * @package		CMS
 * @since		2.0
 * @license     GPL
 * @author      Robert Campbell <calguy1000@cmsmadesimple.org>
 * @see         CMSModule::GetAdminSection() FOO
 * @property string $module The module that hosts the destination action
 * @property string $section The admin section (from CMSModule::GetAdminSection)
 * @property string $title The title of the menu item
 * @property string $action The module action
 * @property string $url The actual URL for the menu item link.  If this is not specified, it is created from the module and action.
 * @property string $icon The URL to the icon to associate with this action
 * @property int $priority Priority for the menu item (minimum of 2)
 */
final class CmsAdminMenuItem
{
    /**
     * @ignore
     * 'system' is for internal use only.
     */
    private static $_keys = array('module','section','title','description','action','url','icon','priority','system');

    /**
     * @ignore
     */
    private $_data = array();


    /**
     * @ignore
     */
    public function __get($k)
    {
        if( !in_array($k,self::$_keys) ) throw new CmsException('Invalid key: '.$k.' for '.__CLASS__.' object');
        switch( $k ) {
        case 'url':
            if( isset($this->_data[$k]) && $this->_data[$k] ) return $this->_data[$k];
            // url can be dynamically generated... maybe
            if( $this->module && $this->action ) {
                $mod = \cms_utils::get_module($this->module);
                if( $mod ) {
                    $url = $mod->create_url('m1_',$this->action);
                    return $url;
                }
            }
            break;

        default:
            if( isset($this->_data[$k]) ) return $this->_data[$k];
        }
    }


    /**
     * @ignore
     */
    public function __set($k,$v)
    {
        if( !in_array($k,self::$_keys) ) throw new CmsException('Invalid key: '.$k.' for '.__CLASS__.' object');
        $this->_data[$k] = $v;
    }

    /**
     * @ignore
     */
    public function __isset($k)
    {
        if( !in_array($k,self::$_keys) ) throw new CmsException('Invalid key: '.$k.' for '.__CLASS__.' object');
        return isset($this->_data[$k]);
    }


    /**
     * @ignore
     */
    public function __unset($k)
    {
        if( !in_array($k,self::$_keys) ) throw new CmsException('Invalid key: '.$k.' for '.__CLASS__.' object');
        throw new CmsException('Cannot unset data from a CmsAdminMenuItem object');
    }

    /**
     * Test if the object is valid
     */
    public function valid()
    {
        foreach( self::$_keys as $ok ) {
            if( $ok == 'icon' || $ok == 'system' || $ok == 'priority' || $ok == 'description' || $ok == 'url' ) continue;  // we don't care if this is set.
            if( !isset($this->_data[$ok]) ) return FALSE;
        }
        return TRUE;
    }

    /**
     * A convenience method to build a standard admin menu item from module methods.
     *
     * @param CMSModule $mod
     */
    public static function &from_module(\CMSModule $mod)
    {
        $obj = null;
        if( $mod->HasAdmin() ) {
            $obj = new CmsAdminMenuItem;
            $obj->module = $mod->GetName();
            $obj->section = $mod->GetAdminSection();
            $obj->title   = $mod->GetFriendlyName();
            $obj->description = $mod->GetAdminDescription();
            $obj->priority = 50;
            $obj->action = 'defaultadmin';
            //$obj->url = $mod->create_url('m1_',$obj->action);
        }
        return $obj;
    }
} // end of class

#
# EOF
#
