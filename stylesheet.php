<?php

require_once("include.php");

$templateid = "";
if (isset($_GET["templateid"])) $templateid = $_GET["templateid"];

echo get_stylesheet($config, $templateid);

?>
