<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Manage Menu')) exit;

$this->clear_cache();
$this->DeleteTemplate($params['tplname']);
audit('',$this->GetName(),'Deleted template '.$params['tplname']);
$this->Redirect($id, 'defaultadmin', $returnid);

?>
