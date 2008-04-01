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

class CmsActsAsList extends CmsActsAs
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function before_save(&$obj)
	{
		//Fix the max value of "order_num" and set that before the save
		$field_name = $this->get_order_field($obj);
		$table_name = $obj->get_table();
		$curval = $obj->$field_name;
		if ($curval == null || intval($curval) < 1)
		{
			$new_map = array_flip($obj->field_maps);
			if (array_key_exists($field_name, $new_map)) $field_name = $new_map[$field_name];

			$params = array();
			$query = "SELECT max({$field_name}) FROM {$table_name}" . $this->generate_where_clause($obj, $params);
			
			$new_order_num = cms_db()->GetOne($query, $params);
			if ($new_order_num)
			{
				$obj->$field_name = $new_order_num + 1;
			}
			else
			{
				$obj->$field_name = 1;
			}
		}
	}
	
	public function move_up(&$obj)
	{
		$field_name = $this->get_order_field($obj);
		$table_name = $obj->get_table();
		$id_field = $obj->id_field;

		if (!isset($obj->$field_name))
		{
			$original_place = $obj[$field_name];
			if ($original_place > 1)
			{
				$new_place = $original_place - 1;
				$new_map = array_flip($obj->field_maps);
				if (array_key_exists($field_name, $new_map)) $field_name = $new_map[$field_name];
			
				$params = array();
				$query = "UPDATE {$table_name} SET {$field_name} = {$original_place}" . $this->generate_where_clause($obj, $params, "{$field_name} = {$new_place}");
				cms_db()->Execute($query, $params);
			
				$obj->$field_name = $new_place;

				return $obj->save();
			}
		}

		return false;
	}
	
	public function move_down(&$obj)
	{
		$field_name = $this->get_order_field($obj);
		$table_name = $obj->get_table();
		$id_field = $obj->id_field;

		if (!isset($obj->$field_name))
		{
			$original_place = $obj[$field_name];
			//if ($original_place > 1)
			//{
				$new_place = $original_place + 1;
				$new_map = array_flip($obj->field_maps);
				if (array_key_exists($field_name, $new_map)) $field_name = $new_map[$field_name];
			
				$params = array();
				$query = "UPDATE {$table_name} SET {$field_name} = {$original_place}" . $this->generate_where_clause($obj, $params, "{$field_name} = {$new_place}");
				cms_db()->Execute($query, $params);
			
				$obj->$field_name = $new_place;

				return $obj->save();
			//}
		}

		return false;
	}
	
	public function after_delete(&$obj)
	{
		$field_name = $this->get_order_field($obj);
		$table_name = $obj->get_table();
		$id_field = $obj->id_field;

		if (!isset($obj->$field_name))
		{
			$original_place = $obj[$field_name];
			
			$new_map = array_flip($obj->field_maps);
			if (array_key_exists($field_name, $new_map)) $field_name = $new_map[$field_name];
			
			$params = array();
			$query = "UPDATE {$table_name} SET {$field_name} = {$field_name} - 1" . $this->generate_where_clause($obj, $params, "{$field_name} > {$original_place}");
			cms_db()->Execute($query, $params);
		}
	}
	
	private function get_order_field(&$obj)
	{
		$field_name = 'order_num';
		if ($obj != null && isset($obj->order_field))
		{
			$field_name = $obj->order_field;
		}
		return $field_name;
	}
	
	private function generate_where_clause(&$obj, &$params, $extra_where = '')
	{
		$query_ext = '';

		if (isset($obj->list_filter_fields) && is_array($obj->list_filter_fields))
		{
			$query_ext = " WHERE " . $extra_where;

			foreach ($obj->list_filter_fields as $one_field)
			{
				$check_field = $one_field;
				$new_map = array_flip($obj->field_maps);
				if (array_key_exists($one_field, $new_map)) $check_field = $new_map[$one_field];
				if ($query_ext != ' WHERE ')
					$query_ext .= ' AND';

				$query_ext .= " {$check_field} = ?";
				$params[] = $obj->$check_field;
			}
		}

		return $query_ext;
	}
}

# vim:ts=4 sw=4 noet
?>
