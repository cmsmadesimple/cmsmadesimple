<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id$

/**
 * Class for handling a one-to-many assocation.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsHasAndBelongsToManyAssociation extends CmsObjectRelationalAssociation implements ArrayAccess, Iterator
{
	var $children = array();
	var $child_class = '';
	var $join_table = '';
	var $join_other_id_field = '';
	var $join_this_id_field = '';

	/**
	 * Create a new has_many association.
	 *
	 * @author Ted Kulp
	 **/
	public function __construct(&$parent_class)
	{
		parent::__construct($parent_class);
	}
	
	/**
	 * Returns the associated has_many association's objects.
	 *
	 * @return array Any array of objects, if they exist.
	 * @author Ted Kulp
	 **/
	public function get_data()
	{
		$this->fill_data();
		return $this;
	}
	
	private function fill_data()
	{
		if (!$this->loaded && $this->child_class != '' && $this->join_table != '')
		{
			$class = cmsms()->{$this->child_class};
			$table = $class->get_table();
			$other_id_field = $class->id_field;
			
			if ($this->parent_class->{$this->parent_class->id_field} > -1)
			{
				$queryattrs = $this->extra_params;
				$queryattrs['joins'] = "INNER JOIN {$this->join_table} ON {$this->join_table}.{$this->join_other_id_field} = {$table}.{$other_id_field} AND {$this->join_table}.{$this->join_this_id_field} = " . $this->parent_class->{$this->parent_class->id_field};
				$this->children = $class->find_all($queryattrs);
			}
			$this->loaded = true;
		}
		return $this->children;
	}
	
	function count()
	{
		$ary = $this->fill_data();
		return count($ary);
	}
	
	//Region ArrayAccess
	function offsetExists($offset)
	{
		$ary = $this->fill_data();
		return ($offset < count($ary));
	}

	function offsetGet($offset)
	{
		$this->fill_data();
		//print_r($this->children[$offset]);
		return $this->children[$offset];
	}

	function offsetSet($offset,$value)
	{
		throw new Exception("This collection is read only.");
		//$ary = $this->fill_data();
		//$ary[$offset] = $value;
	}

	function offsetUnset($offset)
	{
		throw new Exception("This collection is read only.");
		//$ary = $this->fill_data();
		//unset($ary[$offset]);
	}
	//EndRegion
	
	//Region Iterator
	function current()
	{
		return $this->offsetGet($this->currentIndex);
	}

	function key()
	{
		return $this->currentIndex;
	}

	function next()
	{
		return $this->currentIndex++;
	}

	function rewind()
	{
		$this->currentIndex = 0;
	}

	function valid()
	{
		return ($this->offsetExists($this->currentIndex));
	}

	function append($value)
	{
		throw new Exception("This collection is read only");
	}

	function getIterator()
	{
		return $this;
	}
	//EndRegion
}

# vim:ts=4 sw=4 noet
?>