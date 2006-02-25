<?php
/**
 * ADOdb Lite Extend Module for Mysqlt
 * 
 */

eval('class mysql_cmsms_EXTENDER extends mysql_'. $last_module . '_ADOConnection { }');

class mysql_cmsms_ADOConnection extends mysql_cmsms_EXTENDER
{
 	var $_genIDSQL = "update %s set id=LAST_INSERT_ID(id+1);";
	var $_genSeqSQL = "create table %s (id int not null)";
	var $_genSeq2SQL = "insert into %s values (%s)";
	var $_dropSeqSQL = "drop table %s";
	var $genID = 0;

	function GenID($seqname='adodbseq', $startID=1)
	{
		$getnext = sprintf($this->_genIDSQL, $seqname);
		#$holdtransOK = $this->transaction_status;
		$result = @$this->Execute($getnext);
		if (!$result) {
			#if ($holdtransOK)
			#	$this->transaction_status = true;
//			$u = strtoupper($seqname);
			$this->Execute(sprintf($this->_genSeqSQL, $seqname));
			$this->Execute(sprintf($this->_genSeq2SQL, $seqname, $startID-1));
			$result = $this->Execute($getnext);
		}
		$this->genID = mysql_insert_id($this->connectionId);

		if ($result)
			$result->Close();

		return $this->genID;
	}

	function CreateSequence($seqname='adodbseq',$startID=1)
	{
		if (empty($this->_genSeqSQL)) return false;
		$u = strtoupper($seqname);
		
		$ok = $this->Execute(sprintf($this->_genSeqSQL,$seqname));
		if (!$ok) return false;
		return $this->Execute(sprintf($this->_genSeq2SQL,$seqname,$startID-1));
	}

	function DropSequence($seqname='adodbseq')
	{
		if (empty($this->_dropSeqSQL)) return false;
		return $this->Execute(sprintf($this->_dropSeqSQL,$seqname));
	}

	function RowCount()
	{
		return $this->_numOfRows;
	}

	function outp($text, $newline = false)
	{
		$this->debug_output = "(" . $this->dbtype . "): ".htmlspecialchars($text)."\n Error (" . $this->ErrorNo() .'): '. $this->ErrorMsg();
		debug_buffer($this->debug_output);
	}
}

eval('class mysql_cmsms_resultset_EXTENDER extends mysql_'. $last_module . '_ResultSet { }');

class mysql_cmsms_ResultSet extends mysql_cmsms_resultset_EXTENDER
{
	function RowCount()
	{
		return $this->_numOfRows;
	}
}
?>
