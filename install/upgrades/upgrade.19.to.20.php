<?php

/*
#No point in even creating this table if we're going to drop it in 21-22.
echo "<p>Adding Event Handler Table(s)...";


	$dbdict = NewDataDictionary($db);
        $flds = "
          module_name  c(255) KEY,
          event_name   c(80) KEY,
          handler_name c(255)
        ";
        $taboptarray = array('mysql' => 'TYPE=MyISAM');
        $sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."eventhandlers", $flds, $taboptarray);
        $dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";
*/

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 20";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
