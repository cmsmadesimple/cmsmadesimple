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
	var $content = array();

	function __construct()
	{
		parent::__construct();
		$this->root = new CmsPageNode();
		$this->root->tree = $this;
	}
	
	function getRootNode()
	{
		return $this->root;
	}
	
    public function fill_from_db($dbresult)
    {
        $nodelist = array();
		$separator = '.';

		while ($row = $dbresult->FetchRow())
		{
			$data = $row['id_hierarchy'];
			$active = $row['active'];
			$show_in_menu = $row['show_in_menu'];
            $pathparts = explode($separator, $data);

            if (count($pathparts) == 1)
			{
                if (!empty($nodelist[$pathparts[0]]))
				{
                    continue;
                }
				else
				{
					$nodelist[$pathparts[0]] = $this->create_node($pathparts[0], $active, $show_in_menu);
                    $this->get_root_node()->add_child($nodelist[$pathparts[0]]);
                }
            }
			else
			{
                $parentObj = $this->get_root_node();

                for ($j=0; $j<count($pathparts); $j++)
				{
                    $currentPath = implode($separator, array_slice($pathparts, 0, $j + 1));
                    if (!empty($nodelist[$currentPath]))
					{
                        $parentObj = $nodelist[$currentPath];
                        continue;
                    }
					else
					{
						$nodelist[$currentPath] = $this->create_node($pathparts[$j], $active, $show_in_menu);
                        $parentObj = $parentObj->add_child($nodelist[$currentPath]);
                    }
                }
            }
		}
    }
	
	function create_node($id = -1, $active = false, $show_in_menu = false)
	{
		$node = new CmsPageNode($id, $active, $show_in_menu);
		$node->tree = $this;
		return $node;
	}
	
	function get_node_by_id($id)
	{
		$result = null;

		if ($id)
		{
			$flatlist =& $this->get_flat_list();
			foreach ($flatlist as &$node)
			{
				if ($id == $node->id)
				{
					$result = $node;
					break;
				}
			}
		}

		return $result;
	}
	
	function getNodeByID($id)
	{
		return $this->get_node_by_id($id);
	}
	
	function sureGetNodeByID($id)
	{
		return $this->get_node_by_id($id);
	}
	
	function get_node_by_alias($alias)
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
}

# vim:ts=4 sw=4 noet
?>