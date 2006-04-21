<?php
/**
 * ADOdb Lite Extend Module for Mysqlt
 * 
 */

eval('class sqlite_cmsms_EXTENDER extends '. $last_module . '_ADOConnection { }');

class sqlite_cmsms_ADOConnection extends sqlite_cmsms_EXTENDER
{
	var $_genSeqSQL = "create table %s (id integer)";
	var $genID = 0;

	function CreateSequence($seqname='adodbseq',$start=1)
	{
		if (empty($this->_genSeqSQL)) return false;
		$ok = $this->Execute(sprintf($this->_genSeqSQL,$seqname));
		if (!$ok) return false;
		$start -= 1;
		return $this->Execute("insert into $seqname values($start)");
	}
	
	var $_dropSeqSQL = 'drop table %s';
	function DropSequence($seqname)
	{
		if (empty($this->_dropSeqSQL)) return false;
		return $this->Execute(sprintf($this->_dropSeqSQL,$seqname));
	}

	function GenID($seq='adodbseq', $startID=1)
	{
		$MAXLOOPS = 100;
		while (--$MAXLOOPS>=0) {
			@($num = $this->GetOne("select id from $seq"));
			if ($num === false) {
				$this->Execute(sprintf($this->_genSeqSQL, $seq));	
				$start -= 1;
				$num = '0';
				$result = $this->Execute("insert into $seq values($start)");
				if (!$result)
					return false;
			}
			else {
				$num = $num['id'];
			}

			$this->Execute("update $seq set id=id+1 where id=$num");

			var_dump($this->Affected_Rows());
			if ($this->Affected_Rows() > 0) {
				$num += 1;
				$this->genID = $num;
				var_dump($num);
				return $num;
			}
		}
		if ($fn = $this->raiseErrorFn) {
			$fn($this->databaseType, 'GENID',-32000,"Unable to generate unique id after $MAXLOOPS attempts", $seq, $num);
		}
		return false;
	}
}

eval('class sqlite_cmsms_resultset_EXTENDER extends '. $last_module . '_ResultSet { }');

class sqlite_cmsms_ResultSet extends sqlite_cmsms_resultset_EXTENDER
{
	function RowCount()
	{
		return $this->_numOfRows;
	}
}
?>
