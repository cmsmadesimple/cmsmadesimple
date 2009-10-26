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

$orig_memory = (function_exists('memory_get_usage')?memory_get_usage():0);
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
clearstatcache();

if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']))
{
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}

if (!file_exists(CONFIG_FILE_LOCATION) || filesize(CONFIG_FILE_LOCATION) < 800)
{
    require_once($dirname.'/lib/cmsms.api.php');
    if (FALSE == is_file($dirname.'/install/index.php')) {
        die ('There is no config.php file or install/index.php please correct one these errors!');
    } else {
        redirect('install/');
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

require_once($dirname.'/include.php'); 
// optionally enable output compression (as long as debug mode isn't on)
if( isset($config['output_compression']) && ($config['output_compression']) && $config['debug'] != true )
  {
    @ob_start('ob_gzhandler');
  }
else
  {
    @ob_start();
  }


$params = array_merge($_GET, $_POST);
{
  // hack to work around IE problem of not submitting back the original
  // value for an input field of type image.
  $add = array();
  foreach( $params as $key => $value )
    {
      if( preg_match('/_x$/',$key) )
	{
	  $str = substr($key,0,strlen($key)-2);
	  if( isset($params[$str.'_y']) && !isset($params[$str]) )
	    {
	      $add[$key] = $value;
	    }
	}
    }
  if( count($add) )
    {
      $params = array_merge($add,$params);
    }
}
$smarty = &$gCms->smarty;
$smarty->params = $params;

$page = '';
if (isset($params['mact']))
  {
    $ary = explode(',', cms_htmlentities($params['mact']), 4);
    $smarty->id = (isset($ary[1])?$ary[1]:'');
  }
else
  {
    $smarty->id = (isset($params['id'])?intval($params['id']):'');
  }

if (isset($smarty->id) && isset($params[$smarty->id . 'returnid']))
  {
    $page = $params[$smarty->id . 'returnid'];
  }
else if (isset($config["query_var"]) && $config["query_var"] != '' && isset($_GET[$config["query_var"]]))
  {
    $page = $_GET[$config["query_var"]];    
  }
else
   {
     $page = cms_calculate_url();
   }

// strip off GET params.
if( ($tmp = strpos($page,'?')) !== FALSE )
  {
    $page = substr($page,0,$tmp);
  }

// strip off page extension
if ($config['page_extension'] != '' && endswith($page, $config['page_extension']))
  {   
    $page = substr($page, 0, strlen($page) - strlen($config['page_extension']));
  }


//See if our page matches any predefined routes
$page = rtrim($page, '/');
$matched = false;
if (strpos($page, '/') !== FALSE)
{

	$routes =& $gCms->variables['routes'];
	
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
					if (array_key_exists($key, $matches))
					{ 
						$matches[$key] = $val;
					}
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
			break;
		}
	}
}

// strip from the last / forward
if( ($pos = strrpos($page,'/')) !== FALSE && $matched == false )
  {
    $page = substr($page, $pos + 1);
  }


if ($page == '')
  {
    // assume default content
    global $gCms;
    $contentops = $gCms->GetContentOperations();
    $page = $contentops->get_default_page_id();
  }
else
  {
    $page = preg_replace('/\</','',$page);
  }

$pageinfo = '';
if( $page == '__CMS_PREVIEW_PAGE__' && isset($_SESSION['cms_preview']) ) // temporary
  {
    $tpl_name = trim($_SESSION['cms_preview']);
    $fname = '';
    if (is_writable($config["previews_path"]))
      {
	$fname = cms_join_path($config["previews_path"] , $tpl_name);
      }
    else
      {
	$fname = cms_join_path(TMP_CACHE_LOCATION , $tpl_name);
      }
    $fname = $tpl_name;
    if( !file_exists($fname) )
      {
	die('error preview temp file not found: '.$fname);
	return false;
      }

    // build pageinfo
    $fh = fopen($fname,'r');
    $_SESSION['cms_preview_data'] = unserialize(fread($fh,filesize($fname)));
    fclose($fh);
    unset($_SESSION['cms_preview']);

    $pageinfo = PageInfoOperations::LoadPageInfoFromSerializedData($_SESSION['cms_preview_data']);
    $pageinfo->content_id = '__CMS_PREVIEW_PAGE__';
  }

