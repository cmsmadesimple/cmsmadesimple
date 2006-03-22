<?php
/**
 * ADOdb Lite Extend Module for Mysqlt
 * 
 */

eval('class postgres7_cmsms_EXTENDER extends postgres7_'. $last_module . '_ADOConnection { }');

class postgres7_cmsms_ADOConnection extends postgres7_cmsms_EXTENDER
{
	var $_genIDSQL = "SELECT NEXTVAL('%s')";
	var $_genSeqSQL = "CREATE SEQUENCE %s START %s";
	var $_dropSeqSQL = "DROP SEQUENCE %s";
	var $genID = 0;

	function GenID($seqname='adodbseq', $startID=1)
	{
		$getnext = sprintf($this->_genIDSQL, $seqname);
		#$holdtransOK = $this->transaction_status;
		$save_handler = $this->raiseErrorFn;
		$this->raiseErrorFn = '';
		@($result = $this->Execute($getnext));
		$this->raiseErrorFn = $save_handler;
		
		if (!$result) {
			#$this->transaction_status = $holdtransOK;
			$createseq = $this->Execute(sprintf($this->_genSeqSQL, $seqname, $startID));
			$result = $this->Execute($getnext);
		}
		if ($result && !$result->EOF)
			$this->genID = reset($result->fields);
		else $this->genID = 0;

		if ($result)
			$result->Close();

		return $this->genID;
	}

	function CreateSequence($seqname='adodbseq',$startID=1)
	{
		if (empty($this->_genSeqSQL)) return false;
		return $this->Execute(sprintf($this->_genSeqSQL,$seqname,$startID));
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
		$this->debug_output = "(" . $this->dbtype . "): ".$text." - Error (" . $this->ErrorNo() .'): '. $this->ErrorMsg();
		debug_buffer($this->debug_output);
	}
}

eval('class postgres7_cmsms_resultset_EXTENDER extends postgres7_'. $last_module . '_ResultSet { }');

class postgres7_cmsms_ResultSet extends postgres7_cmsms_resultset_EXTENDER
{
	function RowCount()
	{
		return $this->_numOfRows;
	}
}
?>
