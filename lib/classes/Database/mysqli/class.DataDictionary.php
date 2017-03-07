<?php

namespace CMSMS\Database\mysqli;

class DataDictionary extends \CMSMS\Database\DataDictionary
{
    public function __construct(Connection $conn)
    {
        parent::__construct($conn);
        $this->alterCol = ' MODIFY COLUMN';
        $this->alterTableAddIndex = true;
        $this->dropTable = 'DROP TABLE IF EXISTS %s'; // requires mysql 3.22 or later

        $this->dropIndex = 'DROP INDEX %s ON %s';
        $this->renameColumn = 'ALTER TABLE %s CHANGE COLUMN %s %s %s';	// needs column-definition!
    }

    protected function ActualType($meta)
    {
        switch( $meta ) {
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

    protected function MetaType($t,$len=-1,$fieldobj=false)
    {
        // $t can be mixed...
        if (is_object($t)) {
            $fieldobj = $t;
            $t = $fieldobj->type;
            $len = $fieldobj->max_length;
        }

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
        case 'DATETIME':
        case 'TIMESTAMP': return 'T';

        case 'INT':
        case 'INTEGER':
        case 'BIGINT':
        case 'TINYINT':
        case 'MEDIUMINT':
        case 'SMALLINT':
            if (!empty($fieldobj->primary_key)) return 'R';
            return 'I';

        default:
            static $typeMap = array(
                'VARCHAR' => 'C',
                'VARCHAR2' => 'C',
                'CHAR' => 'C',
                'C' => 'C',
                'STRING' => 'C',
                'NCHAR' => 'C',
                'NVARCHAR' => 'C',
                'VARYING' => 'C',
                'BPCHAR' => 'C',
                'CHARACTER' => 'C',
                ##
                'LONGCHAR' => 'X',
                'TEXT' => 'X',
                'NTEXT' => 'X',
                'M' => 'X',
                'X' => 'X',
                'CLOB' => 'X',
                'NCLOB' => 'X',
                'LVARCHAR' => 'X',
                ##
                'BLOB' => 'B',
                'IMAGE' => 'B',
                'BINARY' => 'B',
                'VARBINARY' => 'B',
                'LONGBINARY' => 'B',
                'B' => 'B',
                ##
                'YEAR' => 'D', // mysql
                'DATE' => 'D',
                'D' => 'D',
                ##
                'TIME' => 'T',
                'TIMESTAMP' => 'T',
                'DATETIME' => 'T',
                'TIMESTAMPTZ' => 'T',
                'T' => 'T',
                ##
                'BOOL' => 'L',
                'BOOLEAN' => 'L',
                'BIT' => 'L',
                'L' => 'L',
                ##
                'COUNTER' => 'R',
                'R' => 'R',
                'SERIAL' => 'R', // ifx
                'INT IDENTITY' => 'R',
                ##
                'INT' => 'I',
                'INT2' => 'I',
                'INT4' => 'I',
                'INT8' => 'I',
                'INTEGER' => 'I',
                'INTEGER UNSIGNED' => 'I',
                'SHORT' => 'I',
                'TINYINT' => 'I',
                'SMALLINT' => 'I',
                'I' => 'I',
                ##
                'LONG' => 'N', // interbase is numeric, oci8 is blob
                'BIGINT' => 'N', // this is bigger than PHP 32-bit integers
                'DECIMAL' => 'N',
                'DEC' => 'N',
                'REAL' => 'N',
                'DOUBLE' => 'N',
                'DOUBLE PRECISION' => 'N',
                'SMALLFLOAT' => 'N',
                'FLOAT' => 'N',
                'NUMBER' => 'N',
                'NUM' => 'N',
                'NUMERIC' => 'N',
                'MONEY' => 'N',

                ## informix 9.2
                'SQLINT' => 'I',
                'SQLSERIAL' => 'I',
                'SQLSMINT' => 'I',
                'SQLSMFLOAT' => 'N',
                'SQLFLOAT' => 'N',
                'SQLMONEY' => 'N',
                'SQLDECIMAL' => 'N',
                'SQLDATE' => 'D',
                'SQLVCHAR' => 'C',
                'SQLCHAR' => 'C',
                'SQLDTIME' => 'T',
                'SQLINTERVAL' => 'N',
                'SQLBYTES' => 'B',
                'SQLTEXT' => 'X',
                ## informix 10
                "SQLINT8" => 'I8',
                "SQLSERIAL8" => 'I8',
                "SQLNCHAR" => 'C',
                "SQLNVCHAR" => 'C',
                "SQLLVARCHAR" => 'X',
                "SQLBOOL" => 'L'
                );

            $tmap = false;
            $t = strtoupper($t);
            $tmap = (isset($typeMap[$t])) ? $typeMap[$t] : 'N';
            return $tmap;
        }
    }

    public function MetaTables()
    {
        $sql = 'SHOW TABLES';
        $list = $this->connection->GetCol($sql);
        if( count($list) ) return $list;
    }

    public function MetaColumns($table)
    {
        $table = trim($table);
        if( !$table ) throw new \LogicException('empty table name specified for '.__METHOD__);

        $sql = 'SHOW COLUMNS FROM ?';
        $rs = $this->connection->GetArray($sql,$table);
        if( is_array($rs) && count($rs) ) {
            $out = array();
            foreach( $rs as $row ) {
                $out[] = $row['Field'];
            }
            return $out;
        }
    }

    protected function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{
		$suffix = '';
		if ($funsigned) $suffix .= ' UNSIGNED';
		if ($fnotnull) $suffix .= ' NOT NULL';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fautoinc) $suffix .= ' AUTO_INCREMENT';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}

