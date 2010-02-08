<?php

//$this->CreatePermission('Use FileManager',$this->Lang("permission"));
$this->CreatePermission('Use FileManager Advanced',$this->Lang("permissionadvanced"));

$this->SetPreference("iconsize","32px");
$this->SetPreference("uploadboxes","5");
$this->SetPreference("advancedmode","false");
$this->SetPreference("showhiddenfiles","false");
//$this->SetPreference("confirmsingledelete","false");
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));

/*  $db = &$this->GetDb();
 $dict = NewDataDictionary($db);
 $taboptarray = array('mysql' => 'TYPE=MyISAM');

 $flds = "
 name C(255),
 path C(255),
 size I,
 type C(5),
 data B
 ";

 $sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_filemanager_thumbs", $flds, $taboptarray );
 $dict->ExecuteSQLArray( $sqlarray );
 */

?>
