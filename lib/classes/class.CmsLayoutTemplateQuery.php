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
 * A class to represent a smarty template.
 *
 * @since 1.12
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class CmsLayoutTemplateQuery
{
  private $_totalmatchingrows = null;
  private $_offset = 0;
  private $_limit = 1000;
  private $_rs = null;
  private $_args = array();

  public function __construct($args = '')
  {
    if( empty($args) ) return;
    
    if( is_array($args) ) {
      $this->_args = $args;
    }
    else if( is_string($args) ) {
      $this->_args = explode(',',$args);
    }
  }

  public function execute()
  {
    if( !is_null($this->_rs) ) return;

    $query = 'SELECT SQL_CALC_FOUND_ROWS id FROM '.cms_db_prefix().CmsLayoutTemplate::TABLENAME;
    $where = array('type'=>array(),'category'=>array(),'user'=>array(),'design'=>array());

    $this->_limit = 1000;
    $this->_offset = 0;
    $db = cmsms()->GetDb();
    foreach( $this->_args as $key => $val ) {
      if( empty($val) ) continue;
      if( is_numeric($key) && $val[1] == ':' ) {
				list($key,$second) = explode(':',$val,2);
      }
      switch( strtolower($key) ) {
      case 't': // type
				$second = (int)$second;
				$where['type'][] = 'type_id = '.$db->qstr($second);
				break;
      case 'c': // category
				$second = (int)$second;
				$where['category'][] = 'category_id = '.$db->qstr($second);
				break;
      case 'd': // design
				// find all the templates in design: d
				$q2 = 'SELECT tpl_id FROM '.cms_db_prefix().CmsLayoutCollection::TPLTABLE.'
               WHERE design_id = ?';
				$tpls = $db->GetCol($q2,array((int)$second));
				$where['design'][] = 'id IN ('.implode(',',$tpls).')';
				break;
      case 'u': // user
				$second = (int)$second;
				$where['user'][] = 'owner_id = '.$db->qstr($second);
				break;
      case 'e': // editable
				$second = (int)$second;
				$q2 = 'SELECT DISTINCT tpl_id FROM (
                 SELECT tpl_id FROM '.cms_db_prefix().CmsLayoutTemplate::ADDUSERSTABLE.'
                   WHERE user_id = ? 
                 UNION
                 SELECT id AS tpl_id FROM '.cms_db_prefix().CmsLayoutTemplate::TABLENAME.'
                   WHERE owner_id = ?)
                 AS tmp1';
				$t2 = $db->GetCol($q2,array($second,$second));
				if( is_array($t2) && count($t2) ) {
					$where['user'][] = 'id IN ('.implode(',',$t2).')';
				}
				break;
      case 'limit':
				$this->_limit = max(1,min(1000,$val));
				break;
      case 'offset':
				$this->_offset = max(0,$val);
				break;
      }
    }

    $tmp = array();
    foreach( $where as $key => $exprs ) {
      if( count($exprs) ) {
				$tmp[] = '('.implode(' OR ',$exprs).')';
      }
    }
    if( count($tmp) ) {
      $query .= ' WHERE ' . implode(' AND ',$tmp);
    }
    $query .= ' ORDER BY name ASC';
    
    // execute the query
    $this->_rs = $db->SelectLimit($query,$this->_limit,$this->_offset);
    if( !$this->_rs ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }
    $this->_totalmatchingrows = $db->GetOne('SELECT FOUND_ROWS()');
  }

  public function RecordCount()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->RecordCount();
  }

  public function MoveNext()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->MoveNext();
  }

  public function MoveFirst()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->MoveFirst();
  }

  public function Rewind()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->MoveFirst();
  }

  public function MoveLast()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->MoveLast();
  }

  public function EOF()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->EOF();
    return TRUE;
  }

  public function Close()
  {
    $this->execute();
    if( $this->_rs ) return $this->_rs->Close();
    return TRUE;
  }

  public function &GetTemplate()
  {
    $this->execute();
    if( !$this->_rs ) {
      throw new CmsLogicException('Cannot get template from invalid template query object');
    }

    return CmsLayoutTemplate::load($this->_rs->_fields['id']);
  }

  public function GetMatchedTemplateIds()
  {
    $this->execute();
    if( !$this->_rs ) {
      throw new CmsLogicException('Cannot get template from invalid template query object');
    }

    $out = array();
    while( !$this->EOF() ) {
      $out[] = $this->fields['id'];
      $this->MoveNext();
    }
    $this->Rewind();
    return $out;
  }

  public function GetMatches()
  {
    return CmsLayoutTemplate::load_bulk($this->GetMatchedTemplateIds());
  }

  public function __get($key)
  {
    $this->execute();
    if( $key == 'fields' && $this->_rs && !$this->_rs->EOF() ) {
      return $this->_rs->fields;
    }
    if( $key == 'EOF' ) {
      return $this->_rs->EOF();
    }
    if( $key == 'limit' ) {
      return $this->_limit;
    }
    if( $key == 'offset' ) {
      return $this->_offset;
    }
    if( $key == 'totalrows' ) {
      return $this->_totalmatchingrows;
    }
    if( $key == 'numpages' ) {
      return ceil($this->_totalmatchingrows / $this->_limit);
    }
  }
}

#
# EOF
#
?>