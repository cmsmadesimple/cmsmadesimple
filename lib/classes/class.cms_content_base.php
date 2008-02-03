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
	var $field_maps = array('content_alias' => 'alias', 'titleattribute' => 'title_attribute', 'accesskey' => 'access_key', 'tabindex' => 'tab_index');
	var $unused_fields = array();

	var $mProperties = array();

	var $preview = false;
	
	var $props_loaded = false;
	
	#Stuff needed to do a doppleganger for CmsNode -- Multiple inheritence would rock right now
	var $tree = null;
	var $parentnode = null;
	var $children = array();
	
	function __construct()
	{
		parent::__construct();
		$this->create_belongs_to_association('template', 'cms_template', 'template_id');
		//$this->assign_acts_as('Test');
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
	
	public function check_permission($userid = null)
	{
		$user = CmsLogin::get_current_user();

		if ($userid == null) 
		{
			cmsms()->user->find_by_id($userid);
		}
		else if ($userid == -1)
		{
			$user = new CmsAnonymousUser();
		}

		return CmsAcl::check_permission('Core', 'Page', 'View', $this->id, null, $user);
	}
	
	function friendly_name()
	{
		return '';
	}
	
	function before_save()
	{
		//$this->prop_names = implode(',', $this->get_loaded_property_names());
		$db = cms_db();

		if ($this->id == -1)
		{			
			$this->make_space_for_child();
		}
		else
		{
			$old_content = cms_orm()->content->find_by_id($this->id);
			if ($old_content)
			{
				if ($this->parent_id != $old_content->parent_id)
				{
					//TODO: Make this whole bit a transaction (after_save included)

					//First reorder
					$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
					$result = $db->Execute($query, array($old_content->parent_id, $old_content->item_order));
					
					//Flip all children over to the negative side so they don't get in the way
					$query = 'UPDATE ' . cms_db_prefix() . 'content SET lft = (lft * -1), rgt = (rgt * -1) WHERE lft > ? AND rgt < ?';
					$result = $db->Execute($query, array($old_content->lft, $old_content->rgt));
					
					//Then move lft and rgt so that this node doesn't "exist"
					$diff = $old_content->rgt - $old_content->lft + 1;

					$query = 'UPDATE ' . cms_db_prefix() . 'content SET lft = (lft - ?) WHERE lft > ?';
					$result = $db->Execute($query, array($diff, $old_content->lft));
					
					$query = 'UPDATE ' . cms_db_prefix() . 'content SET rgt = (rgt - ?) WHERE rgt > ?';
					$result = $db->Execute($query, array($diff, $old_content->rgt));
					
					//Now make a new hole under the new child
					$this->make_space_for_child();
					
					//Update the ones currently in the negative space the distance that we've moved
					$moved_by_how_much = $old_content->lft - $this->lft;
					$query = 'UPDATE ' . cms_db_prefix() . 'content SET lft = (lft + ?), rgt = (rgt + ?) WHERE lft < 0 AND rgt < 0';
					$result = $db->Execute($query, array($moved_by_how_much, $moved_by_how_much));
					
					//And flip those back over to the positive side...  hopefully in the correct place now
					$query = 'UPDATE ' . cms_db_prefix() . 'content SET lft = (lft * -1), rgt = (rgt * -1) WHERE lft < 0 AND rgt < 0';
					$result = $db->Execute($query);
				}
			}
		}
		
		CmsEvents::send_event('Core', 'ContentEditPre', array('content' => &$this));
	}
	
	function make_space_for_child()
	{
		$db = cms_db();

		$diff = $this->rgt - $this->lft;
		if ($diff < 2)
		{
			$diff = 1;
		}
		$right = $db->GetOne('SELECT max(rgt) FROM ' . cms_db_prefix() . 'content');
		
		if ($this->parent_id > -1)
		{
			$row = $db->GetRow('SELECT rgt FROM ' . cms_db_prefix() . 'content WHERE id = ?', array($this->parent_id));
			if ($row)
			{
				$right = $row['rgt'];
			}

			$db->Execute("UPDATE ".cms_db_prefix()."content SET lft = lft + ? WHERE lft > ?", array($diff + 1, $right));
			$db->Execute("UPDATE ".cms_db_prefix()."content SET rgt = rgt + ? WHERE rgt >= ?", array($diff + 1, $right));
		}
		else
		{
			$right = $right + $diff;
		}
		
		$this->lft = $right;
		$this->rgt = $right + $diff;
		
		$query = "SELECT max(item_order) as new_order FROM ".cms_db_prefix()."content WHERE parent_id = ?";
		$row = &$db->GetRow($query, array($this->parent_id));
		if ($row)
		{
			if ($row['new_order'] < 1)
			{
				$this->item_order = 1;
			}
			else
			{
				$this->item_order = $row['new_order'] + 1;
			}
		}
	}
	
	function after_save()
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
		
		CmsEvents::send_event('Core', 'ContentEditPost', array('content' => &$this));
		CmsCache::clear();
	}
	
	function validate()
	{
		//$this->validate_not_blank('name', lang('nofieldgiven',array(lang('title'))));
		//$this->validate_not_blank('menu_text', lang('nofieldgiven',array(lang('menutext'))));
	}
	
	/**
	 * Overloaded so that we can pull out properties and set them separately
	 */
	function update_parameters($params, $lang = 'en_US', $strip_slashes = false)
	{
		if (isset($params['property']) && is_array($params['property']))
		{
			foreach ($params['property'] as $k=>$v)
			{
				if ($strip_slashes && is_string($v)) $v = stripslashes($v);
				$this->set_property_value($k, $v, $lang);
			}
		}
		parent::update_parameters($params, $strip_slashes);
	}
	
	function set_property_value($name, $value, $lang = 'en_US')
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
	
	function get_property_names($lang = 'en_US')
	{
		return explode(',', $this->prop_names);
	}
	
	function get_loaded_property_names($lang = 'en_US')
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
	function has_children()
	{
		return $this->rgt > ($this->lft + 1);
	}
	
	function has_property($name)
	{
		return in_array($name, explode(',', $this->prop_names));
	}
	
	function load_properties($force = true)
	{
		if ($force || $this->prop_names != '' && count($this->mProperties) == 0)
		{
			$props = cmsms()->content_property->find_all_by_content_id($this->id);
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
	
	function get_property_value($name, $lang = 'en_US')
	{
		$default_lang = CmsMultiLanguage::get_default_language();

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
		
		if ($lang != $default_lang)
			return $this->get_property_value($name, $default_lang);
		else
			return '';
	}
	
	function add_template(&$smarty, $lang = 'en_US')
	{
		return array();
	}
	
	function edit_template(&$smarty, $lang = 'en_US')
	{
		return array();
	}

	/*
	function hierarchy()
	{
		return CmsContentOperations::create_friendly_hierarchy_position($this->hierarchy);
	}
	*/
	
	function shift_position($direction = 'up')
	{
		$new_item_order = $this->item_order;
		if ($direction == 'up')
			$new_item_order--;
		else
			$new_item_order++;

		$other_content = cms_orm()->content->find_by_parent_id_and_item_order($this->parent_id, $new_item_order);
		
		if ($other_content != null)
		{
			$db = cms_db();
			
			$old_lft = $other_content->lft;
			$old_rgt = $other_content->rgt;
			
			//Assume down
			$diff = $this->lft - $old_lft;
			$diff2 = $this->rgt - $old_rgt;

			if ($direction == 'up')
			{
				//Now up
				$diff = $this->rgt - $old_rgt;
				$diff2 = $this->lft - $old_lft;
			}
			
			$time = $db->DBTimeStamp(time());
			
			//Flip me and children into the negative space
			$query = 'UPDATE ' . cms_db_prefix() . 'content SET lft = (lft * -1), rgt = (rgt * -1), modified_date = '.$time.' WHERE lft >= ? AND rgt <= ?';
			$db->Execute($query, array($this->lft, $this->rgt));
			
			//Shift the other content to the new position
			$query = 'UPDATE ' . cms_db_prefix() . 'content SET lft = (lft + ?), rgt = (rgt + ?), modified_date = '.$time.' WHERE lft >= ? AND rgt <= ?';
			$db->Execute($query, array($diff, $diff, $old_lft, $old_rgt));
			
			//Shift me to the new position in the negative space
			$query = 'UPDATE ' . cms_db_prefix() . 'content SET lft = (lft + ?), rgt = (rgt + ?), modified_date = '.$time.' WHERE lft < 0 AND rgt < 0';
			$db->Execute($query, array($diff2, $diff2));
			
			//Flip me back over to the positive side...  hopefully in the correct place now
			$query = 'UPDATE ' . cms_db_prefix() . 'content SET lft = (lft * -1), rgt = (rgt * -1), modified_date = '.$time.' WHERE lft < 0 AND rgt < 0';
			$result = $db->Execute($query);
			
			//Now flip the item orders
			$query = 'UPDATE ' . cms_db_prefix() . 'content SET item_order = ?, modified_date = '.$time.' WHERE id = ?';
			$db->Execute($query, array($other_content->item_order, $this->id));
			$db->Execute($query, array($this->item_order, $other_content->id));
			
			$this->lft = $this->lft - $diff2;
			$this->rgt = $this->rgt - $diff2;
			
			$this->item_order = $other_content->item_order;
			
			CmsContentOperations::SetAllHierarchyPositions();
			CmsCache::clear();
		}
	}
	
    function get_url($rewrite = true, $lang = '')
    {
		$config = cms_config();
		$url = "";
		$alias = ($this->alias != ''?$this->alias:$this->id);
		if ($config["assume_mod_rewrite"] && $rewrite == true)
		{
		    if ($config['use_hierarchy'] == true)
		    {
				$url = $config['root_url']. '/' . ($lang != '' ? "$lang/" : '') . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
		    }
		    else
		    {
				$url = $config['root_url']. '/' . ($lang != '' ? "$lang/" : '') . $alias . (isset($config['page_extension'])?$config['page_extension']:'.html');
		    }
		}
		else
		{
		    if (isset($_SERVER['PHP_SELF']) && $config['internal_pretty_urls'] == true)
		    {
				if ($config['use_hierarchy'] == true)
				{
				    $url = $config['root_url'] . '/index.php/' . ($lang != '' ? "$lang/" : '') . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
				}
				else
				{
				    $url = $config['root_url'] . '/index.php/' . ($lang != '' ? "$lang/" : '') . $alias . (isset($config['page_extension'])?$config['page_extension']:'.html');
				}
		    }
		    else
		    {
				$url = $config['root_url'] . '/index.php?' . $config['query_var'] . '=' . $alias . ($lang != '' ? '&amp;lang=' . $lang : '');
		    }
		}
		return $url;
    }

    function GetURL($rewrite = true)
    {
		return $this->get_url($rewrite);
	}

	function set_alias($alias)
	{
		$config = cms_config();

		$tolower = false;

		if ($alias == '' && $config['auto_alias_content'] == true)
		{
			$alias = trim($this->get_property_value('menu_text', CmsMultiLanguage::get_default_language()));
			if ($alias == '')
			{
			    $alias = trim($this->get_property_value('name', CmsMultiLanguage::get_default_language()));
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
	
	function SetAlias($alias)
	{
		return $this->set_alias($alias);
	}

    /**
     * Function content types to use to say whether or not they should show
     * up in lists where parents of content are set.  This will default to true,
     * but should be used in cases like Separator where you don't want it to 
     * have any children.
     * 
     * @since 0.11
     */
	function wants_children()
	{
		return true;
	}

    function WantsChildren()
    {
		return $this->wants_children();
    }

    /**
     * Should this link be used in various places where a link is the only
     * useful output?  (Like next/previous links in cms_selflink, for example)
     */
	function has_usable_link()
	{
		return true;
	}

    function HasUsableLink()
    {
		return $this->has_usable_link();
    }

	function is_default_possible()
	{
		return true;
	}

	function IsDefaultPossible()
	{
		return $this->is_default_possible();
	}

	/**
	 * Checks to see if this conte type uses the given field.
	 */
	function field_used($name)
	{
		return !in_array($name, $this->unused_fields);
	}
	
	function before_delete()
	{
		Events::SendEvent('Core', 'ContentDeletePre', array('content' => &$this));
	}
	
	function after_delete()
	{
		$items =& cmsms()->content_property->find_all_by_content_id($this->id);
		foreach ($items as &$item)
		{
			$item->delete();
		}
		
		#Fix the item_order if necessary
		$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
		$result = cms_db()->Execute($query, array($this->parent_id, $this->item_order));
		
		#Remove the cross references
		CmsContentOperations::remove_cross_references($this->id, 'content');
		
		CmsEvents::SendEvent('Core', 'ContentDeletePost', array('content' => &$this));
		CmsCache::clear();
	}
	
	function template_name()
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
	
    function &get_flat_list()
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

	function &getFlatList()
	{
		$tmp =& $this->get_flat_list();
		return $tmp;
	}
	
	function get_content()
	{
		return $this;
	}
}

/**
 * @deprecated Deprecated.  Use CmsContentBase instead.
 **/
class ContentBase extends CmsContentBase {}

?>