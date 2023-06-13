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
 * This file contains the base definition for an admin alert.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 */
namespace CMSMS\AdminAlerts;

/**
 * An abstract class that defines Admin Alerts for CMSMS.
 *
 * Admin alerts have a name, priority, title, message, a timestamp and can optionally refer to a module.
 * Methods are used to test if a module is for a specific user, and to format the message.
 *
 * Alerts are stored as preferences in the database.  If the name is the name of the class or something else that is not data or time dependant the
 * only one instance of that alert can be stored in the database.
 * This class usses the ArrayAccess interface to behave like a PHP array.
 *
 * @since 2.2
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @prop string $name The alert name.  This is set by default on construction, but can be overridden.  It is used to control how the alert is saved.
 * @prop string $module An optional module name.  If specified, the module will be loaded when the alert is read from the database.
 * @prop string $priority The alert priority
 * @prop-read int $created The timestamp that the alert was created.
 * @prop-read bool $loaded Whether or not this object was loaded from the database.  Alerts cannot be modified if they were loaded from the database.
 */
abstract class Alert
{
    /**
     * High priority
     */
    const PRIORITY_HIGH = '_high';

    /**
     * Normal priority
     */
    const PRIORITY_NORMAL = '_normal';

    /**
     * Low/Simple priority
     */
    const PRIORITY_LOW = '_low';

    /**
     * @ignore
     * @var string
     */
    private $_name;

    /**
     * @ignore
     * @var string
     */
    private $_module;

    /**
     * @ignore
     * @var int
     */
    private $_created;

    /**
     * @ignore
     * @var string
     */
    private $_priority;

    /**
     * @ignore
     * @var bool
     */
    private $_loaded;

    /**
     * Constructor.
     *
     * Initialize the name to a unique name, the priority to normal, and the creaed time.
     */
    public function __construct()
    {
        $this->_name = md5(get_class($this).microtime().rand(0,9999));
        $this->_priority = self::PRIORITY_NORMAL;
        $this->_created = time();
    }

    /**
     * PHP's magic __get method.
     *
     * Programmers can get the name, module, priority and title.
     * If an unknown key is provided an exception thrown.
     *
     * @throws InvalidArgumentException
     * @param string $key
     * @return string;
     */
    public function __get($key)
    {
        switch( $key ) {
        case 'name':
            return trim($this->_name);
        case 'module':
            return trim((string)$this->_module);
        case 'priority':
            return trim($this->_priority);
        case 'created':
            return (int) $this->_created;
        case 'loaded':
            return (bool) $this->_loaded;
        default:
            throw new \InvalidArgumentException("$key is not a gettable member of ".get_class($this));
        }
    }

    /**
     * PHP's magic __set method.
     *
     * Progrramers can modify the name, module, priority, and title of the alert.
     * Alerts can only be modified before the object is stored in the database.  Not afterwards.
     * If an unknown key, or invalid priority is provided then an exception is thrown.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @param string $key
     * @param string $val
     */
    public function __set($key,$val)
    {
        if( $this->_loaded ) throw new \LogicException('Alerts cannot be altered once saved');
        switch( $key ) {
        case 'name':
            $this->_name = trim($val);
            break;

        case 'module':
            $this->_module = trim($val);
            break;

        case 'priority':
            switch( $val ) {
            case self::PRIORITY_HIGH:
            case self::PRIORITY_NORMAL:
            case self::PRIORITY_LOW:
                $this->_priority = $val;
                break;
            default:
                throw new \InvalidArgumentException("$val is an invalid value for the priority of an alert");
            }
            break;

        default:
            throw new \InvalidArgumentException("$key is not a settable member of ".get_class($this));
        }
    }

    /**
     * Test if this alert is suitable for a specified admin uid
     *
     * @abstract
     * @param int $admin_uid
     * @return bool
     */
    abstract protected function is_for($admin_uid);

    /**
     * Return the title for this alert
     *
     * @abstract
     * return string
     */
    abstract public function get_title();

    /**
     * Return the message for this alert.
     *
     * @abstract
     * @return string
     */
    abstract public function get_message();

    /**
     * Return the URL for an iconf or this alert.
     *
     * @abstract
     * @return string
     */
    abstract public function get_icon();

    /**
     * Get the name of the preference that this alert will be stored as.
     *
     * @param string $name optionaly provide a name for the alert.  If not specified the current alert name will be used.
     * @return string
     */
    public function get_prefname($name = null)
    {
        if( !$name ) $name = $this->name;
        return self::get_fixed_prefname( $name );
    }

