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

define('CMS_CONTENT_HIDDEN_NAME','--------');
/**
 * Base content type class.  Extend this to create new content types for
 * the system.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsContentBase extends CmsObjectRelationalMapping
{
	var $table = 'content';
	var $params = array('id' => -1, 'template_id' => -1, 'active' => true, 'default_content' => false, 'parent_id' => -1, 'lft' => 1, 'rgt' => 1);
	var $field_maps = array('content_alias' => 'alias', 'titleattribute' => 'title_attribute', 'accesskey' => 'access_key', 'tabindex' => 'tab_index', 'content_name' => 'name', 'content_id' => 'id');
	var $id_field = 'content_id';
	var $sequence = 'content_seq';
	var $unused_fields = array();

	var $mProperties = array();

	var $preview = false;
	
	var $props_loaded = false;
	
	#Stuff needed to do a doppleganger for CmsNode -- Multiple inheritence would rock right now
	var $tree = null;
	var $parentnode = null;
	var $children = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setup()
	{
		$this->create_belongs_to_association('template', 'cms_template', 'template_id');
		$this->assign_acts_as('NestedSet');
		//$this->assign_acts_as('Acl');
	}
	
	/*
	function __sleep()
	{
		//This way if we serialize out to, say, a versioning piece,
		//all the properties are stored as well...
		$this->load_properties();
		return array_merge(parent::__sleep(), array('mProperties', 'props_loaded', 'preview', 'unused_fields', 'field_maps', 'table', 'params'));
	}
	*/
	
	/**
	 * Method to override for indexing content in the search index.  If this is not overridden,
	 * then the content type won't be searchable.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public function index()
	{
	}
	
	public function friendly_name()
	{
		return '';
	}
	
	protected function before_save()
	{
		//CmsEvents::send_event('Core', 'ContentEditPre', array('content' => &$this));
	}
	
	protected function after_save(&$result)
	{
		if ($result)
		{
			$concat = '';
			foreach ($this->mProperties as &$prop)
			{
				if ($prop->content_id != $this->id)
					$prop->content_id = $this->id;
				$prop->save();
				$concat .= $prop->content;
			}
		
			if ($concat != '')
				CmsContentOperations::do_cross_reference($this->id, 'content', $concat);
		
			//CmsEvents::send_event('Core', 'ContentEditPost', array('content' => &$this));
			CmsCache::clear();
		}
	}

	
	protected function validate()
	{
		$this->validate_numericality_of('parent_id',lang('invalidparent'));
		$this->validate_not_blank('name', lang('nofieldgiven',array(lang('title'))));
		$this->validate_not_blank('menu_text', lang('nofieldgiven',array(lang('menutext'))));
		$this->validate_not_blank('menu_text', lang('nofieldgiven',array(lang('menutext'))));
		$this->validate_not_blank('alias', lang('nofieldgiven',array(lang('alias'))));
	}
	

	public function set_property_value($name, $value, $lang = 'en_US')
	{
		$this->load_properties(false);

		foreach ($this->mProperties as &$prop)
		{
			if ($prop->prop_name == $name && $prop->language == $lang)
			{
				$prop->content = $value;

				if (!$this->has_property($name))
					$this->prop_names = implode(',', array_merge(explode(',', $this->prop_names), array($name)));

				return;
			}
		}
		
		//No property exists
		$newprop = new CmsContentProperty();
		$newprop->prop_name = $name;
		$newprop->content = $value;
		$newprop->language = $lang;
		$this->mProperties[] = $newprop;
		
		if (!$this->has_property($name))
			$this->prop_names = implode(',', array_merge(explode(',', $this->prop_names), array($name)));
	}
	
	public function get_property_names($lang = 'en_US')
	{
		return explode(',', $this->prop_names);
	}
	
	public function get_loaded_property_names($lang = 'en_US')
	{
		$result = array();
		foreach ($this->mProperties as &$prop)
		{
			$result[] = $prop->prop_name;
		}
		return $result;
	}
	
    /**
     * Does this have children?
     */
	public function has_children()
	{
		return $this->rgt > ($this->lft + 1);
	}
	
	public function has_property($name)
	{
		return in_array($name, explode(',', $this->prop_names));
	}
	
	public function get_sibling_count()
	{
		if ($this->get_parent()->has_children())
		{
			return $this->get_parent()->get_children_count();
		}
		return 0;
	}
	
	public function load_properties($force = true)
	{
		if ($force || $this->prop_names != '' && count($this->mProperties) == 0)
		{
			$props = cms_orm('CmsContentProperty')->find_all_by_content_id($this->id);
			foreach ($props as &$prop)
			{
				//Make sure we don't overwrite any newly set properties
				if (!in_array($prop->name, $this->get_loaded_property_names()))
				{
					$this->mProperties[] = $prop;
				}
			}
		}
	}
	
	public function get_property_value($name, $lang = 'en_US')
	{
		//See if it exists...
		if ($this->has_property($name))
		{
			//Loop through and see if it's loaded
			foreach ($this->mProperties as &$prop)
			{
				if ($prop->prop_name == $name && $prop->language == $lang)
				{
					if ($prop->content != '')
					{
						return $prop->content;
					}
				}
			}
			
			$this->load_properties(false);
			
			//Loop through and see if it's loaded now
			foreach ($this->mProperties as &$prop)
			{
				if ($prop->prop_name == $name && $prop->language == $lang)
				{
					if ($prop->content != '')
					{
						return $prop->content;
					}
				}
			}
		}
		
		return '';
	}
	
	public function shift_position($direction = 'up')
	{
		if ($direction == 'up')
			$this->move_up();
		else
			$this->move_down();
		
		CmsContentOperations::set_all_hierarchy_positions();
		CmsCache::clear();
	}
	
    public function get_url($rewrite = true, $lang = '')
    {
		$config = cms_config();
		$url = "";
		$alias = ($this->alias != ''?$this->alias:$this->id);
		if ($config["assume_mod_rewrite"] && $rewrite == true)
		{
			$url = $config['root_url']. '/' . ($lang != '' ? "$lang/" : '') . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
		}
		else
		{
		    if (isset($_SERVER['PHP_SELF']) && $config['internal_pretty_urls'] == true)
		    {
				$url = $config['root_url'] . '/index.php/' . ($lang != '' ? "$lang/" : '') . $this->HierarchyPath() . (isset($config['page_extension']) ? $config['page_extension'] : '.html');
		    }
		    else
		    {
				$url = $config['root_url'] . '/index.php?' . $config['query_var'] . '=' . $alias . ($lang != '' ? '&amp;lang=' . $lang : '');
		    }
		}
		return $url;
    }

	public function set_alias($alias = '')
	{
		$config = cms_config();

		$tolower = false;

		if ($alias == '' && $config['auto_alias_content'] == true)
		{
			/*
			$alias = trim($this->get_property_value('menu_text', CmsMultiLanguage::get_default_language()));
			if ($alias == '')
			{
			    $alias = trim($this->get_property_value('name', CmsMultiLanguage::get_default_language()));
			}
			*/
			$alias = trim($this->params['name']);
			if ($alias == '')
			{
				$alias = trim($this->params['menu_text']);
			}
			
			$tolower = true;
			$alias = munge_string_to_url($alias, $tolower);

			// Make sure auto-generated new alias is not already in use on a different page, if it does, add "-2" to the alias
			$error = cmsms()->GetContentOperations()->CheckAliasError($alias);
			if ($error !== FALSE)
			{
				if (FALSE == empty($alias))
				{
					$alias_num_add = 2;
					// If a '-2' version of the alias already exists
					// Check the '-3' version etc.
					while (cmsms()->GetContentOperations()->CheckAliasError($alias.'-'.$alias_num_add) !== FALSE)
					{
						$alias_num_add++;
					}
					$alias .= '-'.$alias_num_add;
				}
				else
				{
					$alias = '';
				}
			}
		}

		$this->params['alias'] = munge_string_to_url($alias, $tolower);
	}
	
    /**
     * Function content types to use to say whether or not they should show
     * up in lists where parents of content are set.  This will default to true,
     * but should be used in cases like Separator where you don't want it to 
     * have any children.
     * 
     * @since 0.11
     */
	public function wants_children()
	{
		return true;
	}

    /**
     * Should this link be used in various places where a link is the only
     * useful output?  (Like next/previous links in cms_selflink, for example)
     */
	public function has_usable_link()
	{
		return true;
	}

	public function is_default_possible()
	{
		return true;
	}

	/**
	 * Checks to see if this conte type uses the given field.
	 */
	public function field_used($name)
	{
		return !in_array($name, $this->unused_fields);
	}
	
	protected function before_delete()
	{
		Events::SendEvent('Core', 'ContentDeletePre', array('content' => &$this));
	}
	
	protected function after_delete()
	{
		$items =& cmsms()->content_property->find_all_by_content_id($this->id);
		foreach ($items as &$item)
		{
			$item->delete();
		}
		
		#Remove the cross references
		CmsContentOperations::remove_cross_references($this->id, 'content');
		
		CmsEvents::SendEvent('Core', 'ContentDeletePost', array('content' => &$this));
		CmsCache::clear();
	}
	
	public function template_name()
	{
		try
		{
			return cmsms()->template->find_by_template_id($this->template_id)->name;
		}
		catch (Exception $e)
		{
			return '';
		}
	}
	
	public function add_child($node)
	{
		$node->set_parent($this);
		$node->tree = $this->tree;
		$this->children[] = $node;
	}
	
	public function get_tree()
	{
		return $this->tree;
	}
	
	public function depth()
	{
		if ($this->hierarchy)
		{
			return count(explode('.', $this->hierarchy)) - 1;
		}
		else
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
	}
	
	public function get_level()
	{
		return $this->depth();
	}
	
	public function get_parent()
	{
		return $this->parentnode;
	}
	
	public function set_parent($node)
	{
		$this->parentnode = $node;
	}
	
	public function get_children_count()
	{
		return count($this->children);
	}
	
	public function getChildrenCount()
	{
		return $this->get_children_count();
	}

	public function &get_children()
	{
		if ($this->has_children())
		{
			//We know there are children, but no nodes have been
			//created yet.  We should probably do that.
			if (!$this->children_loaded())
			{
				//$this->tree->load_child_nodes(-1, $this->lft, $this->rgt);
				$this->tree->load_child_nodes($this->id);
			}
		}
		return $this->children;
	}
	
    public function &get_flat_list()
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

	// ??
	public function get_content()
	{
		return $this;
	}
	
}


?>
