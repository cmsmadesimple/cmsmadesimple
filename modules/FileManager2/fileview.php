<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Files')) exit;

$config=$gCms->GetConfig();

$this->smarty->assign('currentpath',$this->Lang("currentpath"));
$this->smarty->assign('path',$path);
$this->smarty->assign('dirformstart',$this->CreateFormStart($id,'newdir',$returnid));
$this->smarty->assign('hiddenpath',$this->CreateInputHidden($id,"path",$path));
$this->smarty->assign('newdirnametext',$this->Lang("newdirname"));
$this->smarty->assign('newdirnameinput',$this->CreateInputText($id,"newdirname","",40,255));
$this->smarty->assign('dirsubmit', $this->CreateInputSubmit($id,"go",$this->Lang("ok"),"",""));
$this->smarty->assign('dirformend',$this->CreateFormEnd());
$this->smarty->assign('formstart',$this->CreateFormStart($id, 'filesform',$returnid));
// get the dirs from uploadspath
$filerec= get_recursive_file_list ( $config['uploads_path'] , array() ,-1 ,'DIRS');
$dirlist[$this->Lang('selecttargetdir')]='-';
foreach($filerec as $key=>$value) {
	$value1=str_replace($config['root_path'],'',$value);
	//prevent current dir from showing up
	if ($value1==($path."/")) continue;
	//Check for hidden files
	$dirs=explode("/",$value1);
	$hidden=false;
	foreach($dirs as $dir) {
		if (substr($dir,0,1)==".") {$hidden=true; break;}
	}
	if ($hidden) continue;

	//not hidden, add to list
	$dirlist[$this->Slashes($value1)]=$this->Slashes($value1);
}


$themeObject = &$gCms->variables['admintheme'];
$actions=array(
$this->Lang('moveselected')=>'moveselected'
,$this->Lang("deleteselected")=>"deleteselected"
,$this->Lang('copyselected')=>'copyselected'
//,$this->Lang("chmodselected")=>"chmodselected"
);
$this->smarty->assign('actiondropdown',$this->CreateInputDropdown($id,"selectedaction",$actions));
$this->smarty->assign('targetdir',$this->CreateInputDropdown($id, 'targetdir',$dirlist,'-'));

//,"","",$this->Lang("confirmselected")
$this->smarty->assign('okinput', $this->CreateInputSubmit($id,"ok",$this->Lang("ok")));
$this->smarty->assign('tagallinput',$this->CreateInputCheckBox($id,"tagall","tagall","","onclick='selectall();'"));

$titlelink=$this->Lang("filename");
//if ($sortby=="nameasc" || $sortby=="namedesc") {
$newsort="";
if ($sortby=="nameasc") {
	$newsort="namedesc"; $titlelink.="+";
} else {
	$newsort="nameasc"; 
	if ($sortby=="namedesc") $titlelink.="-";
}
$params["newsort"]=$newsort;
$titlelink="<i>".$titlelink."</i>";
//}
$titlelink=$this->CreateLink($id,"defaultadmin",$returnid,$titlelink,$params);
$this->smarty->assign('filenametext',$titlelink);

$titlelink=$this->Lang("filesize");
//if ($sortby=="sizeasc" || $sortby=="sizedesc") {
$newsort="";
if ($sortby=="sizeasc") {
	$newsort="sizedesc"; $titlelink.="+";
} else {
	$newsort="sizeasc"; 
	if ($sortby=="sizedesc") $titlelink.="-";
}
$params["newsort"]=$newsort;
$titlelink="<i>".$titlelink."</i>";
//}
$titlelink=$this->CreateLink($id,"defaultadmin",$returnid,$titlelink,$params);
$this->smarty->assign('filesizetext',$titlelink);


$this->smarty->assign('fileownertext',$this->Lang("fileowner"));
$this->smarty->assign('filepermstext',$this->Lang("fileperms"));
$this->smarty->assign('fileinfotext',$this->Lang("fileinfo"));

$this->smarty->assign('filedatetext',$this->Lang("filedate"));
$this->smarty->assign('actionstext',$this->Lang("actions"));

$countdirs=0; $countfiles=0; $countfilesize=0;
$files=array();

