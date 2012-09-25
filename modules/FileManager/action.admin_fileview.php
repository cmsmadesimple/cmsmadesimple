<?php

if (!function_exists("cmsms")) exit;

if (!$this->CheckPermission('Modify Files')) exit;

$sortby=$this->GetPreference("sortby","nameasc");
$path=filemanager_utils::get_cwd();
$filelist=filemanager_utils::get_file_list($path);

$config = $gCms->GetConfig();
$this->smarty->assign('currentpath', $this->Lang("currentpath"));
$this->smarty->assign('path', $path);
$this->smarty->assign('hiddenpath', $this->CreateInputHidden($id, "path", $path));
$this->smarty->assign('formstart', $this->CreateFormStart($id, 'fileaction', $returnid));

$themeObject = cms_utils::get_theme_object();
$titlelink = $this->Lang("filename");
$newsort = "";
if ($sortby == "nameasc") {
  $newsort = "namedesc";
  $titlelink.="+";
} else {
  $newsort = "nameasc";
  if ($sortby == "namedesc") $titlelink.="-";
}
$params["newsort"] = $newsort;
$titlelink = $this->CreateLink($id, "defaultadmin", $returnid, $titlelink, $params);
$this->smarty->assign('filenametext', $titlelink);

$titlelink = $this->Lang("filesize");
$newsort = "";
if ($sortby == "sizeasc") {
  $newsort = "sizedesc";
  $titlelink.="+";
} else {
  $newsort = "sizeasc";
  if ($sortby == "sizedesc") $titlelink.="-";
}
$params["newsort"] = $newsort;
//}
$titlelink = $this->CreateLink($id, "defaultadmin", $returnid, $titlelink, $params);
$this->smarty->assign('filesizetext', $titlelink);


$this->smarty->assign('fileownertext', $this->Lang("fileowner"));
$this->smarty->assign('filepermstext', $this->Lang("fileperms"));
$this->smarty->assign('fileinfotext', $this->Lang("fileinfo"));

$this->smarty->assign('filedatetext', $this->Lang("filedate"));
$this->smarty->assign('actionstext', $this->Lang("actions"));

$countdirs = 0;
$countfiles = 0;
$countfilesize = 0;
$files = array();

for ($i = 0; $i < count($filelist); $i++) {
  $onerow = new stdClass();
  $onerow->urlname = $this->encodefilename($filelist[$i]['name']);
  $onerow->type = 'file';
  if( isset($params[$onerow->urlname]) ) {
    $onerow->checked = true;
  }
  if ($filelist[$i]["dir"]) {
    $urlname="dir_" . $this->encodefilename($filelist[$i]["name"]);
    $value="";
    if (isset($params[$urlname])) $value="true";
    $onerow->checkbox = $this->CreateInputCheckBox($id, $urlname , "true", $value);
  } else {
    $urlname="file_" . $this->encodefilename($filelist[$i]["name"]);
    $value="";
    if (isset($params[$urlname])) $value="true";
    $onerow->checkbox = $this->CreateInputCheckBox($id, $urlname, "true", $value);
  }

  $onerow->thumbnail = '';
  if ($this->GetPreference("showthumbnails", 0) == 1) {
    if ($filelist[$i]["image"]) {
      $onerow->type = 'image';
      //if (stripos($filelist[$i]["url"], $config["image_uploads_url"]) !== false) {
      $onerow->thumbnail = $this->GetThumbnailLink($filelist[$i], $path);
    }
  }

  if ($filelist[$i]["dir"]) {
    $onerow->iconlink = $this->CreateLink($id,
        "changedir",
        "",
        $this->GetFileIcon($filelist[$i]["ext"], $filelist[$i]["dir"]),
        array("newdir" => $filelist[$i]["name"], "path" => $path, "sortby" => $sortby));
  } else {
    $onerow->iconlink = "<a href='" . $filelist[$i]["url"] . "' target='_blank'>" . $this->GetFileIcon($filelist[$i]["ext"]) . "</a>";
  }

  $link = $filelist[$i]["name"];
  if ($filelist[$i]["dir"]) {
    if( $filelist[$i]['name'] != '..' ) {
      $countdirs++;
      $onerow->type = 'dir';
    }
    else {
      $onerow->noCheckbox = 1;
    }
    $onerow->txtlink = $this->CreateLink($id,
					 "changedir",
					 "",
					 $link,
					 array("newdir" => $filelist[$i]["name"], "path" => $path, "sortby" => $sortby));
  } else {
    $countfiles++;
    $countfilesize+=$filelist[$i]["size"];
    $onerow->txtlink = "<a href='" . $filelist[$i]["url"] . "' target='_blank'>" . $link . "</a>";
  }
  if( $filelist[$i]['archive']  ) {
    $onerow->type = 'archive';
  }

  $onerow->fileinfo = $filelist[$i]["fileinfo"];
  if ($filelist[$i]["name"] == "..") {
    $onerow->fileaction = "&nbsp;";
    $onerow->filepermissions = "&nbsp;";
  } else {
    $onerow->fileowner = $filelist[$i]["fileowner"];
    $onerow->filepermissions = $filelist[$i]["permissions"];
  }
  if ($filelist[$i]["dir"]) {
    $onerow->filesize = "&nbsp;";
  } else {
    $filesize = $this->FormatFileSize($filelist[$i]["size"]);
    $onerow->filesize = $filesize["size"];
    $onerow->filesizeunit = $filesize["unit"];
  }

  if (!$filelist[$i]["dir"]) {
    $onerow->filedate = $filelist[$i]["date"];
  } else {
    $onerow->filedate = '';
  }

  $files[] = $onerow;
}
$this->smarty->assign_by_ref('files', $files);
$this->smarty->assign('itemcount', count($files));
$totalsize = $this->FormatFileSize($countfilesize);
$counts = $totalsize["size"] . " " . $totalsize["unit"] . " " . $this->Lang("in") . " " . $countfiles . " ";
if ($countfiles == 1) $counts.=$this->Lang("file"); else $counts.=$this->Lang("files");
$counts.=" " . $this->Lang("and") . " " . $countdirs . " ";
if ($countdirs == 1) $counts.=$this->Lang("subdir"); else
    $counts.=$this->Lang("subdirs");
$this->smarty->assign('countstext', $counts);

$this->smarty->assign('formend', $this->CreateFormEnd());

if( isset($params['noform']) )
  {
    $smarty->assign('noform',1);
  }
$smarty->assign('refresh_url',$this->Create_url($id,'admin_fileview','',array('noform'=>1)));
$smarty->assign('mod',$this);
$smarty->assign('confirm_unpack',$this->Lang('confirm_unpack'));
echo $this->ProcessTemplate('filemanager.tpl');
?>
