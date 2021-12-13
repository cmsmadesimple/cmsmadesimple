<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: \CMSMS\Database\DataDictionary (c) 2015 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A class to define methods of interacting with database tables.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE

/**
 * This file defines the DataDictionary class.
 *
 * This file is based on the DataDictionary base class from the adodb_lite library
 * which was in turn a fork of the adodb library at approximately 2004.
 *
 * Credits and kudos to the authors of those packages.
 *
 * @package CMS
 */

namespace CMSMS\Database;

// shouldn't need this.
if (!function_exists('ctype_alnum')) {
    /**
     * @ignore
     */
	function ctype_alnum($text) {
		return preg_match('/^[a-z0-9]*$/i', $text);
	}
}

/**
 * @ignore
 */
function _array_change_key_case($an_array)
{
	if (is_array($an_array)) {
		$new_array = array();
		foreach($an_array as $key=>$value)
			$new_array[strtoupper($key)] = $value;

	   	return $new_array;
   }

	return $an_array;
}

/**
 * @ignore
 */
function Lens_ParseArgs($args,$endstmtchar=',',$tokenchars='_.-')
{
	$pos = 0;
	$intoken = false;
	$stmtno = 0;
	$endquote = false;
	$tokens = array();
	$tokens[$stmtno] = array();
	$max = strlen($args);
	$quoted = false;

	while ($pos < $max) {
		$ch = substr($args,$pos,1);
		switch($ch) {
		case ' ':
		case "\t":
		case "\n":
		case "\r":
			if (!$quoted) {
				if ($intoken) {
					$intoken = false;
					$tokens[$stmtno][] = implode('',$tokarr);
				}
				break;
			}
			$tokarr[] = $ch;
			break;
		case '`':
			if ($intoken) $tokarr[] = $ch;
		case '(':
		case ')':
		case '"':
		case "'":
			if ($intoken) {
				if (empty($endquote)) {
					$tokens[$stmtno][] = implode('',$tokarr);
					if ($ch == '(') $endquote = ')';
					else $endquote = $ch;
					$quoted = true;
					$intoken = true;
					$tokarr = array();
				} else if ($endquote == $ch) {
					$ch2 = substr($args,$pos+1,1);
					if ($ch2 == $endquote) {
						$pos += 1;
						$tokarr[] = $ch2;
					} else {
						$quoted = false;
						$intoken = false;
						$tokens[$stmtno][] = implode('',$tokarr);
						$endquote = '';
					}
				} else
					$tokarr[] = $ch;
			}else {
				if ($ch == '(') $endquote = ')';
				else $endquote = $ch;
				$quoted = true;
				$intoken = true;
				$tokarr = array();
				if ($ch == '`') $tokarr[] = '`';
			}
			break;
		default:
			if (!$intoken) {
				if ($ch == $endstmtchar) {
					$stmtno += 1;
					$tokens[$stmtno] = array();
					break;
				}
				$intoken = true;
				$quoted = false;
				$endquote = false;
				$tokarr = array();
			}
			if ($quoted) $tokarr[] = $ch;
			else if (ctype_alnum($ch) || strpos($tokenchars,$ch) !== false) $tokarr[] = $ch;
			else {
				if ($ch == $endstmtchar) {
					$tokens[$stmtno][] = implode('',$tokarr);
					$stmtno += 1;
					$tokens[$stmtno] = array();
					$intoken = false;
					$tokarr = array();
					break;
				}
				$tokens[$stmtno][] = implode('',$tokarr);
				$tokens[$stmtno][] = $ch;
				$intoken = false;
			}
		}
		$pos += 1;
	}
	if ($intoken) $tokens[$stmtno][] = implode('',$tokarr);

	return $tokens;
}


/**
 * A class defining methods to work directly with database tables.
 *
 * This file is based on the DataDictionary base class from the adodb_lite library
 * which was in turn a fork of the adodb library at approximately 2004.
 *
 * Credits and kudos to the authors of those packages.
 *
 * @package CMS
 * @author Robert Campbell
 * @copyright Copyright (c) 2015, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.2
 */
