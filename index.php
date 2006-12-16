<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

$dirname = dirname(__FILE__);
require_once($dirname.'/fileloc.php');

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

if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']))
{
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}

if (!file_exists(CONFIG_FILE_LOCATION) || filesize(CONFIG_FILE_LOCATION) < 800)
{
    require_once($dirname.'/lib/misc.functions.php');
    if (FALSE == is_file($dirname.'/install/install.php')) {
        die ('There is no config.php file or install/install.php please correct one these errors!');
    } else {
        redirect('install/install.php');
    }
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

require_once($dirname.'/include.php'); #Makes gCms object

$params = array_merge($_GET, $_POST);

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

if (isset($smarty->id) && isset($params[$smarty->id . 'returnid']))
{
	$page = $params[$smarty->id . 'returnid'];
}
else if (isset($config["query_var"]) && $config["query_var"] != '' && isset($_GET[$config["query_var"]]))
{
	$page = $_GET[$config["query_var"]];

    //trim off the extension, if there is one set
    if ($config['page_extension'] != '' && endswith($page, $config['page_extension']))
    {   
        $page = substr($page, 0, strlen($page) - strlen($config['page_extension']));
    }
}
else
{
	$calced = cms_calculate_url();
	if ($calced != '')
		$page = $calced;
}

//See if our page matches any predefined routes
$page = rtrim($page, '/');
if (strpos($page, '/') !== FALSE)
{
	$routes =& $gCms->variables['routes'];
	
	$matched = false;
	foreach ($routes as $route)
	{
		$matches = array();
		if (preg_match($route->regex, $page, $matches))
		{
			//Now setup some assumptions
			if (!isset($matches['id']))
				$matches['id'] = 'cntnt01';
			if (!isset($matches['action']))
				$matches['action'] = 'defaulturl';
			if (!isset($matches['inline']))
				$matches['inline'] = 0;
			if (!isset($matches['returnid']))
				$matches['returnid'] = ''; #Look for default page
			if (!isset($matches['module']))
				$matches['module'] = $route->module;

			//Get rid of numeric matches
			foreach ($matches as $key=>$val)
			{
				if (is_int($key))
				{
					unset($matches[$key]);
				}
				else
				{
					if ($key != 'id')
						$_REQUEST[$matches['id'] . $key] = $val;
				}
			}

			//Now set any defaults that might not have been in the url
			if (isset($route->defaults) && count($route->defaults) > 0)
			{
				foreach ($route->defaults as $key=>$val)
				{
					$_REQUEST[$matches['id'] . $key] = $val;
				}
			}

			//Get a decent returnid
			if ($matches['returnid'] == '') {
				global $gCms;
				$contentops =& $gCms->GetContentOperations();
				$matches['returnid'] = $contentops->GetDefaultPageID();
			}

			$_REQUEST['mact'] = $matches['module'] . ',' . $matches['id'] . ',' . $matches['action'] . ',' . $matches['inline'];
			$page = $matches['returnid'];
			$smarty->id = $matches['id'];

			$matched = true;
		}
	}

	if (!$matched)
	{
		$page = substr($page, strrpos($page, '/') + 1);
	}
}

if ($page == '')
{
	global $gCms;
	$contentops =& $gCms->GetContentOperations();
	$page =& $contentops->GetDefaultContent();
}
else
{
    $page = preg_replace('/\</','',$page);
}

$pageinfo = PageInfoOperations::LoadPageInfoByContentAlias($page);

if (isset($pageinfo) && $pageinfo !== FALSE)
{
	$gCms->variables['pageinfo'] =& $pageinfo;

	$gCms->variables['content_id'] = $pageinfo->content_id;
	$gCms->variables['page'] = $page;
	$gCms->variables['page_id'] = $page;

	$gCms->variables['page_name'] = $pageinfo->content_alias;
	$gCms->variables['position'] = $pageinfo->content_hierarchy;
	global $gCms;
	$contentops =& $gCms->GetContentOperations();
	$gCms->variables['friendly_position'] = $contentops->CreateFriendlyHierarchyPosition($pageinfo->content_hierarchy);
	
	$smarty->assign('content_id', $pageinfo->content_id);
	$smarty->assign('page', $page);
	$smarty->assign('page_id', $page);	
	$smarty->assign('page_name', $pageinfo->content_alias);
	$smarty->assign('page_alias', $pageinfo->content_alias);
	$smarty->assign('position', $pageinfo->content_hierarchy);
	$smarty->assign('friendly_position', $gCms->variables['friendly_position']);
}
else if (get_site_preference('enablecustom404') == '' || get_site_preference('enablecustom404') == "0")
{
	//error_handler_404();
	//exit;
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
	if (isset($smarty->id) && $smarty->id != '' && isset($_REQUEST[$smarty->id.'showtemplate']) && $_REQUEST[$smarty->id.'showtemplate'] == 'false')
	{
		$html = $smarty->fetch('template:notemplate') . "\n";
	}
	else
	{
		if(null != $pageinfo)
		{
			$smarty->caching = false;
			$smarty->compile_check = true;
			($smarty->is_cached('template:'.$pageinfo->template_id)?$cached="":$cached="not ");
			$html = $smarty->fetch('template:'.$pageinfo->template_id) . "\n";
			if (isset($_REQUEST['tmpfile']))
			{
				$smarty->clear_compiled_tpl('template:'.$pageinfo->template_id);
			}
		}
	}
}

#if ((get_site_preference('enablecustom404') == '' || get_site_preference('enablecustom404') == "0") && (!$config['debug']))
#{
#	set_error_handler($old_error_handler);
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
	//Events::SendEvent('Core', 'ContentPostRenderNonCached', array(&$html));
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

Events::SendEvent('Core', 'ContentPostRender', array('content' => &$html));

header("Content-Type: " . $gCms->variables['content-type'] . "; charset=" . (isset($pageinfo->template_encoding) && $pageinfo->template_encoding != ''?$pageinfo->template_encoding:get_encoding()));

echo $html;

@ob_flush();

$endtime = microtime();

if ($config["debug"] == true)
{
	echo "<p>Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple using ".(isset($db->query_count)?$db->query_count:'')." SQL queries and ".(function_exists('memory_get_usage')?memory_get_usage():'n/a')." bytes of memory</p>";
}

echo "<!-- Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple using ".(isset($db->query_count)?$db->query_count:'')." SQL queries -->\n";
echo "<p>Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple using ".(isset($db->query_count)?$db->query_count:'')." SQL queries and ".(function_exists('memory_get_usage')?memory_get_usage():'n/a')." bytes of memory</p>";
echo "<!-- CMS Made Simple - Released under the GPL - http://cmsmadesimple.org -->\n";

if (get_site_preference('enablesitedownmessage') == "1" || $config['debug'] == true)
{
	$smarty->clear_compiled_tpl();
	#$smarty->clear_all_cache();
}

if ($config["debug"] == true)
{
	#$db->LogSQL(false); // turn off logging
	
	# output summary of SQL logging results
	#$perf = NewPerfMonitor($db);
	#echo $perf->SuspiciousSQL();
	#echo $perf->ExpensiveSQL();

	#echo $sql_queries;
	foreach ($gCms->errors as $error)
	{
		echo $error;
	}
}

//If we're in a preview, remove the file
if (isset($_GET["tmpfile"]) && $_GET["tmpfile"] != "")
{
	$fname = $_GET["tmpfile"];
	$fname = str_replace('.', '', $fname);
	$fname = str_replace("\\", '', $fname);
	$fname = str_replace('/', '', $fname);
	$fname = str_replace(DIRECTORY_SEPARATOR, '', $fname);
	$fname = basename($fname);

	if (is_writable($config["previews_path"]))
	{
		$fname = $config["previews_path"] . DIRECTORY_SEPARATOR . $fname;
	}
	else
	{
		$fname = TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . $fname;
	}
	unlink($fname);
}

# vim:ts=4 sw=4 noet
?>
