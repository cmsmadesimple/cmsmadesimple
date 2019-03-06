<?php
namespace News2;
use News2;
use \CMSMS\Database\Connection as Database;
use cms_cache_driver;

class FieldDefManager
{
    private $_list;
    private $db;
    private $mod;
    private $ftm;
    private $cache_driver;

    public function __construct( Database $db, News2 $mod, FieldTypeManager $ftm, cms_cache_driver $driver )
    {
        $this->db = $db;
        $this->mod = $mod;
        $this->ftm = $ftm;
        $this->cache_driver = $driver;
    }

    public function getTypeList( News2 $mod ) : array
    {
        $out = null;
        $out[FieldDef::TYPE_TEXT] = $mod->Lang('fieldtype_text');
        $out[FieldDef::TYPE_TEXTAREA] = $mod->Lang('fieldtype_textarea');
        $out[FieldDef::TYPE_SELECT] = $mod->Lang('fieldtype_select');
        $out[FieldDef::TYPE_BOOLEAN] = $mod->Lang('fieldtype_boolean');
        $out[FieldDef::TYPE_URL] = $mod->Lang('fieldtype_url');
        $out[FieldDef::TYPE_MULTISELECT] = $mod->Lang('fieldtype_multiselect');
        return $out;
    }

    public function createNewOfType( FieldType $type ) : FieldDef
    {
        $opts = [ 'type'=>get_class($type) ];
        return FieldDef::from_row( $opts );
    }

    public function loadAll()
    {
        if( $this->cache_driver && $this->cache_driver->exists('all',__CLASS__) ) {
            $this->_list = $this->cache_driver->get('all',__CLASS__);
        }

        if( empty($this->_list) ) {
            $sql = 'SELECT * FROM '.self::table_name().' ORDER BY item_order';
            $list = $this->db->GetArray( $sql );
            if( empty($list) ) return;

            $out = null;
            foreach( $list as $row ) {
                $out[] = FieldDef::from_row( $row );
            }
            if( $this->cache_driver ) $this->cache_driver->set('all',$out,__CLASS__);
            $this->_list = $out;
        }
        return $this->_list;
    }

    public function loadAllAsHash()
    {
        $out = null;
        $list = $this->loadAll();

        if( $list ) {
            foreach( $list as $one ) {
                $out[$one->name] = $one;
            }
        }
        return $out;
    }

    public function loadByID( int $id )
    {
        $list = $this->loadAll();
        if( is_array($list) && count($list) ) {
            foreach( $list as $one ) {
                if( $one->id == $id ) return $one;
            }
        }
    }

    public function loadByName( string $name )
    {
        $list = $this->loadAllAsHash();
        if( isset( $list[$name] ) ) return $list[$name];
    }

    protected function _insert( FieldDef $obj )
    {
        $all = $this->loadAll();
        $item_order = 1;
        if( $all ) {
            $item_order = count($all) + 1;
            foreach( $all as $fd ) {
                if( $obj->name == $fd->name ) throw new \RuntimeException( $this->mod->Lang('err_fieldexists',$obj->name) );
            }
        }

        // and do the insert
        $sql = 'INSERT INTO '.self::table_name().' (name, type, item_order, extra) VALUES (?, ?, ?, ?)';
        $extra_str = $obj->get_extra_as_string();
        $this->db->Execute( $sql, [ $obj->name, $obj->type, $item_order, $extra_str ] );
        if( $this->cache_driver ) $this->cache_driver->clear(__CLASS__);
    }

    public function _update( FieldDef $obj )
    {
        if( !$obj->item_order ) throw new \LogicException('Cannot update an invalid Fielddef');
        $all = $this->loadAll();
        if( !$all ) throw new \LogicError('Cannot update a FieldDef when none exist');
        if( $obj->item_order < 1 || $obj->item_order > count($all) ) throw new \LogicException('Cannot update an invalid Fielddef');
        foreach( $all as $fd ) {
            if( $fd->id != $obj->id && $fd->name == $obj->name ) throw new \RuntimeException( $this->mod->Lang('err_fieldexists',$obj->name) );
        }

        $sql = 'UPDATE '.self::table_name().' SET name = ?, type = ?, item_order = ?, extra = ? WHERE id = ?';
        $extra_str = $obj->get_extra_as_string();
        $this->db->Execute( $sql, [ $obj->name, $obj->type, $obj->item_order, $extra_str, $obj->id ] );
        if( $this->cache_driver ) $this->cache_driver->clear(__CLASS__);
    }

    public function save( FieldDef $obj )
    {
        if( !$obj->name ) throw new \LogicException('Cannot save invalid fielddef 1');
        if( !$obj->type ) throw new \LogicException('Cannot save invalid fielddef 2');
        if( ($obj->type == $obj::TYPE_SELECT || $obj->type == $obj::TYPE_MULTISELECT) ) {
            $options = $obj->options;
            if( !$options ) throw new \LogicException('Cannot save invalid fielddef 3');
        }

        if( $obj->id < 1 ) {
            return $this->_insert( $obj );
        }
        else {
            return $this->_update( $obj );
        }
    }

    public function delete( FieldDef $obj )
    {
        if( !$obj->id || $obj->item_order < 1 ) throw new \LogicException('Cannot delete invalid fielddef');

        $this->db->StartTrans();

        $sql = 'DELETE FROM  '.self::fieldvals_table().' WHERE fielddef_id = ?';
        $this->db->Execute( $sql, $obj->id );

        $sql = 'DELETE FROM '.self::table_name().' WHERE id = ?';
        $this->db->Execute( $sql, $obj->id );

        $sql = 'UPDATE '.self::table_name().' SET item_order = item_order - 1 WHERE item_order > ?';
        $this->db->Execute( $sql, $obj->item_order );

        $this->db->CompleteTrans();
        if( $this->cache_driver ) $this->cache_driver->clear(__CLASS__);
    }

    public static function fieldvals_table() { return CMS_DB_PREFIX.'mod_news2_fieldvals'; }
    public static function table_name() { return CMS_DB_PREFIX.'mod_news2_fielddefs'; }

} // class