abstract class DataDictionary
{
    /**
     * The database connection object.
     *
     * @internal
     */
	protected $connection;

    /**
     * The SQL prefix to use when creating a drop table command.
     *
     * @internal
     */
	protected $dropTable = 'DROP TABLE %s';

    /**
     * The SQL prefix to use when renaming a table.
     *
     * @internal
     */
	protected $renameTable = 'RENAME TABLE %s TO %s';

    /**
     * The SQL prefix to use when dropping an index.
     *
     * @internal
     */
	protected $dropIndex = 'DROP INDEX %s';

    /**
     * The SQL string to use (in the alter table command) when adding a column.
     *
     * @internal
     */
	protected $addCol = ' ADD';

    /**
     * The SQL string to use (in the alter table command) when altering a column.
     *
     * @internal
     */
	protected $alterCol = ' ALTER COLUMN';

    /**
     * The SQL string to use (in the alter table command) when dropping a column.
     *
     * @internal
     */
	protected $dropCol = ' DROP COLUMN';

    /**
     * The SQL command template for renaming a column.
     *
     * @internal
     */
	protected $renameColumn = 'ALTER TABLE %s RENAME COLUMN %s TO %s';	// table, old-column, new-column, column-definitions (not used by default)

    /**
     * @ignore
     */
	protected $nameRegex = '\w';

    /**
     * @ignore
     */
	protected $nameRegexBrackets = 'a-zA-Z0-9_\(\)';

    /**
     * @ignore
     */
	protected $autoIncrement = false;

    /**
     * @ignore
     */
	protected $invalidResizeTypes4 = array('CLOB','BLOB','TEXT','DATE','TIME'); // for changetablesql

    /**
     * Constructor
     *
     * @param \CMSMS\Database\Connection $conn
     */
    protected function __construct(Connection $conn)
    {
        $this->connection = $conn;
    }

    /**
     * A function to return the database type.
     *
     * @internal
     * @return string
     */
    protected function _DBType() { return $this->connection->DbType(); }

    /**
     * A function to return the datadictionary meta type for a database column type.
     *
     * @internal
     * @param string $t The database column type
     * @param int $len The length of the field (some database types may ignore this)
     * @param mixed $fieldobj An optional reference to a field object (advanced)
     * @return string
     */
	abstract protected function MetaType($t,$len=-1,$fieldobj=false);

    /**
     * Return the list of tables in the currently connected database.
     *
     * @return string[]
     */
    abstract public function MetaTables();

    /**
     * Return the list of columns in a table within the currently connected database.
     *
     * @param string $table The table name.
     * @return string[]
     */
    abstract public function MetaColumns($table);

    /**
     * Return a database specific column type given a datadictionary meta column type.
     *
     * @internal
     * @param string $meta The datadictionary column type.
     * @return string
     */
	abstract protected function ActualType($meta);

    /**
     * Given a string name, return a quoted name in a form suitable for the database.
     *
     * @internal
     * @param string $name The input name
     * @param bool $allowBrackets wether brackets should be quoted or not.
     * @return string
     */
	protected function NameQuote($name = NULL,$allowBrackets=false)
	{
		if (!is_string($name)) return FALSE;

		$name = trim($name);

		if ( !is_object($this->connection) ) return $name;

		$quote = $this->connection->nameQuote;

		// if name is of the form `name`, quote it
		if ( preg_match('/^`(.+)`$/', $name, $matches) ) return $quote . $matches[1] . $quote;

		// if name contains special characters, quote it
		$regex = ($allowBrackets) ? $this->nameRegexBrackets : $this->nameRegex;

		if ( !preg_match('/^[' . $regex . ']+$/', $name) ) return $quote . $name . $quote;

		return $name;
	}

    /**
     * Given a table name, optionally quote it.
     *
     * @internal
     * @param string $name
     * @return string
     */
	protected function TableName($name)
	{
		return $this->NameQuote($name);
	}

