<?php

include_once(dirname(__FILE__)."/include.php");

function execute_dump($result) {
	global $dbnew;
	while ($row = $result->FetchRow()) {
		$now = $dbnew->DBTimeStamp(time());
		$length = strlen($now);
		$now = substr($now, 1, $length - 2);
		$row["create_date"] = $now;
		$row["modified_date"] = $now;
		echo $dbnew->GetInsertSQL($result, $row, false, true) . ";\n";
	}
}

$tablelist = $dbnew->MetaTables('TABLES');
foreach ($tablelist as $tablename) {
	$result = $dbnew->Execute("SELECT * FROM $tablename");
	execute_dump(&$result);
}

?>
