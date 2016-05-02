<?php
namespace CMSMS\AdminAlerts;

class SimpleAlert extends Alert
{
    private $_perms = [];
    private $_icon;
    private $_msg;

    public function __construct($perms = null)
    {
        $this->_perms = $perms;
        parent::__construct();
    }

    public function __get($key)
    {
        switch( $key ) {
        case 'perms':
            return $this->_perms;
        case 'icon':
            return $this->_icon;
        case 'msg':
            return $this->_msg;
        default:
            return parent::__get($key);
        }
    }

    public function __set($key,$val)
    {
        switch( $key ) {
        case 'icon':
            $this->_icon = trim($val);
            break;
        case 'msg':
            $this->_msg = trim($val);
            break;
        case 'perms':
            if( !is_array($val) || !count($val) ) throw new \Excecption('perms must be an array of permission name strings');
            $tmp = [];
            foreach( $val as $one ) {
                $one = trim($one);
                if( !$one ) continue;
                if( !in_array($one,$tmp) ) $tmp[] = $one;
            }
            if( !count($tmp) ) throw new \Excecption('perms must be an array of permission name strings');
            $this->_perms = $tmp;
            break;

        default:
            return parent::__set($key,$val);
        }
    }

    protected function is_for($admin_uid)
    {
        $admin_uid = (int) $admin_uid;
        if( !count($this->_perms) ) return FALSE;
        $userops = \UserOperations::get_instance();
        foreach( $this->_perms as $permname ) {
            if( $userops->CheckPermission($admin_uid,$permname) ) return TRUE;
        }
        return FALSE;
    }

    public function get_message()
    {
        return $this->_msg;
    }

    public function get_icon()
    {
        return $this->_icon;
    }

} // end of class