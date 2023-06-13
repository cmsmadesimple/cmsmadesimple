<?php
if (!function_exists("cmsms")) exit;
if( get_userid(FALSE) < 1 || $this->cms->is_frontend_request() ) throw new \CmsError403Exception('Permission denied');

if( !isset($params['file']) ) {
    $params["fmerror"]="nofilesselected";
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$config=cmsms()->GetConfig();
$filename=$this->decodefilename($params['file']);
$src = filemanager_utils::join_path($config['root_path'],filemanager_utils::get_cwd(),$filename);
if( !file_exists($src) ) {
    $params["fmerror"]="filenotfound";
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}

// get its mime type
$mimetype = filemanager_utils::mime_content_type($src);

$handlers = ob_list_handlers();
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: $mimetype");
echo file_get_contents($src);
exit;
