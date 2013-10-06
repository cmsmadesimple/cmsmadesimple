<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['showtemplate']) && $_GET['showtemplate'] == 'false' ) {
  echo filemanager_utils::get_cwd();
  exit;
}

if(!isset($params["newdir"]) || !isset($params["path"])) {
  $this->Redirect($id, 'defaultadmin');
}

$newdir=$params["newdir"];
$path = filemanager_utils::join_path(filemanager_utils::get_cwd(),$newdir);

try 
{
  if( isset($params['ajax']) )
    {
      filemanager_utils::set_cwd(trim($newdir));
    }
  else
    {
      filemanager_utils::set_cwd($path);
      $this->Redirect($id, 'defaultadmin');
    }
}
catch( Exception $e )
{
  audit('','FileManager','Attempt to set working directory to an invalid location: '.$newdir);
  if( isset($params['ajax']) ) exit('ERROR');
}

//echo $params["path"];
if( isset($params['ajax']) ) echo 'OK'; exit;
$this->Redirect($id, 'defaultadmin', $returnid,array("path"=>$path));

?>