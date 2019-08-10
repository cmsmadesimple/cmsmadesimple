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
 * Contains classes to represent a template query and its results.
 * @package CMS
 * @license GPL
 */

/**
 * A class to represent a template query, and its results.
 * This class accepts in it's constructor an array (or a comma separated string, of filter arguments).
 * Accepted filter arguments are:<br/>
 *  o:string - The originator name<br/>
 *  i:##,##,## - A list of template id's<br/>
 *  t:## - A template type id<br/>
 *  c:## - A template category id.<br/>
 *  d:## - A design id<br/>
 *  u:## - A template owner id<br/>
 *  e:## - An additional editor id.<br/>
 *  l:#  - A boolean (0 or 1) indicating listable, or not listable.
 *
 * Example:<br/>
 * <code>
 * $qry = new CmsTemplateQuery(array('o:'.get_userid(false)));<br/>
 * $qry->limit = 50;<br/>
 * $list = $qry->GetMatches();<br/>
 * </code>
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell <calguy1000@gmail.com>
 * @see CmsDbQueryBase
 * @property string $sortby The sorting field for the returned results.  Possible values are: id,name,created,modified,type.  The default is to sort by template name.';
 * @property string $sortorder The sorting order for the returned results.  Possible values are: ASC,DESC.  The default is ASC.
 */
class CmsLayoutTemplateQuery extends CmsDbQueryBase
{
	/**
	 * @ignore
	 */
	private $_sortby = 'tpl.name';

	/**
	 * @ignore
	 */
	private $_sortorder = 'asc';

	/**
	 * Execute the query given the parameters saved in the query
	 *
o	 * @throws CmsInvalidDataException
	 * @throws CmsSQLErrorException
	 * Though this method can be called directly, it is also called by other members automatically.
	 */
    public function execute()
    {
        if( !is_null($this->_rs) ) return;

        $query = 'SELECT SQL_CALC_FOUND_ROWS tpl.id FROM '.CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME.' tpl
              LEFT JOIN '.CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME.' type ON tpl.type_id = type.id';
        $where = array('id'=>array(),'type'=>array(),'category'=>array(),'user'=>array(),'design'=>array());

        $this->_limit = 1000;
        $this->_offset = 0;
        $db = CmsApp::get_instance()->GetDb();
        foreach( $this->_args as $key => $val ) {
            if( empty($val) ) continue;
            if( is_numeric($key) && $val[1] == ':' ) list($key,$second) = explode(':',$val,2);

            switch( strtolower($key) ) {
			case 'o': // orginator
			case 'originator':
				$second = trim($second);
                $q2 = 'SELECT id FROM '.CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME.' WHERE originator = ?';
                $typelist = $db->GetCol($q2,array($second));
                if( !count($typelist) ) $typelist = array(-999);
                $where['type'][] = 'type_id IN ('.implode(',',$typelist).')';
				break;

            case 'l':
            case 'listable':
		if( !isset($second) && $val ) $second = $val;
                $second = (cms_to_bool($second)) ? 0 : 1;
                $where['listable'] = array('listable = '.$second);
                break;

			case 'i': // id list
			case 'idlist':
				$second = trim($second);
				$tmp = explode(',',$second);
				$tmp2 = array();
				for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
					$tmp3 = (int)$tmp[$i];
					if( $tmp3 < 1 ) continue;
					if( in_array($tmp3,$tmp2) ) continue;
					$tmp2[] = $tmp3;
				}
				$where['id'][] = 'tpl.id IN ('.implode(',',$tmp2).')';
				break;

            case 't': // type
			case 'type':
				$second = (int)$second;
				$where['type'][] = 'type_id = '.$db->qstr($second);
				break;

            case 'c': // category
			case 'category':
				$second = (int)$second;
				$where['category'][] = 'category_id = '.$db->qstr($second);
				break;

            case 'd': // design
			case 'design':
				// find all the templates in design: d
				$q2 = 'SELECT tpl_id FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' WHERE design_id = ?';
				$tpls = $db->GetCol($q2,array((int)$second));
                if( !count($tpls) )  $tpls = array(-999); // this won't match anything
                $where['design'][] = 'tpl.id IN ('.implode(',',$tpls).')';
				break;

            case 'u': // user
			case 'user':
				$second = (int)$second;
				$where['user'][] = 'owner_id = '.$db->qstr($second);
				break;

            case 'e': // editable
			case 'editable':
				$second = (int)$second;
				$q2 = 'SELECT DISTINCT tpl_id FROM (
                 SELECT tpl_id FROM '.CMS_DB_PREFIX.CmsLayoutTemplate::ADDUSERSTABLE.'
                   WHERE user_id = ?
                 UNION
                 SELECT id AS tpl_id FROM '.CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME.'
                   WHERE owner_id = ?)
                 AS tmp1';
				$t2 = $db->GetCol($q2,array($second,$second));
				if( is_array($t2) && count($t2) ) $where['user'][] = 'tpl.id IN ('.implode(',',$t2).')';
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
				case 'name':
				case 'created':
				case 'modified':
					$this->_sortby = "tpl.$val";
					break;

				case 'type':
					$this->_sortby = 'CONCAT(type.originator,type.name)';
					break;

				default:
					throw new CmsInvalidDataException($val.' is an invalid sortby for '.__CLASS__);
				}
				break;

