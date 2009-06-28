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
	if (isset($pageinfo) && $pageinfo !== FALSE && isset($pageinfo->content_id) )
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
					    $returnid = $pageinfo->content_id;
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
			$result = $smarty->fetch(str_replace(' ', '_', 'content:' . (isset($params['block'])?$params['block']:'content_en')), '', $pageinfo->content_id);
			$smarty->caching = $oldvalue;
			return _smarty_cms_function_content_return($result, $params, $smarty);
		}
	}
	return _smarty_cms_function_content_return('', $params, $smarty);
}

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
