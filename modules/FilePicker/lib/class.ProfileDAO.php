<?php
namespace FilePicker;

class ProfileDAO
{
    private $_db;

    public static function table_name() { return CMS_DB_PREFIX.'mod_filepicker_profiles'; }

    public function __construct( $db )
    {
        $this->_db = $db;
    }

    public function loadById( $id )
    {
        $id = (int) $id;
        if( $id < 1 ) throw new \LogicException('Invalid id passed to '.__METHOD__);
        $sql = 'SELECT * FROM '.self::table_name().' WHERE id = ?';
        $row = $this->_db->GetRow($sql,[ $id ]);
        if( is_array($row) && count($row) ) {
            $obj = new Profile($row);
            return $obj;
        }
    }

    public function loadByName( $name )
    {
        $name = trim($name);
        if( !$name ) throw new \LogicException('Invalid name passed to '.__METHOD__);
        $sql = 'SELECT * FROM '.self::table_name().' WHERE name = ?';
        $row = $this->_db->GetRow($sql,[ $name ]);
        if( is_array($row) && count($row) ) {
            $obj = new Profile($row);
            return $obj;
        }
    }

    public function delete( Profile $profile )
    {
        if( $profile->id < 1 ) throw new \LogicException('Invalid profile passed to '.__METHOD__);

        $sql = 'DELETE FROM '.self::table_name().' WHERE id = ?';
        $this->_db->Execute( $sql, [ $profile->id ] );

        $profile = $profile->withNewId();
        return $profile;
    }

    protected function _insert( Profile $profile )
    {
        $sql = 'SELECT id FROM '.self::table_name().' WHERE name = ?';
        $tmp = $this->_db->GetOne( $sql, [ $profile->name ] );
        if( $tmp ) throw new \CmsInvalidDataException('err_profilename_exists');

        $sql = 'INSERT INTO '.self::table_name().' (name, data, create_date, modified_date) VALUES (?, ?, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
        $dbr = $this->_db->Execute( $sql, [ $profile->name, $profile->data ] );
        if( !$dbr ) throw new \RuntimeException('Problem inserting profile record');

        $new_id = $this->_db->Insert_ID();
        $obj = $profile->withNewID( $new_id );
        return $obj;
    }

    protected function _update( Profile $profile )
    {
        $sql = 'SELECT id FROM '.self::table_name().' WHERE name = ? AND id != ?';
        $tmp = $this->_db->GetOne( $sql, [ $profile->name, $profile->id ] );
        if( $tmp ) throw new \CmsInvalidDataException('err_profilename_exists');

        $sql = 'UPDATE '.self::table_name().' SET name = ?, data = ?, modified_date = UNIX_TIMESTAMP()';
        $dbr = $this->_db->Execute( $sql, [ $profile->name, $profile->data ] );
        if( !$dbr ) throw new \RuntimeException('Problem updating profile record');

        $obj = $profile->markModified();
        return $obj;
    }

    public function save( Profile $profile )
    {
        $profile->validate();
        if( $profile->id < 1 ) {
            return $this->_insert( $profile );
        } else {
            return $this->_update( $profile );
        }
    }

    public function loadAll()
    {
        $sql = 'SELECT * FROM '.self::table_name().' ORDER BY name';
        $list = $this->_db->GetArray($sql);
        if( !count($list) ) return;

        $out = [];
        foreach( $list as $row ) {
            $out[] = new Profile($row);
        }
        return $out;
    }
} // end of class