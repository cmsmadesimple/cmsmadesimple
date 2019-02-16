<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Clear Admin Log') ) exit;

$this->storage->clear();
unset($_SESSION['adminlog_filter']);
audit('','Admin log','Cleared');
$this->SetMessage($this->Lang('msg_cleared'));
$this->RedirectToAdminTab();
