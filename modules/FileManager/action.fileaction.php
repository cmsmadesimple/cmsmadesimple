<?php
if (!function_exists("cmsms")) exit;

if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

//print_r($params);
//die();

/*$message=""; $messagecount=0;
$error=""; $errorcount=0;*/
//if(!isset($params["selectedaction"]) || !isset($params["path"])) {
if(!isset($params["path"])) {
  $this->Redirect($id, 'defaultadmin');
}

if( filemanager_utils::test_invalid_path($params['path']) ) {
  $this->Redirect($id, 'defaultadmin',$returnid,array("fmerror"=>"fileoutsideuploads"));
}

$path=$params["path"];

$fileaction="";
if (isset($params["fileaction"])) $fileaction=$params["fileaction"];

$selfiles=array();
$seldirs=array();
$paramsnofiles=array();
//$somethingselected=false;
foreach ($params as $key=>$value) {
  if (substr($key,0,5)=="file_") {
    $selfiles[]=$this->decodefilename(substr($key,5));
  } elseif (substr($key,0,4)=="dir_") {
    $seldirs[]=$this->decodefilename(substr($key,4));
  } else {
    $paramsnofiles[$key]=$value;
  }
}

$selall=array_merge($seldirs,$selfiles);

//print_r($selfiles);
//print_r($seldirs);
//print_r($selall);
//print_r($params);
//die();

// get the dirs from uploadspath
$dirlist=array();
$filerec = get_recursive_file_list($config['uploads_path'], array(), -1, 'DIRS');
//$dirlist[$this->Lang('selecttargetdir')] = '-';
foreach ($filerec as $key => $value) {
  $value1 = str_replace($config['root_path'], '', $value);
  //prevent current dir from showing up
  if ($value1 == ($path . "/")) continue;
  //Check for hidden files
  $dirs = explode("/", $value1);
  $hidden = false;
  foreach ($dirs as $dir) {
    if (substr($dir, 0, 1) == ".") {
      $hidden = true;
      break;
    }
  }
  if ($hidden) continue;

  //not hidden, add to list
  $dirlist[$this->Slashes($value1)] = $this->Slashes($value1);
}

/*if (!count($selfiles)==0) {
  $this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"fmerror"=>"nofilesselected"));
} */

if (isset($params["fileactionnewdir"]) || $fileaction=="newdir") {
  include_once(__DIR__."/action.newdir.php");
  return;
}

if (isset($params["fileactionrename"]) || $fileaction=="rename") {
  include_once(__DIR__."/action.rename.php");
  return;
}

if (isset($params["fileactiondelete"]) || $fileaction=="delete") {
  include_once(__DIR__."/action.delete.php");
  return;
}

if (isset($params["fileactioncopy"]) || $fileaction=="copy") {
  include_once(__DIR__."/action.copy.php");
  return;
}

if (isset($params["fileactionmove"]) || $fileaction=="move") {
  include_once(__DIR__."/action.move.php");
  return;
}

if (isset($params["fileactionunpack"]) || $fileaction=="unpack") {
  include_once(__DIR__."/action.unpack.php");
  return;
}

if (isset($params["fileactionthumb"]) || $fileaction=="thumb") {
  include_once(__DIR__."/action.thumb.php");
  return;
}

if (isset($params['fileactionresizecrop']) || $fileaction == 'resizecrop') {
  include_once(__DIR__.'/action.resizecrop.php');
  return;
}

if (isset($params['fileactionrotate']) || $fileaction == 'rotate') {
  include_once(__DIR__.'/action.rotate.php');
  return;
}

$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"fmerror"=>"unknownfileaction"));

?>
