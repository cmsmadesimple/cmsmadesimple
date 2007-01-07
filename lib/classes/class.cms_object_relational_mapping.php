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
 * Base class for all things ORM.  All classes that want to be part of
 * the ORM system need to extend this class.  They also need to call the
 * static register_orm_class() method after the class is defined in order
 * to be reigstered for the system (and allow things like find_by_* to 
 * work).
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
abstract class CmsObjectRelationalMapping extends CmsObject implements ArrayAccess
{
	/**
	 * The ORM version number.  This basically is a number that
	 * should be incremented when an object has a major change.
	 *
	 * Adding or removing fields from the table doesn't really
	 * constitute a change.  It's more like changes to a table
	 * name, enabling or disabling a sequence or changing the
	 * name of an id field.  Since some areas of CMSMS store
	 * serialized versions of objects, there could be a
	 * discrepency between the current version and the version
	 * that was just unserialized.  We can use the version number
	 * to test to see if it's the same.
	 *
	 * If not set, then it defaults to 1.  If you never really
	 * change the object, then you shouldn't need to modify it.
	 */
	var $orm_version = 1;

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
	 * name.  The prefix in config.php will be applied automatically.
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
	 * Used to store validation error messages if a save does not go as
	 * expected.
	 */
	var $validation_errors = array();
	
	/**
	 * Used to store any has_many relationships.
	 **/
	var $has_many = array();
	
	/**
	 * Used to store any has_one relationships.
	 **/
	var $has_one = array();
	
	/**
	 * Used to store any belongs_to relationships.
	 **/
	var $belongs_to = array();
	
	/**
	 * Used to store any has_and_belongs_to_many relationships.
	 **/
	var $has_and_belongs_to_many = array();
	
	/**
	 * Used to define which field holds the record create date.
	 */
	var $create_date_field = 'create_date';
	
	/**
	 * Used to define which field holds the record modified date.
	 */
	var $modified_date_field = 'modified_date';
	
	/**
	 * Used to only update objects (not insert) that have changed
	 * any of their properties.  This means you should be using properites
	 * ($obj->some_field or $obj->SetSomeField()) so that the dirty bit
	 * gets flipped properly.
	 */
	var $dirty = false;
	
	/**
	 * Used in situations where we're doing a bit of polymorphism.  The type 
	 * field will store the name of the class that this object currently is.
	 * Then, when it's loaded, we will automatically instantiate that type of 
	 * object again and not just go by the name of the class that called the 
	 * find.  If you want this functionality to not exist, make this variable
	 * blank.
	 */
	var $type_field = 'type';
	
	function __construct()
	{
		parent::__construct();

		//Setup the predefined fields in the $params array.  Relax: The definitions are cached.
		$fields = $this->get_columns_in_table();
		if (count($fields) > 0) {
			foreach ($fields as $field) {
				if (array_key_exists($field, $this->field_maps)) $field = $this->field_maps[$field];
				if (!array_key_exists($field, $this->params)) {
					$this->params[$field] = '';
				}
			}
		}
	}

	/**
	 * Used to create a has_many association.  This should be called in the constructor of
	 * the data object.  Any associations are lazy loaded on the first call to them and are
	 * cached for the life of the object.
	 *
	 * @param string The name of the association.  It will then be called via 
	 *        $obj->assication_name.  Make sure it's not the same name as a 
	 *        parameter, or it will never get called.
	 * @param string The name of the class on the other end of the association.  This should
	 *        be the name that would be used when calling from the orm (cmsms()->child_class_name).
	 * @param string The name of the field in the association class that contains the matching id to 
	 *        this object.
	 * @return void
	 * @author Ted Kulp
	 **/
	protected function create_has_many_association($association_name, $child_class_name, $child_field)
	{
		$association = new CmsHasManyAssociation($this);
		$association->child_class = $child_class_name;
		$association->child_field = $child_field;
		$this->has_many[$association_name] = $association;
	}
	
