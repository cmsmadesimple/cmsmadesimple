<?php

echo "<p>Modifying Event Handler Table(s)...";

$dbdict = NewDataDictionary($db);

$flds = "
          event_id      I,
          tag_name      c(255),
          module_name   c(255),
          removable     I,
          handler_order I,
          handler_id    I,
        ";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."event_handlers", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$flds = "
          originator   c(200) NOTNULL,
          event_name   c(200) NOTNULL,
          event_id     I
        ";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."events", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);
$db->Execute( 'ALTER TABLE '.cms_db_prefix()."events ADD PRIMARY KEY (originator,event_name)" );

$db->CreateSequence(cms_db_prefix()."events_seq");
$db->CreateSequence(cms_db_prefix()."event_handler_seq");

$db->Execute( 'DROP TABLE '.cms_db_prefix().'eventhandlers' );

echo "[done]</p>";

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 22";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
