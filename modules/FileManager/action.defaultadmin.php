<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission('Modify Files')) exit;

if (isset($params["fmmessage"]) && $params["fmmessage"]!="") {
    // gotta get rid of this stuff.
    $count="";
    if (isset($params["fmmessagecount"]) && $params["fmmessagecount"]!="") $count=$params["fmmessagecount"];
    echo $this->ShowMessage($this->Lang($params["fmmessage"],$count));
}

if (isset($params["fmerror"]) && $params["fmerror"]!="") {
    // gotta get rid of this stuff
    $count="";
    if (isset($params["fmerrorcount"]) && $params["fmerrorcount"]!="") $count=$params["fmerrorcount"];
    echo $this->ShowErrors($this->Lang($params["fmerror"],$count));
}

if (isset($params["newsort"])) $this->SetPreference("sortby",$params["newsort"]);
include(dirname(__FILE__)."/uploadview.php");
include(dirname(__FILE__)."/action.admin_fileview.php"); // this is also an action.

?>