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
//echo count($selall);
if (count($selall)>1) {
  //echo "hi";die();
  $params["fmerror"]="morethanonefiledirselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$config=cmsms()->GetConfig();

$oldname=$this->decodefilename($selall[0]);
$newname=$oldname; //for initial input box

if (isset($params["newname"])) {
  $newname=$params["newname"];
  if (!filemanager_utils::is_valid_filename($newname)) {
    echo $this->ShowErrors($this->Lang("invaliddestname"));
  } else {
    $cwd = filemanager_utils::get_cwd();
    $fullnewname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),trim($params['newname']));
    if (file_exists($fullnewname)) {
      echo $this->ShowErrors($this->Lang("namealreadyexists"));
      //fallthrough
    } else {
      $fulloldname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),$oldname);
      if (@rename($fulloldname,$fullnewname)) {
	$thumboldname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),'thumb_'.$oldname);
	$thumbnewname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),'thumb_'.trim($params['newname']));
	if( file_exists($thumboldname) ) {
	  @rename($thumboldname,$thumbnewname);
	}
	$this->SetMessage($this->Lang('renamesuccess'));
	$this->Audit('',"File Manager", "Renamed file: ".$fullnewname);
	$this->Redirect($id,"defaultadmin",$returnid,$paramsnofiles);
      } else {
	$this->SetError($this->Lang('renameerror'));
	$this->Redirect($id,"defaultadmin",$returnid,$params);
      }
    }
  }
 }

if( is_array($params['selall']) ) {
  $params['selall'] = serialize($params['selall']);
}
$this->smarty->assign('startform', $this->CreateFormStart($id, 'fileaction', $returnid,"post","",false,"",$params));
//$this->CreateInputHidden($id,"fileaction","rename");
$this->smarty->assign('newnametext',$this->lang("newname"));
$this->smarty->assign('newnameinput',$this->CreateInputText($id,"newname",$newname,40));

$this->smarty->assign('endform', $this->CreateFormEnd());

$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('rename')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
echo $this->ProcessTemplate('renamefile.tpl');

?>
