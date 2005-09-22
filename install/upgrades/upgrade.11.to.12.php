<?php

echo "<p>Adding fields to content table...";

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "collapsed L");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 12";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
