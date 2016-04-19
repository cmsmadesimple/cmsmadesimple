<?php
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

//$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_jobs_tab.tpl'),null,null,$smarty);
$tpl = $this->create_new_template('admin_jobs_tab.tpl');
$tpl->display();