<?php
if (!function_exists("cmsms")) exit;
$this->CreatePermission('Use FileManager Advanced',$this->Lang("permissionadvanced"));

$this->SetPreference('advancedmode',0);
$this->SetPreference("iconsize","32px");
$this->SetPreference("uploadboxes","5");
$this->SetPreference("showhiddenfiles",0);
$this->SetPreference('showthumbnails',1);
$this->SetPreference('permissionstyle','xxx');

$this->CreateEvent('OnFileUploaded');
$this->RegisterModulePlugin(true);

?>