<?php

$db = $this->GetDb();
$dict = NewDataDictionary($db);

switch($oldversion) {
  case '0.2.1':
  case '0.2':
  case '0.1':
    $sqlarray=array(0=>'ALTER TABLE `'.cms_db_prefix().'module_simpleslider_shows` ADD `width` INT NOT NULL , ADD `height` INT NOT NULL , ADD `fadetime` INT NOT NULL ');
    $dict->ExecuteSQLArray($sqlarray);
    break;
}
?>
