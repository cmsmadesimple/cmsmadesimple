<?php

require_once("config.php");

$smarty = new Smarty_CMS($config);
$smarty->configCMS = &$config;

$page = "";

if (isset($config->query_var) && $config->query_var != "") {
	if (isset($_GET[$config->query_var])) {
		$page = $_GET[$config->query_var];
	}
}
else {
	if (isset($_SERVER["PATH_INFO"])) {
		$page = $_SERVER["PATH_INFO"];
	}
}

if ($page == "") {
	$page = db_get_default_page($config);
}

echo $smarty->fetch('db:'.$page);

?>
