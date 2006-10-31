<?php
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

debug_buffer('', 'Start Loading ORM');

class CmsObjectRelationalMapping extends Overloader
{
	/**
	 * Used to define any default settings for this object.  Not
	 * all fields need to be defined, as they'll come out of the
	 * database field names anyway.
	 */
	var $params = array();

	/**
	 * Used to define a variation between a database field and
	 * what the property name should be.  Takes a hash of
	 * 'database field name' => 'property name'
	 */
	var $field_maps = array();
	
	/**
	 * Used to define a different table name for this object if it
	 * doesn't match the predetermined name based on the object's class
	 * name.
	 */
	var $table = '';
	
	/**
	 * Used to define an alternate field for the id.  This basically says
	 * whether or not we insert or update.
	 */
	var $id_field = 'id';
	
	/**
	 * Used to define a sequence to use for creating a new id to use.  If 
	 * blank, then the auto increment for the database is used for the id.
	 */
	var $sequence = '';
	
	/**
	 * Used to define which field holds the record create date.
	 */
	var $create_date_field = 'create_date';
	
	/**
	 * Used to define which field holds the record modified date.
	 */
	var $modified_date_field = 'modified_date';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function register_orm_class($classname)
	{
		global $gCms;
		$ormclasses =& $gCms->orm;
		
		$name = underscore($classname);
		$ormclasses[$name] = new $classname;
		
		if (function_exists("overload") && phpversion() < 5)
		{
		   overload($classname);
		}
	}
	
	// Getter
	function _get($n, &$v)
	{
		if (isset($this->params[$n]))
		{
			$v = $this->params[$n];
			return true;
		}
		return false;
	}

	// Setter
	function _set($n, $val)
	{
		if (array_key_exists($n, $this->field_maps)) $n = $this->field_maps[$n];
		$this->params[$n] = $val;
		return true;
	}
	
	// Caller, applied when $function isn't defined
	function _call($function, $arguments, &$return)
	{
		if (startswith($function, 'find_by_'))
		{
			$return = $this->find_by_($function, $arguments);
		}
		else if (startswith($function, 'find_all_by_'))
		{
			$return = $this->find_all_by_($function, $arguments);
		}
	}
	
	/**
	 * Method for handling the dynamic find_by_* functionality.  It basically figures out
	 * what field is being searched for and creates a query based on that field.
	 *
	 * @param $function The name of the function that came into the __call method
	 * @param $arguments The arguments that came into the __call method
	 *
	 * @return The results of the find
	 */
	function find_by_($function, $arguments)
	{
		$field = str_replace('find_by_', '', $function);
		
		//Figure out if we need to replace the field from the field mappings
		$new_map = array_flip($this->field_maps); //Flip the keys, since this is the reverse operation
		if (array_key_exists($field, $new_map)) $field = $new_map[$field];
		
		return $this->find(array('conditions' => array($field . ' = ?', array($arguments[0]))));
	}
	
	/**
	 * Method for handling the dynamic find_all_by_* functionality.  It basically figures out
	 * what field is being searched for and creates a query based on that field.
	 *
	 * @param $function The name of the function that came into the __call method
	 * @param $arguments The arguments that came into the __call method
	 *
	 * @return The results of the find_all
	 */
	function find_all_by_($function, $arguments)
	{
		var_dump('function');
		$field = str_replace('find_all_by_', '', $function);
		
		//Figure out if we need to replace the field from the field mappings
		$new_map = array_flip($this->field_maps); //Flip the keys, since this is the reverse operation
		if (array_key_exists($field, $new_map)) $field = $new_map[$field];
		
		return $this->find_all(array('conditions' => array($field . ' = ?', array($arguments[0]))));
	}
	
	/**
	 * Figures out the proper name of the table that's persisting this class.
	 *
	 * @return Name of the table to use
	 */
	function get_table()
	{
		$classname = get_class($this);
		$table = $this->table != '' ? cms_db_prefix() . $this->table : cms_db_prefix() . strtolower($classname);

		return $table;
	}
	
