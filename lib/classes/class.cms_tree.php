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

class CmsTree extends CmsObject
{
	var $root = null;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function get_root_node()
	{
		return $this->root;
	}
	
	function create_node()
	{
		$node = new CmsNode();
		$node->tree = $this;
		return $node;
	}
	
	function add_child($node)
	{
		$node->set_parent($this->root);
		$this->root->children[] = $node;
	}
	
	function &get_flat_list()
	{
		$temp_var = $this->get_root_node()->get_flat_list();
		return $temp_var;
	}
	
	function &getFlatList()
	{
		return $this->get_flat_list();
	}
}

class CmsNode extends CmsObject
{
	var $tree = null;
	var $parentnode = null;
	var $children = array();
	
	function __construct()
	{
		parent::__construct();
	}
	
	function add_child($node)
	{
		$node->set_parent($this);
		$node->tree = $this->tree;
		$this->children[] = $node;
	}
	
	function get_tree()
	{
		return $this->tree;
	}
	
	function depth()
	{
		$depth = 0;
		$currLevel = &$this;

		while ($currLevel->parentnode)
		{
			$depth++;
			$currLevel = &$currLevel->parentnode;
		}
		
		return $depth;
	}
	
	function get_level()
	{
		return $this->depth();
	}
	
	function getLevel()
	{
		return $this->depth();
	}
	
	function get_parent()
	{
		return $this->parentnode;
	}
	
	function set_parent($node)
	{
		$this->parentnode = $node;
	}
	
	function has_children()
	{
		return count($this->children) > 0;
	}
	
	function get_children_count()
	{
		return count($this->children);
	}
	
	function getChildrenCount()
	{
		return $this->get_children_count();
	}

	function &get_children()
	{
		return $this->children;
	}
	
    function get_flat_list()
    {
        $return = array();

		if ($this->has_children())
		{
			for ($i=0; $i<count($this->children); $i++)
			{
				$return[] = &$this->children[$i];
				$return = array_merge($return, $this->children[$i]->get_flat_list());
			}
		}
        
        return $return;
    }

	function getFlatList()
	{
		return $this->get_flat_list();
	}
}

# vim:ts=4 sw=4 noet
?>