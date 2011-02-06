<?php
$flds = '
	 section     C(255) KEY,
         record      I KEY,
         uid         I KEY,
         signature   C(32),
         created  '.CMS_ADODB_DT.',
         modified '.CMS_ADODB_DT;

echo '<p>'.ilang('install_creating_table', 'locks', $ado_ret).'...';
$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->CreateTableSQL($db_prefix."locks", $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
echo '[done]</p>';

echo '<p>'.ilang('create_new_permission').'...';
$new_id = $db->GenId(cms_db_prefix().'permissions_seq');
$query = 'INSERT INTO '.cms_db_prefix()."permissions (permission_id, permission_name, permission_text, create_date, modified_date)
          VALUES (?,?,?,NOW(),NOW())";
$db->Execute($query,array($new_id,'Steal Content Locks','Steal Content Locks'));
echo '[done]</p>';

echo '<p>'.ilang('updating_schema_version',35).'...';
$query = "UPDATE ".cms_db_prefix()."version SET version = 35";
$db->Execute($query);
echo '[done]</p>';
?>