<?php

require_once("config.php");

$smarty = new Smarty_CMS($config);
$smarty->configCMS = &$config;

#$smarty->register_function("bulletmenu", "getBulletMenu");

$page = "/";

if (isset($_GET["page"])) {
	$page = $_GET["page"];
}
else if (isset($_SERVER["PATH_INFO"])) {
	$page = $_SERVER["PATH_INFO"];
}

echo $smarty->fetch('db:'.$page);

?>
