<?php
#CMS - CMS Made Simple
#(c)2004-2013 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

if (!file_exists(CONFIG_FILE_LOCATION) || filesize(CONFIG_FILE_LOCATION) < 100)
{
	@touch(CONFIG_FILE_LOCATION); // attempt to create the config.php if it doesn't already exist
}

if (!file_exists(CONFIG_FILE_LOCATION) || filesize(CONFIG_FILE_LOCATION) < 100) 
{
	require_once($dirname.'/lib/misc.functions.php');
	if (FALSE == is_file($dirname.'/install/index.php')) 
	{
		die ('There is no config.php file or install/index.php please correct one these errors!');
	} 
	else 
	{
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
@ob_start();

// initial setup
$params = array_merge($_GET, $_POST);
$smarty = cmsms()->GetSmarty();
$smarty->params = $params;
$page = get_pageid_or_alias_from_url();
$contentops = cmsms()->GetContentOperations();
$contentobj = '';
$trycount = 0;

cms_content_cache::get_instance();
$tpl_cache = new CmsTemplateCache();

while( $trycount < 2 ) 
{
	$trycount++;
	try 
	{
		if( $page == '__CMS_PREVIEW_PAGE__' ) 
		{
			if( !isset($_SESSION['__cms_preview__']) ) 
			{
				throw new CmsException('preview selected, but temp data not found');
			}
      
			// todo: get the content type, and load it.
			$contentops->LoadContentType($_SESSION['__cms_preview_type__']);
			$contentobj = unserialize($_SESSION['__cms_preview__']);
			$contentobj->setId('__CMS_PREVIEW_PAGE__');
		}
		else
		{
			$contentobj = $contentops->LoadContentFromAlias($page,true);
		}
	
		if( !is_object($contentobj) ) 
		{
			throw new CmsError404Exception('Page '.$page.' not found');
		}

		// from here in, we're assured to have a content object
		if( !$contentobj->IsViewable() ) 
		{
			$url = $contentobj->GetURL();
			if( $url != '' && $url != '#' ) 
			{
				redirect($url);
			}
		}

		if( $contentobj->Secure() && (!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ) 
		{
			redirect($contentobj->GetURL()); // if this page is marked to be secure, make sure we redirect to the secure page
		}

		$allow_cache = (int)get_site_preference('allow_browser_cache',0);
		$expiry = (int)max(0,get_site_preference('browser_cache_expiry',60));
		$expiry *= $allow_cache;
		if( $_SERVER['REQUEST_METHOD'] == 'POST' || !$contentobj->Cachable() || $page == '__CMS_PREVIEW_PAGE__' || $expiry == 0 ) 
		{
			// Here we adjust headers for non cachable pages
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
		}
		else
		{
			// as far as we know, the output is cachable at this point... 
			// so we mark it so that the output can be cached
			header('Expires: '.gmdate("D, d M Y H:i:s",time() + $expiry * 60).' GMT');
			$the_date = time();
			if( $contentobj->Cachable() ) 
			{
				$the_date = $contentobj->GetModifiedDate();
			}
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s',$the_date) . ' GMT');
		}

		cmsms()->set_variable('content_obj',$contentobj);
		cmsms()->set_variable('content_id',$contentobj->Id());
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
		$smarty->assign('friendly_position', cmsms()->variables['friendly_position']);

		if( $contentobj->Secure() && (! isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ) 
		{
			redirect($contentobj->GetURL()); // if this page is marked to be secure, make sure we redirect to the secure page
		}

		CmsNlsOperations::set_language(); // <- NLS detection for frontend
		$smarty->assign('lang',CmsNlsOperations::get_current_language());
		$smarty->assign('encoding',CmsNlsOperations::get_encoding());

		$html = '';
		$showtemplate = true; 
	
		if ((isset($_REQUEST['showtemplate']) && $_REQUEST['showtemplate'] == 'false') || (isset($smarty->id) && $smarty->id != '' && isset($_REQUEST[$smarty->id.'showtemplate']) && $_REQUEST[$smarty->id.'showtemplate'] == 'false')) 
		{
			$showtemplate = false;
		}

		$smarty->set_global_cacheid('p'.$contentobj->Id());
		$uid = get_userid(FALSE);
		if( $contentobj->Cachable() && $showtemplate && !$uid && get_site_preference('use_smartycache',0) && $_SERVER['REQUEST_METHOD'] != 'POST' ) 
		{
			if( version_compare(phpversion(),'5.3') >= 0 ) 
			{
				$smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT); // this content is cachable... so enable smarty caching of this page data, for this user
			}
		}

		if( !$showtemplate ) 
		{
			$smarty->setCaching(false);
			// in smarty 3, we could use eval:{content} I think
			$html = $smarty->fetch('cms_template:notemplate')."\n";
		}
		else
		{
			debug_buffer('process template top');
			$top  = $smarty->fetch('tpl_top:'.$contentobj->TemplateId());
		
			debug_buffer('process template body');
			$body = $smarty->fetch('tpl_body:'.$contentobj->TemplateId());
		
			debug_buffer('process template head');
			$head = $smarty->fetch('tpl_head:'.$contentobj->TemplateId());
		
			$html = $top.$head.$body;		
			$trycount = 99; // no more iterations
		}
	}

	catch (SmartyCompilerException $e) // <- Catch Smarty compile errors 
	{
		$handlers = ob_list_handlers(); 
		for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }
		echo $smarty->errorConsole($e);	
		return;
	} 

	catch (SmartyException $e) // <- Catch rest of Smarty errors
	{
		$handlers = ob_list_handlers(); 
		for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }
		echo $smarty->errorConsole($e);
		return;
	}	
	
	catch (CmsError404Exception $e) // <- Catch CMSMS 404 error
	{
		// 404 error thrown... gotta do this process all over again
		$page = 'error404';
		$showtemplate = true;
		unset($_REQUEST['mact']);
		unset($_REQUEST['module']);
		unset($_REQUEST['action']);
		$handlers = ob_list_handlers(); 
		for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

		// specified page not found, load the 404 error page
		$contentobj = $contentops->LoadContentFromAlias('error404',true);
		if( is_object($contentobj) ) 
		{
			// we have a 404 error page
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
		}
		else 
		{
			// no 404 error page
			@ob_end_clean();
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
				<html><head>
				<title>404 Not Found</title>
				</head><body>
				<h1>Not Found</h1>
				<p>The requested URL was not found on this server.</p>
				</body></html>';
			exit();
		}
	}
} // end while trycount

Events::SendEvent('Core', 'ContentPostRender', array('content' => &$html));

$ct = cmsms()->get_variable('content-type');
if( !$ct ) $ct = 'text/html';
header("Content-Type: $ct; charset=" . get_encoding());
echo $html;

@ob_flush();

$endtime = microtime();
$db = cmsms()->GetDb();

if( $config['debug'] == TRUE || (isset($config['show_performance_info']) && ($showtemplate == true)) ) 
{
	$memory = (function_exists('memory_get_usage')?memory_get_usage():0);
	$memory = $memory - $orig_memory;
	$memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():0);
	if ( !is_sitedown() && $config["debug"] == true) 
	{
		echo "<p>Generated in ".microtime_diff($starttime,$endtime)." seconds by CMS Made Simple using ".(isset($db->query_count)?$db->query_count:'')." SQL queries and {$memory} bytes of memory (peak memory usage was {$memory_peak})</p>";
	}
	else if( isset($config['show_performance_info']) && ($showtemplate == true) )
	{
		$txt = microtime_diff($starttime,$endtime)." / ".(isset($db->query_count)?$db->query_count:'')." / {$memory} / {$memory_peak}";
		debug_display($txt);
		echo '<!-- '.$txt." -->\n";
		$txt = strftime('%x %X').'/'.$txt;
		debug_to_log($txt);
	}
}

if( is_sitedown() || $config['debug'] == true) 
{
	$smarty->clear_compiled_tpl();
}

if ( !is_sitedown() && $config["debug"] == true) 
{
	foreach (cmsms()->errors as $error) 
	{
		echo $error;
	}
}
?>