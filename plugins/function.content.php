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
	$pageinfo =& $gCms->variables['pageinfo'];
	if (isset($pageinfo) && $pageinfo !== FALSE)
	{
		$id = '';
		$modulename = '';
		$action = '';
		if (isset($_REQUEST['module'])) $modulename = $_REQUEST['module'];
		if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
		if (isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		elseif (isset($_REQUEST['mact']))
		{
			$ary = explode(',', $_REQUEST['mact'], 3);
			$modulename = (isset($ary[0])?$ary[0]:'');
			$id = (isset($ary[1])?$ary[1]:'');
			$action = (isset($ary[2])?$ary[2]:'');
		}

		if ($id != '' && $id == 'cntnt01')
		{
			if (!isset($params['block']))
			{
				$cmsmodules = &$gCms->modules;
			
				if (isset($cmsmodules))
				{
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
								$params = array_merge($params, @ModuleOperations::GetModuleParameters($id));

								$returnid = '';
								if (isset($params['returnid']))
								{
									$returnid = $params['returnid'];
								}
								else if (isset($pageinfo))
								{
									$returnid = $pageinfo->content_id;
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
		}
		else
		{
			$result = '';
			$oldvalue = $smarty->caching;
			$smarty->caching = $pageinfo->cachable;
			$result = $smarty->fetch(str_replace(' ', '_', 'content:' . (isset($params['block'])?$params['block']:'content_en')), '', $pageinfo->content_id);
			$smarty->caching = $oldvalue;
			return $result;
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
		<li><em>(optional)</em>wysiwyg (true/false) - If set to false, then a wysiwyg will never be used while editing this block.  If true, then it acts as normal.  Only works when block parameter is used.</li>
		<li><em>(optional)</em>oneline (true/false) - If set to true, then only one edit line will be shown while editing this block.  If false, then it acts as normal.  Only works when block parameter is used.</li>
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

# vim:ts=4 sw=4 noet
?>
