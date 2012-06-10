<?php
$gCms = cmsms();
echo '<p>Adding &quot;Reorder Content&quot; permission';
$nid = $db->GenId(cms_db_prefix().'permissions_seq');
$db->Execute('Insert INTO '.cms_db_prefix()."permissions (permission_id,permission_name,permission_text,create_date,modified_date)
              VALUES (?,?,?,NOW(),NOW())",array($nid,'Reorder Content','Reorder Content'));
echo '[done]</p>';

$dbdict = NewDataDictionary($db);
echo '<p>Adding New Events';
$query = 'INSERT INTO '.cms_db_prefix().'events (originator,event_name,event_id) VALUES (?,?,?)';

$new_id = $db->GenId( cms_db_prefix().'events_seq');
$db->Execute($query,array('Core','StylesheetPreCompile',$new_id));
$new_id = $db->GenId( cms_db_prefix().'events_seq');
$db->Execute($query,array('Core','StylesheetPostCompile',$new_id));
$new_id = $db->GenId( cms_db_prefix().'events_seq');
$db->Execute($query,array('Core','LoginFailed',$new_id));

echo '<p>Adding columns to adminlog table...';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'adminlog','ip_addr C(20)');
$return = $dbdict->ExecuteSQLArray($sqlarray);
echo "[done]</p>";

echo '<p>Adding columns to modules table...';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'modules','allow_fe_lazyload I1,allow_admin_lazyload I1');
$return = $dbdict->ExecuteSQLArray($sqlarray);
echo "[done]</p>";

echo '<p>Making sure all modules (except nuSOAP) are enabled...';
$query = 'UPDATE '.cms_db_prefix().'modules SET active = 1 WHERE module_name != ?';
$return = $db->Execute($query,array('nuSOAP'));
echo "[done]</p>";

echo '<p>Disabling the nusoap module...';
$query = 'UPDATE '.cms_db_prefix().'modules SET active = 0 WHERE module_name = ?';
$return = $db->Execute($query,array('nuSOAP'));
echo "[done]</p>";

echo '<p>Adding columns to userplugins table...';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'userplugins','description X');
$return = $dbdict->ExecuteSQLArray($sqlarray);
echo "[done]</p>";

echo '<p>Adding an index to the content table...';
$sqlarray = $dbdict->CreateIndexSQL(cms_db_prefix().'index_content_by_hierarchy', cms_db_prefix()."content", 'hierarchy');
$return = $dbdict->ExecuteSQLArray($sqlarray);
echo "[done]</p>";

$sqlarray = $dbdict->CreateIndexSQL(cms_db_prefix().'event_id', cms_db_prefix()."events", 'event_id');
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
echo ilang('install_creating_index', 'events', $ado_ret);
echo "[done]</p>";

echo '<p>Updating schema version... ';
$query = "UPDATE ".cms_db_prefix()."version SET version = 35";
$db->Execute($query);
echo '[done]</p>';


?>