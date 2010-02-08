<?php

if (!isset($gCms)) exit;
if (!$this->AccessAllowed() && !$this->AdvancedAccessAllowed()) exit;

$advancedmode=$this->GetPreference("advancedmode",0);
if (!$this->AdvancedAccessAllowed()) $advancedmode=0;
$showhiddenfiles=$this->GetPreference("showhiddenfiles",0);
$showthumbnails=$this->GetPreference("showthumbnails",1);
$iconsize=$this->GetPreference("iconsize",0);
$uploadboxes=$this->GetPreference("uploadboxes",5);
$permissionstyle=$this->GetPreference("permissionstyle","xxx");
$thousanddelimiter=$this->GetPreference("thousanddelimiter",".");
$path="";
if (!isset($params["path"])) {
  if ($advancedmode==1 && $this->AdvancedAccessAllowed()) {
    //$path=$config["root_path"];
    $path="/";
    $advancedmode=true;
  } else {
    $path=$this->upDir();
  }
} else {
  $path=$params["path"];
  if (strpos($path,"..")!==false) {
    $path=$this->upDir();
  }
}



$tab="";
if (isset($params["tab"])) $tab=$params["tab"];

if (isset($params["newsort"])) {
	$this->SetPreference("sortby",$params["newsort"]);
}
$sortby=$this->GetPreference("sortby","nameasc");

echo $this->StartTabHeaders();
$filestitle="";
if ($this->CheckPermission('Modify Files')) {
  echo $this->SetTabHeader("files",$this->Lang("fileview"),($tab=="files"));
  echo $this->SetTabHeader("upload",$this->Lang("uploadview"),($tab=="upload"));
}

if($this->CheckPermission('Modify Site Preferences')) {
  echo $this->SetTabHeader("settings",$this->Lang("settings"),($tab=="settings"));
}

echo $this->EndTabHeaders();
echo $this->StartTabContent();
if ($this->CheckPermission('Modify Files')) {
  echo $this->StartTab("files");
  //echo $path;
  $filelist=$this->GetFileList($path,($advancedmode==1));
  
//print_r($filelist);

 //echo "<h2>$path</h2>";
 
  include(dirname(__FILE__)."/fileview.php");
  echo $this->EndTab();

  echo $this->StartTab("upload");
  echo "<h3>".$this->Lang("uploadfilesto")."$path</h3>";
  include(dirname(__FILE__)."/uploadview.php");
  echo $this->EndTab();
}
if($this->CheckPermission('Modify Site Preferences')) {
  echo $this->StartTab("settings");
  include(dirname(__FILE__)."/settings.php");
  echo $this->EndTab();

}
echo $this->EndTabContent();

?>

