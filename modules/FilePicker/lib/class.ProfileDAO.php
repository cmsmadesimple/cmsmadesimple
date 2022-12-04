<?php
namespace FilePicker;

class ProfileDAO
{
    private $_mod;
    private $_db;
    const DFLT_PREF = 'ProfileDAO_defaultProfileId';

    public static function table_name() { return CMS_DB_PREFIX.'mod_filepicker_profiles'; }

    public function __construct( \FilePicker $mod )
    {
        $this->_mod = $mod;
        $this->_db = $mod->GetDb();
    }

    protected function profile_from_row(array $row)
    {
        $data = unserialize($row['data']);
        $data['name'] = $row['name'];
        $data['id'] = $row['id'];
        $data['create_date'] = $row['create_date'];
        $data['modified_date'] = $row['modified_date'];
        $obj = new Profile($data);
        return $obj;
    }

    public function getDefaultProfileId()
    {
        return (int) $this->_mod->GetPreference(self::DFLT_PREF);
    }

    public function clearDefault()
    {
        $this->_mod->RemovePreference(self::DFLT_PREF);
    }

    public function setDefault( Profile $profile )
    {
        if( $profile->id < 1 ) throw new \LogicException('Cannot set a profile as default if it is not yet saved');
        $this->_mod->SetPreference(self::DFLT_PREF,$profile->id);
    }

    public function loadDefault()
    {
        $dflt_id = $this->getDefaultProfileId();
        if( $dflt_id < 1 ) return;

        return $this->loadById( $dflt_id );
    }

    public function loadById( $id )
    {
        $id = (int) $id;
        if( $id < 1 ) throw new \LogicException('Invalid id passed to '.__METHOD__);
        $sql = 'SELECT * FROM '.self::table_name().' WHERE id = ?';
        $row = $this->_db->GetRow($sql,[ $id ]);
        if( is_array($row) && count($row) ) return $this->profile_from_row($row);
    }

    public function loadByName( $name )
    {
        $name = trim($name);
        if( !$name ) throw new \LogicException('Invalid name passed to '.__METHOD__);
        $sql = 'SELECT * FROM '.self::table_name().' WHERE name = ?';
        $row = $this->_db->GetRow($sql,[ $name ]);
        if( is_array($row) && count($row) ) return $this->profile_from_row($row);
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
        $dbr = $this->_db->Execute( $sql, [ $profile->name, serialize($profile->getRawData()) ] );
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

        $sql = 'UPDATE '.self::table_name().' SET name = ?, data = ?, modified_date = UNIX_TIMESTAMP() WHERE id = ?';
        $dbr = $this->_db->Execute( $sql, [ $profile->name, serialize($profile->getRawData()), $profile->id ] );
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
            $out[] = $this->profile_from_row($row);
        }
        return $out;
    }
} // end of class