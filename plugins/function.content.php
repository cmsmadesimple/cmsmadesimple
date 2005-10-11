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

function smarty_cms_function_content($params, &$smarty)
{
	global $gCms;
	$pageinfo = $gCms->variables['pageinfo'];
	if (isset($pageinfo) && $pageinfo !== FALSE)
	{
		if ((isset($_GET['module']) || isset($_POST['module']) || isset($_GET['mact'])) &&
			( ( isset( $_GET['id'] ) && $_GET['id'] == 'cntnt01') || (isset($_POST['id']) && $_POST['id'] == 'cntnt01') ) )
		{
			$module = '';
			if (isset($_GET['module'])) $module = $_GET['module'];
			if (isset($_POST['module'])) $module = $_POST['module'];
			if (isset($_GET['mact'])) $module = $_GET['mact'];
			if (!isset($params['block']))
			{
				$smarty->id = 'cntnt01';
				return $smarty->fetch('module:' . $module);
			}
		}
		else
		{
			return $smarty->fetch('content:' . (isset($params['block'])?$params['block']:'content_en'), '', $pageinfo->content_id);
		}
	}
	return '';
}

function smarty_cms_help_function_content()
{
	?>
	<h3>What does this do?</h3>
	<p>This is where the content for your page will be displayed.  It's inserted into the template and changed based on the current page being displayed.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template like: <code>{content}</code>.</p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em>block - Allows you to have more than one content block per page.  When multiple content tags are put on a template, that number of edit boxes will be displayed when the page is edited.</li>
	</ul>
	<?php
}

function smarty_cms_about_function_content()
{
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
