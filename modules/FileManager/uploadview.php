<?php
if (!function_exists("cmsms")) exit;

if (!$this->CheckPermission('Modify Files')) exit;

$path=filemanager_utils::get_cwd();
$smarty->assign('path',$path);
$smarty->assign('prompt_path',$this->Lang('uploadfilesto'));
$smarty->assign('formstart',$this->CreateFormStart($id, 'upload', $returnid,"post","multipart/form-data"));
$smarty->assign('actionid',$id);
$smarty->assign('maxfilesize',$config["max_upload_size"]);

$smarty->assign('submit',$this->CreateInputSubmit($id,"ok",$this->Lang("ok"),"",""));
$smarty->assign('formend',$this->CreateFormEnd());


$post_max_size = filemanager_utils::str_to_bytes(ini_get('post_max_size'));
$upload_max_filesize = filemanager_utils::str_to_bytes(ini_get('upload_max_filesize'));
$smarty->assign('max_chunksize',min($upload_max_filesize,$post_max_size-1024));
if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
  {
    $smarty->assign('is_ie',1);
  }
$smarty->assign('action_url',$this->create_url('m1_','upload',$returnid));
$smarty->assign('ie_upload_message',$this->Lang('ie_upload_message'));

echo $this->ProcessTemplate('uploadview.tpl');

?>
