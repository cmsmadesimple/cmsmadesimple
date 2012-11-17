<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if (isset($params["cancel"])) {
  $this->Redirect($id, "defaultadmin", $returnid, $params);
}

$path = filemanager_utils::get_cwd();

$newdirname = "";
if (isset($params["newdirname"])) {
  $newdirname = trim($params["newdirname"]);


  if (!filemanager_utils::is_valid_filename($params['newdirname'])) {
    // $this->Redirect($id, 'defaultadmin',$returnid,array("fmerror"=>"invalidnewdir"));
    echo $this->ShowErrors($this->Lang("invalidnewdir"));
    //fallthrough
  } else {

    $config = cmsms()->GetConfig();
    $base = $config['root_path'];
    $newdir = $this->Slash($params["path"], $params["newdirname"]);
    $newdir = $this->Slash($base, $newdir);

    if (is_dir($newdir)) {
      echo $this->ShowErrors($this->Lang("direxists"));
      //fallthrough
    } else {

      if (mkdir($newdir)) {
        $params["fmmessage"] = "newdirsuccess"; //strips the file data
        $this->Audit(0, "File Manager", "Created new directory: " . $params["newdirname"]);
        $this->Redirect($id, "defaultadmin", $returnid, $params);
      } else {
        $params["fmerror"] = "newdirfail";
        $this->Redirect($id, "defaultadmin", $returnid, $params);
      }
    }
  }
}
$this->smarty->assign('startform', $this->CreateFormStart($id, 'fileaction', $returnid, "post", "", false, "", $params));
//$this->CreateInputHidden($id, "fileaction", "newdir");
$this->smarty->assign('newdirtext', $this->lang("newdir"));
$this->smarty->assign('newdirinput', $this->CreateInputText($id, "newdirname", $newdirname, 40));

$this->smarty->assign('endform', $this->CreateFormEnd());

$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('create')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
echo $this->ProcessTemplate('newdir.tpl');


?>
