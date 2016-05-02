<?php
namespace CMSMS\AdminAlerts;

abstract class Alert
{
    const PRIORITY_HIGH = '_high';
    const PRIORITY_NORMAL = '_normal';
    const PRIORITY_LOW = '_low';

    private $_name;
    private $_module;
    private $_created;
    private $_priority;
    private $_title;
    private $_loaded;

    public function __construct()
    {
        $this->_name = md5(get_class($this).microtime().rand(0,9999));
        $this->_priority = self::PRIORITY_NORMAL;
        $this->_created = time();
    }

    public function __get($key)
    {
        switch( $key ) {
        case 'name':
            return trim($this->_name);
        case 'module':
            return trim($this->_module);
        case 'priority':
            return trim($this->_priority);
        case 'title':
            return trim($this->_title);
        default:
            throw new \LogicException("$key is not a gettable member of ".get_class($this));
        }
    }

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
                throw new \LogicException("$val is an invalid value for the priority of an alert");
            }
            break;

        case 'title':
            $this->_title = trim(strip_tags($val));
            break;

        default:
            throw new \LogicException("$key is not a settable member of ".get_class($this));
        }
    }

    abstract protected function is_for($admin_uid);

    abstract public function get_message();

    abstract public function get_icon();

    protected function get_prefname()
    {
        return 'adminalert_'.$this->name;
    }

    protected static function &decode_object($serialized)
    {
        $tmp = unserialize($serialized);
        if( !is_array($tmp) || !isset($tmp['data']) ) return;

        $obj = null;
        if( !empty($tmp['module']) ) {
            $mod = \cms_utils::get_module($tmp['module']); // hopefully module is valid.
            if( $mod ) $obj = unserialize($tmp['data']);
        } else {
            $obj = unserialize($tmp['data']);
        }
        return $obj;
    }

    protected static function encode_object($obj)
    {
        $tmp = array('module'=>$obj->module,'data'=>serialize($obj));
        return serialize($tmp);
    }

    public static function &load_by_name($name)
    {
        $name = trim($name);
        if( !$name ) throw new \LogicException('Invalid alert name passed to '.__METHOD__);
        $prefname = 'adminalert_'.$name;
        $tmp = \cms_siteprefs::get($prefname);
        if( !$tmp ) throw new \LogicException('Could not find an alert with the name '.$name);

        $obj = self::decode_object($tmp);
        if( !is_object($obj) ) throw new \LogicException('Problem loading alert named '.$name);
        return $obj;
    }

    public static function load_all()
    {
        $list = \cms_siteprefs::list_by_prefix('adminalert_');
        if( !count($list) ) return;

        $out = [];
        foreach( $list as $prefname ) {
            $tmp = self::decode_object(\cms_siteprefs::get($prefname));
            if( !is_object($tmp) ) continue;
            $tmp->_loaded = 1;

            $out[] = $tmp;
        }
        if( count($out) ) return $out;
    }

    public static function load_my_alerts($uid = null)
    {
        $uid = (int) $uid;
        if( $uid < 1 ) $uid = get_userid(FALSE);
        if( !$uid ) return;

        $alerts = self::load_all();
        if( !count($alerts) ) return;

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

    public function save()
    {
        if( !$this->name ) throw new \LogicException('A '.__CLASS__.' object must have a name');
        if( !$this->title ) throw new \LogicException('A '.__CLASS__.' object must have a title');
        $tmp = \cms_siteprefs::get($this->get_prefname());
        // can only save if preference does not already exist
        if( $tmp ) throw new \LogicException('Cannot save a class that has already been saved '.$this->get_prefname());
        \cms_siteprefs::set($this->get_prefname(),self::encode_object($this));
    }

    public function delete()
    {
        \cms_siteprefs::remove($this->get_prefname());
    }

}