	/**
	 * Used to create a has_one association.  This should be called in the constructor of
	 * the data object.  Any associations are lazy loaded on the first call to them and are
	 * cached for the life of the object.
	 *
	 * @param string The name of the association.  It will then be called via 
	 *        $obj->assication_name.  Make sure it's not the same name as a 
	 *        parameter, or it will never get called.
	 * @param string The name of the class on the other end of the association.  This should
	 *        be the name that would be used when calling from the orm (cmsms()->child_class_name).
	 * @param string The name of the field in the association class that contains the matching id to 
	 *        this object.
	 * @return void
	 * @author Ted Kulp
	 **/
	protected function create_has_one_association($association_name, $child_class_name, $child_field)
	{
		$association = new CmsHasOneAssociation($this);
		$association->child_class = $child_class_name;
		$association->child_field = $child_field;
		$this->has_one[$association_name] = $association;
	}
	
	/**
	 * Used to create a belongs_to association.  This should be called in the constructor of
	 * the data object.  Any associations are lazy loaded on the first call to them and are
	 * cached for the life of the object.
	 *
	 * @param string The name of the association.  It will then be called via 
	 *        $obj->assication_name.  Make sure it's not the same name as a 
	 *        parameter, or it will never get called.
	 * @param string The name of the class on the other end of the association.  This should
	 *        be the name that would be used when calling from the orm (cmsms()->belongs_to_class_name).
	 * @param string The name of the field in the this class that contains the matching id to 
	 *        the given belongs_to_class_name.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	protected function create_belongs_to_association($association_name, $belongs_to_class_name, $child_field)
	{
		$association = new CmsBelongsToAssociation($this);
		$association->belongs_to_class_name = $belongs_to_class_name;
		$association->child_field = $child_field;
		$this->belongs_to[$association_name] = $association;
	}
	
	/**
	 * Used to create a belongs_to association.  This should be called in the constructor of
	 * the data object.  Any associations are lazy loaded on the first call to them and are
	 * cached for the life of the object.
	 *
	 * @param string The name of the association.  It will then be called via 
	 *        $obj->assication_name.  Make sure it's not the same name as a 
	 *        parameter, or it will never get called.
	 * @param string The name of the class on the other end of the association.  This should
	 *        be the name that would be used when calling from the orm (cmsms()->belongs_to_class_name).
	 * @param string The name of the field in the this class that contains the matching id to 
	 *        the given belongs_to_class_name.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	protected function create_has_and_belongs_to_many_association($association_name, $child_class, $join_table, $join_other_id_field, $join_this_id_field)
	{
		$association = new CmsHasAndBelongsToManyAssociation($this);
		$association->child_class = $child_class;
		$association->join_table = $join_table;
		$association->join_other_id_field = $join_other_id_field;
		$association->join_this_id_field = $join_this_id_field;
		$this->has_and_belongs_to_many[$association_name] = $association;
	}

	/**
	 * Used for the ArrayAccessor implementation.
	 *
	 * @param string The key to set with the given value
	 * @param mixed The value to set for the given key
	 * @return void
	 * @author Ted Kulp
	 **/
	function offsetSet($key, $value)
	{
		if (array_key_exists($key, $this->field_maps)) $key = $this->field_maps[$key];
		$this->params[$key] = $value;
		$this->dirty = true;
	}

	/**
	 * Used for the ArrayAccessor implementation.
	 *
	 * @param string The key to look up
	 * @return mixed The value of the $obj['field']
	 * @author Ted Kulp
	 **/
	function offsetGet($key)
	{
		if (array_key_exists($key, $this->params))
		{
			return $this->params[$key];
		}
	}

	/**
	 * Used for the ArrayAccessor implementation.
	 *
	 * @param string The key to unset
	 * @return bool Whether or not it does exist
	 * @author Ted Kulp
	 **/
	function offsetUnset($key)
	{
		if (array_key_exists($key, $this->params))
		{
			unset($this->params[$key]);
		}
	}

	/**
	 * Used for the ArrayAccessor implementation.
	 *
	 * @param string The key to lookup to see if it exists
	 * @return bool Whether or not it does exist
	 * @author Ted Kulp
	 **/
	function offsetExists($offset)
	{
		return array_key_exists($offset, $this->params);
	}
	
