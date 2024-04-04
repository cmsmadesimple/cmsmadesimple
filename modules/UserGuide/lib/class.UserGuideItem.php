<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

class UserGuideItem {

    private const LOCAL_VIDEO_TYPES = [
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'ogg' => 'video/ogg'
    ];

    private $_data = [
        'id'            => null,
        'title'         => null,
        'position'      => null,
        'active'        => null,
        'admin'         => null,
        'content'       => null,
        'embed_type'    => null,
        'embed_code'    => null,
        'embed_first'   => null,
    ];

    public function __get($key) {
        switch( $key ) {
            case 'id':
            case 'title':
            case 'position':
            case 'active':
            case 'admin':
            case 'content':
            case 'embed_type':
            case 'embed_code':
            case 'embed_first':
                
                return $this->_data[$key];
        }
    }

    public function __set($key,$val) {
        switch( $key ) {
            case 'title':
            case 'content':
            case 'embed_type':
            case 'embed_code':
                $this->_data[$key] = trim($val);
                break;

            case 'position':
                $this->_data[$key] = (int) $val;
                break;

            case 'active':
            case 'admin':
            case 'embed_first':
                $this->_data[$key] = (bool) $val;
                break;

        }
    }

    public function save() {
        // test if valid before calling save()
        if ( $this->id > 0 ) {
            $saved = $this->update();
        } else {
            $saved = $this->insert();
        }
        return $saved;
    }


    protected function insert() {
        $db = \cms_utils::get_db();
        $sql = 'INSERT INTO '.CMS_DB_PREFIX.'module_userguide (title, position, active, admin, content, embed_type, embed_code, embed_first) VALUES (?,?,?,?,?,?,?,?)';
        $dbr = $db->Execute($sql, [$this->title, $this->position, $this->active, $this->admin, $this->content, $this->embed_type, $this->embed_code, $this->embed_first]);
        if ( !$dbr ) return FALSE;
        $this->_data['id'] = $db->Insert_ID();
        return TRUE;
    }

    protected function update() {
        $db = \cms_utils::get_db();
        $sql = 'UPDATE '.CMS_DB_PREFIX.'module_userguide SET title = ?, position = ?, active = ?, admin = ?, content = ?, embed_type=?, embed_code=?, embed_first=? WHERE id = ?';
        $dbr = $db->Execute($sql, [$this->title, $this->position, $this->active, $this->admin, $this->content,$this->embed_type, $this->embed_code, $this->embed_first, $this->id]);
        if ( !$dbr ) return FALSE;
        return TRUE;
    }

    public function delete() {
        if ( !$this->id ) return FALSE;
        $db = \cms_utils::get_db();
        $sql = 'DELETE FROM '.CMS_DB_PREFIX.'module_userguide WHERE id = ?';
        $dbr = $db->Execute($sql,array($this->id));
        if ( !$dbr ) return FALSE;
        $this->_data['id'] = null;
        return TRUE;
    }

    public function toggle_active() {
        if ( !$this->id ) return FALSE;
        $db = \cms_utils::get_db();
        $sql = 'UPDATE '.CMS_DB_PREFIX.'module_userguide SET active = ? WHERE id = ?';
        $dbr = $db->Execute($sql, array(!(bool)$this->active, $this->id));
        if ( !$dbr ) return FALSE;
        return TRUE;
    }

    public function toggle_admin_only() {
        if ( !$this->id ) return FALSE;
        $db = \cms_utils::get_db();
        $sql = 'UPDATE '.CMS_DB_PREFIX.'module_userguide SET admin = ? WHERE id = ?';
        $dbr = $db->Execute($sql, array(!(bool)$this->admin, $this->id));
        if ( !$dbr ) return FALSE;
        return TRUE;
    }

    /**
     *  Parse local embed code to extract sources & errors
     *  @return stdClass with properties: sources, errors
     */
    public function parse_local_embed() 
    {
        if ( !$this->id || $this->embed_type!='local') return;

        $result = new stdClass();
        $result->sources = [];
        $result->errors = [];
        $config = cmsms()->GetConfig();

        $filenames = explode(',', $this->embed_code);
        foreach( $filenames as &$filename ) {
            $filename = trim($filename);
            if ( $filename=='' ) continue;

            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ( !array_key_exists($ext, self::LOCAL_VIDEO_TYPES) ) {
                $result->errors[] = 'Invalid filename type: '.$filename;
                continue;
            }
            // check if file exists
            if ( !file_exists($config['uploads_path'].'/'.$filename) ) {
                $result->errors[] = 'File not found: '.$filename;
                continue;
            }
            $result->sources[$ext] = $filename;
        }

        return $result;
    }


    /** internal */
    public function fill_from_array($row) {
        foreach( $row as $key => $val ) {
            if ( array_key_exists($key,$this->_data) ) {
                $this->_data[$key] = $val;
            }
        }
    }

    public static function &load_by_id($id) {
        $id = (int) $id;
        $db = \cms_utils::get_db();
        $sql = 'SELECT * FROM '.CMS_DB_PREFIX.'module_userguide WHERE id = ?';
        $row = $db->GetRow($sql,array($id));
        if ( is_array($row) ) {
            $obj = new self();
            $obj->fill_from_array($row);
            return $obj;
        }
    }



}


