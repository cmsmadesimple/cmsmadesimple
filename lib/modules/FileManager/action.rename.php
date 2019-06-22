<?php
if (!function_exists("cmsms")) exit;
if (!$this->AccessAllowed()) exit;

if (isset($params["cancel"])) {
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$selall = $params['selall'];
if( !is_array($selall) ) {
    $selall = unserialize($selall);
}
if (count($selall)==0) {
    $this->SetError($this->Lang('err_nofilesselected'));
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}
//echo count($selall);
if (count($selall)>1) {
    //echo "hi";die();
    $this->SetError($this->Lang('morethanonefiledirselected'));
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$config = $this->config;

$oldname=$this->decodefilename($selall[0]);
$newname=$oldname; //for initial input box

if (isset($params["newname"])) {
    $newname=cleanValue($params["newname"]);
    if (!filemanager_utils::is_valid_filename($newname)) {
        echo $this->ShowErrors($this->Lang("invaliddestname"));
    } else {
        $cwd = filemanager_utils::get_cwd();
        $fullnewname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),trim($newname));
        if (file_exists($fullnewname)) {
            echo $this->ShowErrors($this->Lang("namealreadyexists"));
            //fallthrough
        } else {
            $fulloldname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),$oldname);
            if (@rename($fulloldname,$fullnewname)) {
                $thumboldname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),'thumb_'.$oldname);
                $thumbnewname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),'thumb_'.trim($newname));
                if( file_exists($thumboldname) ) {
                       @rename($thumboldname,$thumbnewname);
                }
                $this->SetMessage($this->Lang('renamesuccess'));
                $this->Audit('',"File Manager", "Renamed file: ".$fullnewname);
                $this->Redirect($id,"defaultadmin",$returnid);
            } else {
                $this->SetError($this->Lang('renameerror'));
                $this->Redirect($id,"defaultadmin",$returnid);
            }
        }
    }
}

if( is_array($params['selall']) ) {
    $params['selall'] = serialize($params['selall']);
}

//$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('renamefile.tpl'), null, null, $smarty );
$smarty->assign('selall', $selall);
$smarty->assign('newname', $newname);
$smarty->display($this->GetTemplateResource('renamefile.tpl'));
