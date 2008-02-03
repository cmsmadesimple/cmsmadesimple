<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

/**
 * Entry point for all non-admin pages
 **/
//Cut them off at the pass...
if (version_compare(phpversion(), "5.2", "<"))
{ 
    echo 'CMS Made Simple 2.0 requires php 5.2 and above to run.  Please upgrade your system before proceeding.';
    exit;
}

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
    if (FALSE == is_file($dirname.'/install/install.php'))
	{
        die ('There is no config.php file or install/install.php please correct one these errors!');
    }
	else
	{
		//die('Do a redirect - ' . $config_location);
        CmsResponse::redirect('install/index.php');
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

//Can we find a page somewhere in the request?
$page = CmsRequest::calculate_page_from_request();

//Are we using full page caching?  Now is a good time to check and 
//output any cached data.
if (CmsConfig::get('full_page_caching'))
{
	if (!isset($_REQUEST['mact']) && !isset($_REQUEST['id']) && $data = CmsCache::get_instance('page')->get($page, CmsMultiLanguage::get_client_language()))
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

//Startup the output buffering
@ob_start();

//All systems are go...  let's include all the good stuff
require_once($dirname.DIRECTORY_SEPARATOR.'include.php');

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
$pageinfo = CmsPageInfoOperations::load_page_info_by_content_alias($page);

//Testing the permission check
//$thepage = cms_orm()->cms_content_base->find_by_id($pageinfo->content_id);
//var_dump($thepage->check_permission());
//$thepage->test_me();

//No info?  Then it's a bum page.  If we had a custom 404, then it's info
//would've been returned earlier.  The only option left is to show the generic
//404 message and exit out.
if ($pageinfo == null)
{
	//CmsResponse::send_error_404();
}

//Render the pageinfo object
echo $pageinfo->render();

//Send any headers.  After the render?  Sure, because modules that
//have been processed could have changed what values should be in the
//headers.  Plus, it's output buffered, so the content isn't actually
//getting sent until the ob_flush below this.
echo $pageinfo->send_headers();

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

#if (CmsConfig::get('debug'))
#{
	echo "<p>Generated in ".$endtime." seconds by CMS Made Simple using ".CmsDatabase::query_count()." SQL queries and " . $memory . " bytes of memory</p>";
	echo CmsProfiler::get_instance()->report();
#}

if (get_site_preference('enablesitedownmessage') == "1" || $config['debug'] == true)
{
	cms_smarty()->clear_compiled_tpl();
}

//Clear out any previews that may be going on
CmsPreview::clear_preview();

# vim:ts=4 sw=4 noet
?>
