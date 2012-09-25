<?php

echo "obsolete";

die();
/*$CMS_AMIN_PAGE=1;
$path = dirname(dirname(dirname(__FILE__)));
require_once($path . DIRECTORY_SEPARATOR . 'include.php');
check_login(); $userid = get_userid();

$config = cmsms()->GetConfig();
$smarty = cmsms()->GetSmarty();

$modules =&$gCms->modules;
$tiny=$modules["TinyMCE"]['object'];

$filepickerstyle=$tiny->GetPreference("filepickerstyle","both");
$smarty->assign("filepickerstyle",$filepickerstyle);

$smarty->assign("rooturl",$config["root_url"]);
$smarty->assign("admindir",$config["admin_dir"]);
$smarty->assign("filepickertitle",$tiny->Lang("filepickertitle"));
$smarty->assign("youareintext",$tiny->Lang("youareintext"));


$rootpath=""; $rooturl="";
if ($_GET["type"]=="image") {
	$rootpath=$config["image_uploads_path"];
	$rooturl=$config["image_uploads_url"];
	$smarty->assign("isimage","1");
} else {
	$rootpath=$config["uploads_path"];
	$rooturl=$config["uploads_url"];
	$smarty->assign("isimage","0");
}
if (strtolower(substr($rooturl,0,5))=="https") {
  $rooturl=substr($rooturl,8); //remove https:/
} else {
	$rooturl=substr($rooturl,7); //remove http:/
}
$rooturl=substr($rooturl,strpos($rooturl,"/")); //Remove domain

$subdir="";
if (isset($_GET["subdir"])) $subdir=trim($_GET["subdir"]);
$subdir=str_replace(".","",$subdir); //avoid hacking attempts

if ($subdir!="" && $subdir[0]=="/") $subdir=substr($subdir,1);

$thisdir=$rootpath.'/';
if ($subdir!="") $thisdir.=$subdir."/";
$thisurl=$rooturl.'/';
if ($subdir!="") $thisurl.=$subdir."/";

$smarty->assign("subdir",$subdir);

//Handle File upload and dir creation
if ($tiny->GetPreference("allowupload")=="1") {
	if (isset($_POST["uploadformupload"])) {
		//print_r($_FILES);
		if (isset($_FILES["uploadformnewfile"])) {
			//CHeck for uploaded file
			if ($_FILES["uploadformnewfile"]["name"]!="") {
				//checkfor file size
				if (($_FILES["uploadformnewfile"]["size"]>$config["max_upload_size"])
				|| ($_FILES["uploadformnewfile"]["error"]==1)) {
					$smarty->assign('messagefail',$tiny->Lang("filetoobig"));
				} else {
					$filename=$tiny->Slash($thisdir,$_FILES["uploadformnewfile"]["name"]);
					if (cms_move_uploaded_file($_FILES["uploadformnewfile"]["tmp_name"],$filename)) {
						$smarty->assign('messagesuccess',$tiny->Lang("fileuploaded"));
					} else {
						$smarty->assign('messagefail',$tiny->Lang("uploadfailed"));
					}
				}
			} else {
				$smarty->assign('messagefail',$tiny->Lang("nofile"));
			}
		} else {
			//This shouldn't happen
			$smarty->assign('messagefail',$tiny->Lang("nofile"));
		}
	}

	if (isset($_POST["uploadformcreate"])) {
		//$smarty->assign('messagesuccess',"This is a success message");
		if (isset($_POST["uploadformnewdir"]) && $_POST["uploadformnewdir"]!="") {
			$newdir=$tiny->Slash($thisdir,$_POST["uploadformnewdir"]);
			if (!is_dir($newdir)) {
				if (mkdir($newdir)) {
					$smarty->assign('messagesuccess',$tiny->Lang("newdircreated"));
				} else {
					$smarty->assign('messagefail',$tiny->Lang("newdirfailed"));
				}
			} else {
				$smarty->assign('messagefail',$tiny->Lang("direxists"));
			}
		} else {
			$smarty->assign('messagefail',$tiny->Lang("nodirname"));
		}
	}
}




function sortfiles($file1,$file2) {
	if ($file1["isdir"] && !$file2["isdir"]) return -1;
	if (!$file1["isdir"] && $file2["isdir"]) return 1;
	return strnatcasecmp($file1["name"],$file2["name"]);
}

$files = Array();

$d = dir($thisdir);
while ($entry = $d->read()) {
	if ($entry[0]==".") continue;
	if (substr($entry,0,6)=="thumb_") continue;

	$file=array();
	$file["name"]=$entry;
	$file["isimage"]="0";
	$file["fullpath"]=$thisdir.$entry;
	$file["fullurl"]=$thisurl.$entry;
	$file["ext"]=strtolower(substr($entry,strrpos($entry,".")));

	$file["isdir"]=is_dir($file["fullpath"]);


	if (!$file["isdir"]) {
	  if ($_GET["type"]=="image") {
	    if ($file["ext"]!=".jpg" && $file["ext"]!=".gif" && $file["ext"]!=".png") {
	      continue;
	    }
	    // 		}
	    // 		if ($file["ext"]==".jpg" || $file["ext"]==".gif" || $file["ext"]==".png") {
	    else {
	      $file["isimage"]="1";
	    }

	    if ($filepickerstyle!="filename") {
	      $file["thumbnail"]=$tiny->GetThumbnailFile($entry,$thisdir,$thisurl);
	    }
	    $imgsize=@getimagesize($file["fullpath"]);
	    if ($imgsize) {
	      $file["dimensions"]=$imgsize[0]."x".$imgsize[1];
	    } else {
	      $file["dimensions"]="&nbsp;";
	    }
	  }
	}


	if (!$file["isdir"]) {
		$info=stat($file["fullpath"]);
		$file["size"]=$info["size"];
		//getimagesize($file["fullpath"]);
	}
	$files[]=$file;
}
$d->close();
usort($files,"sortfiles");

$showfiles=array();

if ($subdir!="") {
	$onerow = new stdClass();
	$onerow->isdir="1";
	$onerow->thumbnail="";
	$onerow->dimensions="";
	$onerow->size="";
	$newsubdir=dirname($subdir);
	$onerow->namelink="<a href='".$config["root_url"]."/modules/TinyMCE/filepicker.php?type=".$_GET["type"]."&amp;subdir=".$newsubdir."'>[..]</a>\n";
	array_push($showfiles, $onerow);
}

$filecount=0;
$dircount=0;
foreach($files as $file) {
	$onerow = new stdClass();
	$onerow->name=$file["name"];
	if ($file["isdir"]) {
		$onerow->isdir="1";

		$onerow->namelink="<a href='".$config["root_url"]."/modules/TinyMCE/filepicker.php?type=".$_GET["type"]."&amp;subdir=".$subdir."/".$file["name"]."'>[".$file["name"]."]</a>\n";
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

		$onerow->size=number_format($file["size"],0,"",$tiny->Lang("thousanddelimiter"));
		$filecount++;
	}
	array_push($showfiles, $onerow);
}
$smarty->assign('dircount', $dircount);
$smarty->assign('filecount', $filecount);

$smarty->assign('sizetext', $tiny->Lang("size"));
$smarty->assign('dimensionstext', $tiny->Lang("dimensions"));

if ($tiny->GetPreference("allowupload")=="1") {
	$smarty->assign('successtext',$tiny->Lang("success"));
	$smarty->assign('errortext',$tiny->Lang("error"));


	$smarty->assign('fileoperations',$tiny->Lang("fileoperations"));
	$smarty->assign('formstart','<form id="uploadform" method="post" action="'.$config["root_url"].'/modules/TinyMCE/filepicker.php?type='.$_GET["type"].'&amp;subdir='.$subdir.'" enctype="multipart/form-data">');
	$smarty->assign('fileuploadtext',$tiny->Lang("uploadnewfile"));
	$smarty->assign('fileuploadinput',$tiny->CreateInputFile("uploadform","newfile","",20));
	$smarty->assign('fileuploadsubmit',$tiny->CreateInputSubmit("uploadform","upload",$tiny->Lang("uploadfile")));

	$smarty->assign('newdirtext',$tiny->Lang("createnewdir"));
	$smarty->assign('newdirinput',$tiny->CreateInputText("uploadform","newdir","",20));
	$smarty->assign('newdirsubmit',$tiny->CreateInputSubmit("uploadform","create",$tiny->Lang("createdir")));

	$smarty->assign('formend',$tiny->CreateFormEnd());
}

$smarty->assign('files', $showfiles);
$smarty->assign('filescount', count($showfiles));

echo $tiny->ProcessTemplate('filepicker.tpl');
  */
?>