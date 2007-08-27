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

if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))
{
	@ini_set( 'zlib.output_compression','Off' );
}

$templateid = '';
if (isset($_GET["templateid"])) $templateid = $_GET["templateid"];

$mediatype = '';
if (isset($_GET["mediatype"])) $mediatype = $_GET["mediatype"];

$cssid = '';
if (isset($_GET['cssid'])) $cssid = $_GET['cssid'];

$name = '';
if (isset($_GET['name'])) $name = $_GET['name'];

$stripbackground = false;
if (isset($_GET["stripbackground"])) $stripbackground = true;

if ($templateid == '' && $name == '' && $cssid == '') return '';

require_once('config.php');

$css='';

if (isset($config['old_stylesheet']) && $config['old_stylesheet'] == false)
{
	// connect to the database
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'misc.functions.php');
	require_once(cms_join_path(dirname(__FILE__),'lib','adodb.functions.php'));
	load_adodb();
	$db =& adodb_connect();
	
	// select the stylesheet(s)
	if ($name != '')
		$sql="SELECT css_text, css_name FROM ".$config['db_prefix']."css WHERE css_name = " . $db->qstr($name);
	else if( $cssid != '' )
	  $sql="SELECT css_text, css_name FROM ".$config['db_prefix']."css WHERE css_id = ".$db->qstr($cssid);
	else
		$sql="SELECT c.css_text, c.css_id, c.css_name FROM ".$config['db_prefix']."css c,".$config['db_prefix']."css_assoc ac WHERE ac.assoc_type='template' AND ac.assoc_to_id = ".$db->qstr($templateid)." AND ac.assoc_css_id = c.css_id AND c.media_type = " . $db->qstr($mediatype) . " ORDER BY ac.create_date";
	$result = $db->Execute($sql);
	
	// add a comment at the start
	while ($result && $row = $result->FetchRow())
	{
		$db = mysql_connect($config['db_hostname'], $config['db_username'], $config['db_password']);
		mysql_select_db($config['db_name']);
		if ($name != '')
			$sql="SELECT css_text, css_name FROM ".$config['db_prefix']."css WHERE css_name = '" . mysql_real_escape_string($name, $db) . "'";
		else
			$sql="SELECT c.css_text, c.id, c.css_name FROM ".$config['db_prefix']."css c,".$config['db_prefix']."css_assoc ac WHERE ac.assoc_type='template' AND ac.assoc_to_id = $templateid AND ac.assoc_css_id = c.id AND c.media_type = '" . mysql_real_escape_string($mediatype, $db) . "' ORDER BY ac.create_date";
		$result=mysql_query($sql);
		while ($result && $row = mysql_fetch_assoc($result))
		{
			$css .= "/* Start of CMSMS style sheet '{$row['css_name']}' */\n{$row['css_text']}\n/* End of '{$row['css_name']}' */\n\n";
		}
	}
	else if($config['dbms'] == 'sqlite') {
		$db = sqlite_open($config['db_hostname']);
		sqlite_exec($db,'PRAGMA short_column_names = 1;');
		if ($name != '')
			$sql="SELECT css_text, css_name FROM ".$config['db_prefix']."css WHERE css_name = '" . sqlite_escape_string($name) . "'";
		else
			$sql="SELECT c.css_text, c.id, c.css_name FROM ".$config['db_prefix']."css c,".$config['db_prefix']."css_assoc ac WHERE ac.assoc_type='template' AND ac.assoc_to_id = $templateid AND ac.assoc_css_id = c.id AND c.media_type = '" . sqlite_escape_string($mediatype) . "' ORDER BY ac.create_date";
		$result=sqlite_array_query($db,$sql,SQLITE_ASSOC);
		foreach($result as $row)
		{
			$css .= "/* Start of CMSMS style sheet '{$row['css_name']}' */\n{$row['css_text']}\n/* End of '{$row['css_name']}' */\n\n";
		}
	}
	else
	{
		$db=pg_connect((isset($config['db_hostname']) && $config['db_hostname'] ? "host=".$config['db_hostname'] : '')." dbname=".$config['db_name']." user=".$config['db_username']." password=".$config['db_password']);
		if ($name != '')
			$sql="SELECT css_text, css_name FROM ".$config['db_prefix']."css WHERE css_name = '" . pg_escape_string($name) . "'";
		else
			$sql="SELECT c.css_text, c.id, c.css_name FROM ".$config['db_prefix']."css c,".$config['db_prefix']."css_assoc ac WHERE ac.assoc_type='template' AND ac.assoc_to_id = $templateid AND ac.assoc_css_id = c.id AND c.media_type = '" . pg_escape_string($mediatype) . "' ORDER BY ac.create_date";
		$result=pg_query($db, $sql);
		while ($result && $row = pg_fetch_array($result, null, PGSQL_ASSOC))
		{
			$css .= "/* Start of CMSMS style sheet '{$row['css_name']}' */\n{$row['css_text']}\n/* End of '{$row['css_name']}' */\n\n";
		}
	}

	header("Content-Type: text/css; charset=" .$encoding);
}
else
{
	require_once(dirname(__FILE__)."/include.php");
	
	if ($name != '')
	{
		//TODO: Make stylesheet handling OOP
		global $gCms;
		$db =& $gCms->GetDb();
		$cssquery = "SELECT css_text, css_name FROM ".cms_db_prefix()."css WHERE css_name = ?";
		$cssresult = &$db->Execute($cssquery, array($name));

		while ($cssresult && !$cssresult->EOF)
		{
			$css .= "/* Start of CMSMS style sheet '{$cssresult->fields['css_name']}' */\n{$cssresult->fields['css_text']}\n/* End of '{$cssresult->fields['css_name']}' */\n";
			$cssresult->MoveNext();
		}
	}
	else
	{
		$result = get_stylesheet($templateid, $mediatype);
		$css = $result['stylesheet']; 
		if (!isset($result['nostylesheet']))
		{
			#$nostylesheet = true;
			#Perform the content stylesheet callback
			#if ($nostylesheet == false)
			#{
				reset($gCms->modules);
				while (list($key) = each($gCms->modules))
				{
					$value =& $gCms->modules[$key];
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentStylesheet($css);
					}
				}
				
				CmsEvents::SendEvent('Core', 'ContentStylesheet', array('stylesheet' => &$stylesheet));
			#}
		}
	}
	// set encoding
	$encoding = isset($result['encoding']) ? $result['encoding'] : 'UTF-8';
}
	

// send HTTP header
header("Content-Type: text/css; charset=$encoding");

#sending content length allows HTTP/1.0 persistent connections
#(and also breaks if gzip is on)
#header("Content-Length: ".strlen($css));

if ($stripbackground)
{
	#$css = preg_replace('/(\w*?background-color.*?\:\w*?).*?(;.*?)/', '', $css);
	$css = preg_replace('/(\w*?background-color.*?\:\w*?).*?(;.*?)/', '\\1transparent\\2', $css);
	$css = preg_replace('/(\w*?background-image.*?\:\w*?).*?(;.*?)/', '', $css);
}

#Do cache-control stuff but only if we are running Apache
if(function_exists('getallheaders'))
{
	$headers = getallheaders();
	$hash = md5($css);

	#if browser sent etag and it is the same then reply with 304
	if (isset($headers['If-None-Match']) && $headers['If-None-Match'] == '"'.$hash.'"')
	{
		header('HTTP/1.1 304 Not Modified');
		exit;
	}
	else {
		header('ETag: "'.$hash.'"');
	}
}

echo $css;

# vim:ts=4 sw=4 noet
?>
