<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission("Modify Site Preferences") && !$this->AdvancedAccessAllowed()) exit;
//print_r($params);die();
if (isset($params["advancedmode"])) {
  $this->SetPreference("advancedmode",1);
} else {
	$this->SetPreference("advancedmode",0);
	if (substr($params["path"],0,strlen($this->upDir()))!=$this->upDir()) {
  	$params["path"]=$this->upDir();
	}
	echo $params["path"];
}
if (isset($params["showhiddenfiles"])) {
  $this->SetPreference("showhiddenfiles",1);
} else {
  $this->SetPreference("showhiddenfiles",0);
}
if (isset($params["showthumbnails"])) {
  $this->SetPreference("showthumbnails",1);
} else {
  $this->SetPreference("showthumbnails",0);
}
/*
if (isset($params["confirmsingledelete"])) {
  $this->SetPreference("confirmsingledelete",1);
} else {
  $this->SetPreference("confirmsingledelete",0);
}
*/
if (isset($params["iconsize"])) {
  $this->SetPreference("iconsize",$params["iconsize"]);
}

if (isset($params["permissionflavor"])) {
  $this->SetPreference("permissionflavor",$params["permissionflavor"]);
}

if (isset($params["uploadboxes"])) {
  $this->SetPreference("uploadboxes",$params["uploadboxes"]);
}

if (isset($params["permissionstyle"])) {
  $this->SetPreference("permissionstyle",$params["permissionstyle"]);
}

if (isset($params["thousanddelimiter"])) {
  $this->SetPreference("thousanddelimiter",$params["thousanddelimiter"]);
}

$message=$this->Lang("settingssaved");
$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$message,"tab"=>"settings"));
?>
