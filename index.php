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

$starttime = microtime();
clearstatcache();


if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']))
{
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}

if (!file_exists(CONFIG_FILE_LOCATION) || filesize(CONFIG_FILE_LOCATION) < 800)
  {
    // attempt to create the config.php if it doesn't already exist.
    @touch(CONFIG_FILE_LOCATION);
  }
if (!file_exists(CONFIG_FILE_LOCATION) || filesize(CONFIG_FILE_LOCATION) < 800)
{
    require_once($dirname.'/lib/misc.functions.php');
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

$smarty = cmsms()->GetSmarty();
$smarty->params = $params;

$page = get_pageid_or_alias_from_url();
$contentops = cmsms()->GetContentOperations();

$contentobj = '';
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

    $contentobj = $contentops->LoadContentFromSerializedData($_SESSION['cms_preview_data']);
    $contentobj->setId('__CMS_PREVIEW_PAGE__');
  }
else
  {
    $contentobj = $contentops->LoadContentFromAlias($page,true);
  }


if( !is_object($contentobj) )
  {
    // specified page not found, load the 404 error page.
    $contentobj = $contentops->LoadContentFromAlias('error404',true);
    if( !is_object($contentobj) )
      {
	// that wasn't found either... display a hardcoded message.
	ErrorHandler404();
	return;
      }
  }

// $page cannot be empty here
if (is_object($contentobj))
{
  if( !$contentobj->IsViewable() )
    {
      $url = $contentobj->GetURL();
      if( $url != '' && $url != '#' )
	{
	  redirect($url);
	}
    }

  if( $contentobj->Secure() && 
      (! isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') )
    {
      // if this page is marked to be secure, make sure we redirect to the secure page.
      redirect($contentobj->GetURL());
    }

  cmsms()->set_variable('content_obj',$contentobj);
  cmsms()->set_variable('content_id',$contentobj->Id());
  cmsms()->set_variable('page',$page);
  cmsms()->set_variable('page_id',$page);
  cmsms()->set_variable('page_name',$contentobj->Alias());
  cmsms()->set_variable('position',$contentobj->Hierarchy());
  cmsms()->set_variable('friendly_position',$contentops->CreateFriendlyHierarchyPosition($contentobj->Hierarchy()));

  $smarty->assign('content_obj',$contentobj);
  $smarty->assign('content_id', $contentobj->Id());
  $smarty->assign('page', $page);
  $smarty->assign('page_id', $page);
  $smarty->assign('page_name', $contentobj->Alias());
  $smarty->assign('page_alias', $contentobj->Alias());
  $smarty->assign('position', $contentobj->Hierarchy());
  $smarty->assign('friendly_position', $gCms->variables['friendly_position']);
}
else
// else if (get_site_preference('enablecustom404') == '' || get_site_preference('enablecustom404') == "0")
{
	ErrorHandler404();
	exit;
}

$html = '';
$cached = '';
$showtemplate = true;

if ((isset($_REQUEST['showtemplate']) && $_REQUEST['showtemplate'] == 'false') ||
    (isset($smarty->id) && $smarty->id != '' && isset($_REQUEST[$smarty->id.'showtemplate']) && $_REQUEST[$smarty->id.'showtemplate'] == 'false'))
{
  $showtemplate = false;
}


if (isset($_GET["print"]))
{
  $pageinfo = cmsms()->get_variable('pageinfo');
  ($smarty->is_cached('print:'.$page, '', $pageinfo->template_id)?$cached="":$cached="not ");
  $html = $smarty->fetch('print:'.$page, '', $pageinfo->template_id) . "\n";
}
else
{
	#If this is a case where a module doesn't want a template to be shown, just disable caching
        if( !$showtemplate )
	{
		$html = $smarty->fetch('template:notemplate') . "\n";
	}
	else
	{
	  //$smarty->caching = false;
	  //	$smarty->compile_check = true;
		($smarty->is_cached('template:'.$contentobj->TemplateId())?$cached="":$cached="not ");

		// we allow backward compatibility (for a while)
		// for people that have hacks for setting page title
		// or header variables by capturing a modules output
		// to a smarty variable, and then displaying it later.
		if( isset($config['process_whole_template']) && $config['process_whole_template'] === false )
		  {
		    $top  = $smarty->fetch('tpl_top:'.$contentobj->TemplateId());
		    $body = $smarty->fetch('tpl_body:'.$contentobj->TemplateId());
		    $head = $smarty->fetch('tpl_head:'.$contentobj->TemplateId());
		    $html = $top.$head.$body;
		  }
		else
		  {
		    $html = $smarty->fetch('template:'.$contentobj->TemplateId());
		  }
	}
}


Events::SendEvent('Core', 'ContentPostRender', array('content' => &$html));

header("Content-Type: " . cmsms()->get_variable('content-type') . "; charset=" . get_encoding());

echo $html;

@ob_flush();

$endtime = microtime();

$db =& cmsms()->GetDb();

$memory = (function_exists('memory_get_usage')?memory_get_usage():0);
$memory = $memory - $orig_memory;
$memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():0);
if ( !is_sitedown() && $config["debug"] == true)
{
  echo "<p>Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple using ".(isset($db->query_count)?$db->query_count:'')." SQL queries and {$memory} bytes of memory (peak memory usage was {$memory_peak})</p>";
}
else if( isset($config['show_performance_info']) && ($showtemplate == true) )
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


# vim:ts=4 sw=4 noet
?>