    /**
     * Given an array of SQL commands execute them in sequence.
     *
     * @param string[] $sql An array of sql commands.
     * @param bool $continueOnError wether to continue on errors or not.
     * @return int 2 for no errors, 1 if an error occured.
     */
	public function ExecuteSQLArray($sql, $continueOnError = true)
	{
		$rez = 2;
		$conn = &$this->connection;
		foreach($sql as $line) {
            try {
                $ok = $conn->Execute($line);
                if (!$ok) {
                    if (!$continueOnError) return 0;
                    $rez = 1;
                }
            }
            catch( \Exception $e ) {
                if( !$continueOnError ) throw $e;
                $rez = 1;
                // eat the exception
            }
		}
		return $rez;
	}

    /**
     * Create the SQL commands that will result in a database being created.
     *
     * @param string $dbname
     * @param array  An associative array of database options.
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
     */
	public function CreateDatabase($dbname,$options=false)
	{
		$options = $this->_Options($options);
		$sql = array();

		$s = 'CREATE DATABASE ' . $this->NameQuote($dbname);
		if (isset($options[$this->upperName]))
			$s .= ' '.$options[$this->upperName];

		$sql[] = $s;
		return $sql;
	}

	/**
     * Generate the SQL to create an index.
     *
     * @param string $idxname The index name
     * @param string $tabname The table name
     * @param string|string[] $flds A list of the table fields to create the index with.  Either an array of strings or a comma separated list.
     * @param array An associative array of options
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
	*/
	public function CreateIndexSQL($idxname, $tabname, $flds, $idxoptions = false)
	{
		if (!is_array($flds)) {
			$flds = explode(',',$flds);
		}
		foreach($flds as $key => $fld) {
			# some indexes can use partial fields, eg. index first 32 chars of "name" with NAME(32)
			$flds[$key] = $this->NameQuote($fld,$allowBrackets=true);
		}
		return $this->_IndexSQL($this->NameQuote($idxname), $this->TableName($tabname), $flds, $this->_Options($idxoptions));
	}

    /**
     * Generate the SQL to drop an index
     *
     * @param string $idxname The index name
     * @param string $tabname The table name
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
     */
	public function DropIndexSQL ($idxname, $tabname = NULL)
	{
		return array(sprintf($this->dropIndex, $this->NameQuote($idxname), $this->TableName($tabname)));
	}

    /**
     * Generate the SQL to add columns to a table.
     *
     * @param string $tabname The Table name.
     * @param string $flds The column definitions (using DataDictionary meta types)
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
     * @see CreateTableSQL
     */
	public function AddColumnSQL($tabname, $flds)
	{
		$tabname = $this->TableName ($tabname);
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$alter = 'ALTER TABLE ' . $tabname . $this->addCol . ' ';
		foreach($lines as $v) {
			$sql[] = $alter . $v;
		}
		return $sql;
	}

	/**
	 * Change the definition of one column
	 *
	 * @param string $tabname table-name
	 * @param string $flds column-name and type for the changed column.
	 * @param string $tableflds complete defintion of the new table, eg. for postgres, default ''
	 * @param array/string $tableoptions options for the new table see CreateTableSQL, default ''
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
	 */
	public function AlterColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
	{
		$tabname = $this->TableName ($tabname);
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$alter = 'ALTER TABLE ' . $tabname . $this->alterCol . ' ';
		foreach($lines as $v) {
			$sql[] = $alter . $v;
		}
		return $sql;
	}

	/**
	 * Rename one column in a table.
	 *
	 * @param string $tabname table-name
	 * @param string $oldcolumn column-name to be renamed
	 * @param string $newcolumn new column-name
	 * @param string $flds complete column-defintion-string like for AddColumnSQL, only used by mysql atm., default=''
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
	 */
	public function RenameColumnSQL($tabname,$oldcolumn,$newcolumn,$flds='')
	{
		$tabname = $this->TableName ($tabname);
		if ($flds) {
			list($lines,$pkey) = $this->_GenFields($flds);
			$first = current($lines);
			list(,$column_def) = preg_split("/[\t ]+/",$first,2);
		}
		return array(sprintf($this->renameColumn,$tabname,$this->NameQuote($oldcolumn),$this->NameQuote($newcolumn),$column_def));
	}