for ($i=0; $i<count($filelist);$i++) {
	$onerow = new stdClass();
	if ($filelist[$i]["dir"]) {
		$onerow->checkbox="&nbsp;";
	} else {
		$onerow->checkbox=$this->CreateInputCheckBox($id,"file_".base64_encode($filelist[$i]["name"]),"true","");
	}

	if ($this->GetPreference("showthumbnails",0)==1) {
		if ($filelist[$i]["image"]) {
			//echo $filelist[$i]["url"]." ".$config["image_uploads_url"];
			if (stripos($filelist[$i]["url"],$config["image_uploads_url"])!==false) {
				//echo $path;
				$onerow->thumbnail=$this->GetThumbnailLink($filelist[$i],$path);
			} else {
				$onerow->thumbnail="";
			}
		}
	} else {
		$onerow->thumbnail="";
	}

	if ($filelist[$i]["dir"]) {
		$onerow->iconlink=$this->CreateLink($id,
    			     						"changedir",
    											"",
		$this->GetFileIcon($filelist[$i]["ext"],$filelist[$i]["dir"]),
		array("newdir"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby));
	} else {
		$onerow->iconlink="<a href='".$this->Slashes($filelist[$i]["url"])."' target='_blank'>".$this->GetFileIcon($filelist[$i]["ext"])."</a>";
	}
	//echo "<pre>".$onerow->iconlink."</pre>";

	$link=$filelist[$i]["name"];
	if ($filelist[$i]["dir"]) {
		if ($filelist[$i]["name"]!="..") $countdirs++;
		$onerow->txtlink=$this->CreateLink($id,
    			     						"changedir",
    											"",
		$link,
		array("newdir"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby));
	} else {
		$countfiles++;
		$countfilesize+=$filelist[$i]["size"];
		$onerow->txtlink="<a href='".$this->Slashes($filelist[$i]["url"])."' target='_blank'>".$link."</a>";
	}
	$onerow->fileinfo=$filelist[$i]["fileinfo"];
	if ($filelist[$i]["name"]=="..") {
		$onerow->fileaction="&nbsp;";
		$onerow->filepermissions="&nbsp;";
	} else {
		$onerow->fileowner=$filelist[$i]["fileowner"];
		$onerow->filepermissions=$filelist[$i]["permissions"];
	}
	if ($filelist[$i]["dir"]) {
		$onerow->filesize="&nbsp;";
	} else {
		$filesize=$this->FormatFileSize($filelist[$i]["size"]);
		$onerow->filesize=$filesize["size"];
		$onerow->filesizeunit=$filesize["unit"];
	}

	if (!$filelist[$i]["dir"]) {
		$onerow->filedate=str_replace(" ","&nbsp;",date($this->Lang("filedateformat"),$filelist[$i]["date"]));
	} else {
		$onerow->filedate="&nbsp;";
	}

	$onerow->deleteaction="&nbsp;";
	$onerow->renameaction="&nbsp;";
	$onerow->chmodaction="&nbsp;";
	$onerow->writeprotected="&nbsp;";
	if ($filelist[$i]["name"]!="..") {
		if ($filelist[$i]["writable"]) {
			if ($filelist[$i]["dir"]) {
				$onerow->deleteaction=$this->CreateLink($id,
  		  												"deletedir",
  		  												'',
				$this->GetActionIcon("delete"),
				array("dirname"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby),
				$this->Lang("confirmsingledirdelete"),
				false,
				false,
  		  												"title='".$this->Lang("delete")."'");
				$onerow->renameaction=$this->CreateLink($id,"renamefile",'',$this->GetActionIcon("rename"),array("filename"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby),"",false,false,"title='".$this->Lang("rename")."'");


			} else {
				$warnmessage=$this->Lang("confirmsingledelete");
				$onerow->deleteaction=$this->CreateLink($id,"deletefile",'',$this->GetActionIcon("delete"),array("filename"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby),$warnmessage,false,false,"title='".$this->Lang("delete")."'");
				$onerow->renameaction=$this->CreateLink($id,"renamefile",'',$this->GetActionIcon("rename"),array("filename"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby),"",false,false,"title='".$this->Lang("rename")."'");
					
			}
		} else {
			$onerow->writeprotected="<img src='../modules/FileManager/icons/themes/default/actions/padlock.gif' alt='".$this->Lang("notwritable")."' />\n";
		}
		if ($filelist[$i]["dir"]) {
			if ($this->CHModMakesSense()) {
				$onerow->chmodaction=$this->CreateLink($id,"chmoddir",'',$this->GetActionIcon("chmod"),array("dirname"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby),"",false,false,"title='".$this->Lang("chmod")."'");
			} else {
				$onerow->chmodaction="&nbsp;";
				//$onerow->chmodaction=$this->CreateLink($id,"chmoddirwin",'',$this->GetActionIcon("chmod"),array("dirname"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby),"",false,false,"title='".$this->Lang("chmod")."'");
			}
		} else {
			if ($this->CHModMakesSense()) {
				$onerow->chmodaction=$this->CreateLink($id,"chmodfile",'',$this->GetActionIcon("chmod"),array("filename"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby),"",false,false,"title='".$this->Lang("chmod")."'");
			} else {
				$onerow->chmodaction=$this->CreateLink($id,"chmodfilewin",'',$this->GetActionIcon("chmod"),array("filename"=>$filelist[$i]["name"],"path"=>$path,"sortby"=>$sortby),"",false,false,"title='".$this->Lang("chmod")."'");
			}
		}
	}
	array_push($files, $onerow);
}
$this->smarty->assign_by_ref('files', $files);
$this->smarty->assign('itemcount', count($files));
$totalsize=$this->FormatFileSize($countfilesize);
$counts=$totalsize["size"]." ".$totalsize["unit"]." ".$this->Lang("in")." ".$countfiles." ";
if ($countfiles==1) $counts.=$this->Lang("file"); else $counts.=$this->Lang("files");
$counts.=" ".$this->Lang("and")." ".$countdirs." ";
if ($countdirs==1) $counts.=$this->Lang("subdir"); else $counts.=$this->Lang("subdirs");
$this->smarty->assign('countstext',$counts);
$this->smarty->assign('formend',$this->CreateFormEnd());

echo $this->ProcessTemplate('filemanager.tpl');

?>
