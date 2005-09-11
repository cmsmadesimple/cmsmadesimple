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

function smarty_cms_function_stylesheet($params, &$smarty)
{
	global $gCms;
	$config = &$gCms->config;
	$pageinfo = &$gCms->variables['pageinfo'];

	$stylesheet = '';

	foreach (get_stylesheet_media_types($pageinfo->template_id) as $media)
	{
		$stylesheet .= '<link rel="stylesheet" type="text/css" ';
		if ($media != '')
		{
			$stylesheet .= 'media="'.$media.'" ';
		}
		$stylesheet .= 'href="'.$config['root_url'].'/stylesheet.php?templateid='.$pageinfo->template_id;
		if ($media != '')
		{
			$stylesheet .= '&amp;mediatype='.urlencode($media);
		}
		$stylesheet .= '" />'."\n"; 
	}

	return $stylesheet;
}

function smarty_cms_help_function_stylesheet() {
	?>
	<h3>What does this do?</h3>
	<p>Prints the current date and time.  If no format is given, it will default to a format similar to 'Jan 01, 2004'.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{current_date format="%A %d-%b-%y %T %Z"}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em>format - Date/Time format using parameters from php's strftime function.  See <a href="http://php.net/strftime" target="_blank">here</a> for a parameter list and information.</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_stylesheet() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
