<?php
namespace AdminLog;
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

echo __FILE__;
