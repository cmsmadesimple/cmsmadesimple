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

function soap_error( $str )
{
  $namespaces = array(
		      'SOAP-ENV' => 'http://schemas.xmlsoap.org/soap/envelope/',
		      'xsd' => 'http://www.w3.org/2001/XMLSchema',
		      'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
		      'SOAP-ENC' => 'http://schemas.xmlsoap.org/soap/encoding/'
		      );
  $ns_string = '';
  foreach( $namespaces as $k => $v )
    {
      $ns_string .= "\n  xmlns:$k=\"$v\"";
    }
  $txt = 
        '<?xml version="1.0" encoding="ISO-8859-1"?>'.
        '<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"'.$ns_string.">\n".
		'<SOAP-ENV:Body>'. 
		'<SOAP-ENV:Fault>'.
                '<faultcode>Server</faultcode>'.
	        '<faultstring>'.$str.'</faultstring>'.
		'</SOAP-ENV:Fault>'.
		'</SOAP-ENV:Body>'.
	'</SOAP-ENV:Envelope>';
  echo $txt;
}

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
  header('Content-Type: text/xml');
  echo soap_error('site down for maintenance');
  exit;
}

require_once(dirname(__FILE__)."/include.php"); #Makes gCms object
$params = array_merge($_GET, $_POST);

if( !isset( $params['module'] ) )
  {
    header('Content-Type: text/xml');
    echo soap_error('missing module parameter');
    exit;
  }
if( !isset( $gCms->modules[$params['module']] ) )
  {
    header('Content-Type: text/xml');
    echo soap_error("module ".$params['module']." not found");  
    exit;
  }

// this code copied from function.cms_module.php
// then slightly hacked
$cmsmodules = &$gCms->modules;

$modresult = '';
if (isset($cmsmodules))
  {
    $modulename = $params['module'];
    
    foreach ($cmsmodules as $key=>$value)
      {
	if (!strcasecmp($modulename,$key))
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
		$params = array_merge($params, GetModuleParameters($id));
		$action = 'soap';
		if (isset($params['action']))
		  {
		    $action = $params['action'];
		  }
		
		$returnid = '';
		if (isset($gCms->variables['pageinfo']) && isset($gCms->variables['pageinfo']->content_id) )
		  {
		    $returnid = $gCms->variables['pageinfo']->content_id;
		  }
		$params['HTTP_RAW_POST_DATA'] = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
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

# vim:ts=4 sw=4 noet
?>