if( !is_object($pageinfo) )
  {
    $pageinfo = PageInfoOperations::LoadPageInfoByContentAlias($page);
  }

// $page cannot be empty here
if (isset($pageinfo) && $pageinfo !== FALSE)
{
	$gCms->variables['pageinfo'] =& $pageinfo;

	if( isset($pageinfo->template_encoding) && 
	    $pageinfo->template_encoding != '' )
	{
	  set_encoding($pageinfo->template_encoding);
	}

	if($pageinfo->content_id > 0)
	{
		$manager = $gCms->GetHierarchyManager();
		$node = $manager->sureGetNodeById($pageinfo->content_id);
		if(is_object($node))
		{
		  $contentobj = $node->GetContent(true,true,false);
		  if( is_object($contentobj) )
		    {
		      $smarty->assign('content_obj',$contentobj);
		    }
		}
	}

	$gCms->variables['content_id'] = $pageinfo->content_id;
	$gCms->variables['page'] = $page;
	$gCms->variables['page_id'] = $page;

	$gCms->variables['page_name'] = $pageinfo->content_alias;
	$gCms->variables['position'] = $pageinfo->content_hierarchy;
	global $gCms;
	$contentops = $gCms->GetContentOperations();
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
  if ((isset($_REQUEST['showtemplate']) && $_REQUEST['showtemplate'] == 'false') || 
      (isset($smarty->id) && $smarty->id != '' && isset($_REQUEST[$smarty->id.'showtemplate']) && $_REQUEST[$smarty->id.'showtemplate'] == 'false'))
	{
		$html = $smarty->fetch('template:notemplate') . "\n";
	}
	else
	{
		$smarty->caching = false;
		$smarty->compile_check = true;
		($smarty->is_cached('template:'.$pageinfo->template_id)?$cached="":$cached="not ");

		// we allow backward compatibility (for a while)
		// for people that have hacks for setting page title
		// or header variables by capturing a modules output
		// to a smarty variable, and then displaying it later.
		if( isset($config['process_whole_template']) && $config['process_whole_template'] === false )
		  {
		    $top  = $smarty->fetch('tpl_top:'.$pageinfo->template_id);
		    $body = $smarty->fetch('tpl_body:'.$pageinfo->template_id);
		    $head = $smarty->fetch('tpl_head:'.$pageinfo->template_id);
		    $html = $top.$head.$body;
		  }
		else
		  {
		    $html = $smarty->fetch('template:'.$pageinfo->template_id);
		  }
	}
}

#if ((get_site_preference('enablecustom404') == '' || get_site_preference('enablecustom404') == "0") && (!$config['debug']))
#{
#	set_error_handler($old_error_handler);
#}

if (!$cached)
{
	//Events::SendEvent('Core', 'ContentPostRenderNonCached', array(&$html));
}

Events::SendEvent('Core', 'ContentPostRender', array('content' => &$html));

header("Content-Type: " . $gCms->variables['content-type'] . "; charset=" . (isset($pageinfo->template_encoding) && $pageinfo->template_encoding != ''?$pageinfo->template_encoding:get_encoding()));

echo $html;

@ob_flush();

$endtime = microtime();

$db = $gCms->GetDb();

$memory = (function_exists('memory_get_usage')?memory_get_usage():0);
$memory = $memory - $orig_memory;
$memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():0);
if( !isset($config['hide_performance_info']) )
{
echo "<!-- ".microtime_diff($starttime,$endtime)." / ".(isset($db->query_count)?$db->query_count:'')." / {$memory} / {$memory_peak} -->\n";

}

if( is_sitedown() || $config['debug'] == true)
{
	$smarty->clear_compiled_tpl();
	#$smarty->clear_all_cache();
}

if ( !is_sitedown() && $config["debug"] == true)
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

if( $page == '__CMS_PREVIEW_PAGE__' && isset($_SESSION['cms_preview']) ) // temporary
  {
    unset($_SESSION['cms_preview']);
  }

//CmsContentOperations::ResetNestedSet();
# vim:ts=4 sw=4 noet
?>