    function _ProcessOptions($opts)
    {
        // fixes for old TYPE= stuff in tabopts.
        if( is_array($opts) && count($opts) ) {
            foreach( $opts as $key => &$val ) {
                if( startswith(strtolower($key),'mysql') ) {
                    $val = preg_replace('/TYPE\s?=/i','ENGINE=',$val);
                }
            }
        }
        return $opts;
    }

	function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
	{
		$sql = array();

		if ( isset($idxoptions['REPLACE']) || isset($idxoptions['DROP']) ) {
			if ($this->alterTableAddIndex) $sql[] = "ALTER TABLE $tabname DROP INDEX $idxname";
			else $sql[] = sprintf($this->dropIndex, $idxname, $tabname);

			if ( isset($idxoptions['DROP']) ) return $sql;
		}

		if ( empty ($flds) ) return $sql;

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

        if( ($opts = $this->get_dbtype_options($idxoptions)) ) $s .= $opts;

		$sql[] = $s;

		return $sql;
	}

	function CreateTableSQL($tabname, $flds, $tableoptions=false)
	{
        $str = 'ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci';
        $dbtype = $this->_DBType();

        // clean up input tableoptions
        if( !$tableoptions ) {
            $tableoptions = [ $dbtype => $str ];
        }
        else if( is_string($tableoptions) ) {
            $tableoptions = [ $dbtype => $tableoptions ];
        }
        else if( is_array($tableoptions) && !isset($tableoptions[$dbtype]) && isset($tableoptions['mysql']) ) {
            $tableoptions[$dbtype] = $tableoptions['mysql'];
        }
        else if( is_array($tableoptions) && !isset($tableoptions[$dbtype]) && isset($tableoptions['MYSQL']) ) {
            $tableoptions[$dbtype] = $tableoptions['MYSQL'];
        }

        foreach( $tableoptions as $key => &$val ) {
            if( strpos($val,'TYPE=') !== FALSE ) $val = str_replace('TYPE=','ENGINE=',$val);
        }
        if( isset($tableoptions[$dbtype]) && strpos($tableoptions[$dbtype],'CHARACTER') === FALSE &&
            strpos($tableoptions[$dbtype],'COLLATE') === FALSE ) {
            // if no character set and collate options specified, force UTF8
            $tableoptions[$dbtype] .= " CHARACTER SET utf8 COLLATE utf8_general_ci";
        }

        return parent::CreateTableSQL($tabname, $flds, $tableoptions);
	}

} // end of class
