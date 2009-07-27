<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Manage Menu')) exit;

$this->DeleteTemplate($params['tplname']);
$this->RemovePreference('tpl_' . $params['tplname'] . '_recursive');
$this->Redirect($id, 'defaultadmin', $returnid);

?>