	/**
	 * Drop one column from a table.
	 *
	 * @param string $tabname table-name
	 * @param string $flds column-name and type for the changed column
	 * @param string $tableflds complete defintion of the new table, eg. for postgres, default ''
	 * @param array/string $tableoptions options for the new table see CreateTableSQL, default ''
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
	 */
	public function DropColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
	{
		$tabname = $this->TableName ($tabname);
		if (!is_array($flds)) $flds = explode(',',$flds);
		$sql = array();
		$alter = 'ALTER TABLE ' . $tabname . $this->dropCol . ' ';
		foreach($flds as $v) {
			$sql[] = $alter . $this->NameQuote($v);
		}
		return $sql;
	}

    /**
     * Drop one table, and all of it's indexes
     *
     * @param string $tabname The table name to drop.
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
     */
	public function DropTableSQL($tabname)
	{
		return array (sprintf($this->dropTable, $this->TableName($tabname)));
	}

    /**
     * Rename a table.
     *
     * @param string $tabname The table name
     * @param string $newname The new table name
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
     */
	public function RenameTableSQL($tabname,$newname)
	{
		return array (sprintf($this->renameTable, $this->TableName($tabname),$this->TableName($newname)));
	}

	/**
     * Generate the SQL to create a new table.
     *
     * The flds string is a comma separated of field definitions, where each definition is of the form
     *    fieldname type columnsize otheroptions
     *
     * The type fields are codes that map to real database types as follows:
     * <dl>
     *  <dt>C</dt>
     *  <dd>Varchar, capped to 255 characters.</dd>
     *  <dt>X</dt>
     *  <dd>Text</dd>
     *  <dt>XL</dt>
     *  <dd>LongText</dd>
     *  <dt>C2</dt>
     *  <dd>Varchar, capped to 255 characters</dd>
     *  <dt>XL</dt>
     *  <dd>LongText</dd>
     *  <dt>B</dt>
     *  <dd>LongBlob</dd>
     *  <dt>D</dt>
     *  <dd>Date</dd>
     *  <dt>DT</dt>
     *  <dd>DateTime</dd>
     *  <dt>T</dt>
     *  <dd>Time</dd>
     *  <dt>TS</dt>
     *  <dd>Timestamp</dd>
     *  <dt>L</dt>
     *  <dd>TinyInt</dd>
     *  <dt>R / I4 / I</dt>
     *  <dd>Integer</dd>
     *  <dt>I1</dt>
     *  <dd>TinyInt</dd>
     *  <dt>I2</dt>
     *  <dd>SmallInt</dd>
     *  <dt>I4</dt>
     *  <dd>BigInt</dd>
     *  <dt>F</dt>
     *  <dd>Double</dd>
     *  <dt>N</dt>
     *  <dd>Numeric</dd>
     *</dl>
     *
     * The otheroptions field includes the following options:
     *<dl>
     *  <dt>AUTO</dt>
     *  <dd>Auto increment. Also sets NOTNULL.</dd>
     *  <dt>AUTOINCREMENT</dt>
     *  <dd>Same as AUTO</dd>
     *  <dt>KEY</dt>
     *  <dd>Primary key field.  Also sets NOTNULL. Compound keys are supported.</dd>
     *  <dt>PRImARY</dt>
     *  <dd>Same as KEY</dd>
     *  <dt>DEFAULT</dt>
     *  <dd>The default value.  Character strings are auto-quoted unless the string begins with a space.  i.e: ' SYSDATE '.</dd>
     *  <dt>DEF</dt>
     *  <dd>Same as DEFAULT</dd>
     *  <dt>CONSTRAINTS</dt>
     *  <dd>Additional constraints defined at the end of the field definition.</dd>
     *</dl>
     *
     * @param string $tabname The table name
     * @param string $flds a comma separated list of field definitions using datadictionary syntax.
     * @param mixed  $tableoptions A string specifying table options (database driver specific) for the table creation command.  Or an associative array of table options, keys being the database type (as available).
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
     */
	public function CreateTableSQL($tabname, $flds, $tableoptions=false)
	{
        if( $tableoptions && is_string($tableoptions)) {
            $dbtype = $this->_DBType();
            $tableoptions = [ $dbtype => $tableoptions ];
        }

        list($lines,$pkey) = $this->_GenFields($flds, true);
		$taboptions = $this->_Options($tableoptions);
		$tabname = $this->TableName ($tabname);
		$sql = $this->_TableSQL($tabname,$lines,$pkey,$taboptions);
		$tsql = $this->_Triggers($tabname,$taboptions);
		foreach($tsql as $s) $sql[] = $s;

		return $sql;
	}

