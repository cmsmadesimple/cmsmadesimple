<?php
if(!isset($gCms) ) exit;
if( !$this->CheckPermission('Modify News') ) return;

include(dirname(__FILE__).'/function.admin_articlestab.php');

?>
