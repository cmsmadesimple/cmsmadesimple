<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

$uploadboxes=$this->GetPreference("uploadboxes","5");

if (!isset($params["path"])) {
	$this->Redirect($id, 'defaultadmin',$returnid,array("module_error"=>"Internal error, no path send with upload form"));
	exit;
}

if ($this->IntruderCheck($params["path"])) {
	$this->Redirect($id, 'defaultadmin',$returnid,array("module_error"=>$this->Lang("fileoutsideuploads")));
}

$somefound=false;
for ($i=0; $i<$uploadboxes; $i++) {
	if (isset($_FILES[$id."file_".$i])) {
		$somefound=true;
		break;
	}
}
if (!$somefound) {
	$this->Redirect($id, 'defaultadmin', $returnid,array("module_error"=>$this->Lang("nothinguploaded")));
	exit;
}

global $gCms;
$config =& $gCms->GetConfig();
//print_r($_FILES);die();

$messages="";
$errors="";
$fullpath=$this->Slash($config["root_path"],$params["path"]);
for ($i=0; $i<$uploadboxes; $i++) {

  if (!isset($_FILES[$id."file_".$i])) continue;
  if ($this->ContainsIllegalChars($_FILES[$id."file_".$i]["name"])) {
    $errors.="<span class='fm-messages'>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("containsillegalchars")."</span>\n";
    continue;
  }
  if (($_FILES[$id."file_".$i]["size"]>$config["max_upload_size"]) 
  		|| ($_FILES[$id."file_".$i]["error"]==1)) {
  	$errors.="<span class='fm-messages'>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("filetoobig")."</span>\n";
    continue;
  }

	if (isset($params["unpack".$i])) {
    $pos=strrpos($_FILES[$id."file_".$i]["name"],'.');
		$extension="";
		if ($pos>0) {
			$extension=substr($_FILES[$id."file_".$i]["name"],$pos+1);
		}
		switch (strtolower($extension)) {
			case "zip" : {
				include_once(dirname(__FILE__)."/dunzip.php");
				$unzipping=new dUnzip2($_FILES[$id."file_".$i]["tmp_name"]);
				$message=$unzipping->unzipAll($fullpath);
				if ($unzipping->debugstrings!="") {
					$errors.="<li>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("unpackfail");
					$errors.=$unzipping->debugstrings."</li>";
				} else {
					$messages.="<li>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("unpacksuccess")."</li>\n";
				}
				break;
			}
			case "tgz" :
			case "gz" : {
				include_once(dirname(__FILE__)."/untgz.php");
				$extracter=new gzip_file($_FILES[$id."file_".$i]["tmp_name"]);
				$extracter->set_options(array("basedir"=>$fullpath/*."/tmp/testtmp/"*/,"overwrite"=>1));
				$extracter->extract_files();
				if (count($extracter->error)>0) {
					$errors.="<li>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("unpackfail");
					$errors.=implode(",",$extracter->error)."</li>\n";
				} else {
					$messages.="<li>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("unpacksuccess")."</li>\n";
				}
				break;
			}
			case "bzip" :
			case "bz" : {
				include_once(dirname(__FILE__)."/untgz.php");
				$extracter=new gzip_file($_FILES[$id."file_".$i]["tmp_name"]);
				$extracter->set_options(array("basedir"=>$fullpath/*."/tmp/testtmp/"*/,"overwrite"=>1));
				$extracter->extract_files();
				if (count($extracter->error)>0) {
					$errors.="<li>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("unpackfail");
					$errors.=implode(",",$extracter->error)."</li>\n";
				} else {
					$messages.="<li>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("unpacksuccess")."</li>\n";
				}
				break;
			}
			default: $errors.=$this->Lang("unsupportedarchive")." ($extension)";
		}

	} else {
		if (trim($_FILES[$id."file_".$i]["name"])=="") continue;
		$thispath=$this->Slash($fullpath,$_FILES[$id."file_".$i]["name"]);
		if (cms_move_uploaded_file($_FILES[$id."file_".$i]["tmp_name"],$thispath)) {
			$messages.="<span class='fm-messages'>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("uploadsuccess")."</span>\n";
		} else {
			$errors.="<span class='fm-messages'>".$_FILES[$id."file_".$i]["name"]." ".$this->Lang("uploadfail")."</span>\n";
		}
	}
}

/*if ($messages!="") $messages="<ul>".$messages."</ul>";
if ($errors!="") $errors="<ul>".$errors."</ul>";
*/

$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$messages,"module_error"=>$errors));
?>
