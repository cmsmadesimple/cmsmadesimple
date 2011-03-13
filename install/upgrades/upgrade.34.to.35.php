<?php
global $gCms;

echo '<p>Adding New Events';
$auery 'INSERT INTO '.cms_db_prefix().'events (originator,event_name,event_id) VALUES (?,?,?)';

$new_id = $db->GenId( cms_db_prefix().'events_seq');
$db->Execute($query,array('Core','StylesheetPreCompile',$new_id));

$new_id = $db->GenId( cms_db_prefix().'events_seq');
$db->Execute($query,array('Core','StylesheetPostCompile',$new_id));

$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_content_by_hierarchy', $db_prefix."content", 'hierarchy');
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
echo ilang('install_creating_index', 'content', $ado_ret);

$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'event_id', $db_prefix."events", 'event_id');
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
echo ilang('install_creating_index', 'content', $ado_ret);

echo '[done]</p>';

