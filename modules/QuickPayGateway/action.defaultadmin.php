<?php

if (!isset($gCms)) exit;
if (!$this->VisibleToAdminUser()) exit;
global $config;
$tab="";
if (isset($params["tab"])) $tab=$params["tab"];

//$view='files';
echo $this->StartTabHeaders();
echo $this->SetTabHeader("settings","Indstillinger",($tab=="settings"));
echo $this->SetTabHeader("log","Transaktionslog",($tab=="log"));
echo $this->EndTabHeaders();

echo $this->StartTabContent();


echo $this->StartTab("settings");
include_once(dirname(__file__)."/function.settings.php");
echo $this->EndTab();

echo $this->StartTab("log");
include_once(dirname(__file__)."/function.log.php");
echo $this->EndTab();

echo $this->EndTabContent();

?>