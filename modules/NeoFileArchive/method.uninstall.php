<?php
if (!isset($gCms)) exit;
/*
$db =& $this->GetDb();
$dict = NewDataDictionary($db);


$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_neoupload_profiles");
$dict->ExecuteSQLArray($sqlarray);
$db->DropSequence(cms_db_prefix()."module_neoupload_profils_seq");

$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_neoupload_profileprops");
$dict->ExecuteSQLArray($sqlarray);
*/

$this->RemoveModuleEntities();
$this->RemovePreference();

$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('postininstall',$this->GetVersion()));


?>