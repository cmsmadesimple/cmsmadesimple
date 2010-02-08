<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if(!isset($params["newdirname"]) || !isset($params["path"])) {
	$this->Redirect($id, 'defaultadmin');
}

if ($this->IntruderCheck($params["path"])) {
	$this->Redirect($id, 'defaultadmin',$returnid,array("module_error"=>$this->Lang("fileoutsideuploads")));
}

if (strpos($params["newdirname"],"/")!==false ||
    strpos($params["newdirname"],"\\")!==false ||
    strpos($params["newdirname"],"..")!==false) {
  $this->Redirect($id, 'defaultadmin',$returnid,array("module_error"=>$this->Lang("invalidnewdir")));
}

$config =& $gCms->GetConfig();
$newdir=$this->Slash($params["path"],$params["newdirname"]);
$newdir=$this->Slash($config["root_path"],$newdir);

//echo $newdir; die();
$message=""; $error="";
if (mkdir($newdir)) {
  $message=$this->Lang("newdirsuccess");
} else {
  $error=$this->Lang("newdirfail");
}

$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$message,"module_error"=>$error))

?>
