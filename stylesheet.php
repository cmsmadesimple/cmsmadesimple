<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

$dirname = dirname(__FILE__);
require_once($dirname.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'cmsms.api.php');

$template_id = coalesce_key($_GET, 'templateid', '');
$mediatype = coalesce_key($_GET, 'mediatype', '');
$cssid = coalesce_key($_GET, 'cssid', '');
$name = coalesce_key($_GET, 'name', '');
$stripbackground = isset($_GET["stripbackground"]) ? true :false;

if ($template_id == '' && $name == '' && $cssid == '') return '';

$css='';

if ($name != '')
{
	$stylesheet = cms_orm('cms_stylesheet')->find_by_name($name);
	if ($stylesheet)
	{
		$css .= "/* Start of CMSMS style sheet '{$stylesheet->name}' */\n{$stylesheet->value}\n/* End of '{$stylesheet->name}' */\n";
	}
}
else
{
	if (isset($template_id) && is_numeric($template_id) && $template_id > -1)
	{
		$template = cms_orm('cms_template')->find_by_id($template_id);
		if ($template)
		{
			$stylesheets = $template->active_stylesheets;
			if ($stylesheets)
			{
				foreach ($stylesheets as $stylesheet)
				{
					$css .= "/* Start of CMSMS style sheet '{$stylesheet->name}' */\n{$stylesheet->value}\n/* End of '{$stylesheet->name}' */\n";
					CmsEvents::SendEvent('Core', 'ContentStylesheet', array('stylesheet' => &$stylesheet));
				}
			}
		}
	}
}

// send HTTP header
header("Content-Type: text/css; charset=UTF-8");

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
