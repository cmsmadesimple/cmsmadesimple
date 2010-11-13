<?php
if (!isset($gCms)) exit;

if (!$this->CheckPermission('Modify Site Preferences')) {
  exit;
}

$this->NeoSetup($id,$this,$returnid,$params);
$this->smarty->assign('formstart',$this->CreateFormStart($id,"savesettings",$returnid,"post","",false,""));
$this->smarty->assign('formend',$this->CreateFormEnd());

$this->SmartyAssign("imagelocationinput",$this->NeoInputText(array("name"=>"imagelocation","value"=>$this->Pref("imagelocation","uploads/images/links"),"size"=>40)));
$this->SmartyAssign("includejqinput",$this->NeoInputCheckbox(array("name"=>"includejq","value"=>"1","selectedvalue"=>$this->Pref("includejq","1"))));

$this->SmartyAssign("labelsinput",$this->NeoTextArea(
        array(
            "name"=>"labels",
            "value"=>$this->Pref("labels"),
            "class"=>"pagesmalltextarea"
        )
        ));

$this->SmartyAssign('submit', $this->CreateInputSubmit($id,"submitsettings",$this->Lang("savesettings")));

echo $this->ProcessTemplate('adminsettings.tpl');

 ?>