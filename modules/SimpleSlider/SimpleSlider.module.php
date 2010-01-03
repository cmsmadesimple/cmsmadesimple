<?php

@error_reporting(E_ERROR);

class SimpleSlider extends CMSModule {

  /*	function SimpleSlider(){
		$this->CMSModule();
	}
  */
  function GetName() {
    return 'SimpleSlider';
  }

  function GetFriendlyName() {
    return $this->Lang("friendlyname");
  }


  function VisibleToAdminUser() {
    return $this->CheckPermission('SimpleSlider Admin');
  }
  
  function GetVersion() {
    return '0.4';
  }

  function GetHelp() {
    return $this->Lang('help');
  }

  function IsPluginModule() {
    return true;
  }

  function HasAdmin() {
    return true;
  }

  function GetChangeLog() {
    return $this->ProcessTemplate("changelog.tpl");
  }

  function GetAdminSection() {
    return 'content';
  }

  function GetAdminDescription() {
    return 'Manage Simple Slider';
  }

  function GetAuthor() {
    return 'Burhan BAVKIR';
  }

  function GetAuthorEmail() {
    return 'info@bvkyazilim.com';
  }

  function displaySelectArray($selectlist, $id=0) {

    foreach($selectlist as $key => $val) {
      ?>                      <option value="<?=$key?>"<? if($key==$id) { ?> selected="selected" <? } ?>><?=$val?></option>
      <?
    }
  }


  function GenerateInsertSQL($table, $fields) {
    $sql='INSERT INTO '.$table.' (';
    $keys=array_keys($fields);
    $values=' VALUES (';
    for($i=0; $i<(sizeof($fields)-1); $i++) {
      $values.='"'.$fields[$keys[$i]].'", ';
      $sql.=$keys[$i].', ';
    }
    $values.='"'.$fields[$keys[$i]].'"); ';
    $sql.=$keys[$i].') '.$values;

    return $sql;
  }

  function GenerateUpdateSQL($table, $fields, $where) {
    $sql='UPDATE '.$table.' SET ';
    $keys=array_keys($fields);
    for($i=0; $i<(sizeof($fields)-1); $i++) {
      $sql.=$keys[$i].'="'.$fields[$keys[$i]].'", ';
    }
    $sql.=$keys[$i].'="'.$fields[$keys[$i]].'" WHERE '.$where.';';

    return $sql;
  }

}

?>
