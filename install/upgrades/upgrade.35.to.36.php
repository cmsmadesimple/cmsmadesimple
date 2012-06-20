<?php
$gCms = cmsms();

echo '<p>Adding module_smarty_plugins table...';
$flds = "
         sig C(80) KEY NOT NULL,
         name C(80) NUT NULL,
         module C(160) NOT NULL,
         type C(40) NOT NULL,
         callback C(255) NOT NULL,
         available I,
         cachable I1
";
$dbdict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."module_smarty_plugins", $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
echo '[done]</p>';

echo '<p>Enhancing the adminlog table...';
$sqlarray = $dbdict->AlterColumnSQL(cms_db_prefix().'adminlog','ip_addr C(40)');
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
if( $return == 2 )
  {
    $sqlarray = $dbdict->CreateIndexSQL(cms_db_prefix().'index_adminlog1', cms_db_prefix()."adminlog", 'timestamp');
    $return = $dbdict->ExecuteSQLArray($sqlarray);
  }
echo "[done]</p>";

echo '<p>Enhancing the css table table...';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'css','media_query X');
$return = $dbdict->ExecuteSQLArray($sqlarray);
echo "[done]</p>";

$flds = "
          term C(255) KEY NOT NULL,
          key1 C(50) KEY NOT NULL,
          key2 C(50),
          key3 C(50),
          data X, 
          created ".CMS_ADODB_DT;
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."routes", $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
echo ilang('install_creating_table', 'routes', $ado_ret);

$sqlarray = $dbdict->CreateIndexSQL(cms_db_prefix().'index_content_by_idhier', cms_db_prefix()."content", 'hierarchy');
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
echo ilang('install_creating_index', 'content', $ado_ret);

cms_route_manager::rebuild_static_routes();

echo '<p>Updating schema version... ';
$query = "UPDATE ".cms_db_prefix()."version SET version = 36";
$db->Execute($query);
echo '[done]</p>';

?>