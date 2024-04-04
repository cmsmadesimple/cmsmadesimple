<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

class UserGuideQuery extends CmsDbQueryBase {

    public function __construct($args = '') 
    {
        parent::__construct($args);
        if ( isset($this->_args['limit']) ) $this->_limit = (int) $this->_args['limit'];
    }

    public function execute() 
    {
        if ( !is_null($this->_rs) ) return;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS UG.* FROM '.CMS_DB_PREFIX.'module_userguide AS UG';
        if ( isset($this->_args['active']) ) {
            // store only active or non-active items
            $tmp = $this->_args['active'];
            if ( $tmp === 0 ) {
                $sql .= ' WHERE active = 0';
            } else if ( $tmp === 1 ) {
                $sql .= ' WHERE active = 1';
            }
        }
        $sql .= ' ORDER BY position';
        $db = \cms_utils::get_db();
        $this->_rs = $db->SelectLimit($sql,$this->_limit,$this->_offset);
        if ( $db->ErrorMsg() ) throw new \CmsSQLErrorException( $db->sql.' -- '.$db->ErrorMsg() );
        $this->_totalmatchingrows = $db->GetOne('SELECT FOUND_ROWS()');
    }

    public function &GetObject() 
    {
        $obj = new UserGuideItem;
        $obj->fill_from_array($this->fields);
        return $obj;
    }

    public function updatePositions() 
    {
        $db = \cms_utils::get_db();
        $sql = 'SET @rownumber = 0';
        $res = $db->Execute($sql);
        $sql = 'UPDATE '.CMS_DB_PREFIX.'module_userguide
            SET position = (@rownumber:=@rownumber+1)
            ORDER BY position';
        $res = $db->Execute($sql);
        if ( $db->ErrorMsg() ) {
            throw new \CmsSQLErrorException( $db->sql.' -- '.$db->ErrorMsg(). '(updatePositions)' );
        }
        return $res;
    }


}


