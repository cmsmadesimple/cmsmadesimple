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
 * Class to easily add attributes to your ORM class.
 * To use this class:
 * 1. Create variables named $attr_module and $attr_extra in your class.
 * 2. Set them to the name of your module and an optional "namespace"
 *    (ex. $attr_module = 'MyModule';  $attr_extra = 'Widget';)
 *
 * @package default
 * @author Ted Kulp
 **/
class CmsActsAsAttributed extends CmsActsAs
{
	function __construct()
	{
		parent::__construct();
	}
	
	function check_variables_are_set(&$obj)
	{
		if (!isset($obj->attr_module) || !isset($obj->attr_extra))
		{
			die('Must set the $attr_module and $attr_extra variables to use CmsActsAsAttributed');
		}
	}
	
	function get_attribute_defnitions(&$obj)
	{
		$this->check_variables_are_set($obj);
		return cms_orm('CmsAttributeDefinition')->find_all_by_module_and_extra_attr($obj->attr_module, $obj->attr_extra);
	}
	
	function set_attribute_definition(&$obj, $name, $type = 'text', $optional = false, $user_generated = false)
	{
		$this->check_variables_are_set($obj);
		
		//See if it exists -- return true
		foreach ($this->get_attribute_defnitions($obj) as $one_def)
		{
			if ($one_def->name == $name)
			{
				return true;
			}
		}
		
		//Create the new one
		$def = new CmsAttributeDefinition();
		$def->module = $obj->attr_module;
		$def->extra_attr = $obj->attr_extra;
		$def->name = $name;
		$def->attribute_type = $type;
		$def->optional = $optional;
		$def->user_generated = $user_generated;
		return $def->save();
	}
	
	function get_attribute_by_name(&$obj, $attribute_name)
	{
		$this->check_variables_are_set($obj);
		$prefix = CMS_DB_PREFIX;
		return cms_orm('CmsAttribute')->find(array('joins' => "INNER JOIN {$prefix}attribute_defns ON {$prefix}attribute_defns.id = {$prefix}attributes.attribute_id", 'conditions' => array("{$prefix}attribute_defns.module = ? AND {$prefix}attribute_defns.extra_attr = ? AND {$prefix}attribute_defns.name = ? AND {$prefix}attributes.object_id = ?", $obj->attr_module, $obj->attr_extra, $attribute_name, $obj->id)));
	}
	
	function get_attribute_value(&$obj, $attribute_name)
	{
		$attribute = $this->get_attribute_by_name($obj, $attribute_name);

		if ($attribute != null)
			return $attribute->content;

		return null;
	}
	
	function set_attribute_value(&$obj, $attribute_name, $attribute_value)
	{
		$attribute = $this->get_attribute_by_name($obj, $attribute_name);

		if ($attribute != null)
		{
			$attribute->content = $attribute_value;
			return $attribute->save();
		}

		return false;
	}

}

# vim:ts=4 sw=4 noet
?>