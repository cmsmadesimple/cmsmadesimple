<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if (isset($params["cancel"])) {
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
$selall = $params['selall'];
if( !is_array($selall) ) {
  $selall = unserialize($selall);
}
if (count($selall)==0) {
  $params["fmerror"]="nofilesselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
if (count($selall)>1) {
  $params["fmerror"]="morethanonefiledirselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}


$config=cmsms()->GetConfig();
$filename=$this->decodefilename($selall[0]);
$src = filemanager_utils::join_path($config['root_path'],filemanager_utils::get_cwd(),$filename);
if( !file_exists($src) ) {
  $params["fmerror"]="filenotfound";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

include_once(dirname(__FILE__).'/easyarchives/EasyArchive.class.php');
$archive = new EasyArchive;
$destdir = filemanager_utils::join_path($config['root_path'],filemanager_utils::get_cwd());
if( !endswith($destdir,'/') ) $destdir .= '/';
$res = $archive->extract($src,$destdir);

$paramsnofiles["fmmessage"]="unpacksuccess"; //strips the file data
$this->Audit('',"File Manager", "Unpacked file: ".$src);
$this->Redirect($id,"defaultadmin",$returnid,$paramsnofiles);

#
# EOF
#
?>