	/**
	 * "Static" method to register this class with the orm system.  This must be called
	 * right after an ORM class has been defined.
	 *
	 * @param $classname Name of the class to register with the ORM system
	 * @return void
	 * @author Ted Kulp
	 */
	static function register_orm_class($classname)
	{
		global $gCms;
		$ormclasses =& $gCms->orm;
		
		$name = underscore($classname);
		$ormclasses[$name] = new $classname;
		
		return FALSE;
	}
	
	/**
	 * Getter overload method.  Called when an $obj->field and field
	 * does not exist in the object's variable list.
	 *
	 * @param string The field to look up
	 * @return mixed The value for that field, if it exists
	 * @author Ted Kulp
	 **/
	function __get($n)
	{
		if (array_key_exists($n, $this->params))
		{
			if (method_exists($this, 'get_' . $n))
				return call_user_func_array(array($this, 'get_'.$n), array($val));
			else
				return $this->params[$n];
		}
		
		if (array_key_exists($n, $this->has_many))
		{
			return $this->has_many[$n]->get_data();
		}
		if (array_key_exists($n, $this->has_one))
		{
			return $this->has_one[$n]->get_data();
		}
		if (array_key_exists($n, $this->belongs_to))
		{
			return $this->belongs_to[$n]->get_data();
		}
		if (array_key_exists($n, $this->has_and_belongs_to_many))
		{
			return $this->has_and_belongs_to_many[$n]->get_data();
		}
	}

	/**
	 * Setter overload method.  Called when an $obj->field = '' and field
	 * does not exist in the object's variable list.
	 *
	 * @param string The field to set
	 * @param mixed The value to set for said field
	 * @return void
	 * @author Ted Kulp
	 **/
	function __set($n, $val)
	{
		if (array_key_exists($n, $this->field_maps)) $n = $this->field_maps[$n];
		if (method_exists($this, 'set_' . $n))
			call_user_func_array(array($this, 'set_'.$n), array($val));
		else
			$this->params[$n] = $val;
		$this->dirty = true;
	}
	
	/**
	 * Caller overload method.  Called when an $obj->method() is called and
	 * that method does not exist.
	 *
	 * @param string The name of the method called
	 * @param array The parameters sent along with that method call
	 * @return mixed The result of the method call
	 * @author Ted Kulp
	 **/
	function __call($function, $arguments)
	{
		$function_converted = underscore($function);
		if (array_key_exists($function, $this->field_maps)) $function_converted = $this->field_maps[$function];

		if (starts_with($function, 'find_by_'))
		{
			return $this->find_by_($function, $arguments);
		}
		else if (starts_with($function, 'find_all_by_'))
		{
			return $this->find_all_by_($function, $arguments);
		}
		else if (starts_with($function, 'find_count_by_'))
		{
			return $this->find_count_by_($function, $arguments);
		}
		else if (starts_with($function_converted, 'set_'))
		{
			#This handles the SetSomeParam() dynamic function calls
			return $this->__set(substr($function_converted, 4), $arguments[0]);
		}
		#else if (array_key_exists($function_converted, $this->params))
		else
		{
			#This handles the SomeParam() dynamic function calls
			return $this->__get($function_converted);
		}
	}
	
	/**
	 * Private helper function for processing dynaimc find_by methods.  It essentially does several things...
	 * 1. Split out any "and" or "or" clauses in a dynamic find method
	 * 2. Pops the corresponding arguments off of the array so they don't get processed further
	 * 3. Creates the conditions clause and returns it
	 *
	 * @param string The field (or fields in the case of an "and" or "or" lookup)
	 * @param array Reference to the arguments passed to the method.  The array is modified as necessary.
	 * @return array Conditions clause after processing
	 * @author Ted Kulp
	 **/
	private function split_conditions($field, &$arguments)
	{
		//Figure out if we need to replace the field from the field mappings
		$new_map = array_flip($this->field_maps); //Flip the keys, since this is the reverse operation
		
		$numparams = 1;
		$params = array();
		$fields = preg_split('/(_and_|_or_)/', $field, -1, PREG_SPLIT_DELIM_CAPTURE);
		$conditions = '';
		
		for ($i = 0; $i < count($fields); $i=$i+2)
		{
			$params[] = array_shift($arguments);
			if ($i > 0 && $fields[$i-1] == '_and_')
				$conditions .= ' AND ';
			else if ($i > 0 && $fields[$i-1] == '_or_')
				$conditions .= ' OR ';
			
			//Make sure we're looking it up by what the class thinks the parameter is called,
			//not the database.
			if (array_key_exists($fields[$i], $new_map)) $fields[$i] = $new_map[$fields[$i]];

			$conditions .= $this->get_table($fields[$i]) . ' = ?';
		}
		
		return array('conditions' => array($conditions, $params));
	}
	
