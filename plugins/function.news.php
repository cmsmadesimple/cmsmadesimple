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

function smarty_cms_function_news($params, &$smarty)
{
	global $gCms;
	$cmsmodules = &$gCms->modules;

	if (isset($cmsmodules))
	{
		$modulename = 'news';
		$inline = false;

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
					&& $cmsmodules[$modulename]['object']->IsPluginModule())
				{
					@ob_start();
					$id = 'm' . ++$gCms->variables["modulenum"];
					$params = array_merge($params, GetModuleParameters($id));
					if ($inline == false || $action == '')
						$action = 'default';

					$returnid = '';
					if (isset($gCms->variables['pageinfo']) && isset($gCms->variables['pageinfo']->content_id))
					{
						$returnid = $gCms->variables['pageinfo']->content_id;
					}
					$result = $cmsmodules[$modulename]['object']->DoActionBase($action, $id, $params, $returnid);
					if ($result !== FALSE)
					{
						echo $result;
					}
					$modresult = @ob_get_contents();
					@ob_end_clean();
					return $modresult;
				}
				else
				{
					return "<!-- Not a tag module -->\n";
				}
			}
		}
	}
}

function smarty_cms_help_function_news() {
	?>
	<h3>What does this do?</h3>
	<p>This is actually just a wrapper tag for the <a href="listmodules.php?action=showmodulehelp&module=News">News module</a> to make the tag syntax easier. 
	Instead of having to use <code>{cms_module module='News'}</code> you can now just use <code>{news}</code> to insert the module on pages and templates.
	</p>
	<h3>How do I use it?</h3>
	<p>Just put <code>{news}</code> on a page or in a template. For help about the News module, what parameters it takes etc., please refer to the <a href="listmodules.php?action=showmodulehelp&module=News">News module help</a>.
	<?php
}

function smarty_cms_about_function_news() {
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
