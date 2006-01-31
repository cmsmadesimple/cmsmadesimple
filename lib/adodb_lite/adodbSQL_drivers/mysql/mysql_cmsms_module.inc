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
	var $genID = 0;

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
}

eval('class mysql_cmsms_resultset_EXTENDER extends mysql_'. $last_module . '_ResultSet { }');

class mysql_cmsms_ResultSet extends mysql_cmsms_resultset_EXTENDER
{
}
?>
