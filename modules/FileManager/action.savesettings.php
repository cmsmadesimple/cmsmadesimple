<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Site Preferences") && !$this->AdvancedAccessAllowed()) exit;

$this->SetPreference('advancedmode',(int)$params['advancedmode']);
$this->SetPreference('showhiddenfiles',(int)$params['showhiddenfiles']);
$this->SetPreference('showthumbnails',(int)$params['showthumbnails']);
$this->SetPreference('create_thumbnails',(int)$params['create_thumbnails']);
$this->SetPreference("iconsize",$params["iconsize"]);
$this->SetPreference("permissionstyle",$params["permissionstyle"]);

$this->SetMessage($this->Lang('settingssaved'));
$this->SetCurrentTab('settings');
$this->RedirectToAdminTab();
?>
