<?php
if (!file_exists("config.php")) {
    require_once("lib/misc.functions.php");
    redirect("install.php");
    exit;
} ## if
if ($_GET["deleteinstall"] == "true") {
    @unlink("install.php");
} ## if

if (file_exists("config.php") && file_exists("install.php")) {
    echo "You cannot start CMS until you remove the install.php<br>\n";
    if ($_GET["deleteinstall"] == "true") {
        echo "Looks like you tried to have CMS delete the install file but that was not sucessful.  You will have to remove it manually before you can continue<br>\n";
        exit;
    } ## if
    echo "Click <a href=\"index.php?deleteinstall=true\">here</a> to have CMS try to delete it for you.  If successful you will see the CMS main page<br>\n";
    exit;
} ## if

require_once("include.php");

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
