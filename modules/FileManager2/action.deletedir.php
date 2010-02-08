<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if(!isset($params["dirname"]) || !isset($params["path"])) {
	$this->Redirect($id, 'defaultadmin');
}

if ($this->IntruderCheck($params["path"])) {
	$this->Redirect($id, 'defaultadmin',$returnid,array("module_error"=>$this->Lang("fileoutsideuploads")));
}

$config =& $gCms->GetConfig();
$fullname=$this->Slash($params["path"],$params["dirname"]);
$fullname=$this->Slash($config["root_path"],$fullname);

function rmdirRecursive($path) {
	$dir = opendir($path);
	while ($entry = readdir($dir)) {
		if (is_file("$path/$entry")) {
			unlink("$path/$entry");
		} elseif (is_dir("$path/$entry") && $entry!='.' && $entry!='..') {
			rmdirRecursive("$path/$entry");
		}
	}
	closedir($dir);
	return rmdir($path);
}

function isEmpty($path) {
	$empty=true;
	$dir = opendir($path) ;
	while ($entry = readdir($dir)) {
		if ($entry!="." && $entry!=".." && $entry!="\\" && $entry!="/") {
			return false;
		}
	}
	return true;
}

if (@rmdir($fullname)) { //try just to delete it... will fail if not empty
  $message=$this->Lang("singledirdeletesuccess"); 
  $this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$message));
}
if (isEmpty($fullname)) {
	$error=$this->Lang("singledirdeletefail");
	$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_error"=>$error));
} else {
	if (isset($params["confirmed"]) && $params["confirmed"]=="ayesir") {
		//echo deleting;die();
		if (isset($params["cancel"])) {
			$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$this->Lang("dirtreedeletecancelled")));
		} else {
			if (rmdirRecursive($fullname)) {
				$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$this->Lang("dirtreedeletesuccess")));
			} else {
				$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_error"=>$this->Lang("dirtreedeletefail")));
			}
		}
	} else {
		$this->smarty->assign('startform', $this->CreateFormStart($id, 'deletedir', $returnid));
		$this->smarty->assign('confirmed', $this->CreateInputHidden($id,"confirmed","ayesir"));
		$this->smarty->assign('dirname', $this->CreateInputHidden($id,"dirname",$params["dirname"]));
		$this->smarty->assign('path', $this->CreateInputHidden($id,"path",$params["path"]));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('dirnotempty', $this->Lang("dirnotemptyconfirm"));
		$this->smarty->assign('sure', $this->CreateInputSubmit($id, 'submit', $this->Lang('imsure')));
		$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
		echo $this->ProcessTemplate('confirmdeltree.tpl');
	}
}
?>