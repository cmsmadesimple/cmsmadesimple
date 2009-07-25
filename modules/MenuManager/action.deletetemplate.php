<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Manage Menu')) exit;

$this->DeleteTemplate($params['tplname']);
$this->Redirect($id, 'defaultadmin', $returnid);

?>
