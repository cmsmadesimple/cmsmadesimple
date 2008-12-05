<?php
if (!isset($gCms)) exit;

$current_version = $oldversion;
switch($current_version)
{
	case "1.0":
	case "1.1":
		$this->create_permission('Manage Menu', 'Manage Menu');

		$current_version = '2.0';
}

?>