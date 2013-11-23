<?php

if (!function_exists("cmsms")) exit;

$current_version = $oldversion;
$this->SetPreference("uploadboxes","5");
switch($current_version) {
	case "0.1.0":
	case "0.1.1":
	case "0.1.2":
	case "0.1.3":
	case "0.1.4": $this->Install(true);
}

if( version_compare($oldversion,'1.3.1') < 0 ) {
  $this->CreateEvent('OnFileUploaded');
}
$this->SetPreference('advancedmode',0);
$this->RemovePermission('Use Filemanager');
$this->RegisterModulePlugin(true);

// TODO Rolf remove preference uploadboxes


?>