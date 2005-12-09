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
#$Id: soap.php 2258 2005-12-03 03:30:54Z wishy $

require_once(dirname(__FILE__).'/fileloc.php');

$log =& LoggerManager::getLogger('soap.php');
$log->info('Serving URL:' . $_SERVER['REQUEST_URI']);
$log->debug('Starting soap.php');

/**
 * Entry point for all non-admin pages
 *
 * @package CMS
 */	
$starttime = microtime();

//@ob_start();

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

require_once(dirname(__FILE__)."/include.php"); #Makes gCms object
$params = array_merge($_GET, $_POST);

if( !isset( $params['module'] ) )
  {
    echo "<html><head><title>Error</title></head><body><p>Missing module parameter</p></body></html>";
    exit;
  }
if( !isset( $gCms->modules[$params['module']] ) )
  {
    echo "<html><head><title>Error</title></head><body><p>module not found</p></body></html>";
    exit;
  }

if( isset( $params['debug'] ) )
  {
    echo "<html><body>";
  }

// soap stuff goes here

// this code copied from function.cms_module.php
// then slightly hacked
$cmsmodules = &$gCms->modules;
$modresult = '';
if (isset($cmsmodules))
  {
    $modulename = $params['module'];
    
    foreach ($cmsmodules as $key=>$value)
      {
	if (strtolower($modulename) == strtolower($key))
	  {
	    $modulename = $key;
	  }
      }
    
    if (isset($modulename))
      {
	if (isset($cmsmodules[$modulename]))
	  {
	    if (isset($cmsmodules[$modulename]['object'])
		&& $cmsmodules[$modulename]['installed'] == true
		&& $cmsmodules[$modulename]['active'] == true
	        && $cmsmodules[$modulename]['object']->IsSoapModule())
	      {
		//@ob_start();
		$id = 'm' . ++$gCms->variables["modulenum"];
		$params = array_merge($params, @ModuleOperations::GetModuleParameters($id));
		$action = 'soap';
		if (isset($params['action']))
		  {
		    $action = $params['action'];
		  }
		
		$returnid = '';
		if (isset($gCms->variables['pageinfo']))
		  {
		    $returnid = $gCms->variables['pageinfo']->content_id;
		  }
		$params['HTTP_RAW_POST_DATA'] = $HTTP_RAW_POST_DATA;
		$result = $cmsmodules[$modulename]['object']->DoActionBase($action, $id, $params, $returnid);
		if ($result !== FALSE)
		  {
		    echo $result;
		  }
		$modresult = @ob_get_contents();
		//@ob_end_clean();
	      }
	    else
	      {
		$log->debug('Leaving smarty_cms_function_cms_module');
		return "<!-- Not a tag module -->\n";
	      }
	  }
      }
  }

// here's the output
echo $modresult;

// end of soap stuff
//@ob_flush();

$endtime = microtime();

$log->debug('Leaving soap.php');

if ($config["debug"] == true)
{
	echo $sql_queries;
	foreach ($gCms->errors as $error)
	{
		echo $error;
	}
}


# vim:ts=4 sw=4 noet
?>
