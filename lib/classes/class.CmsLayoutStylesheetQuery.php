<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
 * A class to perform advanced queries on the layout stylesheets.
 *
 * @since   2.0
 * @package CMS
 * @license GPL
 */

/**
 * A class to represent a template query, and its results.
 * This class accepts in it's constructor an array of filter arguments.
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell <calguy1000@gmail.com>
 * @see CmsDbQueryBase
 * @property int $id The stylesheet id.  This will result in at most 1 result being returned.
 * @property string $name A stylesheet name to filter upon.  If a partial name is provided, it is assumed to be a prefix.
 * @property int $design A design id to filter upon.
 * @property string $sortby Possible values are id,item_order,design,name  Default is to sort by name.
 * @property string $sortorder Possible values are ASC, DESC.  Default is ASC.
 */
class CmsLayoutStylesheetQuery extends CmsDbQueryBase
{
	/**
	 * Execute the query in this object.
	 *
     * @throws CmsInvalidDataException
	 * @throws CmsSQLErrorException
	 */
    public function execute()
    {
        if( !is_null($this->_rs) ) return;
        $query = 'SELECT SQL_CALC_FOUND_ROWS S.id FROM '.CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME.' S';

        // if we are using a design id argument
        // we do join, and sort by item order in the design
        $dflt_sort = TRUE;
        $have_design = FALSE;
        foreach( $this->_args as $key => $val ) {
            switch( $key ) {
            case 'sortby':
                $dflt_sort = FALSE;
                break;

            case 'd':
            case 'design':
                $have_design = TRUE;
            }
        }

        if( $dflt_sort && $have_design ) $this->_args['sortby'] = 'item_order';

        $sortorder = 'ASC';
        $sortby = 'S.name';
        $this->_limit = 1000;
        $this->_offset = 0;
        $db = cmsms()->GetDb();
        $where = array();
        foreach( $this->_args as $key => $val ) {
            if( empty($val) ) continue;
            if( is_numeric($key) && $val[1] == ':' ) list($key,$val) = explode(':',$val,2);
            switch( strtolower($key) ) {
            case 'i':
            case 'id':
                $val = (int)$val;
                $where[] = "id = $val";
                break;

            case 'n': // name (prefix)
            case 'name': // name (prefix)
                $val = trim($val);
                $where[] = 'name LIKE '.$db->qstr($val.'%');
                break;

            case 'fullname':
                $val = trim($val);
                $where[] = 'name = '.$db->qstr($val);
                break;

            case 'd': // design
            case 'design':
                $query .= ' LEFT JOIN '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' D ON S.id = D.css_id';
                $val = (int)$val;
                $where[] = "D.design_id = $val";
                break;

            case 'limit':
                $this->_limit = max(1,min(1000,$val));
                break;

            case 'offset':
                $this->_offset = max(0,$val);
                break;

            case 'sortby':
                $val = strtolower($val);
                switch( $val ) {
                case 'id':
                    $sortby = 'S.id';
                    break;

                case 'item_order':
                case 'design':
                    if( !$have_design ) throw new CmsInvalidDataException('Cannot sort by item_order/design if design_id is not known');
                    $sortby = 'D.item_order';
                    break;

                case 'name':
                default:
                    $sortby = 'S.name';
                    break;
                }
                break;

            case 'sortorder':
                $val = strtoupper($val);
                switch( $val ) {
                case 'DESC':
                    $sortorder = 'DESC';
                    break;

                case 'ASC':
                default:
                    $sortorder = 'ASC';
                    break;
                }
                break;
            }
        }

        if( count($where) ) $query .= ' WHERE '.implode(' AND ',$where);
        $query .= ' ORDER BY '.$sortby.' '.$sortorder;

        $this->_rs = $db->SelectLimit($query,$this->_limit,$this->_offset);
        if( $db->ErrorMsg() != '' ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $this->_totalmatchingrows = $db->GetOne('SELECT FOUND_ROWS()');
    }

    /**
     * Get a CmsLayoutStylesheet object for the current data in the fieldset.
     *
     * This method is not as efficient as the GetMatches() method when the resultset has multiple items.
     *
	 * @throws CmsLogicException
     * @return CmsLayoutStylesheet
     */
    public function &GetObject()
    {
        $this->execute();
        if( !$this->_rs ) throw new CmsLogicException('Cannot get stylesheet from invalid stylesheet query object');
        $id = (int) $this->fields['id'];
        $obj = CmsLayoutStylesheet::load($id);
        return $obj;
    }

	/**
	 * Return all of the matches for this query
	 *
	 * @throws CmsLogicExceptin
	 * @return array Array of CmsLayoutStylesheet object
	 */
    public function GetMatches()
    {
        $this->execute();
        if( !$this->_rs ) throw new CmsLogicException('Cannot get template from invalid template query object');

        $tmp = array();
        while( !$this->EOF() ) {
            $tmp[] = $this->fields['id'];
            $this->MoveNext();
        }

        $deep = (!empty($this->_args['deep']) && $this->_args['deep']) ? TRUE : FALSE;
        return CmsLayoutStylesheet::load_bulk($tmp,$deep);
    }
} // end of class

#
# EOF
#
?>
