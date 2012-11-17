<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Site Preferences") && !$this->AdvancedAccessAllowed()) exit;

if (isset($params["advancedmode"])) {
  $this->SetPreference("advancedmode",1);
} else {
  $this->SetPreference("advancedmode",0);
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
if (isset($params["create_thumbnails"])) {
  $this->SetPreference('create_thumbnails',(int)$params['create_thumbnails']);
}

$this->SetMessage($this->Lang('settingssaved'));
$this->SetCurrentTab('settings');
$this->RedirectToAdminTab();
?>
