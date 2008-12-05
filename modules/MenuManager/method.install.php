<?php
if (!isset($gCms)) exit;

// create a permission
$this->create_permission('Manage Menu', 'Manage Menu');

$this->set_template('menu_template', 'Default Template', $this->get_default_template(), true);

?>