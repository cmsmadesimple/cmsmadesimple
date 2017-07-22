<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) return;

echo $this->StartTabHeaders();
echo $this->SetTabHeader('categories',$this->Lang('categories'));
echo $this->SetTabHeader('customfields',$this->Lang('customfields'));
echo $this->SetTabHeader('options',$this->Lang('options'));
echo $this->EndTabHeaders();

echo $this->StartTabContent();

echo $this->StartTab('categories', $params);
include(dirname(__FILE__).'/function.admin_categoriestab.php');
echo $this->EndTab();

echo $this->StartTab('customfields', $params);
include(dirname(__FILE__).'/function.admin_customfieldstab.php');
echo $this->EndTab();

echo $this->StartTab('options', $params);
include(dirname(__FILE__).'/function.admin_optionstab.php');
echo $this->EndTab();

echo $this->EndTabContent();

?>
