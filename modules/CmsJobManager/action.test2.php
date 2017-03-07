<?php
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

$newjob = new Test1Cron;
$newjob->save();

$this->SetMessage('Job Created');
$this->RedirectToAdminTab();