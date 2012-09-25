<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission('Modify Site Preferences')) exit;

$advancedmode = $this->GetPreference('advancedmode',0);

$smarty->assign('formstart',$this->CreateFormStart($id, 'savesettings', $returnid));
$smarty->assign('path',$this->CreateInputHidden($id,"path",$path));

$smarty->assign('advancedmodetext',$this->Lang("enableadvanced"));
$smarty->assign('advancedmodeinput',$this->CreateInputCheckbox($id,"advancedmode","1",$advancedmode));
$smarty->assign('advancedmodehelp',$this->Lang("advancedhelp"));

$smarty->assign('showhiddenfilestext',$this->Lang("showhiddenfiles"));
$smarty->assign('showhiddenfilesinput',$this->CreateInputCheckbox($id,"showhiddenfiles","1",$showhiddenfiles));
$smarty->assign('showhiddenfileshelp',$this->Lang("showhiddenfileshelp"));

$smarty->assign('showthumbnailstext',$this->Lang("showthumbnails"));
$smarty->assign('showthumbnailsinput',$this->CreateInputCheckbox($id,"showthumbnails","1",$showthumbnails));
$smarty->assign('showthumbnailshelp',$this->Lang("showthumbnailshelp"));

$smarty->assign('iconsizetext',$this->Lang("iconsize"));
$smarty->assign('iconsizeinput',$this->CreateInputDropDown($id,"iconsize",array($this->Lang("largeicons")=>"32px",$this->Lang("smallicons")=>"16px"),"",$iconsize));

$smarty->assign('yesno_options',array(0=>lang('no'),1=>lang('yes')));
$smarty->assign('create_thumbnails',$this->GetPreference('create_thumbnails'));

$permstyles=array($this->Lang("rwxstyle")=>"xxxxxxxxx",$this->Lang("755style")=>"xxx");
$smarty->assign('permissionstyletext',$this->Lang("permissionstyle"));
$smarty->assign('permissionstyleinput',$this->CreateInputDropDown($id,"permissionstyle",$permstyles,"",$permissionstyle));

$delimiters=array("."=>".",","=>",",$this->Lang("space")=>" ",$this->Lang("none")=>"");
$smarty->assign('thousanddelimitertext',$this->Lang("thousanddelimiter"));
$smarty->assign('thousanddelimiterinput',$this->CreateInputDropDown($id,"thousanddelimiter",$delimiters,"",$thousanddelimiter));

$smarty->assign('submit',  $this->CreateInputSubmit($id,"save",$this->Lang("savesettings")));
$smarty->assign('formend',$this->CreateFormEnd());

$smarty->assign('mod',$this);

echo $this->ProcessTemplate('settings.tpl');

?>
