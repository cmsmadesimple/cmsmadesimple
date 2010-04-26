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
#$Id$

/**
 * Classes for storing the frontend page hierarchy.  Includes methods to load
 * the structure from the database and properly return related Content objects.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsPageTree extends CmsTree
{
	static private $content = array();
	static private $instance = NULL;

	function __construct()
	{
		parent::__construct();
		$this->root = new CmsNode();
		$this->root->tree = $this;
		$this->load_child_nodes(); //Fill the base of the tree
	}
	
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsPageTree();
		}
		return self::$instance;
	}
	
	function getRootNode()
	{
		return $this->root;
	}

	public function load_child_nodes($parent_id = -1, $lft = -1, $rgt = -1)
	{
		$pages = array();

		if ($lft == -1 && $rgt == -1)
		{
			$pages = CmsContentOperations::LoadMultipleFromParentId($parent_id);
		}
		else
		{
			$pages = CmsContentOperations::LoadMultipleFromLeftAndRight($lft, $rgt);
		}

		//var_dump(count($pages));
		//stack_trace(); echo '<br/>';
		//debug_print_backtrace();
		
		foreach ($pages as $page)
		{
			if ($page->parent_id > -1)
			{
				$parent_node = $this->get_node_by_id($page->parent_id);
				if ($parent_node != null)
				{
					$parent_node->add_child($page);
					self::$content[(string)$page->id] = $page; //Put a reference up so we can quickly check to see if it's loaded already
					$parent_node->children_loaded = true;
				}
			}
			else
			{
				$this->root->add_child($page);
				self::$content[(string)$page->id] = $page;
				$this->root->children_loaded = true;
			}
		}
	}
	
	public function load_parent_nodes($id)
	{
		//TODO: Optimize this more -- right now we're just making sure it works
		//First we find the page.  If it exists, we then grab the great-great-grandparent
		//and load all of the nodes in between.
		$page = cms_orm('CmsPage')->find_by_id($id);
		//$page = CmsContentOperations::LoadContentFromId($id);
		if ($page)
		{
			$ancestor = null;
			$top_nodes = $this->root->get_children();
			foreach ($top_nodes as $one_node)
			{
				//Don't bother doing this if we're only level 2
				if ($one_node->lft < $page->lft && $one_node->rgt > $page->rgt && $one_node->id != $page->id)
				{
					$ancestor = $one_node;
					break;
				}
			}
			if ($ancestor != null)
			{
				$this->load_child_nodes(-1, $ancestor->lft, $ancestor->rgt);
			}
		}
	}
	
	public function get_node_by_id($id)
	{
		if ($id)
		{
			if ($id == -1)
			{
				return $this->get_root_node();
			}
			else if (array_key_exists((string)$id, self::$content))
			{
				return self::$content[(string)$id];
			}
			else
			{
				$this->load_parent_nodes($id);
				return self::$content[(string)$id];
			}
		}
	}
	
	function getNodeByID($id)
	{
		return $this->get_node_by_id($id);
	}
	
	function sureGetNodeByID($id)
	{
		return $this->get_node_by_id($id);
	}
	
	public function get_node_by_alias($alias)
	{
		$result = null;
		//$id = CmsCache::get_instance()->call('CmsContentOperations::get_page_id_from_alias', $alias);
		$id = CmsContentOperations::GetPageIdFromAlias($alias);
		if ($id)
		{
			$result = $this->get_node_by_id($id);
		}
		return $result;
	}
	
	function getNodeByAlias($alias)
	{
		return $this->get_node_by_alias($alias);
	}
	
	function sureGetNodeByAlias($alias)
	{
		return $this->get_node_by_alias($alias);
	}
	
	function get_node_by_hierarchy($position)
	{
		$result = null;
		//$id = CmsCache::get_instance()->call('CmsContentOperations::get_page_id_from_hierarchy', $position);
		$id = CmsContentOperations::GetPageIdFromHierarchy($position);
		if ($id)
		{
			$result = $this->get_node_by_id($id);
		}
		return $result;
	}
	
	function getNodeByHierarchy($position)
	{
		return $this->get_node_by_hierarchy($position);
	}
}

# vim:ts=4 sw=4 noet
?>