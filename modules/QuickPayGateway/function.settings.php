<?php
if (!isset($gCms)) exit;

//$this->NeoSetup($id,$this,$returnid);
$this->smarty->assign('formstart',$this->CreateFormStart($id,"savesettings",$returnid,"post","",false,""));
$this->smarty->assign('formend',$this->CreateFormEnd());

$this->smarty->assign('merchantidinput',$this->CreateInputText($id,"merchantid",$this->GetPreference("merchantid",4),8,8));


$this->smarty->assign('submit', $this->CreateInputSubmit($id,"submit","Gem indstillinger"));

echo $this->ProcessTemplate('settings.tpl');

 ?>