	/**
	 * Method for handling the dynamic find_by_* functionality.  It basically figures out
	 * what field is being searched for and creates a query based on that field.
	 *
	 * @param string The name of the function that came into the __call method
	 * @param array The arguments that came into the __call method
	 * @return The results of the find
	 * @author Ted Kulp
	 */
	function find_by_($function, $arguments)
	{
		$field = str_replace('find_by_', '', $function);
		
		$parameters = $this->split_conditions($field, $arguments);
		if (count($arguments) > 0)
		{
			$parameters = array_merge($parameters, $arguments[0]);
		}
		
		return $this->find($parameters);
	}
	
	/**
	 * Method for handling the dynamic find_all_by_* functionality.  It basically figures out
	 * what field is being searched for and creates a query based on that field.
	 *
	 * @param string The name of the function that came into the __call method
	 * @param array The arguments that came into the __call method
	 * @return The results of the find_all
	 * @author Ted Kulp
	 */
	function find_all_by_($function, $arguments)
	{
		$field = str_replace('find_all_by_', '', $function);
		
		$parameters = $this->split_conditions($field, $arguments);
		if (count($arguments) > 0)
		{
			$parameters = array_merge($parameters, $arguments[0]);
		}
		
		return $this->find_all($parameters);
	}
	
	/**
	 * Method for handling the dynamic find_count_by_* functionality.  It basically figures out
	 * what field is being searched for and creates a query based on that field.
	 *
	 * @param string The name of the function that came into the __call method
	 * @param array The arguments that came into the __call method
	 * @return integer The result of the find_count
	 * @author Ted Kulp
	 */
	function find_count_by_($function, $arguments)
	{
		$field = str_replace('find_count_by_', '', $function);
		
		$parameters = $this->split_conditions($field, $arguments);
		if (count($arguments) > 0)
		{
			$parameters = array_merge($parameters, $arguments[0]);
		}
		
		return $this->find_count($parameters);
	}
	
	/**
	 * Figures out the proper name of the table that's persisting this class.
	 *
	 * @param string Field to append to the returned string
	 * @return string Name of the table to use
	 * @author Ted Kulp
	 */
	function get_table($fieldname = '')
	{
		$classname = underscore(get_class($this));
		if (starts_with($classname, 'cms_')) $classname = substr($classname, 4);
		$table = $this->table != '' ? cms_db_prefix() . $this->table : cms_db_prefix() . $classname;
		$table = $table . ($fieldname != '' ? '.' . $fieldname : '');
		return $table;
	}
	
	/**
	 * The generic catch-all find method.  Takes all the given parameters, constructs a query, and calls find_by_query
	 * on it.  It returns the results of find_by_query.
	 *
	 * @param array The list of parameters that we should calculate to constuct the select query
	 * @return mixed The object that is found, or null if none is found in the database.
	 * @author Ted Kulp
	 **/
	function find($arguments = array())
	{
		$table = $this->get_table();
		
		$query = '';
		$queryparams = array();
		
		list($query, $queryparams, $numrows, $offset) = $this->generate_select_query_and_parameters($table, $arguments, $query, $queryparams);
		
		return $this->find_by_query($query, $queryparams, $numrows, $offset);
	}
	
