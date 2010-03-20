<?php
if (!isset($gCms)) exit;

// create a permission
$this->Permission->create('Manage Menu', 'Manage Menu');

$this->Template->set('menu', 'cssmenu', $this->Template->get_from_file('cssmenu'));
$this->Template->set('menu', 'cssmenu_ulshadow', $this->Template->get_from_file('cssmenu_ulshadow'));
$this->Template->set('menu', 'minimal_menu', $this->Template->get_from_file('minimal_menu'));
$this->Template->set('menu', 'simple_navigation', $this->Template->get_from_file('simple_navigation'));
?>