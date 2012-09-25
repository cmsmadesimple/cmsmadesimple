<?php
if (!isset($gCms)) exit;

// create a permission
$this->CreatePermission('Manage Menu', 'Manage Menu');

// add events
$this->AddEventHandler('Core','ContentEditPost',false);
$this->AddEventHandler('Core','ContentDeletePost',false);

// register plugins
$this->RegisterModulePlugin(true);
$this->RegisterSmartyPlugin('menu','function','function_plugin');
$this->RegisterSmartyPlugin('cms_breadcrumbs','function','smarty_cms_breadcrumbs');
?>