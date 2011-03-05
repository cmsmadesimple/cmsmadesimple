<?php

echo "<p>Modifying Event Handler Table(s)...";

$dbdict = NewDataDictionary($db);

$flds = "
          event_id      I,
          tag_name      c(255),
          module_name   c(160),
          removable     I,
          handler_order I,
          handler_id    I KEY
        ";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."event_handlers", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$flds = "
          originator   c(200) NOTNULL,
          event_name   c(200) NOTNULL,
          event_id     I KEY
        ";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."events", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix()."events_seq");
$db->CreateSequence(cms_db_prefix()."event_handler_seq");

//Just in case someone has the table in 19-20
$db->Execute( 'DROP TABLE '.cms_db_prefix().'eventhandlers' );

echo "[done]</p>";

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 22";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
