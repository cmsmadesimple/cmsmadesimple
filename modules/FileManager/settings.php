<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission('Modify Site Preferences')) exit;

$advancedmode = $this->GetPreference('advancedmode',0);

//$smarty->assign('path',$this->CreateInputHidden($id,"path",$path)); //why?

$smarty->assign('advancedmode',$advancedmode);
$smarty->assign('showhiddenfiles',$showhiddenfiles);
$smarty->assign('showthumbnails',$showthumbnails);
$smarty->assign('create_thumbnails',$this->GetPreference('create_thumbnails',1));
$iconsizes = array($this->Lang("largeicons")=>"32px",$this->Lang("smallicons")=>"16px");
$smarty->assign('iconsizes',array_flip($iconsizes));

$permstyles=array($this->Lang("rwxstyle")=>"xxxxxxxxx",$this->Lang("755style")=>"xxx");
$smarty->assign('permstyles',array_flip($permstyles));
$smarty->assign('permissionstyle',$permissionstyle);

echo $this->ProcessTemplate('settings.tpl');

?>
