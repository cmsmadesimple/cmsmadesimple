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

$name = '';
if (isset($_GET['name'])) $name = $_GET['name'];

$stripbackground = false;
if (isset($_GET["stripbackground"])) $stripbackground = true;

if ($templateid == '' && $name == '') return '';

require_once('config.php');

$css='';

if (isset($config['old_stylesheet']) && $config['old_stylesheet'] == false)
{

	$encoding = '';

	if ($config['default_encoding'] =='')
		$encoding = $config['admin_encoding'];
	else
		$encoding = $config['default_encoding'];

	if ($encoding=='')
		$encoding = 'UTF-8';


	if ($config['dbms'] == 'mysqli' || $config['dbms'] == 'mysql')
	{
		$db = mysql_connect($config['db_hostname'], $config['db_username'], $config['db_password']);
		mysql_select_db($config['db_name']);
		if ($name != '')
			$sql="SELECT css_text, css_name FROM ".$config['db_prefix']."css WHERE css_name = '" . mysql_real_escape_string($name, $db) . "'";
		else
			$sql="SELECT c.css_text, c.css_id, c.css_name FROM ".$config['db_prefix']."css c,".$config['db_prefix']."css_assoc ac WHERE ac.assoc_type='template' AND ac.assoc_to_id = $templateid AND ac.assoc_css_id = c.css_id AND c.media_type = '" . mysql_real_escape_string($mediatype, $db) . "' ORDER BY ac.create_date";
		$result=mysql_query($sql);
		while ($result && $row = mysql_fetch_assoc($result))
		{
			$css .= "/* Start of CMSMS style sheet '{$row['css_name']}' */\n{$row['css_text']}\n/* End of '{$row['css_name']}' */\n";
		}
	}
	else
	{
		$db=pg_connect("host=".$config['db_hostname']." dbname=".$config['db_name']." user=".$config['db_username']." password=".$config['db_password']);
		if ($name != '')
			$sql="SELECT css_text, css_name FROM ".$config['db_prefix']."css WHERE css_name = '" . pg_escape_string($name) . "'";
		else
			$sql="SELECT c.css_text, c.css_id, c.css_name FROM ".$config['db_prefix']."css c,".$config['db_prefix']."css_assoc ac WHERE ac.assoc_type='template' AND ac.assoc_to_id = $templateid AND ac.assoc_css_id = c.css_id AND c.media_type = '" . pg_escape_string($mediatype) . "' ORDER BY ac.create_date";
		$result=pg_query($db, $sql);
		while ($result && $row = pg_fetch_array($result, null, PGSQL_ASSOC))
		{
			$css .= "/* Start of CMSMS style sheet '{$row['css_name']}' */\n{$row['css_text']}\n/* End of '{$row['css_name']}' */\n";
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
				
				Events::SendEvent('Core', 'ContentStylesheet', array('stylesheet' => &$stylesheet));
			#}
		}
	}

	header("Content-Type: text/css; charset=" . (isset($result['encoding'])?$result['encoding']:'UTF-8'));


}

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
