<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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

class CmsAssociationCollection extends CmsObject implements ArrayAccess, Iterator
{
	var $children = array();
	var $currentIndex = 0;

	function __construct()
	{
		parent::__construct();
	}
	
	function count()
	{
		return count($this->children);
	}
	
	//Region ArrayAccess
	function offsetExists($offset)
	{
		return ($offset < $this->count());
	}

	function offsetGet($offset)
	{
		return $this->offsetExists($offset) ? $this->children[$offset] : null;
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
}

# vim:ts=4 sw=4 noet
?>