			case 'sortorder':
				$val = strtolower($val);
				switch( $val ) {
				case 'asc':
				case 'desc':
					$this->_sortorder = $val;
					break;

				default:
					throw new CmsInvalidDataException($val.' is an invalid sortorder for '.__CLASS__);
				}
				break;
            }
        }

        $tmp = array();
        foreach( $where as $key => $exprs ) {
            if( count($exprs) ) $tmp[] = '('.implode(' OR ',$exprs).')';
        }
        if( count($tmp) ) $query .= ' WHERE ' . implode(' AND ',$tmp);
        $query .= ' ORDER BY '.$this->_sortby.' '.$this->_sortorder;

        // execute the query
        $this->_rs = $db->SelectLimit($query,$this->_limit,$this->_offset);
        if( !$this->_rs ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $this->_totalmatchingrows = $db->GetOne('SELECT FOUND_ROWS()');
    }

	/**
	 * Get the template object for the current member of the resultset (if any)
	 *
	 * This method calls the execute method.
	 *
	 * @throws CmsLogicException
	 * @return CmsLayoutTemplate
	 */
    public function &GetTemplate()
    {
        return $this->GetObject();
    }

	/**
	 * Get the template object for the current member of the resultset (if any).
     *
     * This method is not as efficient as GetMatches() when the resultset has multiple items.
	 *
	 * This method calls the execute method.
	 *
	 * @throws CmsLogicException
	 * @return CmsLayoutTemplate
	 */
    public function &GetObject()
    {
        $this->execute();
        if( !$this->_rs ) throw new CmsLogicException('Cannot get template from invalid template query object');
        return CmsLayoutTemplate::load($this->fields['id']);
    }
	/**
	 * Get the list of matched template ids
	 *
	 * This method calls the execute method.
	 *
	 * @throws CmsLogicException
	 * @return array Array of integers
	 */
    public function GetMatchedTemplateIds()
    {
        $this->execute();
        if( !$this->_rs ) throw new CmsLogicException('Cannot get template from invalid template query object');

        $out = array();
        while( !$this->EOF() ) {
            $out[] = $this->fields['id'];
            $this->MoveNext();
        }
        $this->Rewind();
        return $out;
    }

	/**
	 * Get all matches
	 *
	 * This method calls the execute method
	 *
	 * @return array Array of CmsLayoutTemplate objects
	 */
    public function GetMatches()
    {
        return CmsLayoutTemplate::load_bulk($this->GetMatchedTemplateIds());
    }
}

#
# EOF
#
?>
