<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

/**
 * Entry point for all non-admin pages
 *
 * @package CMS
 */	
$starttime = microtime();

@ob_start();

if (!file_exists("config.php") || filesize("config.php") == 0)
{
    require_once("lib/misc.functions.php");
    redirect("install/install.php");
}
else if (file_exists(dirname(__FILE__)."/tmp/cache/SITEDOWN"))
{
	echo "<html><head><title>Maintenance</title></head><body><p>Site down for maintenance.</p></body></html>";
	exit;
}

/*
if (isset($_GET["deleteinstall"]) && $_GET["deleteinstall"] == "true") {
    @unlink("install/install.php");
} ## if

if (file_exists("config.php") && file_exists("install/install.php")) {
    echo "You cannot start CMS until you remove the install.php<br>\n";
    if (isset($_GET["deleteinstall"]) && $_GET["deleteinstall"] == "true") {
        echo "Looks like you tried to have CMS delete the install file but that was not sucessful.  You will have to remove it manually before you can continue<br>\n";
        exit;
    } ## if
    echo "Click <a href=\"index.php?deleteinstall=true\">here</a> to have CMS try to delete it for you.  If successful you will see the CMS main page<br>\n";
    exit;
} ## if
*/

require_once(dirname(__FILE__)."/include.php"); #Makes gCms object


$smarty = new Smarty_CMS($config);
$gCms->smarty = &$smarty;

$page = "";

if (isset($config["query_var"]) && $config["query_var"] != "" && isset($_GET[$config["query_var"]]))
{
	$page = $_GET[$config["query_var"]];
}
#else if (isset($_SERVER["PATH_INFO"]) && (isset($_SERVER["SCRIPT_URL"]) && ($_SERVER["PATH_INFO"] != $_SERVER["SCRIPT_URL"])))
#{
#	$page = $_SERVER["PATH_INFO"];
#}
#else if (isset($_SERVER["QUERY_STRING"]) && strpos($_SERVER["QUERY_STRING"], 'deleteinstall') === false)
#{
#	$page = $_SERVER["QUERY_STRING"];
#}

if ($page == "")
{
	$page = ContentManager::GetDefaultContent();
}

if (get_site_preference('enablecustom404') == "0")
{
	$old_error_handler = set_error_handler("ErrorHandler404");
}

$html = "";
if (isset($_GET["print"]))
{
	($smarty->is_cached('print:'.$page)?$cached="":$cached="not ");
	$html = $smarty->fetch('print:'.$page) . "\n";
}
else
{
	($smarty->is_cached('db:'.$page)?$cached="":$cached="not ");
	$html = $smarty->fetch('db:'.$page) . "\n";
}

if (get_site_preference('enablecustom404') == "0")
{
	set_error_handler($old_error_handler);
}

#if(password_protected($page) != -1 && !check_access(password_protected($page)))
#{
#	$html = display_login_form();
#}

#Perform the content prerender callback
foreach($gCms->modules as $key=>$value)
{
	if (isset($gCms->modules[$key]['content_postrender_function']) &&
		$gCms->modules[$key]['Installed'] == true &&
		$gCms->modules[$key]['Active'] == true)
	{
		call_user_func_array($gCms->modules[$key]['content_postrender_function'], array(&$gCms, &$html));
	}
}

echo $html;

@ob_flush();

$endtime = microtime();

echo "<!-- Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple $CMS_VERSION (".$cached."cached) using $sql_execs SQL queries -->\n";
echo "<!-- CMS Made Simple - Released under the GPL - http://cmsmadesimple.org -->\n";

if (get_site_preference('enablesitedownmessage') == "1")
{
	$smarty->clear_compiled_tpl();
	$smarty->clear_all_cache();
}

if ($config["debug"] == true)
{
	echo $sql_queries;
}

# vim:ts=4 sw=4 noet
?>
