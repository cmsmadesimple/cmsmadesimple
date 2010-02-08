<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;
//var_dump($params);die();
if(!isset($params["filename"]) || !isset($params["path"])) {
	$this->Redirect($id, 'defaultadmin');
}

if ($this->IntruderCheck($params["path"])) {
	$this->Redirect($id, 'defaultadmin',$returnid,array("module_error"=>$this->Lang("fileoutsideuploads")));
}
$config =& $gCms->GetConfig();
$fullname=$this->Slash($params["path"],$params["filename"]);
$fullname=$this->Slash($config["root_path"],$fullname);

$message=""; $error="";

if (@unlink($fullname)) $message=$this->Lang("singlefiledeletesuccess"); else $error=$this->Lang("singlefiledeletefail");

$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$message,"module_error"=>$error));

?>