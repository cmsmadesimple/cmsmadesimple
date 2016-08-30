<?php
#CMS - CMS Made Simple
#(c)2004-2013 by Ted Kulp (wishy@users.sf.net)
#(c)2011-2016 by The CMSMS Dev Team
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

namespace CMSMS\Internal;

class CachedStylesheetsInDesignQuery
{
    const TIMEOUT = 604800;
    private $_design_id;
    private $_key;
    private $_data;

    public function __construct($design_id)
    {
        $design_id = (int) $design_id;
        if( $design_id < 1 ) throw new \InvalidArgumentException('Invalid design id passed to '.__METHOD__);
        $this->_design_id = $design_id;
        $this->_key = __CLASS__.$design_id;
    }

    private static function _get_driver()
    {
        static $_driver = null;
        if( !$_driver ) {
            $_driver = new \cms_filecache_driver(array('lifetime'=>self::TIMEOUT,'autocleaning'=>1,'group'=>__CLASS__));
        }
        return $_driver;
    }

    private function _get_data()
    {
        if( !is_array($this->_data) ) {
            $this->_data = self::_get_driver()->get($this->_design_id);
            if( !is_array($this->_data) ) {
                $db = \cms_utils::get_db();
                $query = new \CmsLayoutStylesheetQuery( [ 'design' => $this->_design_id] );
                $out = $query->GetMatches();
                if( !$out ) $out = [];
                $this->_data = $out;
                self::_get_driver()->set($this->_design_id,$out);
            }
        }
        return $this->_data;
    }

    public static function reset()
    {
        self::_get_driver()->clear();
    }

    public function TotalMatches()
    {
        return count($this->_get_data());
    }

    public function GetMatches()
    {
        return $this->_get_data();
    }
}