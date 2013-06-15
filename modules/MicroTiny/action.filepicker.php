<?php
if( !isset($gCms) ) exit;
check_login();
$config=$gCms->GetConfig();

$filepickerstyle=$this->GetPreference("filepickerstyle","both");
$smarty->assign("filepickerstyle",$filepickerstyle);

$smarty->assign("rooturl",$config["root_url"]);
$smarty->assign("admindir",$config["admin_dir"]);
$smarty->assign("filepickertitle",$this->Lang("filepickertitle"));
$smarty->assign("youareintext",$this->Lang("youareintext"));

$type="";
if(isset($params["type"])) {
    $type=$params["type"];
} else {
  if (isset($_GET["type"])) {
    $type=$_GET["type"];
  }
}

function fp_format_size($size)
{
    $suffix = '';
    $val = $size;
    if( $size > 1024 ) {
      // kilobytes
      $val = (float)$size / 1024.0;
      $suffix = 'Kb';
      $val = round($val);

      if( $val > 1024 ) {
	$val = (float)$size / 1024.0 / 1024.0;
	$suffix = 'Mb';
	$val = round($val,2);
      }
    }

    return $val.$suffix;
}

$filepickerstyle=$this->GetPreference("filepickerstyle","both");
$smarty->assign("filepickerstyle",$filepickerstyle);

$smarty->assign("rooturl",$config->smart_root_url());
$smarty->assign("filepickertitle",$this->Lang("filepickertitle"));
$smarty->assign("youareintext",$this->Lang("youareintext"));

{
  $tmp=filemanager_utils::get_cwd();
  if( isset($_GET['subdir']) ) { 
    $tmp .= '/'.trim($_GET['subdir']);
    filemanager_utils::set_cwd($tmp);
  }
}
$startpath=filemanager_utils::get_cwd();

if ($type=="image") {
    $smarty->assign("isimage","1");
} else {
    $smarty->assign("isimage","0");
}
$starturl = $config['root_url'].'/'.$startpath;
$startdir = filemanager_utils::join_path($config['root_path'],$startpath);

function sortfiles($file1,$file2) {
    if ($file1["isdir"] && !$file2["isdir"]) return -1;
    if (!$file1["isdir"] && $file2["isdir"]) return 1;
    return strnatcasecmp($file1["name"],$file2["name"]);
}

$fmmodule = cms_utils::get_module("FileManager");

$files = array();

$d = dir($startdir);
while ($entry = $d->read()) {
  if( $entry == '.' ) continue;
  if( ($entry[0] == '.' || $entry[0] == '_') && !$fmmodule->GetPreference('showhiddenfiles') ) continue;

  if (substr($entry,0,6)=="thumb_") {
    if ($this->GetPreference("showthumbnailfiles")!="1") {
      continue;
    }
  }

  $file=array();
  $file['name']=$entry;
  $file['isimage']="0";
  $file['fullpath']=$startdir.'/'.$entry;
  $file['fullurl']=$starturl.'/'.$entry;
  $file['relurl'] = str_replace($config['root_url'].'/','',$file['fullurl']);
  $file['ext']=strtolower(substr($entry,strrpos($entry,".")));
  $file['isdir']=is_dir($file["fullpath"]);

  if (!$file["isdir"]) {
    if ($type=="image") {
      if ($file["ext"]!=".jpeg" && $file["ext"]!=".jpg" && $file["ext"]!=".gif" && $file["ext"]!=".png") {
	continue;
      }
      else {
	$file["isimage"]="1";
      }
      
      if ($filepickerstyle!="filename") {
	if ($this->GetPreference("showthumbnailfiles")=='1') {
	  $file["thumbnail"] = microtiny_utils::GetThumbnailFile(str_replace("thumb_","",$entry),$startdir,$starturl);
	} else {
	  $file["thumbnail"] = microtiny_utils::GetThumbnailFile($entry,$startdir,$starturl);
	}
      }
      $imgsize=@getimagesize($file["fullpath"]);
      if ($imgsize) {
	$file["dimensions"]=$imgsize[0]."x".$imgsize[1];
      } else {
	$file["dimensions"]="&nbsp;";
      }
    }
  }

  if ($fmmodule) {
    $file["fileicon"]=$fmmodule->GetFileIcon($file["ext"],$file["isdir"]);
  }
  if (!$file["isdir"]) {
    $info=@stat($file["fullpath"]);
    if ($info) {
      $file["size"]=$info["size"];
    }
  }
  $files[]=$file;
}
$d->close();
usort($files,'sortfiles');

$showfiles=array();
if( (filemanager_utils::check_advanced_mode() && $startdir != $config['root_path'] && startswith($startdir,$config['root_path'])) ||
    ($startdir != $config['uploads_path'] && startswith($startdir,$config['uploads_path'])) ) {
  // changedir up... causing problems
  $onerow = new stdClass();
  $onerow->isdir="1";
  $onerow->thumbnail="";
  $onerow->dimensions="";
  $onerow->size="";
  $newsubdir='/..';
  $onerow->namelink=$this->CreateLink($id,"filepicker",$returnid,"[..]",array("subdir"=>$newsubdir,"showtemplate"=>"false","type"=>$type));
  $showfiles[] = $onerow;
}

$filecount=0;
$dircount=0;
foreach($files as $file) {
  $onerow = new stdClass();
  $onerow->name=$file["name"];
  $onerow->fileicon=$file["fileicon"];
  
  if ($file["isdir"]) {
    $onerow->isdir="1";
    $onerow->namelink=$this->CreateLink($id,"filepicker",$returnid,$file["name"],array("subdir"=>$file["name"],"showtemplate"=>"false","type"=>$type));
    $dircount++;
  } else {
    $onerow->isdir="0";
    $onerow->isimage=$file["isimage"];
    if (isset($file["thumbnail"])) $onerow->thumbnail=$file["thumbnail"];
    $onerow->fullurl=$file["fullurl"];
    if (isset($file["dimensions"])) {
      $onerow->dimensions=$file["dimensions"];
    } else {
      $onerow->dimensions="&nbsp;";
    }
    $onerow->relative_url = $file['relurl'];
    $onerow->size=fp_format_size($file['size']);
    $filecount++;
  }
  $showfiles[] = $onerow;
}

$smarty->assign('startpath',$startpath);
$smarty->assign('dircount', $dircount);
$smarty->assign('filecount', $filecount);
$smarty->assign('sizetext', $this->Lang("size"));
$smarty->assign('dimensionstext', $this->Lang("dimensions"));
$smarty->assign('files', $showfiles);
$smarty->assign('filescount', count($showfiles));

echo $this->ProcessTemplate('filepicker.tpl');
?>