	/**
	 * Takes a SQL query related to the class, executes it, and loads the object, if found.
	 * If it's not found, we return null.
	 *
	 * @param string The SELECT query to run
	 * @param array An array of query parameters to replace the ? in the query with
	 * @return mixed The found object, or null if none are found
	 * @author Ted Kulp
	 **/
	function find_by_query($query, $queryparams = array())
	{
		$db = cms_db();
		
		$classname = get_class($this);

		$row = $db->GetRow($query, $queryparams);

		if($row)
		{
			//Basically give before_load a chance to load that class type if necessary
			$newclassname = $classname;
			if ($this->type_field != '' && isset($row[$this->type_field]))
			{
				$newclassname = $row[$this->type_field];
			}
			
			$this->before_load($newclassname, $row);
			
			if (!($newclassname != $classname && class_exists($newclassname)))
			{
				$newclassname = $classname;
			}

			$oneobj = new $newclassname;
			$oneobj = $this->fill_object($row, $oneobj);
			return $oneobj;
		}

		return null;
	}
	
	/**
	 * The generic catch-all find_all method.  Takes all the given parameters, constructs a query, and calls find_all_by_query
	 * on it.  It returns the results of find_all_by_query.
	 *
	 * @param array The list of parameters that we should calculate to constuct the select query
	 * @return array An array of objects if found.  If none are found, it will be an empty array.
	 * @author Ted Kulp
	 **/
	function find_all($arguments = array())
	{
		$table = $this->get_table();
		
		$query = '';
		$queryparams = array();
		
		list($query, $queryparams, $numrows, $offset) = $this->generate_select_query_and_parameters($table, $arguments, $query, $queryparams);
		
		return $this->find_all_by_query($query, $queryparams, $numrows, $offset);
	}
	
