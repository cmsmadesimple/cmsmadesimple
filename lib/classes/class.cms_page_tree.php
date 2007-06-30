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
	static public $content = array();
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
			$pages = cms_orm()->content->find_all_by_parent_id($parent_id, array('order' => 'lft ASC'));
		}
		else
		{
			$pages = cms_orm()->content->find_all(array('conditions' => array('lft > ? AND rgt < ?', $lft, $rgt), 'order' => 'lft ASC'));
		}
		
		foreach ($pages as $page)
		{
			$parent_node = $this->get_node_by_id($page->parent_id);
			if ($parent_node != null)
			{
				$parent_node->add_child($page);
				self::$content[(string)$page->id] = $page; //Put a reference up so we can quickly check to see if it's loaded already
				$parent_node->children_loaded = true;
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
				//TODO: Optimize this more -- right now we're just making sure it works
				//First we find the page.  If it exists, we then grab the great-great-grandparent
				//and load all of the nodes in between.
				$page = cms_orm()->content->find_by_id($id);
				if ($page)
				{
					$ancestor = null;
					$top_nodes = $this->tree->get_root_node()->get_children();
					foreach ($top_nodes as $one_node)
					{
						if ($one_node->lft < $page->lft && $one_node->rgt > $page->rgt && $one_node->id != $page->id) //Don't bother doing this if we're only level 2
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
		$id = CmsCache::get_instance()->call('CmsContentOperations::get_page_id_from_alias', $alias);
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
		$id = CmsCache::get_instance()->call('CmsContentOperations::get_page_id_from_hierarchy', $position);
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

class CmsPageNode extends CmsNode
{
	var $id = -1;
	var $active = false;
	var $show_in_menu = false;
	var $children_loaded = false;
	
	function __construct($id = -1, $active = false, $show_in_menu = false)
	{
		parent::__construct();
		$this->id = $id;
		$this->active = $active;
		$this->show_in_menu = $show_in_menu;
	}
	
	function getParentNode()
	{
		return $this->parentnode;
	}
	
	function get_content()
	{
		$content = null;
		
		$tree = $this->get_tree();
		if (array_key_exists($this->id, $tree->content))
		{
			$content = $tree->content[$this->id];
		}
		else
		{
			$content = cmsms()->content_base->find_by_id($this->id);
			$tree->content[$this->id] = $content;
		}
		return $content;
	}
	
	function &get_children()
	{
		if ($this->has_children())
		{
			//We know there are children, but no nodes have been
			//created yet.  We should probably do that.
			if (!$this->children_loaded())
			{
				$content = $this->get_content();
				var_dump($content);
				if ($content != null)
				{
					CmsContentOperations::create_page_tree($content->id, $content->lft, $content->rgt);
				}
			}
			$node = null;
			//It seems like the first child is already loaded elsewhere
			//So we trick it a bit to make sure we get all the children
			if (count($this->children) > 1)
				$node = $this->children[1];
			else
				$node = $this->children[0];
			$checkid = $node->id;
			$tree = $this->get_tree();
			if (!array_key_exists($checkid, $tree->content))
			{
				CmsContentOperations::load_children_into_tree($this->id, $this->tree);
			}
		}
		return $this->children;
	}
	
	public function getContent()
	{
		return $this->get_content();
	}
	
    public function hasChildren()
	{
        return $this->has_children();        
    }
    
    public function &getChildren()
	{
		$result =& $this->get_children();
        return $result;
    }

	function getTag()
	{
		return $this->id;
	}
	
	public function children_loaded()
	{
		return $this->children_loaded;
	}
}

# vim:ts=4 sw=4 noet
?>