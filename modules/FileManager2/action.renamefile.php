<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if(!isset($params["filename"]) || !isset($params["path"])) {
	$this->Redirect($id, 'defaultadmin');
}

if ($this->IntruderCheck($params["path"])) {
	$this->Redirect($id, 'defaultadmin',$returnid,array("module_error"=>$this->Lang("fileoutsideuploads")));
}

$config =& $gCms->GetConfig();
$fullname=$this->Slash($params["path"],$params["filename"]);
$fullname=$this->Slash($config["root_path"],$fullname);

if (isset($params["newname"]) && !empty($params["newname"])) {
	$fullnewname=$this->Slash($params["path"],$params["newname"]);
	$fullnewname=$this->Slash($config["root_path"],$fullnewname);
	//echo deleting;die();
	if (isset($params["cancel"])) {
		$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$this->Lang("renamecancelled")));
	} else {
		if (rename($fullname,$fullnewname)) {
			$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$this->Lang("renamesuccess")));
		} else {
			$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_error"=>$this->Lang("renamefailure")));
		}
	}
} else {
	$this->smarty->assign('startform', $this->CreateFormStart($id, 'renamefile', $returnid));
	$this->smarty->assign('filename', $this->CreateInputHidden($id,"filename",$params["filename"]));
	$this->smarty->assign('path', $this->CreateInputHidden($id,"path",$params["path"]));
	$this->smarty->assign('endform', $this->CreateFormEnd());
	$this->smarty->assign('newnametext', $this->Lang("newname"));
	$this->smarty->assign('newname',$this->CreateInputText($id,"newname",$params["filename"],80,255));
	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('rename')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
	echo $this->ProcessTemplate('renamefile.tpl');
}

?>