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
 * Classes for storing the admin menu.  Includes methods to load
 * the structure from an xml file and properly create the admin
 * menu structure.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsAdminTree extends CmsTree
{
	static private $instance = NULL;

	function __construct()
	{
		parent::__construct();
		$this->root = new CmsAdminNode();
		$this->root->tree = $this;
	}
	
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsAdminTree();
		}
		return self::$instance;
	}
	
	function create_node()
	{
		$node = new CmsAdminNode();
		$node->tree = $this;
		return $node;
	}
	
	static public function find_node_by_title($title)
	{
		$flatlist = self::get_instance()->get_flat_list();
		foreach ($flatlist as &$onenode)
		{
			if ($title == $onenode->title)
			{
				return $onenode;
			}
		}
		return null;
	}
	
    static public function fill_from_file(&$admin_theme)
    {
		$xml = simplexml_load_file(cms_join_path(ROOT_DIR,'admin','adminmenu.xml'));
		
		$root = self::get_instance()->get_root_node();
		
		foreach ($xml as $node)
		{
			self::create_admin_node($node, $root, $admin_theme);
		}
		
		//print_r($root->tree);
    }

	static public function create_admin_node(&$xml, &$node, &$admin_theme)
	{
		$newnode = new CmsAdminNode();
		$newnode->tree = $node->tree;
		
		if (isset($xml->name))
			$newnode->name = (string)$xml->name;

		if (isset($xml->title))
		{
			$newnode->title = lang((string)$xml->title);
			$newnode->english_title = (string)$xml->title;
		}

		if (isset($xml->url))
			$newnode->url = (string)$xml->url;
			
		if (isset($xml->target))
			$newnode->target = (string)$xml->target;

		if (isset($xml->description))
		{
			if ((string)$xml->description != '')
				$newnode->description = lang((string)$xml->description);
		}

		if (isset($xml->show_in_menu))
		{
			if (isset($xml->show_in_menu->permission))
			{
				$newnode->show_in_menu = $admin_theme->has_permission((string)$xml->show_in_menu->permission);
			}
			else
			{
				$newnode->show_in_menu = ((string)$xml->show_in_menu == 'true' ? true : false);
			}
		}
		
		$node->add_child($newnode);
		
		if (isset($xml->children))
		{
			foreach ($xml->children->node as $child)
			{
				self::create_admin_node($child, $newnode, $admin_theme);
			}
		}
	}
}

class CmsAdminNode extends CmsNode
{
	var $name = '';
	var $title = '';
	var $english_title = '';
	var $url = '';
	var $description = '';
	var $target = '';
	var $icon_url = '';
	var $show_in_menu = true;
	var $first_module = false;
	var $selected = false;
	var $external = false;
	var $module = false;

	function __construct()
	{
		parent::__construct();
	}
}

# vim:ts=4 sw=4 noet
?>