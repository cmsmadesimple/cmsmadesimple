<?php

include_once(dirname(__FILE__)."/include.php");

function execute_dump(&$result) {
	global $db;
	while ($row = $result->FetchRow()) {
		$now = $db->DBTimeStamp(time());
		$length = strlen($now);
		#$now = substr($now, 1, $length - 2);
		$row["create_date"] = $now;
		$row["modified_date"] = $now;
		echo $db->GetInsertSQL($result, $row, false, true) . ";\n";
	}
}

$tablelist = $db->MetaTables('TABLES');
foreach ($tablelist as $tablename) {
	$result = $db->Execute("SELECT * FROM $tablename");
	execute_dump($result);
}

?>
