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

$path = filemanager_utils::get_cwd();
$smarty->assign('path',$path);
$tmp_path_parts = explode('/',$path);
$path_parts = [];
for( $i = 0; $i < count($tmp_path_parts); $i++ ) {
    $obj = new StdClass;
    $obj->name = $tmp_path_parts[$i];
    $obj->rel_path = implode('/',array_slice($tmp_path_parts,0,$i+1));
    if( $i < count($tmp_path_parts) - 1 ) {
        // not the last entry
        $obj->url = $this->create_url($id,'changedir','',[ 'setdir' => $obj->rel_path ]);
    } else {
        // the last entry... no link
    }
    $path_parts[] = $obj;
}
$smarty->assign('path',$path);
$smarty->assign('path_parts',$path_parts);
echo $this->ProcessTemplate('fmpath.tpl');

include(dirname(__FILE__)."/uploadview.php");
include(dirname(__FILE__)."/action.admin_fileview.php"); // this is also an action.
