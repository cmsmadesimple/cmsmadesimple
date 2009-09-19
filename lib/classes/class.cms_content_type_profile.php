<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id: class.cms_content_base.php 5976 2009-09-07 14:23:29Z wishy $

class CmsContentTypeProfile extends CmsObject
{
  private $_tabs = array();
  private $_attrs = array();

  public function add_tab($tabname,$permission='')
  {
    if( !isset($this->_tabs[$tabname]) )
    {
		$this->_tabs[$tabname] = $permission;
    }
  }

  public function add_attribute($attr)
  {
    if( !is_a($attr,'CmsContentTypeProfileAttribute') )
      return;

    if( !in_array($attr->get_tab(),array_keys($this->_tabs)) )
      $this->add_tab($attr->get_tab());

    // if the attribute already exists... we're gonna replace it
    $obj =& $this->find_by_name($attr->get_name());
    if( $obj )
	{
		$obj = $attr;
		return;
	}
    $this->_attrs[] = $attr;
  }


  public function remove_by_name($attrname)
  {
    $new_attrs = array();
    for( $i = 0; $i < count($this->_attrs); $i++ )
	{
		if( $this->_attrs[$i]->get_name() != $attrname )
		{
			$new_attrs[] = $this->_attrs[$i];
		}
	}
    $this->_attrs = $new_attrs;
  }


  public function &find_by_name($attrname)
  {
    for( $i = 0; $i < count($this->_attrs); $i++ )
    {
		if( $this->_attrs[$i]->get_name() == $attrname )
			return $this->_attrs[$i];
    }

    $tmp = null;
    return $tmp;
  }


  public function find_all_by_tab($tabname)
  {
    $results = array();
    for( $i = 0; $i < count($this->_attrs); $i++ )
    {
		if( $this->_attrs[$i]->get_tab() == $tabname )
		{
			$results[] = $this->_attrs[$i];
		}
	}
    if( !$results ) return FALSE;

    // sort this array
    usort($results, array('CmsContentTypeProfileAttribute','compare'));

    return $results;
  }


  public function get_tab_list()
  {
    return $this->_tabs;
  }

} // end of class


class CmsContentTypeProfileAttribute
{
  private $_name;
  private $_tab;
  private $_priority;
  private $_permission;

  public function __construct($name,$tab,$priority = 1,$permission = '')
  {
    $this->_name = $name;
    $this->_tab  = $tab;
    $this->_priority = (int)$priority;
    $this->_permission = $permission;
  }

  public function get_name()
  {
    return $this->_name;
  }

  public function get_tab()
  {
    return $this->_tab;
  }

  public function get_priority()
  {
    return $this->_priority;
  }

  public function get_permission()
  {
    return $this->_permission;
  }

  public static function compare($a,$b)
  {
    if( $a->_priority < $b->_priority ) return -1;
    if( $a->_priority > $b->_priority ) return 1;
    if( $a->_name < $b->_name ) return -1;
    if( $a->_name > $b->_name ) return 1;
    return 0;
  }
}

#
# EOF
#
?>