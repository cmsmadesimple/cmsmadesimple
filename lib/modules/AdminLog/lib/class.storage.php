<?php
namespace AdminLog;

class storage
{

    private $_mod;

    public function __construct( \AdminLog $mod )
    {
        $this->_mod = $mod;
    }

    public function clear()
    {
        $sql = 'TRUNCATE TABLE '.self::table_name();
        $db = $this->_mod->GetDb();
        $db->Execute( $sql );
    }

    public function save( event $ev )
    {
        $db = $this->_mod->GetDb();
        $sql = 'INSERT INTO '.self::table_name().' ( timestamp, severity, uid, ip_addr, username, subject, msg, item_id ) VALUES (?,?,?,?,?,?,?,?)';
        $db->Execute( $sql, [ $ev->timestamp, $ev->severity, $ev->uid, $ev->ip_addr, $ev->username, $ev->subject, $ev->msg, $ev->item_id ] );
    }

    public static function table_name() { return CMS_DB_PREFIX.'mod_adminlog';
    }

    public function clear_older_than( $time )
    {
        $time = (int) $time;
        $db = $this->_mod->GetDb();
        $sql = 'DELETE FROM '.self::table_name().' WHERE timestamp < ?';
        $db->Execute( $sql, [ $time ] );
    }
}
