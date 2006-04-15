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
#
#$Id$

require_once(dirname(__FILE__).'/fileloc.php');

/**
 * Entry point for all non-admin pages
 *
 * @package CMS
 */	
#echo '<code style="align: left;">';
#var_dump($_SERVER);
#echo '</code>';

$starttime = microtime();

@ob_start();

clearstatcache();

if (!isset($_SERVER['REQUEST_URI']))
{
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}

if (!file_exists(CONFIG_FILE_LOCATION) || filesize(CONFIG_FILE_LOCATION) < 800)
{
    require_once("lib/misc.functions.php");
    redirect("install/install.php");
}
else if (file_exists(TMP_CACHE_LOCATION.'/SITEDOWN'))
{
	echo "<html><head><title>Maintenance</title></head><body><p>Site down for maintenance.</p></body></html>";
	exit;
}

if (!is_writable(TMP_TEMPLATES_C_LOCATION) || !is_writable(TMP_CACHE_LOCATION))
{
	echo '<html><title>Error</title></head><body>';
	echo '<p>The following directories must be writable by the web server:<br />';
	echo 'tmp/cache<br />';
	echo 'tmp/templates_c<br /></p>';
	echo '<p>Please correct by executing:<br /><em>chmod 777 tmp/cache<br />chmod 777 tmp/templates_c</em><br />or the equivilent for your platform before continuing.</p>';
	echo '</body></html>';
	exit;
}

require_once(dirname(__FILE__)."/include.php"); #Makes gCms object

$params = array_merge($_GET, $_POST);

global $gCms;
$smarty = &$gCms->smarty;
$smarty->params = $params;

$page = '';

if (isset($params['mact']))
{
	$ary = explode(',', $params['mact'], 4);
	$smarty->id = (isset($ary[1])?$ary[1]:'');
}
else
{
	$smarty->id = (isset($params['id'])?$params['id']:'');
}

#var_dump($smarty->id);

if (isset($smarty->id) && isset($params[$smarty->id . 'returnid']))
{
	$page = $params[$smarty->id . 'returnid'];
}
else if (isset($config["query_var"]) && $config["query_var"] != '' && isset($_GET[$config["query_var"]]))
{
	$page = $_GET[$config["query_var"]];
}
#else if (isset($_SERVER["PATH_INFO"]) && (isset($_SERVER["SCRIPT_URL"]) && ($_SERVER["PATH_INFO"] != $_SERVER["SCRIPT_URL"])))
else if (isset($_SERVER["PHP_SELF"]) && !endswith($_SERVER['PHP_SELF'], 'index.php'))
{
	$matches = array();
	//Handle routes here...  this could get hairy
	if (preg_match('/.*index\.php.*\/(.*?)$/', $_SERVER['PHP_SELF'], $matches))
	{
		//var_dump($matches);
		$page = $matches[1];
	}
}

if ($page == '')
{
	$page =& ContentManager::GetDefaultContent();
}
else
{
    $page = preg_replace('/\</','',$page);
}

$pageinfo = PageInfoOperations::LoadPageInfoByContentAlias($page);
#var_dump($pageinfo);
if (isset($pageinfo) && $pageinfo !== FALSE)
{
	$gCms->variables['pageinfo'] =& $pageinfo;

	$gCms->variables['content_id'] = $pageinfo->content_id;
	$gCms->variables['page'] = $page;
	$gCms->variables['page_id'] = $page;

	$gCms->variables['page_name'] = $pageinfo->content_alias;
	$gCms->variables['position'] = $pageinfo->content_hierarchy;
	$gCms->variables['friendly_position'] = ContentManager::CreateFriendlyHierarchyPosition($pageinfo->content_hierarchy);
}
else if (get_site_preference('enablecustom404') == '' || get_site_preference('enablecustom404') == "0")
{
	ErrorHandler404();
	exit;
}

$html = '';
$cached = '';

if (isset($_GET["print"]))
{
	($smarty->is_cached('print:'.$page, '', $pageinfo->template_id)?$cached="":$cached="not ");
	$html = $smarty->fetch('print:'.$page, '', $pageinfo->template_id) . "\n";
}
else
{
	#If this is a case where a module doesn't want a template to be shown, just disable caching
	if (isset($smarty->id) && $smarty->id != '' && isset($_GET[$smarty->id.'showtemplate']) && $_GET[$smarty->id.'showtemplate'] == 'false')
	{
		$html = $smarty->fetch('template:notemplate') . "\n";
	}
	else
	{
		$oldvalue = $smarty->caching;
		$smarty->caching = !$config['debug'];
		$smarty->compile_check = true;
		($smarty->is_cached('template:'.$pageinfo->template_id)?$cached="":$cached="not ");
		$html = $smarty->fetch('template:'.$pageinfo->template_id) . "\n";
		$smarty->caching = $oldvalue;
	}
}

#if ((get_site_preference('enablecustom404') == '' || get_site_preference('enablecustom404') == "0") && (!$config['debug']))
#{
	#set_error_handler($old_error_handler);
#}

if (!$cached)
{
	#Perform the content postrendernoncached callback
	reset($gCms->modules);
	while (list($key) = each($gCms->modules))
	{
		$value =& $gCms->modules[$key];
		if ($gCms->modules[$key]['installed'] == true &&
			$gCms->modules[$key]['active'] == true)
		{
			$gCms->modules[$key]['object']->ContentPostRenderNonCached($html);
		}
	}
}

#Perform the content postrender callback
reset($gCms->modules);
while (list($key) = each($gCms->modules))
{
	$value =& $gCms->modules[$key];
	if ($gCms->modules[$key]['installed'] == true &&
		$gCms->modules[$key]['active'] == true)
	{
		$gCms->modules[$key]['object']->ContentPostRender($html);
	}
}

header("Content-Type: " . $gCms->variables['content-type'] . "; charset=" . (isset($pageinfo->template_encoding) && $pageinfo->template_encoding != ''?$pageinfo->template_encoding:get_encoding()));

echo $html;

@ob_flush();

$endtime = microtime();

if ($config["debug"] == true)
{
	echo "<p>Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple $CMS_VERSION (".$cached."cached) using $db->query_count SQL queries and ".(function_exists('memory_get_usage')?memory_get_usage():'n/a')." bytes of memory</p>";
}

echo "<!-- Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple $CMS_VERSION (".$cached."cached) using $db->query_count SQL queries -->\n";
#echo "<p>Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple $CMS_VERSION (".$cached."cached) using $db->query_count SQL queries and ".(function_exists('memory_get_usage')?memory_get_usage():'n/a')." bytes of memory</p>";
echo "<!-- CMS Made Simple - Released under the GPL - http://cmsmadesimple.org -->\n";

if (get_site_preference('enablesitedownmessage') == "1" || $config['debug'] == true)
{
	$smarty->clear_compiled_tpl();
	$smarty->clear_all_cache();
}

if ($config["debug"] == true)
{
	#$db->LogSQL(false); // turn off logging
	
	# output summary of SQL logging results
	#$perf = NewPerfMonitor($db);
	#echo $perf->SuspiciousSQL();
	#echo $perf->ExpensiveSQL();

	echo $sql_queries;
	foreach ($gCms->errors as $error)
	{
		echo $error;
	}
}

# vim:ts=4 sw=4 noet
?>
