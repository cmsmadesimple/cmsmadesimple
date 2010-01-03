<?php
$db = $this->GetDb();
$dict = NewDataDictionary( $db );

$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_simpleslider_images' );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_simpleslider_shows' );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_simpleslider_showimages' );
$dict->ExecuteSQLArray($sqlarray);
//Remove the sequence
$this->RemovePermission('SimpleSlider Admin');
$this->RemovePreference();
?>
