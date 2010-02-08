<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) exit;

$this->smarty->assign('formstart',$this->CreateFormStart($id, 'savesettings', $returnid));
$this->smarty->assign('path',$this->CreateInputHidden($id,"path",$path));

$this->smarty->assign('advancedmodetext',$this->Lang("enableadvanced"));
$this->smarty->assign('advancedmodeinput',$this->CreateInputCheckbox($id,"advancedmode","1",$advancedmode));
$this->smarty->assign('advancedmodehelp',$this->Lang("advancedhelp"));

$this->smarty->assign('showhiddenfilestext',$this->Lang("showhiddenfiles"));
$this->smarty->assign('showhiddenfilesinput',$this->CreateInputCheckbox($id,"showhiddenfiles","1",$showhiddenfiles));
$this->smarty->assign('showhiddenfileshelp',$this->Lang("showhiddenfileshelp"));

$this->smarty->assign('showthumbnailstext',$this->Lang("showthumbnails"));
$this->smarty->assign('showthumbnailsinput',$this->CreateInputCheckbox($id,"showthumbnails","1",$showthumbnails));
$this->smarty->assign('showthumbnailshelp',$this->Lang("showthumbnailshelp"));

$this->smarty->assign('iconsizetext',$this->Lang("iconsize"));
$this->smarty->assign('iconsizeinput',$this->CreateInputDropDown($id,"iconsize",array($this->Lang("largeicons")=>"32px",$this->Lang("smallicons")=>"16px"),"",$iconsize));

$boxes=array();
for($i=1; $i<11; $i++) $boxes["$i"]="$i";
$this->smarty->assign('uploadboxestext',$this->Lang("uploadboxes"));
$this->smarty->assign('uploadboxesinput',$this->CreateInputDropDown($id,"uploadboxes",$boxes,"",$uploadboxes));

$permstyles=array($this->Lang("rwxstyle")=>"xxxxxxxxx",$this->Lang("755style")=>"xxx");
$this->smarty->assign('permissionstyletext',$this->Lang("permissionstyle"));
$this->smarty->assign('permissionstyleinput',$this->CreateInputDropDown($id,"permissionstyle",$permstyles,"",$permissionstyle));

$delimiters=array("."=>".",","=>",",$this->Lang("space")=>" ",$this->Lang("none")=>"");
$this->smarty->assign('thousanddelimitertext',$this->Lang("thousanddelimiter"));
$this->smarty->assign('thousanddelimiterinput',$this->CreateInputDropDown($id,"thousanddelimiter",$delimiters,"",$thousanddelimiter));

$this->smarty->assign('submit',  $this->CreateInputSubmit($id,"save",$this->Lang("savesettings")));
$this->smarty->assign('formend',$this->CreateFormEnd());
echo $this->ProcessTemplate('settings.tpl');

?>
