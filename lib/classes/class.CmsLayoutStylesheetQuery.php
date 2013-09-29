<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * @package CMS
 */

/**
 * A class to represent a template query, and its results.
 *
 * @since 1.12
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class CmsLayoutStylesheetQuery extends CmsDbQueryBase
{
	protected $_sortby = 'name';
	protected $_sortorder = 'ASC';

  public function execute()
  {
    if( !is_null($this->_rs) ) return;

    $query = 'SELECT SQL_CALC_FOUND_ROWS id FROM '.cms_db_prefix().CmsLayoutStylesheet::TABLENAME;

    $this->_limit = 1000;
    $this->_offset = 0;
    $db = cmsms()->GetDb();
    $where = array();
    foreach( $this->_args as $key => $val ) {
      if( empty($val) ) continue;
      if( is_numeric($key) && $val[1] == ':' ) list($key,$val) = explode(':',$val,2);
      switch( strtolower($key) ) {
      case 'n': // name (prefix)
      case 'name': // name (prefix)
				$val = trim($val);
				$where[] = 'name LIKE '.$db->qstr($val.'%');
				break;
      case 'd': // design
			case 'design':
				$q2 = 'SELECT css_id FROM '.cms_db_prefix().CmsLayoutCollection::CSSTABLE.' WHERE design_id = ?';
				$tpls = $db->GetCol($q2,array((int)$val));
				if( is_array($tpls) && count($tpls) ) {
					$where[] = 'id IN ('.implode(',',$tpls).')';
				}
				else {
					$where[] = 'id IN (-1)';
				}
				break;
      case 'limit':
				$this->_limit = max(1,min(1000,$val));
				break;
      case 'offset':
				$this->_offset = max(0,$val);
				break;
			case 'sortby':
				break;
			case 'sortorder':
				$val = strtoupper($val);
				switch( $val ) {
				case 'DESC':
					$this->_sortorder = 'DESC';
					break;

				case 'ASC':
				default:
					$this->_sortorder = 'ASC';
					break;
				}
				break;
      }
    }
    
    if( count($where) ) $query .= ' WHERE '.implode(' AND ',$where);
    $query .= ' ORDER BY '.$this->_sortby.' '.$this->_sortorder;

    $this->_rs = $db->SelectLimit($query,$this->_limit,$this->_offset);
    if( $db->ErrorMsg() != '' ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    $this->_totalmatchingrows = $db->GetOne('SELECT FOUND_ROWS()');
  }

  public function GetMatches()
  {
    $this->execute();
    if( !$this->_rs ) throw new CmsLogicException('Cannot get template from invalid template query object');

    $tmp = array();
    while( !$this->EOF() ) {
      $tmp[] = $this->fields['id'];
      $this->MoveNext();
    }

    return CmsLayoutStylesheet::load_bulk($tmp);
  }
} // end of class

#
# EOF
#
?>
