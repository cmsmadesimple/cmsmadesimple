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
	$request = cms_request();
	
	$page = $smarty->get_template_vars('current_page');
	
	if ($page != null)
	{
		$block_name = 'default';
		if (isset($params['name']))
		{
			$block_name = $params['name'];
		}
		else if (isset($params['block']))
		{
			$block_name = $params['block'];
		}
		
		$block_name = strtolower(str_replace(' ', '_', $block_name));
		
		$id = '';
		$module_name = '';
		$action = '';
		$inline = false;

		if ($request->has('request:module'))
			$modulename = $request->get('request:module');

		if ($request->has('request:id'))
		{
			$id = $request->get('request:id');
		}
		else if ($request->has('request:mact'))
		{
			$ary = explode(',', cms_htmlentities($request->get('request:mact')), 4);
			$module_name = (isset($ary[0]) ? $ary[0] : '');
			$id = (isset($ary[1]) ? $ary[1] : '');
			$action = (isset($ary[2]) ? $ary[2] : '');
			$inline = (isset($ary[3]) && $ary[3] == 1 ? true : false);
		}

		if ($request->has('request:'.$id.'action'))
			$action = $request->get('request:'.$id.'action');
		else if ($request->has('request:action'))
			$action = $request->get('request:action');

		$target_block = 'default';
		if ($request->has('request:'.$id.'target'))
		{
			//Only if it actually exists do we use it...  just in case the template was changed we want to
			//make sure the module still shows up
			$blocks = $page->template->get_page_blocks();
			foreach ($blocks as $block_name)
			{
				if ($request->get('request:'.$id.'target') == $block_name)
				{
					$target_block = $request->get('request:'.$id.'target');
				}
			}
		}

		$target_block = strtolower(str_replace(' ', '_', $target_block));

		//Only consider doing module processing if
		//a. this is the target block
		//b. then
		//   1. $id is cntnt01
		//   2. or inline is false
		if ($block_name == $target_block && ($id == 'cntnt01' || ($id != '' && $inline == false)))
		{
			if (CmsModuleLoader::is_active($module_name) &&
				CmsModuleLoader::get_module_info($module_name, 'plugin_module') == true)
			{
				$module = CmsModuleLoader::get_module_class($module_name);
				if ($module)
				{
					@ob_start();
					
					$params = array_merge($params, GetModuleParameters($id));

					$return_id = '';
					if (isset($params['returnid']))
					{
						$return_id = $params['returnid'];
					}
					else
					{
						$return_id = $page->id;
					}

					if (CmsModuleLoader::get_module_info($module_name, 'old_module'))
					{
						echo $module->DoActionBase($action, $id, $params, $return_id);
					}
					else
					{
						$module->set_id($id, $return_id);
						echo $module->run_action($action, $params);
					}
					$content = @ob_get_contents();
					@ob_end_clean();
				}
			}
		}
		else
		{
			$content = $page->render_block($block_name);
		}

		if (isset($params['assign']) && !empty($params['assign']))
		{
			$smarty->assign($params['assign'], $content);
			return '';
		}
		else
		{
			return $content;
		}
	}
	
	/*
	$gCms = cmsms();
	$pageinfo = $gCms->current_page;
	if (isset($pageinfo) && $pageinfo !== FALSE)
	{
		$id = '';
		$modulename = '';
		$action = '';
		$inline = false;
		if (isset($_REQUEST['module'])) $modulename = $_REQUEST['module'];
		if (isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		elseif (isset($_REQUEST['mact']))
		{
			$ary = explode(',', cms_htmlentities($_REQUEST['mact']), 4);
			$modulename = (isset($ary[0])?$ary[0]:'');
			$id = (isset($ary[1])?$ary[1]:'');
			$action = (isset($ary[2])?$ary[2]:'');
			$inline = (isset($ary[3]) && $ary[3] == 1?true:false);
		}
		if (isset($_REQUEST[$id.'action'])) $action = $_REQUEST[$id.'action'];
		else if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];

		//Only consider doing module processing if
		//a. There is no block parameter
		//b. then
		//   1. $id is cntnt01
		//   2. or inline is false

		if (!isset($params['block']) && ($id == 'cntnt01' || ($id != '' && $inline == false)))
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

				if (!isset($modulename) || empty($modulename) ||
				    !isset($cmsmodules[$modulename]))
				  {
				    // module not found
				    @trigger_error('Attempt to access module '.$modulename.' which could not be foune (is it properly installed and configured?');
				    return _smarty_cms_function_content_return('', $params, $smarty);
				  }

				if (isset($cmsmodules[$modulename]))
				  {
				    if (isset($cmsmodules[$modulename]['object'])
					&& $cmsmodules[$modulename]['installed'] == true
					&& $cmsmodules[$modulename]['active'] == true
					&& $cmsmodules[$modulename]['object']->IsPluginModule())
				      {
					@ob_start();
					unset($params['block']);
					unset($params['label']);
					unset($params['wysiwyg']);
					unset($params['oneline']);
					unset($params['default']);
					unset($params['size']);
					$params = array_merge($params, GetModuleParameters($id));
					//$params = GetModuleParameters($id);
					$returnid = '';
					if (isset($params['returnid']))
					  {
					    $returnid = $params['returnid'];
					  }
					else
					  {
					    $returnid = $pageinfo->id;
					  }
					$result = $cmsmodules[$modulename]['object']->DoActionBase($action, $id, $params, $returnid);
					if ($result !== FALSE)
					  {
					    echo $result;
					  }
					$modresult = @ob_get_contents();
					@ob_end_clean();
					return _smarty_cms_function_content_return($modresult, $params, $smarty);
				      }
				    else
				      {
					@trigger_error('Attempt to access module '.$key.' which could not be foune (is it properly installed and configured?');
					return _smarty_cms_function_content_return("<!-- Not a tag module -->\n", $params, $smarty);
				      }
				  }
			}
		}
		else
		{
			$result = '';
			$oldvalue = $smarty->caching;
			$smarty->caching = false;
			$result = $smarty->fetch(str_replace(' ', '_', 'content:' . (isset($params['block'])?$params['block']:'content_en')), '', $pageinfo->id);
			$smarty->caching = $oldvalue;
			return _smarty_cms_function_content_return($result, $params, $smarty);
		}
	}
	return _smarty_cms_function_content_return('', $params, $smarty);
	*/
}

/*
function _smarty_cms_function_content_return($result, &$params, &$smarty)
{
	if ( empty($params['assign']) )
	{
		return $result;
	}
	else
	{
		$smarty->assign($params['assign'], $result);
		return '';
	}
}
*/

function smarty_cms_help_function_content()
{
  echo lang('help_function_content');
}

function smarty_cms_about_function_content()
{
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.1</p>
	<p>
	Change History:<br/>
	1.1 - Added assign parameter from djnz's patch in the forge<br />
	1.0 - Initial version
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
