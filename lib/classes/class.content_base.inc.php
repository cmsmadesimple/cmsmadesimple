<?php
# CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Generic content class.
 *
 * As for some treatment we don't need the extra properties of the content
 * we load them only when required. However, each function which makes use
 * of extra properties should first test if the properties object exist.
 *
 * @since		0.8
 * @package		CMS
 */

class ContentBase extends CmsObjectRelationalMapping
{
	var $table = 'content';
	var $params = array('id' => -1, 'template_id' => -1, 'name' => '', 'menu_text' => '', 'active' => true, 'default_content' => false);
	var $field_maps = array('content_name' => 'name', 'content_alias' => 'alias', 'titleattribute' => 'title_attribute', 'accesskey' => 'access_key', 
	'tabindex' => 'tab_index');
	var $unused_fields = array();

	var $mProperties = array();

	var $preview = false;
	
	var $props_loaded = false;
	
	function friendly_name()
	{
		return '';
	}
	
	function before_load($type, $fields)
	{
		cmsms()->GetContentOperations()->LoadContentType($type);
	}
	
	function before_save()
	{
		//$this->prop_names = implode(',', $this->get_loaded_property_names());
		if ($this->id == -1)
		{
			global $gCms;
			$db =& $gCms->GetDb();

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
	}
	
	function after_save()
	{
		foreach ($this->mProperties as &$prop)
		{
			if ($prop->content_id != $this->id)
				$prop->content_id = $this->id;
			$prop->save();
		}
	}
	
	function validate()
	{
		$this->validate_not_blank('name', lang('nofieldgiven',array(lang('title'))));
		$this->validate_not_blank('menu_text', lang('nofieldgiven',array(lang('menutext'))));
	}
	
	/**
	 * Overloaded so that we can pull out properties and set them separately
	 */
	function update_parameters($params)
	{
		if (isset($params['property']) && is_array($params['property']))
		{
			foreach ($params['property'] as $k=>$v)
			{
				$this->set_property_value($k, $v);
			}
		}
		parent::update_parameters($params);
	}
	
	function set_property_value($name, $value)
	{
		if ($this->prop_names != '' && count($this->mProperties) == 0)
			$this->load_properties();

		foreach ($this->mProperties as &$prop)
		{
			if ($prop->prop_name == $name)
			{
				$prop->content = $value;

				if (!$this->has_property($name))
					$this->prop_names = implode(',', array_merge(explode(',', $this->prop_names), array($name)));

				return;
			}
		}
		
		//No property exists
		$newprop = new ContentProperty();
		$newprop->prop_name = $name;
		$newprop->content = $value;
		$this->mProperties[] = $newprop;
		
		if (!$this->has_property($name))
			$this->prop_names = implode(',', array_merge(explode(',', $this->prop_names), array($name)));
	}
	
	function get_loaded_property_names()
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
		$count = cmsms()->content_base->find_count_by_parent_id($this->id);
		
		if (isset($count) && $count > 0)
			return true;
		
		return false;
	}
	
	function has_property($name)
	{
		return in_array($name, explode(',', $this->prop_names));
	}
	
	function load_properties()
	{
		//No go.  Ok, load all of them...
		$props = cmsms()->content_property->find_all_by_content_id($this->id);
		foreach ($props as &$prop)
		{
			//Make sure we don't overwrite any newly set properties
			if (!in_array($prop->name, $this->get_loaded_property_names()))
			{
				$this->mProperties[] =& $prop;
			}
		}
	}
	
	function get_property_value($name)
	{
		//See if it exists...
		if ($this->has_property($name))
		{	
			//Loop through and see if it's loaded
			foreach ($this->mProperties as &$prop)
			{
				if ($prop->prop_name == $name)
				{
					return $prop->content;
				}
			}
			
			$this->load_properties();
			
			//Loop through and see if it's loaded now
			foreach ($this->mProperties as &$prop)
			{
				if ($prop->prop_name == $name)
				{
					return $prop->content;
				}
			}
		}

		return '';
	}
	
	function add_template(&$smarty)
	{
		return array();
	}
	
	function edit_template(&$smarty)
	{
		return array();
	}

	function hierarchy()
	{
		return cmsms()->GetContentOperations()->CreateFriendlyHierarchyPosition($this->hierarchy);
	}
	
    function get_url($rewrite = true)
    {
		$config = config();
		$url = "";
		$alias = ($this->mAlias != ''?$this->mAlias:$this->mId);
		if ($config["assume_mod_rewrite"] && $rewrite == true)
		{
		    if ($config['use_hierarchy'] == true)
		    {
				$url = $config['root_url']. '/' . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
		    }
		    else
		    {
				$url = $config['root_url']. '/' . $alias . (isset($config['page_extension'])?$config['page_extension']:'.html');
		    }
		}
		else
		{
		    if (isset($_SERVER['PHP_SELF']) && $config['internal_pretty_urls'] == true)
		    {
				if ($config['use_hierarchy'] == true)
				{
				    $url = $config['root_url'] . '/index.php/' . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
				}
				else
				{
				    $url = $config['root_url'] . '/index.php/' . $alias . (isset($config['page_extension'])?$config['page_extension']:'.html');
				}
		    }
		    else
		    {
				$url = $config['root_url'] . '/index.php?' . $config['query_var'] . '=' . $alias;
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
		$config =& $config();

		$tolower = false;

		if ($alias == '' && $config['auto_alias_content'] == true)
		{
			$alias = trim($this->mMenuText);
			if ($alias == '')
			{
			    $alias = trim($this->mName);
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

		$this->alias = munge_string_to_url($alias, $tolower);
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
	
	function after_delete()
	{
		$items =& cmsms()->content_property->find_all_by_content_id($this->id);
		foreach ($items as &$item)
		{
			$item->delete();
		}
		
		#Fix the item_order if necessary
		$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
		$result = db()->Execute($query, array($this->parent_id, $this->item_order));
		
		#Remove the cross references
		remove_cross_references($this->id, 'content');
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
}

//ContentBase::register_orm_class('ContentBase');

?>