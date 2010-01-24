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

if (version_compare(phpversion(), "5.2", "<"))
{ 
	echo 'CMS Made Simple 2.0 requires php 5.2 and above to run.  Please upgrade your system before proceeding.';
	exit;
}

//xdebug_start_trace('/tmp/mytrace');

//Where are we?
$dirname = dirname(__FILE__);

//Yes, we do this again in include.php.  That's why we have the
//require_once.  We need to make sure that we have a valid
//config file location and tmp location before doing the
//whole include.php include.  If not, then either show an
//error or redirect to the installer.
require_once($dirname.DIRECTORY_SEPARATOR.'fileloc.php');

//CmsProfiler isn't able to autoload yet, so we
//calculate the start_time the hard way
list( $usec, $sec ) = explode( ' ', microtime() );
$start_time = ((float)$usec + (float)$sec);

//Load necessary global functions.  This allows us to load a few
//things before hand... like the configuration
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'cmsms.api.php');

//If we have a missing or empty config file, then we should think
//about redirecting to the installer.  Also, check to see if the SITEDOWN
//file is there.  That means we're probably in mid-upgrade.
$config_location = CmsConfig::get_config_filename();
if (!file_exists($config_location) || filesize($config_location) < 800)
{
	if (FALSE == is_file($dirname.'/install/index.php'))
	{
		die ('There is no config.php file or install/index.php please correct one these errors!');
	}
	else
	{
		//die('Do a redirect - ' . $config_location);
		redirect('install/index.php');
	}
}
else if (file_exists(TMP_CACHE_LOCATION.'/SITEDOWN'))
{
	echo "<html><head><title>Maintenance</title></head><body><p>Site down for maintenance.</p></body></html>";
	exit;
}

//Ok, one more check.  Make sure we can write to the following locations.  If not, then 
//we won't even be able to push stuff through smarty.  Error out now while we have the 
//chance.
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

//Start up a profiler for getting render times for this page.  Use
//the start time we generated way up at the top.
$profiler = CmsProfiler::get_instance('', $start_time);

if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']))
{
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}

//Start up a profiler for getting render times for this page.  Use
//the start time we generated way up at the top.
$profiler = CmsProfiler::get_instance('', $start_time);

//Can we find a page somewhere in the request?
$page = CmsRequest::calculate_page_from_request();

//Are we using full page caching?  Now is a good time to check and 
//output any cached data.
if (CmsConfig::get('full_page_caching'))
{
	if (!isset($_REQUEST['mact']) && !isset($_REQUEST['id']) && $data = CmsCache::get_instance('page')->get($page))
	{
		echo $data;
		$endtime = $profiler->get_time();
		$memory = $profiler->get_memory();
		echo "<!-- Generated in ".$endtime." seconds by CMS Made Simple (cached) -->\n";
		echo "<!-- CMS Made Simple - Released under the GPL - http://cmsmadesimple.org -->\n";
		//if (CmsConfig::get('debug'))
		//{
			echo "<p>Generated in ".$endtime." seconds by CMS Made Simple (cached) using " . $memory . " bytes of memory</p>";
			echo CmsProfiler::get_instance()->report();
		//}
		exit;
	}
}

// optionally enable output compression (as long as debug mode isn't on)
if( isset($config['output_compression']) && ($config['output_compression']) && $config['debug'] != true )
{
	@ob_start('ob_gzhandler');
}
else
{
	@ob_start();
}

//All systems are go...  let's include all the good stuff
require_once(cms_join_path($dirname, DS, 'include.php'));

//Make sure the id is set inside smarty if needed for modules
cms_smarty()->set_id_from_request();

//See if our page matches any predefined routes.  If so,
//the updated $page will be returned. (No point in matching
//if we have no url to match).
if ($page != '')
	$page = CmsRoute::match_route($page);

//Last ditch effort.  If we still have no page, then
//grab the default.
if ($page == '')
	$page = CmsContentOperations::get_default_page_id();

//Ok, we should have SOMETHING at this point.  Grab it's info
//from the database.
$pageinfo = PageInfoOperations::load_page_info_by_content_alias($page);

//No info?  Then it's a bum page.  If we had a custom 404, then it's info
//would've been returned earlier.  The only option left is to show the generic
//404 message and exit out.
if ($pageinfo == null)
{
	//CmsResponse::send_error_404();
	echo "404 error\n";
	exit;
}

//Render the pageinfo object
echo $pageinfo->render();

//Send any headers.  After the render?  Sure, because modules that
//have been processed could have changed what values should be in the
//headers.  Plus, it's output buffered, so the content isn't actually
//getting sent until the ob_flush below this.
echo $pageinfo->send_headers();

/*
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

	cmsms()->GetPageInfoOperations(); //Hack: Just to load it for now
	$pageinfo = PageInfoOperations::LoadPageInfoFromSerializedData($_SESSION['cms_preview_data']);
	$pageinfo->content_id = '__CMS_PREVIEW_PAGE__';
}

if( !is_object($pageinfo) )
{
	cmsms()->GetPageInfoOperations(); //Hack: Just to load it for now
	$pageinfo = PageInfoOperations::LoadPageInfoByContentAlias($page);
}
*/

// $page cannot be empty here
/*
if (isset($pageinfo) && $pageinfo !== FALSE)
{
	$gCms->variables['pageinfo'] = $pageinfo;

	if( isset($pageinfo->template_encoding) && $pageinfo->template_encoding != '' )
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
*/

/*
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
*/

#if ((get_site_preference('enablecustom404') == '' || get_site_preference('enablecustom404') == "0") && (!$config['debug']))
#{
#	set_error_handler($old_error_handler);
#}

/*
if (!$cached)
{
	//Events::SendEvent('Core', 'ContentPostRenderNonCached', array(&$html));
}

Events::SendEvent('Core', 'ContentPostRender', array('content' => &$html));

header("Content-Type: " . $gCms->variables['content-type'] . "; charset=" . (isset($pageinfo->template_encoding) && $pageinfo->template_encoding != ''?$pageinfo->template_encoding:get_encoding()));

echo $html;

*/

//Calculate our profiler data
$endtime = $profiler->get_time();
$memory = $profiler->get_memory();

//Flush the buffer out to the browser
//If caching is on, save the data to the cache
//as well.
//If not, then just flush it out and save the
//memory of putting it into a variable.
if (CmsConfig::get('full_page_caching'))
{
	$data = @ob_get_flush();
	CmsCache::get_instance('page')->save($data);
}
else
{
	@ob_flush();
}

echo "<!-- Generated in ".$endtime." seconds by CMS Made Simple using ".CmsDatabase::query_count()." SQL queries -->\n";
echo "<!-- CMS Made Simple - Released under the GPL - http://cmsmadesimple.org -->\n";

//var_dump(CmsLogin::get_current_user());

if (CmsConfig::get('debug') || !CmsConfig::get('debug'))
{
	echo CmsProfiler::get_instance()->report();
}

if (CmsApplication::get_preference('enablesitedownmessage') == "1" || CmsConfig::get('debug') == true)
{
	cms_smarty()->clear_compiled_tpl();
}

//Clear out any previews that may be going on
//CmsPreview::clear_preview();

if( $page == '__CMS_PREVIEW_PAGE__' && isset($_SESSION['cms_preview']) ) // temporary
{
	unset($_SESSION['cms_preview']);
}

//xdebug_stop_trace();

//CmsContentOperations::ResetNestedSet();
# vim:ts=4 sw=4 noet
?>
