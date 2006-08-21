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

function smarty_cms_function_search($params, &$smarty)
{
	global $gCms;
	$cmsmodules = &$gCms->modules;

	if (isset($cmsmodules))
	{
		$modulename = 'search';
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

function smarty_cms_help_function_search() {
	?>
	<h3>What does this do?</h3>
	<p>This is actually just a wrapper tag for the <a href="listmodules.php?action=showmodulehelp&module=Search">Search module</a> to make the tag syntax easier. 
	Instead of having to use <code>{cms_module module='Search'}</code> you can now just use <code>{search}</code> to insert the module in a template.
	</p>
	<h3>How do I use it?</h3>
	<p>Just put <code>{search}</code> in a template where you want the search input box to appear. For help about the Search module, please refer to the <a href="listmodules.php?action=showmodulehelp&module=Search">Search module help</a>.

	<?php
}

function smarty_cms_about_function_search() {
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
