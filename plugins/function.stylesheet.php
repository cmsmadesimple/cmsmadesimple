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

function smarty_cms_function_stylesheet($params, &$smarty)
{
	global $gCms;
	$pageinfo =& $gCms->variables['pageinfo'];
	$root_url = CmsConfig::get('root_url');
	
	$result = '';
	
	if (isset($params['name']) && $params['name'] != '')
	{
		$result .= '<link rel="result" type="text/css" ';
		if (isset($params['media']) && $params['media'] != '')
		{
			$result .= 'media="' . $params['media'] . '" ';
		}
		$result .= 'href="'.$root_url.'/stylesheet.php?name='.$params['name'];
		$result .= "\" />\n"; 
	}
	else
	{
		$template = cms_orm('cms_template')->find_by_id($pageinfo->template_id);
		if ($template)
		{
			foreach ($template->get_stylesheet_media_types() as $media)
			{
				$result .= '<link rel="result" type="text/css" ';
				if ($media != '')
				{
					$result .= 'media="'.$media.'" ';
				}
				$result .= "href=\"{$root_url}/stylesheet.php?templateid={$pageinfo->template_id}";
				if ($media != '')
				{
					$result .= '&amp;mediatype='.urlencode($media);
				}
				$result .= "\" />\n"; 
			}
		}
	}
	
	if (array_key_exists('assign', $params))
	{
		$smarty->assign($params['assign'], $result);
	}
	else
	{
		return $result;
	}
}

function smarty_cms_help_function_stylesheet() {
	?>
	<h3>What does this do?</h3>
	<p>Gets stylesheet information from the system.  By default, it grabs all of the stylesheets attached to the current template.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page's head section like: <code>{stylesheet}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em>name - Instead of getting all stylesheets for the given page, it will only get one spefically named one, whether it's attached to the current template or not.</li>
		<li><em>(optional)</em>media - If name is defined, this allows you set a different media type for that stylesheet.</li>
		<li><em>(optional)</em>assign - Assign the output to a smarty variable named in assign instead of outputting it directly.</li>
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

# vim:ts=4 sw=4 noet
?>
