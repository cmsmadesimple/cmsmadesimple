<?php
if (!isset($gCms)) exit;
if (!$this->AccessAllowed() && !$this->AdvancedAccessAllowed()) exit;

if(!isset($params["filename"]) || !isset($params["path"])) {
	$this->Redirect($id, 'defaultadmin');
}

if ($this->IntruderCheck($params["path"])) {
	$this->Redirect($id, 'defaultadmin',$returnid,array("module_error"=>$this->Lang("fileoutsideuploads")));
}

$config =& $gCms->GetConfig();
$fullname=$this->Slash($params["path"],$params["filename"]);
$fullname=$this->Slash($config["root_path"],$fullname);

if (isset($params["newmode"])) {
	if (isset($params["cancel"])) {
		$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$this->Lang("chmodcancelled")));
	} else {		
		if ($this->SetModeWin($params["newmode"],$fullname)) {
			$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$this->Lang("chmodsuccess")));
		} else {
			$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_error"=>$this->Lang("chmodfailure")));
		}
	}
} else {
  $currentmode=$this->GetModeWin($params["path"],$params["filename"]);	
	$this->smarty->assign('startform', $this->CreateFormStart($id, 'chmodfilewin', $returnid));
	
	$this->smarty->assign('filename', $this->CreateInputHidden($id,"filename",$params["filename"]));
	$this->smarty->assign('path', $this->CreateInputHidden($id,"path",$params["path"]));
	$this->smarty->assign('endform', $this->CreateFormEnd());
	$this->smarty->assign('newmodetext', $this->Lang("newpermissions"));
	
	$this->smarty->assign('modeswitch', 
		  $this->CreateInputRadioGroup($id,"newmode",array($this->Lang("writable")=>"777",$this->Lang("writeprotected")=>"444"),$currentmode));
  $this->smarty->assign('modeswitchof', $this->GetModeTable($id,$this->GetPermissions($params["path"],$params["filename"])));	
	
	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('setpermissions')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
	echo $this->ProcessTemplate('chmodfilewin.tpl');

}

?>