    /**
     * Part of the process of parsing the datadictionary format into database specific commands.
     *
     * @internal
     */
	protected function _GenFields($flds,$widespacing=false)
	{
		if (is_string($flds)) {
			$padding = '	 ';
			$txt = $flds.$padding;
			$flds = array();
			$flds0 = Lens_ParseArgs($txt,',');
			$hasparam = false;
			foreach($flds0 as $f0) {
                if( !count($f0) ) break;
				$f1 = array();
				foreach($f0 as $token) {
					switch (strtoupper($token)) {
					case 'CONSTRAINT':
					case 'DEFAULT':
						$hasparam = $token;
						break;
					default:
						if ($hasparam) $f1[$hasparam] = $token;
						else $f1[] = $token;
						$hasparam = false;
						break;
					}
				}
				$flds[] = $f1;
			}
		}
		$this->autoIncrement = false;
		$lines = array();
		$pkey = array();
		foreach($flds as $fld) {
			$fld = _array_change_key_case($fld);
			$fname = false;
			$fdefault = false;
			$fautoinc = false;
			$ftype = false;
			$fsize = false;
			$fprec = false;
			$fprimary = false;
			$fnoquote = false;
			$fdefts = false;
			$fdefdate = false;
			$fconstraint = false;
			$fnotnull = false;
			$funsigned = false;

			//-----------------
			// Parse attributes
			foreach($fld as $attr => $v) {
				if ($attr == 2 && is_numeric($v)) $attr = 'SIZE';
				else if (is_numeric($attr) && $attr > 1 && !is_numeric($v)) $attr = strtoupper($v);
				switch($attr) {
					case '0':
					case 'NAME':
						$fname = $v;
						break;
					case '1':
					case 'TYPE':
						$ty = $v; $ftype = $this->ActualType(strtoupper($v));
						break;
					case 'SIZE':
						$dotat = strpos($v,'.');
						if ($dotat === false) $dotat = strpos($v,',');
						if ($dotat === false) $fsize = $v;
						else {
								$fsize = substr($v,0,$dotat);
								$fprec = substr($v,$dotat+1);
							}
						break;
					case 'UNSIGNED':
						$funsigned = true;
						break;
					case 'AUTOINCREMENT':
					case 'AUTO':
						$fautoinc = true;
						$fnotnull = true;
						break;
					case 'KEY':
					case 'PRIMARY':
						$fprimary = $v;
						$fnotnull = true;
						break;
					case 'DEF':
					case 'DEFAULT':
						$fdefault = $v;
						break;
					case 'NOTNULL':
						$fnotnull = $v;
						break;
					case 'NOQUOTE':
						$fnoquote = $v;
						break;
					case 'DEFDATE':
						$fdefdate = $v;
						break;
					case 'DEFTIMESTAMP':
						$fdefts = $v;
						break;
					case 'CONSTRAINT':
						$fconstraint = $v;
						break;
				}
			}

			//--------------------
			// VALIDATE FIELD INFO
			if (!strlen($fname)) {
                die('failed');
				return false;
			}

			$fid = strtoupper(preg_replace('/^`(.+)`$/', '$1', $fname));
			$fname = $this->NameQuote($fname);

			if (!strlen($ftype)) {
				return false;
			} else {
				$ftype = strtoupper($ftype);
			}

			$ftype = $this->_GetSize($ftype, $ty, $fsize, $fprec);

			if ($ty == 'X' || $ty == 'X2' || $ty == 'B') $fnotnull = false; // some blob types do not accept nulls

			if ($fprimary) $pkey[] = $fname;

			// some databases do not allow blobs to have defaults
			if ($ty == 'X') $fdefault = false;

			//--------------------
			// CONSTRUCT FIELD SQL
			if ($fdefts) {
				if (substr($this->_DbType(),0,5) == 'mysql') {
					$ftype = 'TIMESTAMP';
				} else {
					$fdefault = $this->connection->sysTimeStamp;
				}
			} else if ($fdefdate) {
				if (substr($this->_DBType(),0,5) == 'mysql') {
					$ftype = 'TIMESTAMP';
				} else {
					$fdefault = $this->connection->sysDate;
				}
			} else if ($fdefault !== false && !$fnoquote)
				if ($ty == 'C' or $ty == 'X' or
					( substr($fdefault,0,1) != "'" && !is_numeric($fdefault)))
					if (strlen($fdefault) != 1 && substr($fdefault,0,1) == ' ' && substr($fdefault,strlen($fdefault)-1) == ' ')
						$fdefault = trim($fdefault);
					else if (strtolower($fdefault) != 'null')
						$fdefault = $this->connection->qstr($fdefault);
			$suffix = $this->_CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned);

			if ($widespacing) $fname = str_pad($fname,24);
			$lines[$fid] = $fname.' '.$ftype.$suffix;

			if ($fautoinc) $this->autoIncrement = true;
		} // foreach $flds
		return array($lines,$pkey);
	}

	/**
     * Generate the size part of the datatype.
     *
     * @ignore
     * @internal
     */
	protected function _GetSize($ftype, $ty, $fsize, $fprec)
	{
		if (strlen($fsize) && $ty != 'X' && $ty != 'B' && strpos($ftype,'(') === false) {
			$ftype .= "(".$fsize;
			if (strlen($fprec)) $ftype .= ",".$fprec;
			$ftype .= ')';
		}
		return $ftype;
	}

    /**
     * Create a suffix
     *
     * @internal
     */
	protected function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{
		$suffix = '';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fnotnull) $suffix .= ' NOT NULL';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}

    /**
     * build SQL commands for indexes.
     *
     * @internal
     */
	protected function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
	{
		$sql = array();

		if ( isset($idxoptions['REPLACE']) || isset($idxoptions['DROP']) ) {
			$sql[] = sprintf ($this->dropIndex, $idxname);
			if ( isset($idxoptions['DROP']) ) return $sql;
		}

		if ( empty ($flds) ) return $sql;

		$unique = isset($idxoptions['UNIQUE']) ? ' UNIQUE' : '';

		$s = 'CREATE' . $unique . ' INDEX ' . $idxname . ' ON ' . $tabname . ' ';

		if ( isset($idxoptions[$this->upperName]) )
			$s .= $idxoptions[$this->upperName];

		if ( is_array($flds) )	$flds = implode(', ',$flds);
		$s .= '(' . $flds . ')';
		$sql[] = $s;

		return $sql;
	}

    /**
     * A method to drop the auto increment column on a table.
     *
     * @internal
     */
	protected function _DropAutoIncrement($tabname)
	{
		return false;
	}

    /**
     * An internal method to get a list of database type specific options for a command.
     *
     * @internal
     */
    protected function get_dbtype_options($opts,$suffix = null)
    {
        $dbtype = $this->_DBType();
        $list = array($dbtype.$suffix,strtoupper($dbtype).$suffix,strtolower($dbtype).$suffix);

        foreach( $list as $one ) {
            if( isset($opts[$one]) && is_string($opts[$one]) && strlen($opts[$one]) ) return $opts[$one];
        }
    }

    /**
     * Build strings for generating tables.
     *
     * @internal
     */
	protected function _TableSQL($tabname,$lines,$pkey,$tableoptions)
	{
		$sql = array();

		if (isset($tableoptions['REPLACE']) || isset ($tableoptions['DROP'])) {
			$sql[] = sprintf($this->dropTable,$tabname);
			if ($this->autoIncrement) {
				$sInc = $this->_DropAutoIncrement($tabname);
				if ($sInc) $sql[] = $sInc;
			}
			if ( isset ($tableoptions['DROP']) ) {
				return $sql;
			}
		}
		$s = "CREATE TABLE $tabname (\n";
		$s .= implode(",\n", $lines);
		if (sizeof($pkey)>0) {
			$s .= ",\n				 PRIMARY KEY (";
			$s .= implode(", ",$pkey).")";
		}
		if (isset($tableoptions['CONSTRAINTS']))
			$s .= "\n".$tableoptions['CONSTRAINTS'];

        $str = $this->get_dbtype_options($tableoptions,'_CONSTRAINTS');
        if( $str ) $s .= "\n".$str;

		$s .= "\n)";
        $str = $this->get_dbtype_options($tableoptions);
        if( $str ) $s .= $str;
		$sql[] = $s;

		return $sql;
	}

    /**
     * Generate triggers if needed.
	 * This is used when table has auto-incrementing field that is emulated using triggers.
     *
     * @internal
     */
	protected function _Triggers($tabname,$taboptions)
	{
		return array();
	}

	/**
     * Sanitize options,
     *
     * @internal
     */
    protected function _ProcessOptions($opts)
    {
        return $opts;
    }

    /**
     * Convert options into a format usable by the system.
     *
     * @internal
     */
	protected function _Options($opts)
	{
        $opts = $this->_ProcessOptions($opts);
		if (!is_array($opts)) return array();
		$newopts = array();
		foreach($opts as $k => $v) {
			if (is_numeric($k)) $newopts[strtoupper($v)] = $v;
			else $newopts[strtoupper($k)] = $v;
		}
		return $newopts;
	}

	/**
     * Add, drop or change columns within a table.
     *
     * This function changes/adds new fields to your table. You don't
     * have to know if the col is new or not. It will check on its own.
     *
     * @param string $tablename The table name
     * @param string $flds The field definitions
     * @param array  $tableoptions Table options
     * @return string[] An array of strings suitable for use with the ExecuteSQLArray method
     */
	public function ChangeTableSQL($tablename, $flds, $tableoptions = false)
	{
		// check table exists
		$cols = $this->MetaColumns($tablename);

		if ( empty($cols)) {
			return $this->CreateTableSQL($tablename, $flds, $tableoptions);
		}

		if (is_array($flds)) {
			// Cycle through the update fields, comparing
			// existing fields to fields to update.
			// if the Metatype and size is exactly the
			// same, ignore - by Mark Newham
			$holdflds = array();
			foreach($flds as $k=>$v) {
				if ( isset($cols[$k]) && is_object($cols[$k]) ) {
					$c = $cols[$k];
					$ml = $c->max_length;
					$mt = &$this->MetaType($c->type,$ml);
					if ($ml == -1) $ml = '';
					if ($mt == 'X') $ml = $v['SIZE'];
					if (($mt != $v['TYPE']) ||  $ml != $v['SIZE']) $holdflds[$k] = $v;
				} else {
					$holdflds[$k] = $v;
				}
			}
			$flds = $holdflds;
		}

		// already exists, alter table instead
		list($lines,$pkey) = $this->_GenFields($flds);
		$alter = 'ALTER TABLE ' . $this->TableName($tablename);
		$sql = array();

		foreach ( $lines as $id => $v ) {
			if ( isset($cols[$id]) && is_object($cols[$id]) ) {
				$flds = Lens_ParseArgs($v,',');
				//  We are trying to change the size of the field, if not allowed, simply ignore the request.
				if ($flds && in_array(strtoupper(substr($flds[0][1],0,4)),$this->invalidResizeTypes4)) continue;

				$sql[] = $alter . $this->alterCol . ' ' . $v;
			} else {
				$sql[] = $alter . $this->addCol . ' ' . $v;
			}
		}
		return $sql;
	}

} // end of class