    protected static function get_fixed_prefname( $name )
    {
        return 'adminalert_'.md5($name);
    }

    /**
     * Decode a serialized object read from the database.
     *
     * @param string $serialized A serialized array, containing an optional module name that must be loaded and the serialized alert object.
     * @return Alert
     */
    protected static function decode_object($serialized)
    {
        $tmp = unserialize($serialized);
        if( !is_array($tmp) || !isset($tmp['data']) ) return;

        $obj = null;
        if( !empty($tmp['module']) && strtolower($tmp['module']) != 'core' ) {
            $mod = \cms_utils::get_module($tmp['module']); // hopefully module is valid.
            if( $mod ) $obj = unserialize($tmp['data']);
        } else {
            $obj = unserialize($tmp['data']);
        }
	if( !is_object($obj) || !$obj instanceof self ) return;
        return $obj;
    }

    /**
     * Encode an alert into a format suitable for storing
     *
     * @param Alert $obj The object to be ecoded.
     * @return string A serialized array, containing an optional module name that must be loaded, and the serrialized alert object.
     */
    protected static function encode_object(Alert $obj)
    {
        $tmp = array('module'=>$obj->module,'data'=>serialize($obj));
        return serialize($tmp);
    }

    /**
     * Given an alert preference name, load it from the database.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @param string $name The preference name
     * @return Alert
     */
    public static function load_by_name($name, $throw = true )
    {
        $name = trim($name);
        if( !$name ) throw new \InvalidArgumentException('Invalid alert name passed to '.__METHOD__);
        if( !startswith( $name, 'adminalert_') ) $name = self::get_fixed_prefname( $name );
        $tmp = \cms_siteprefs::get( $name );
        if( !$tmp && $throw ) throw new \LogicException('Could not find an alert with the name '.$name);
        if( !$tmp ) return;

        $obj = self::decode_object($tmp);
        if( !is_object($obj) ) throw new \LogicException('Problem loading alert named '.$name);
        return $obj;
    }

    /**
     * Load all known alerts from the database.
     *
     * return Alert[]
     */
    public static function load_all()
    {
        $list = \cms_siteprefs::list_by_prefix('adminalert_');
        if( !$list || !count($list) ) return [];

        $out = [];
        foreach( $list as $prefname ) {
            $tmp = self::decode_object(\cms_siteprefs::get($prefname));
            if( !is_object($tmp) ) continue;
            $tmp->_loaded = 1;

            $out[] = $tmp;
        }
        if( count($out) ) return $out;
    }

    /**
     * Load all alerts that are suitable for the specified user id.
     *
     * @param int|null $uid The admin userid to test for.  If no uid is specified, the currently logged in admin user id is used.
     * @return Alert[]
     */
    public static function load_my_alerts($uid = null)
    {
        $uid = (int) $uid;
        if( $uid < 1 ) $uid = get_userid(FALSE);
        if( !$uid ) return;

        $alerts = self::load_all();
        if( !$alerts || !count($alerts) ) return;

        $out = [];
        foreach( $alerts as $alert ) {
            if( $alert->is_for($uid) ) {
                $out[] = $alert;
            }
        }
        if( !count($out) ) return;

        // now sort these fuggers by priority
        $map = [ Alert::PRIORITY_HIGH => 0, Alert::PRIORITY_NORMAL => 1, Alert::PRIORITY_LOW => 2 ];
        usort($out,function($a,$b) use ($map) {
                $pa = $map[$a->priority];
                $pb = $map[$b->priority];
                if( $pa < $pb ) return -1;
                if( $pa > $pb ) return 1;
                return strcasecmp($a->module,$b->module);
            });
        if( count($out) ) return $out;
    }

    /**
     * Save an alert object to the database.
     *
     * @throws LogicException
     */
    public function save()
    {
        if( !$this->name ) throw new \LogicException('A '.__CLASS__.' object must have a name');

        // can only save if preference does not already exist
        //$tmp = \cms_siteprefs::get($this->get_prefname());
        //if( $tmp ) throw new \LogicException('Cannot save a class that has already been saved '.$this->get_prefname());
        \cms_siteprefs::set($this->get_prefname(),self::encode_object($this));
    }

    /**
     * Delete this alert from the database.
     *
     */
    public function delete()
    {
        \cms_siteprefs::remove($this->get_prefname());
        $this->_loaded = false;
    }
}