	function find($arguments)
	{
		$table = $this->get_table();
		
		$query = '';
		$queryparams = array();
		
		list($query, $queryparams) = $this->generate_select_query_and_parameters($table, $arguments, $query, $queryparams);
		
		return $this->find_by_query($query, $queryparams);
	}
	
	function find_by_query($query, $queryparams = array())
	{
		global $gCms;
		$db =& $gCms->GetDb();
		
		$classname = get_class($this);

		$row =& $db->GetRow($query, $queryparams);

		if($row)
		{
			$oneobj =& new $classname;
			$oneobj = $this->fill_object($row, $oneobj);
			return $oneobj;
		}

		return FALSE;
	}
	
	function find_all($arguments)
	{
		$table = $this->get_table();
		
		$query = '';
		$queryparams = array();
		
		list($query, $queryparams) = $this->generate_select_query_and_parameters($table, $arguments, $query, $queryparams);
		
		return $this->find_all_by_query($query, $queryparams);
	}
	
	function find_all_by_query($query, $queryparams = array())
	{
		global $gCms;
		$db =& $gCms->GetDb();
		
		$classname = get_class($this);

		$result = array();
		$dbresult = &$db->Execute($query, $queryparams);

		while ($dbresult && !$dbresult->EOF)
		{
			$oneobj =& new $classname;
			$oneobj = $this->fill_object($dbresult->fields, $oneobj);
			$result[] =& $oneobj;
			$dbresult->MoveNext();
		}
		
		return $result;
	}
	
	function save()
	{
		global $gCms;
		$db =& $gCms->GetDb();

		$table = $this->get_table();

		$id_field = $this->id_field;
		$id = $this->$id_field;
		
		//Figure out if we need to replace the field from the field mappings
		$new_map = array_flip($this->field_maps); //Flip the keys, since this is the reverse operation
		if (array_key_exists($id_field, $new_map)) $id_field = $new_map[$id_field];
		
		$fields = $this->get_columns_in_table();
		
		$time = $db->DBTimeStamp(time());
		
		//If we have an id, so an update.
		//If not, do an insert.
		if (isset($id) && $id > 0)
		{
			$query = 'UPDATE ' . $table . ' SET ';
			$midpart = '';
			$queryparams = array();

			foreach($fields as $onefield)
			{
				$localname = $onefield;
				if (array_key_exists($localname, $this->field_maps)) $localname = $this->field_maps[$localname];
				if ($onefield == $this->modified_date_field)
				{
					#$queryparams[] = $time;
					$midpart .= $onefield . ' = ' . $time . ', ';
					$this->$onefield = time();
				}
				else if (array_key_exists($localname, $this->params))
				{
					$queryparams[] = $this->params[$localname];
					$midpart .= $onefield . ' = ?, ';
				}
			}
			
			if ($midpart != '')
			{	
				$midpart = substr($midpart, 0, -2);
				$query .= $midpart . ' WHERE ' . $id_field . ' = ?';
				$queryparams[] = $id;
			}
			
			return $db->Execute($query, $queryparams);
		}
		else
		{
			$new_id = -1;
			if ($this->sequence != '')
			{
				$new_id = $db->GenID(cms_db_prefix() . $this->sequence);
				$this->$id_field = $new_id;
			}

			$query = 'INSERT INTO ' . $table . ' (';
			$midpart = '';
			$queryparams = array();
			
			foreach($fields as $onefield)
			{
				$localname = $onefield;
				if (array_key_exists($localname, $this->field_maps)) $localname = $this->field_maps[$localname];
				
				if ($onefield == $this->create_date_field || $onefield == $this->modified_date_field)
				{
					$queryparams[] = trim($time, "'");
					$midpart .= $onefield . ', ';
					$this->$onefield = time();
				}
				else if (array_key_exists($localname, $this->params))
				{
					if (!($new_id == -1 && $localname == $this->$id_field))
					{
						$queryparams[] = $this->params[$localname];
						$midpart .= $onefield . ', ';
					}
				}
			}
			
			if ($midpart != '')
			{
				$midpart = substr($midpart, 0, -2);
				$query .= $midpart . ') VALUES (';
				$query .= implode(',', array_fill(0, count($queryparams), '?'));
				$query .= ')';
			}
			
			$result = $db->Execute($query, $queryparams);
			
			if ($new_id == -1)
			{
				$new_id = $db->Insert_ID();
				$this->$id_field = $new_id;
			}
			
			return $result;
		}
	}
	
