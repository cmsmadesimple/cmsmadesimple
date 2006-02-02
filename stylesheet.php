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

require_once(dirname(__FILE__)."/include.php");

if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))
{
	@ini_set( 'zlib.output_compression','Off' );
}

$templateid = "";
$mediatype = '';
$name = '';
$css = '';
$nostylesheet = false;
$stripbackground = false;
if (isset($_GET["templateid"])) $templateid = $_GET["templateid"];
if (isset($_GET["mediatype"])) $mediatype = $_GET["mediatype"];
if (isset($_GET['name'])) $name = $_GET['name'];
if (isset($_GET["stripbackground"])) $stripbackground = true;

if ($name != '')
{
	//TODO: Make stylesheet handling OOP
	global $gCms;
	$db =& $gCms->GetDb();
	$cssquery = "SELECT css_text FROM ".cms_db_prefix()."css WHERE css_name = ?";
	$cssresult = &$db->Execute($cssquery, $name);

	while (!$cssresult->EOF)
	{
		$css .= "\n".$cssresult->fields['css_text']."\n";
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
		#}
	}
}

#header("Content-Language: " . $current_language);
header("Content-Type: text/css; charset=" . (isset($result['encoding'])?$result['encoding']:'UTF-8'));

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
		#header("Cache-Control: maxage=".(60*60*24));
	}
}

#sending content length allows HTTP/1.0 persistent connections
header("Content-Length: ".strlen($css));

echo $css;

if (isset($gCms->db))
{
    $db =& $gCms->GetDb();
	if ($db->IsConnected())
		$db->Close();
}

# vim:ts=4 sw=4 noet
?>
