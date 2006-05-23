<?php

echo "<p>Modifying Event Handler Table(s)...";

$db->Execute( 'ALTER TABLE '.cms_db_prefix()."eventhandlers DROP PRIMARY KEY" );
$db->Execute( 'ALTER TABLE '.cms_db_prefix()."eventhandlers ADD COLUMN module_handler varchar(255)" );
$db->Execute( 'ALTER TABLE '.cms_db_prefix()."eventhandlers ADD COLUMN removable int default 1" );

echo "[done]</p>";

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 22";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
