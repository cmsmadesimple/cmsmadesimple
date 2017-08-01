<?php
if (!isset($gCms)) exit;

$db = $this->GetDb();
$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci', 'mysqli' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');

$flds = "timestamp I NOTNULL,
         severity  I NOTNULL DEFAULT 0,
         uid I,
         ip_addr C(40),
         username C(50),
         subject C(255),
         msg X NOTNULL,
         item_id I
";
$sqlarr = $dict->CreateTableSQL( \AdminLog\storage::table_name(), $flds, $taboptarray );
$dict->ExecuteSQLArray( $sqlarr );

$this->CreatePermission('View Admin Log','View Admin Log');
$this->CreatePermission('Clear Admin Log','Clear Admin Log');
