<?php

if (!isset($gCms))
	exit;
$this->RemovePreference();

//$this->DeleteTemplate("pdftemplate");
$this->DeleteTemplate("printtemplate");
$this->DeleteTemplate("linktemplate");
?>