	/**
	 * Takes a SQL query related to the class, executes it, and loads the object(s), if found.
	 * If it's not found, we return an empty array.
	 *
	 * @param string The SELECT query to run
	 * @param array An array of query parameters to replace the ? in the query with
	 * @return mixed The found object(s), or empty array if none are found
	 * @author Ted Kulp
	 **/
	function find_all_by_query($query, $queryparams = array(), $numrows = -1, $offset = -1)
	{
		$db = cms_db();
		
		$classname = get_class($this);

		$result = array();
		$dbresult = &$db->SelectLimit($query, $numrows, $offset, $queryparams);

		while ($dbresult && !$dbresult->EOF)
		{
			//Basically give before_load a chance to load that class type if necessary
			$newclassname = $classname;
			if ($this->type_field != '' && isset($dbresult->fields[$this->type_field]))
			{
				$newclassname = $dbresult->fields[$this->type_field];
			}
			
			$this->before_load($newclassname, $dbresult->fields);

			if (!($newclassname != $classname && class_exists($newclassname)))
			{
				$newclassname = $classname;
			}

			$oneobj = new $newclassname;
			$oneobj = $this->fill_object($dbresult->fields, $oneobj);
			$result[] = $oneobj;
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();
		
		return $result;
	}
	
	/**
	 * Used exactly like find_all, but returns a count instead of the actual objects.
	 *
	 * @param array The parameters used to the construct the SQL query
	 * @return integer The resulting count
	 * @author Ted Kulp
	 **/
	function find_count($arguments = array())
	{
		$db = cms_db();

		$table = $this->get_table();
		
		$query = '';
		$queryparams = array();
		
		list($query, $queryparams, $numrows, $offset) = $this->generate_select_query_and_parameters($table, $arguments, $query, $queryparams, true);
		
		return $db->GetOne($query, $queryparams);
	}

	/**
	 * Saves the ORM'd object back to the database.  First it calls the validation method to make
	 * sure that all validation passes.  If successful, it then determines if this is a new record
	 * or updated record and INSERTs or UPDATEs accordingly.
	 *
	 * Updated records are only saved if they have been changed (dirty flag is set).  If you're doing
	 * any low level changes to the $params array directly, you should set the dirty flag to true
	 * to make sure any changes are saved.
	 *
	 * @return boolean Whether or not the save was successful or not.  If it wasn't, check the validation stack for errors.
	 */
	function save()
	{
		if ($this->_call_validation())
			return false;

		$this->before_save();

		$gCms = cmsms();
		$db = cms_db();

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
			CmsProfiler::get_instance()->mark('Before Update');
			if ($this->dirty)
			{
				CmsProfiler::get_instance()->mark('Dirty Bit Not Set');
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
					else if ($this->type_field != '' && $this->type_field == $onefield)
					{
						$this->$onefield = strtolower(get_class($this));
						$queryparams[] = strtolower(get_class($this));
						$midpart .= $onefield . ' = ?, ';
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
			
				$result = $db->Execute($query, $queryparams);
				
				if ($result)
				{
					$this->dirty = false;
					$this->after_save();
				}
				
				return $result;
			}
			
			CmsProfiler::get_instance()->mark('Dirty Bit Set');
			
			return true;
		}
		else
		{
			$new_id = -1;
			
			CmsProfiler::get_instance()->mark('Before Insert');

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
				else if ($this->type_field != '' && $this->type_field == $onefield)
				{
					$queryparams[] = strtolower(get_class($this));
					$midpart .= $onefield . ', ';
					$this->$onefield = strtolower(get_class($this));
				}
				else if (array_key_exists($localname, $this->params))
				{
					if (!($new_id == -1 && $localname == $this->id_field))
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
			
			if ($result)
			{
				if ($new_id == -1)
				{
					$new_id = $db->Insert_ID();
					$this->$id_field = $new_id;
				}
		
				$this->dirty = false;
				$this->after_save();
			}
			
			return $result;
		}
	}
	
	/**
	 * Deletes a record from the table that persists this class.  If no id is given, then
	 * it deletes the object given.  If an id is given, then it deletes that one from the
	 * database directly.  Keep in mind that deleting a object from the database directly
	 * while having one in memory simultaniously could cause issues.
	 *
	 * @param integer The id to delete.  If none, then deletes the object called on.
	 *
	 * @return boolean Boolean based on whether or not the delete was successful.
	 */
	function delete($id = -1)
	{
		if ($id > -1)
		{
			$method = 'find_by_' . $this->id_field;
			$obj = $this->$method($id);
			if ($obj)
				return $obj->delete();
			return false;
		}
		else
		{
			$table = $this->get_table();
			$id_field = $this->id_field;
		
			$id = $this->$id_field;
		
			$this->before_delete();
		
			//Figure out if we need to replace the field from the field mappings
			$new_map = array_flip($this->field_maps); //Flip the keys, since this is the reverse operation
			if (array_key_exists($id_field, $new_map)) $id_field = $new_map[$id_field];

			$result = cms_db()->Execute('DELETE FROM ' . $table . ' WHERE ' . $this->get_table($id_field) . ' = ' . $id);
		
			if ($result)
				$this->after_delete();
		
			return $result;
		}
	}
	
	/**
	 * Used to push a hash of keys and values to the object.  This is very helpful
	 * for updating an object based on the fields in a form.
	 *
	 * @param array The hash of keys and values to set in the object
	 */
	function update_parameters($params)
	{
		foreach ($params as $k=>$v)
		{
			if (array_key_exists($k, $this->params))
			{
				//Just in case there is an override
				$this->params[$k] = $v;
				$this->dirty = true;
			}
		}
	}
	
	/**
	 * Fills an object with the fields from the database.
	 *
	 * @param array Reference to the hash for this record that came from the database
	 * @param mixed Reference to the object we should fill
	 * @return The object we filled (php4 doesn't seem to handle the reference right)
	 */
	function fill_object(&$resulthash, &$object)
	{
		foreach ($resulthash as $k=>$v)
		{
			if (array_key_exists($k, $this->field_maps)) $k = $this->field_maps[$k];
			$object->params[$k] = $v;
		}
		
		$this->dirty = false;
		
		$object->after_load();

		return $object;
	}
	
	/**
	 * Generates a select query based on the arguments sent to the find and find_by
	 * methods.
	 * 
	 * @param string Name of the table that should be SELECT'd from
	 * @param array Arguments passed to the find and find_by methods
	 * @param string Reference to the query string that will be modified by this method
	 * @param array Reference to the array of query params passed on to adodb
	 *
	 * @return array An array of $query and $queryparams
	 */
	function generate_select_query_and_parameters($table, $arguments, $query, $queryparams, $count = false)
	{
		$numrows = -1;
		$offset = -1;

		$query = "SELECT * FROM " . $table;
		if ($count) $query = "SELECT count(*) as the_count FROM " . $table;

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
			$args = $arguments['order'];
			foreach ($this->field_maps as $db=>$obj)
			{
				$args = preg_replace("/(^|[^_0-9A-Za-z\-])".$obj."/i", '$1'.$db, $args);
			}
			$query .= ' ORDER BY ' . $args;
		}
		
		if (array_key_exists('limit', $arguments))
		{
			$offset = $arguments['limit'][0];
			$numrows = $arguments['limit'][1];
		}
		
		return array($query, $queryparams, $numrows, $offset);
	}
	
	/**
	 * Generates an array of column names in the table that perists this class.  This
	 * list is then cached during the life of the request.
	 *
	 * @return array An array of column names
	 */
	function get_columns_in_table()
	{
		return CmsCache::get_instance()->call(array(&$this, '_get_columns_in_table'), $this->get_table());
	}
	
	function _get_columns_in_table($table)
	{
		$config = cms_config();

		$dbms = $config['dbms'];
		if ($dbms == 'mysqli') $dbms = 'mysql';

		include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dbo' . DIRECTORY_SEPARATOR . $dbms . '.get_columns_in_table.php');
		
		$fields = dbo_get_columns_in_table($table);
		
		return $fields;
	}
	
	/**
	 * Callback to call before the class is instantiated and the fields
	 * are set.  Keep in mind the scopes of before_load and after_load.
	 * before_load is called on the orm object, so keep it's implementation
	 * sort of "static" in nature.  after_load is called on the instantiated
	 * object.
	 *
	 * @param string Name of the class that we're going to instantiate
	 * @param array Hash of the fields that will be inserted into the object
	 * @return void
	 * @author Ted Kulp
	 */
	protected function before_load($type, $fields)
	{
	}
	
	/**
	 * Callback after object is loaded.  This allows the object to do any
	 * housekeeping, setting up other fields, etc before it's returned.
	 *
	 * @return void
	 * @author Ted Kulp
	 */
	protected function after_load()
	{
	}
	
	/**
	 * Callback sent before the object is saved.  This allows the object to
	 * send any events, manipulate any values, etc before the objects is
	 * persisted.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	protected function before_save()
	{
	}
	
	/**
	 * Callback sent after the object is saved.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	protected function after_save()
	{
	}
	
	/**
	 * Callback sent before the object is deleted from
	 * the database.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	protected function before_delete()
	{
	}
	
	/**
	 * Callback sent after the object is deleted from
	 * the database.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	protected function after_delete()
	{
	}
	
	/**
	 * Virtual function that is called before a save operation can be
	 * completed.  Allows the object to make sure that all the necessary
	 * fields are filled in, they're in the proper range, etc.
	 *
	 * @return void
	 * @author Ted Kulp
	 */
	function validate()
	{
	}
	
	/**
	 * Validation method to see if a parameter has been filled in.  This should
	 * be called from an object's validate() method on each field that needs to be
	 * filled in before it can be saved.
	 *
	 * @param string Name of the field to check
	 * @param string If given, this is the message that will be set in the object if the method didn't succed.
	 * @return void
	 * @author Ted Kulp
	 */
	function validate_not_blank($field, $message = '')
	{
		if ($this->$field == null || $this->$field == '')
		{
			$this->add_validation_error(($message != '' ? $message : lang('nofieldgiven',array($field))));
		}
	}
	
	/**
	 * Method for quickly adding a new validation error to the object.  If this is
	 * called, then it's a safe bet that save() will fail.
	 *
	 * @param string Message to add to the validation error stack
	 * @return void
	 * @author Ted Kulp
	 */
	function add_validation_error($message)
	{
		$this->validation_errors[] = $message;
	}
	
	/**
	 * Method to call the validation methods properly.
	 *
	 * @return int The number of validation errors
	 * @author Ted Kulp
	 **/
	public function _call_validation()
	{	
		//Clear them out first
		$this->validation_errors = array();
		
		//Call the validate method
		$this->validate();
		
		return (count($this->validation_errors) > 0);
	}
	
	public function __toString()
	{
		$id_field = $this->id_field;
		if (isset($this->$id_field))
			return get_class($this) . '- id:' . $this->$id_field;
		else
			return parent::__toString();
	}
}

# vim:ts=4 sw=4 noet
?>