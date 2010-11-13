<?php

if (!isset($gCms)) exit;
if (!$this->VisibleToAdminUser()) exit;
global $config;
$tab="";
if (isset($params["tab"])) $tab=$params["tab"];

if (isset($_REQUEST["tab"])) $tab=$_REQUEST["tab"];

if (isset($params["hidedonationssubmit"])) {
  $this->SetPreference("hidedonationstab",1);
}


//$view='files';
echo $this->StartTabHeaders();

if ($this->CheckPermission('Modify Content')) {
  echo $this->SetTabHeader("links",$this->Lang("links"),($tab=="links"));
}

if ($this->CheckPermission('Modify Templates')) {
   echo $this->SetTabHeader("templates",$this->Lang("templates"),($tab=="templates"));
}

if ($this->CheckPermission('Modify Site Preferences')) {
  echo $this->SetTabHeader("settings",$this->Lang("settings"),($tab=="settings"));
}

echo $this->EndTabHeaders();

echo $this->StartTabContent();

global $config;

if ($this->CheckPermission('Modify Content')) {
  echo $this->StartTab("links");
  include_once(dirname(__file__)."/function.links.php");
  echo $this->EndTab();
}

if ($this->CheckPermission('Modify Templates')) {
    echo $this->StartTab("templates");
  include_once(dirname(__file__)."/function.templates.php");
  echo $this->EndTab();
}


if ($this->CheckPermission('Modify Site Preferences')) {
  echo $this->StartTab("settings");
  include_once(dirname(__file__)."/function.settings.php");
  echo $this->EndTab();
}



 /*if ($this->GetPreference("hidedonationstab")!="1") {
    echo $this->StartTab("donations");
    include(dirname(__FILE__)."/function.donations.php");
    echo $this->EndTab();
  }*/

echo $this->EndTabContent();

?>