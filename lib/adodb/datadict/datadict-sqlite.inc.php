<?php

/**
  v4.991 16 Oct 2008  (c) 2000-2008 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB2_sqlite extends ADODB_DataDict {
	
	var $databaseType = 'sqlite';
	var $seqField = false;
	
 	
 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'XL':return 'TEXT';
		case 'X': return 'TEXT';

		case 'C2': return 'VARCHAR';
		case 'X2': return 'TEXT';
		
		case 'B': return 'BLOB';
			
		case 'D': return 'DATE';
		case 'T': return 'DATETIME';
		case 'L': return 'INTEGER';
		
		case 'I4': return 'INTEGER';
		case 'I': return 'INTEGER';
		case 'I1': return 'INTEGER';
		case 'I2': return 'INTEGER';
		case 'I8': return 'INTEGER';
		
		case 'F': return 'NUMERIC';
		case 'N': return 'NUMERIC';
		default:
			return $meta;
		}
	}
	
	function AlterColumnSQL($tabname, $flds)
	{
		if ($this->debug) ADOConnection::outp("AlterColumnSQL not supported");
		return array();
	}
	
	function DropColumnSQL($tabname, $flds)
	{
		if ($this->debug) ADOConnection::outp("DropColumnSQL not supported");
		return array();
	}
	
	// return string must begin with space
	function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{	
		$suffix = '';
		//if ($funsigned) $suffix .= ' UNSIGNED';
		if ($fnotnull && !$fautoinc) $suffix .= ' NOT NULL'; //Can't have NOT NULL and AUTOINC in the same entry
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fautoinc) $suffix .= ' AUTOINCREMENT';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}
	
}

/*
//db2
 	function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';
		case 'X': return 'VARCHAR'; 
		
		case 'C2': return 'VARCHAR'; // up to 32K
		case 'X2': return 'VARCHAR';
		
		case 'B': return 'BLOB';
			
		case 'D': return 'DATE';
		case 'T': return 'TIMESTAMP';
		
		case 'L': return 'SMALLINT';
		case 'I': return 'INTEGER';
		case 'I1': return 'SMALLINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INTEGER';
		case 'I8': return 'BIGINT';
		
		case 'F': return 'DOUBLE';
		case 'N': return 'DECIMAL';
		default:
			return $meta;
		}
	}
	
// ifx
function ActualType($meta)
	{
		switch($meta) {
		case 'C': return 'VARCHAR';// 255
		case 'X': return 'TEXT'; 
		
		case 'C2': return 'NVARCHAR';
		case 'X2': return 'TEXT';
		
		case 'B': return 'BLOB';
			
		case 'D': return 'DATE';
		case 'T': return 'DATETIME';
		
		case 'L': return 'SMALLINT';
		case 'I': return 'INTEGER';
		case 'I1': return 'SMALLINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INTEGER';
		case 'I8': return 'DECIMAL(20)';
		
		case 'F': return 'FLOAT';
		case 'N': return 'DECIMAL';
		default:
			return $meta;
		}
	}
*/
?>