<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if (isset($params["cancel"])) $this->Redirect($id,"defaultadmin",$returnid,$params);

$selall = $params['selall'];
if( !is_array($selall) ) $selall = unserialize($selall);
unset($params['selall']);

if (count($selall)==0) {
  $params["fmerror"]="nofilesselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
if (count($selall)>1) {
  $params["fmerror"]="morethanonefiledirselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$config = cmsms()->getConfig();
$basedir = $config['root_path'];
$filename=$this->decodefilename($selall[0]);
$src = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),$filename);
if( !file_exists($src) ) {
  $params["fmerror"]="filenotfound";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
$imageinfo = getimagesize($src);
if( !$imageinfo || !isset($imageinfo['mime']) || !startswith($imageinfo['mime'],'image') ) {
    $this->SetError($this->Lang('filenotimage'));
    $this->Redirect($id,"defaultadmin",$returnid);
}
if( !is_writable($src) ) {
    $this->SetError($this->Lang('filenotimage'));
    $this->Redirect($id,"defaultadmin",$returnid);
}

//
// handle submit action(s).
//

if(empty($params['reset'])
   && !empty($params['cx']) && !empty($params['cy'])
   && !empty($params['cw']) && !empty($params['ch'])
   && !empty($params['iw']) && !empty($params['ih'])) {

  //Get the mimeType
  $mimeType = imageEditor::getMime($src);

  //Open new Instance
  $instance = imageEditor::open($src);

  //Resize it if necessary
  if( !empty($params['iw']) && !empty($params['ih']) ) {
      $instance = imageEditor::resize($instance, $mimeType, $params['iw'], $params['ih']);
  }

  //Crop it if necessary
  if( !empty($params['cx']) && !empty($params['cy']) && !empty($params['cw']) && !empty($params['ch']) ) {
      $instance = imageEditor::crop($instance, $mimeType, $params['cx'], $params['cy'], $params['cw'], $params['ch']);
  }

  //Save it
  $res = imageEditor::save($instance, $src, $mimeType);
  if( $this->GetPreference('create_thumbnails') ) filemanager_utils::create_thumbnail($src, NULL, TRUE);

  $this->Redirect($id,"defaultadmin",$returnid);
}


//
// build the form
//
if( is_array($selall) ) $params['selall'] = serialize($selall);
$smarty->assign('formstart',$this->CreateFormStart($id,'resizecrop',$returnid,'post','',false,'',$params));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('filename',$filename);
$url = filemanager_utils::get_cwd_url()."/$filename";
$smarty->assign('image',$url);
$smarty->assign('image_width',$imageinfo[0]);

echo $this->ProcessTemplate('pie.tpl');

?>