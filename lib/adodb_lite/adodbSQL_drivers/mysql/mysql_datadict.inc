<?php
/**
  V4.65 22 July 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
  
  Modified 28 August, 2005 for use with ADOdb Lite by Mark Dickenson
  
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB2_mysql extends ADODB_DataDict {
	var $dbtype = 'mysql';
	var $alterCol = ' MODIFY COLUMN';
	var $alterTableAddIndex = true;
	var $dropTable = 'DROP TABLE IF EXISTS %s'; // requires mysql 3.22 or later
	
	var $dropIndex = 'DROP INDEX %s ON %s';
	var $renameColumn = 'ALTER TABLE %s CHANGE COLUMN %s %s %s';	// needs column-definition!
	var $metaTablesSQL = "SHOW TABLES";	
	var $metaColumnsSQL = "SHOW COLUMNS FROM %s";
	
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}
		$is_serial = is_object($fieldobj) && $fieldobj->primary_key && $fieldobj->auto_increment;
		
		$len = -1; // mysql max_length is not accurate
		switch (strtoupper($t)) {
		case 'STRING': 
		case 'CHAR':
		case 'VARCHAR': 
		case 'TINYBLOB': 
		case 'TINYTEXT': 
		case 'ENUM': 
		case 'SET':
			if ($len <= $this->blobSize) return 'C';
			
		case 'TEXT':
		case 'LONGTEXT': 
		case 'MEDIUMTEXT':
			return 'X';
			
		// php_mysql extension always returns 'blob' even if 'text'
		// so we have to check whether binary...
		case 'IMAGE':
		case 'LONGBLOB': 
		case 'BLOB':
		case 'MEDIUMBLOB':
			return !empty($fieldobj->binary) ? 'B' : 'X';
			
		case 'YEAR':
		case 'DATE': return 'D';
		
		case 'TIME':
			return 'T';
		case 'DATETIME':
			return 'DT';
		case 'TIMESTAMP': return 'TS';
		
		case 'FLOAT':
		case 'DOUBLE':
			return 'F';
			
		case 'INT': 
		case 'INTEGER': return $is_serial ? 'R' : 'I';
		case 'TINYINT': return $is_serial ? 'R' : 'I1';
		case 'SMALLINT': return $is_serial ? 'R' : 'I2';
		case 'MEDIUMINT': return $is_serial ? 'R' : 'I4';
		case 'BIGINT':  return $is_serial ? 'R' : 'I8';
		default: return 'N';
		}
	}

	function ActualType($meta)
	{
		switch(strtoupper($meta)) {
		case 'C': return 'VARCHAR';
		case 'XL':return 'LONGTEXT';
		case 'X': return 'TEXT';
		
		case 'C2': return 'VARCHAR';
		case 'X2': return 'LONGTEXT';
		
		case 'B': return 'LONGBLOB';
			
		case 'D': return 'DATE';
		case 'DT': return 'DATETIME';
		case 'T': return 'TIME';
		case 'TS': return 'TIMESTAMP';
		case 'L': return 'TINYINT';
		
		case 'R':
		case 'I4':
		case 'I': return 'INTEGER';
		case 'I1': return 'TINYINT';
		case 'I2': return 'SMALLINT';
		case 'I8': return 'BIGINT';
		
		case 'F': return 'DOUBLE';
		case 'N': return 'NUMERIC';
		default:
			return $meta;
		}
	}
	
	// return string must begin with space
	function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{	
		$suffix = '';
		if ($funsigned) $suffix .= ' UNSIGNED';
		if ($fnotnull) $suffix .= ' NOT NULL';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fautoinc) $suffix .= ' AUTO_INCREMENT';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}

	function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
	{
		$sql = array();
		
		if ( isset($idxoptions['REPLACE']) || isset($idxoptions['DROP']) ) {
			if ($this->alterTableAddIndex) $sql[] = "ALTER TABLE $tabname DROP INDEX $idxname";
			else $sql[] = sprintf($this->dropIndex, $idxname, $tabname);

			if ( isset($idxoptions['DROP']) )
				return $sql;
		}
		
		if ( empty ($flds) ) {
			return $sql;
		}
		
		if (isset($idxoptions['FULLTEXT'])) {
			$unique = ' FULLTEXT';
		} elseif (isset($idxoptions['UNIQUE'])) {
			$unique = ' UNIQUE';
		} else {
			$unique = '';
		}
		
		if ( is_array($flds) ) $flds = implode(', ',$flds);
		
		if ($this->alterTableAddIndex) $s = "ALTER TABLE $tabname ADD $unique INDEX $idxname ";
		else $s = 'CREATE' . $unique . ' INDEX ' . $idxname . ' ON ' . $tabname;
		
		$s .= ' (' . $flds . ')';
		
		if ( isset($idxoptions[$this->upperName]) )
			$s .= $idxoptions[$this->upperName];
		
		$sql[] = $s;
		
		return $sql;
	}

	/**
	 * @param ttype can either be 'VIEW' or 'TABLE' or false. 
	 * 		If false, both views and tables are returned.
	 *		"VIEW" returns only views
	 *		"TABLE" returns only tables
	 * @param showSchema returns the schema/user with the table name, eg. USER.TABLE
	 * @param mask  is the input mask - only supported by oci8 and postgresql
	 *
	 * @return  array of tables for current database.
	 */ 
	function &MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{	
		$save = $this->metaTablesSQL;
		if ($showSchema && is_string($showSchema)) {
			$this->metaTablesSQL .= " from $showSchema";
		}
		
		if ($mask) {
			$mask = $this->connection->qstr($mask);
			$this->metaTablesSQL .= " like $mask";
		}
		$ret =& $this->_MetaTables($ttype,$showSchema);
		
		$this->metaTablesSQL = $save;
		return $ret;
	}

	function &_MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{
	global $ADODB_FETCH_MODE;
	
		
		$false = false;
		if ($mask) {
			return $false;
		}
		if ($this->metaTablesSQL) {
			$save = $ADODB_FETCH_MODE; 
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM; 
			
			if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
			
			$rs = $this->connection->Execute($this->metaTablesSQL);
			if (isset($savem)) $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save; 
			
			if ($rs === false) return $false;
			$arr =& $rs->GetArray();
			$arr2 = array();
			
			if ($hast = ($ttype && isset($arr[0][1]))) { 
				$showt = strncmp($ttype,'T',1);
			}
			
			for ($i=0; $i < sizeof($arr); $i++) {
				if ($hast) {
					if ($showt == 0) {
						if (strncmp($arr[$i][1],'T',1) == 0) $arr2[] = trim($arr[$i][0]);
					} else {
						if (strncmp($arr[$i][1],'V',1) == 0) $arr2[] = trim($arr[$i][0]);
					}
				} else
					$arr2[] = trim($arr[$i][0]);
			}
			$rs->Close();
			return $arr2;
		}
		return $false;
	}

	/**
	 * List columns in a database as an array of ADOFieldObjects. 
	 * See top of file for definition of object.
	 *
	 * @param table	table name to query
	 * @param upper	uppercase table name (required by some databases)
	 * @schema is optional database schema to use - not supported by all databases.
	 *
	 * @return  array of ADOFieldObjects for current table.
	 */
 	function MetaColumns($table) 
	{
		$this->_findschema($table,$schema);
		if ($schema) {
			$dbName = $this->database;
			$this->connection->SelectDB($schema);
		}
		global $ADODB_FETCH_MODE;
		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		
		if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
		$rs = $this->connection->Execute(sprintf($this->metaColumnsSQL,$table));
		
		if ($schema) {
			$this->connection->SelectDB($dbName);
		}
		
		if (isset($savem)) $this->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;
		if (!is_object($rs)) {
			$false = false;
			return $false;
		}
			
		$retarr = array();
		while (!$rs->EOF){
			$fld = new ADOFieldObject();
			$fld->name = $rs->fields[0];
			$type = $rs->fields[1];
			
			// split type into type(length):
			$fld->scale = null;
			if (preg_match("/^(.+)\((\d+),(\d+)/", $type, $query_array)) {
				$fld->type = $query_array[1];
				$fld->max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
				$fld->scale = is_numeric($query_array[3]) ? $query_array[3] : -1;
			} elseif (preg_match("/^(.+)\((\d+)/", $type, $query_array)) {
				$fld->type = $query_array[1];
				$fld->max_length = is_numeric($query_array[2]) ? $query_array[2] : -1;
			} elseif (preg_match("/^(enum)\((.*)\)$/i", $type, $query_array)) {
				$fld->type = $query_array[1];
				$arr = explode(",",$query_array[2]);
				$fld->enums = $arr;
				$zlen = max(array_map("strlen",$arr)) - 2; // PHP >= 4.0.6
				$fld->max_length = ($zlen > 0) ? $zlen : 1;
			} else {
				$fld->type = $type;
				$fld->max_length = -1;
			}
			$fld->not_null = ($rs->fields[2] != 'YES');
			$fld->primary_key = ($rs->fields[3] == 'PRI');
			$fld->auto_increment = (strpos($rs->fields[5], 'auto_increment') !== false);
			$fld->binary = (strpos($type,'blob') !== false);
			$fld->unsigned = (strpos($type,'unsigned') !== false);
				
			if (!$fld->binary) {
				$d = $rs->fields[4];
				if ($d != '' && $d != 'NULL') {
					$fld->has_default = true;
					$fld->default_value = $d;
				} else {
					$fld->has_default = false;
				}
			}
			
			if ($save == ADODB_FETCH_NUM) {
				$retarr[] = $fld;
			} else {
				$retarr[strtoupper($fld->name)] = $fld;
			}
				$rs->MoveNext();
			}
		
			$rs->Close();
			return $retarr;	
	}

	function _findschema(&$table,&$schema)
	{
		if (!$schema && ($at = strpos($table,'.')) !== false) {
			$schema = substr($table,0,$at);
			$table = substr($table,$at+1);
		}
	}

	/**
	 * @returns an array with the primary key columns in it.
	 */
	function MetaPrimaryKeys($table, $owner=false)
	{
	// owner not used in base class - see oci8
		$p = array();
		$objs =& $this->MetaColumns($table);
		if ($objs) {
			foreach($objs as $v) {
				if (!empty($v->primary_key))
					$p[] = $v->name;
			}
		}
		if (sizeof($p)) return $p;
		if (function_exists('ADODB_VIEW_PRIMARYKEYS'))
			return ADODB_VIEW_PRIMARYKEYS($this->databaseType, $this->database, $table, $owner);
		return false;
	}

    /**
      * List indexes on a table as an array.
      * @param table  table name to query
      * @param primary true to only show primary keys. Not actually used for most databases
	  *
      * @return array of indexes on current table. Each element represents an index, and is itself an associative array.
	  
		 Array (
		    [name_of_index] => Array
		      (
	          [unique] => true or false
	          [columns] => Array
	          (
	          	[0] => firstname
		      	[1] => lastname
	          )
		)		
      */
	function MetaIndexes ($table, $primary = FALSE, $owner=false)
	{
        // save old fetch mode
        global $ADODB_FETCH_MODE;
        
		$false = false;
        $save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        if ($this->fetchMode !== FALSE) {
               $savem = $this->SetFetchMode(FALSE);
        }
        
        // get index details
        $rs =& $this->connection->Execute(sprintf('SHOW INDEX FROM %s',$table));
        
        // restore fetchmode
        if (isset($savem)) {
                $this->SetFetchMode($savem);
        }
        $ADODB_FETCH_MODE = $save;
        
        if (!is_object($rs)) {
                return $false;
        }
        
        $indexes = array ();
        
        // parse index data into array
        while (!$rs->EOF)
        {
                if ($primary == FALSE AND $rs->fields[2] == 'PRIMARY') {
                	$rs->MoveNext();
                        continue;
                }

                if (!isset($indexes[$rs->fields[2]])) {
                        $indexes[$rs->fields[2]] = array(
                                'unique' => ($rs->fields[1] == 0),
                                'columns' => array()
                        );
                }

                $indexes[$rs->fields[2]]['columns'][$rs->fields[3] - 1] = $rs->fields[4];

        	$rs->MoveNext();
        }
        
        // sort columns by order in the index
        foreach ( array_keys ($indexes) as $index )
        {
                ksort ($indexes[$index]['columns']);
        }
        
        return $indexes;
	}
}

?>