	/**
	 * Deletes a record from the table that persists this class.  If no id is given, then
	 * it deletes the object given.  If an id is given, then it deletes that one from the
	 * database directly.
	 *
	 * @param $id The id to delete.  If none, then deletes the object called on.
	 *
	 * @return Boolean based on whether or not the delete was successful.
	 */
	function delete($id = -1)
	{
		global $gCms;
		$db =& $gCms->GetDb();

		if ($id == -1)
			$id = $this->$id;
			
		$table = $this->get_table();
		$id_field = $this->id_field;
		
		//Figure out if we need to replace the field from the field mappings
		$new_map = array_flip($this->field_maps); //Flip the keys, since this is the reverse operation
		if (array_key_exists($id_field, $new_map)) $id_field = $new_map[$id_field];

		return $db->Execute('DELETE FROM ' . $table . ' WHERE ' . $id_field . ' = ' . $id);
	}
	
	/**
	 * Fills an object with the fields from the database.
	 *
	 * @param &$resulthash Reference to the has for this record that came from the database
	 * @param &$object Reference to the object we should fill
	 *
	 * @return The object we filled (php4 doesn't seem to handle the reference right)
	 */
	function fill_object(&$resulthash, &$object)
	{
		foreach ($resulthash as $k=>$v)
		{
			$object->$k = $v;
		}

		return $object;
	}
	
	/**
	 * Generates a select query based on the arguments sent to the find and find_by
	 * methods.
	 * 
	 * @param $table Name of the table that should be SELECT'd from
	 * @param $arguments Arguments passed to the find and find_by methods
	 * @param &$query Reference to the query string that will be modified by this method
	 * @param &$queryparams Reference to the array of query params passed on to adodb
	 *
	 * @return An array of $query and $queryparams
	 */
	function generate_select_query_and_parameters($table, $arguments, $query, $queryparams)
	{
		$query = "SELECT * FROM " . $table;
		if (array_key_exists('conditions', $arguments))
		{
			$query .= ' WHERE ' . $arguments['conditions'][0];
			if (isset($arguments['conditions'][1]) && is_array($arguments['conditions'][1]))
			{
				$queryparams = array_merge($queryparams, $arguments['conditions'][1]);
			}
		}
		if (array_key_exists('order', $arguments))
		{
			$query .= ' ORDER BY ' . $arguments['order'];
		}
		
		return array($query, $queryparams);
	}
	
	/**
	 * Generates an array of column names in the table that perists this class.
	 *
	 * @return An array of column names
	 */
	function get_columns_in_table()
	{
		$cache = $this->_getDescriptionCache();
		if ($cache !== FALSE)
			return $cache;

		global $gCms;
		$config =& $gCms->GetConfig();

		$dbms = $gCms->config['dbms'];
		if ($dbms == 'mysqli') $dbms = 'mysql';

		include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dbo' . DIRECTORY_SEPARATOR . $dbms . '.get_columns_in_table.php');
		
		$fields = dbo_get_columns_in_table($this);
		
		$this->_cacheDescription($fields);
		return $fields;
	}
	
	function _cacheDescription($fields)
	{
		global $gCms;
		$cache =& $gCms->desccache;
		
		$cache[$this->get_table()] =& $fields;
	}
	
	function _getDescriptionCache()
	{
		global $gCms;
		$cache =& $gCms->desccache;
		
		if (isset($cache[$this->get_table()]))
		{
			return $cache[$this->get_table()];
		}

		return FALSE;
	}
}

if (function_exists("overload") && phpversion() < 5)
{
   overload("CmsObjectRelationalMappting");
}

debug_buffer('', 'End Loading ORM');

?>