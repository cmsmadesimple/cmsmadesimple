<?php
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

class CmsObjectRelationalManager extends CmsObject
{
	static private $instance = NULL;

	var $classes = array();
	
	var $assoc = array();
	
	var $acts_as = array();

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns an instance of the CmsObjectRelationalManager singleton.  Most 
	 * people can generally use cms_orm() instead of this, but they 
	 * both do the same thing.
	 *
	 * @return CmsObjectRelationalManager The singleton CmsObjectRelationalManager instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsObjectRelationalManager();
		}
		return self::$instance;
	}
	
	/**
	 * Getter overload method.
	 *
	 * @param string The field to look up
	 * @return mixed The value for that field, if it exists
	 * @author Ted Kulp
	 **/
	function __get($name)
	{
		return $this->get_orm_class($name);
	}
	
	/**
	 * Retrieves an instance of an ORM'd object for doing
	 * find_by_* methods.
	 *
	 * @return mixed The object with the given name, or null if it's not registered
	 * @author Ted Kulp
	 **/
	function get_orm_class($name, $try_prefix = true)
	{
		if (isset($this->classes[$name]))
			return $this->classes[$name];
		elseif (isset($this->classes[underscore($name)]))
			return $this->classes[underscore($name)];
		else
		{
			// Let's try to load the thing dynamically
			$name = camelize($name);
			if (class_exists($name))
			{
				$this->classes[underscore($name)] = new $name;
				return $this->classes[underscore($name)];	
			}
			else
			{
				if ($try_prefix)
				{
					return $this->get_orm_class('cms_' . $name, false);
				}
				else
				{
					var_dump('Class not found! -- ' . $name);
					return null;
				}
			}
		}
	}
	
	function has_association(&$obj, $association_name)
	{
		return $obj != null && isset($this->assoc[get_class($obj)][$association_name]);
	}
	
	function process_association(&$obj, $association_name)
	{
		if ($this->has_association($obj, $association_name))
		{
			return $this->assoc[get_class($obj)][$association_name]->get_data($obj);
		}
	}
	
	function create_has_many_association(&$obj, $association_name, $child_class_name, $child_field, $extra_params = array())
	{
		if (!isset($this->assoc[get_class($obj)][$association_name]))
		{
			$association = new CmsHasManyAssociation($association_name);
			$association->child_class = $child_class_name;
			$association->child_field = $child_field;
			$association->extra_params = $extra_params;
			$this->assoc[get_class($obj)][$association_name] = $association;
		}
	}
	
	function create_has_one_association(&$obj, $association_name, $child_class_name, $child_field, $extra_params = array())
	{
		if (!isset($this->assoc[get_class($obj)][$association_name]))
		{
			$association = new CmsHasOneAssociation($association_name);
			$association->child_class = $child_class_name;
			$association->child_field = $child_field;
			$association->extra_params = $extra_params;
			$this->assoc[get_class($obj)][$association_name] = $association;
		}
	}
	
	function create_belongs_to_association(&$obj, $association_name, $belongs_to_class_name, $child_field, $extra_params = array())
	{
		if (!isset($this->assoc[get_class($obj)][$association_name]))
		{
			$association = new CmsBelongsToAssociation($association_name);
			$association->belongs_to_class_name = $belongs_to_class_name;
			$association->child_field = $child_field;
			$association->extra_params = $extra_params;
			$this->assoc[get_class($obj)][$association_name] = $association;
		}
	}
	
	function create_has_and_belongs_to_many_association(&$obj, $association_name, $child_class, $join_table, $join_other_id_field, $join_this_id_field, $extra_params = array())
	{
		if (!isset($this->assoc[get_class($obj)][$association_name]))
		{
			$association = new CmsHasAndBelongsToManyAssociation($association_name);
			$association->child_class = $child_class;
			$association->join_table = cms_db_prefix().$join_table;
			$association->join_other_id_field = $join_other_id_field;
			$association->join_this_id_field = $join_this_id_field;
			$association->extra_params = $extra_params;
			$this->assoc[get_class($obj)][$association_name] = $association;
		}
	}
	
	function create_acts_as(&$obj, $name)
	{
		if (!isset($this->acts_as[get_class($obj)][$name]))
		{
			$class_name = 'CmsActsAs' . camelize($name);
			$acts_as_obj = new $class_name;
			if ($acts_as_obj != null)
			{
				$this->acts_as[get_class($obj)][$name] = $acts_as_obj;
			}
		}
	}
	
	function get_acts_as(&$obj)
	{
		if (isset($this->acts_as[get_class($obj)]))
		{
			return $this->acts_as[get_class($obj)];
		}
		return array();
	}
}

# vim:ts=4 sw=4 noet
?>