<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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

class CmsModuleOperations extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	function install($name)
	{
		if (CmsModuleLoader::get_module_info($name) !== false && !CmsModuleLoader::get_module_info($name, 'installed'))
		{
			$version = CmsModuleLoader::get_module_info($name, 'version');
			
			$filename = cms_join_path(ROOT_DIR, 'modules', $name, 'method.install.php');
			if (@is_file($filename))
			{
				$module = CmsModuleLoader::get_module_class($name, false, false);
				$module->include_file_in_scope($filename);
			}
			
			$query = "INSERT INTO {modules} (module_name, version, status, active) VALUES (?, ?, ?, ?)";
			cms_db()->Execute($query, array($name, $version, 'installed', 1));

			$event_params = array('name' => $name, 'version' => $version);
			CmsEventManager::send_event('Core:module_installed', $event_params);
			//Events::SendEvent('Core', 'ModuleInstalled', $event_params);
		}
	}
	
	function uninstall($name)
	{
		if (CmsModuleLoader::get_module_info($name) !== false && CmsModuleLoader::get_module_info($name, 'installed'))
		{
			$version = CmsModuleLoader::get_module_info($name, 'installed_version');
			
			$filename = cms_join_path(ROOT_DIR, 'modules', $name, 'method.uninstall.php');
			if (@is_file($filename))
			{
				$module = CmsModuleLoader::get_module_class($name, false, false);
				$module->include_file_in_scope($filename);
			}
			
			$query = "DELETE FROM {modules} WHERE module_name = ?";
			cms_db()->Execute($query, array($name));

			$event_params = array('name' => $module, 'version' => $version);
			CmsEventManager::send_event('Core:module_uninstalled', $event_params);
			//Events::SendEvent('Core', 'ModuleUninstalled', $event_params);
		}
	}
	
	function upgrade($name)
	{
		if (CmsModuleLoader::get_module_info($name) !== false && CmsModuleLoader::get_module_info($name, 'installed') && CmsModuleLoader::get_module_info($name, 'needs_upgrade'))
		{
			$old_version = CmsModuleLoader::get_module_info($name, 'installed_version');
			$new_version = CmsModuleLoader::get_module_info($name, 'version');
			
			$filename = cms_join_path(ROOT_DIR, 'modules', $name, 'method.upgrade.php');
			if (@is_file($filename))
			{
				$module = CmsModuleLoader::get_module_class($name, false, false);
				$module->include_file_in_scope($filename);
			}
		
			$query = "UPDATE {modules} SET version = ? WHERE module_name = ?";
			cms_db()->Execute($query, array($new_version, $name));

			$event_params = array('name' => $module, 'oldversion' => $old_version, 'newversion' => $new_version);
			CmsEventManager::send_event('Core:module_upgraded', $event_params);
			//Events::SendEvent('Core', 'ModuleUpgraded', $event_params);
		}
	}
	
	function activate($name)
	{
		if (CmsModuleLoader::get_module_info($name) !== false && CmsModuleLoader::get_module_info($name, 'installed') && !CmsModuleLoader::get_module_info($name, 'active'))
		{
			$query = "UPDATE {modules} SET active = 1 WHERE module_name = ?";
			cms_db()->Execute($query, array($name));
		}
	}
	
	function deactivate($name)
	{
		if (CmsModuleLoader::get_module_info($name) !== false && CmsModuleLoader::get_module_info($name, 'installed') && CmsModuleLoader::get_module_info($name, 'active'))
		{
			$query = "UPDATE {modules} SET active = 0 WHERE module_name = ?";
			cms_db()->Execute($query, array($name));
		}
	}

	public static function cms_module_plugin($params,&$smarty)
	{
		global $gCms;
		$cmsmodules = &$gCms->modules;

		//$id = 'm' . ++$gCms->variables["modulenum"];
		if( !isset($gCms->variables['mid_cache']) )
		{
			$gCms->variables['mid_cache'] = array();
		}
		for( $i = 0; $i < 10; $i++ )
		{
			$tmp = $i;
			foreach($params as $key=>$value)
				$tmp .= $key.'='.$value;
			$id = 'm'.substr(md5($tmp),0,5);
			if( !isset($gCms->variables['mid_cache'][$id]) )
			{
				$gCms->variables['mid_cache'][$id] = $id;
				break;
			}
		}

		$returnid = '';
		if (isset($gCms->variables['pageinfo']) && isset($gCms->variables['pageinfo']->content_id))
		{
			$returnid = $gCms->variables['pageinfo']->content_id;
		}
		$params = array_merge($params, GetModuleParameters($id));

		$modulename = '';
		$action = 'default';
		$inline = false;

		$checkid = '';

		if (isset($params['module'])) 
			$modulename = $params['module'];
		else
			return '<!-- ERROR: module name not specified -->';

		if (isset($params['idprefix']))
			$id = trim($params['idprefix']);
		
		if (isset($params['action']) && $params['action'] != '')
		{
			// action was set in the module tag
			$action = $params['action'];
		}

		if (cms_request('request:id') != null) //Not really needed now...
		{
			$checkid = cms_request('request:id');
		}
		else if (cms_request('request:mact') != null)
		{
			// we're handling an action
			$ary = explode(',', cms_htmlentities(cms_request('request:mact'), 4));
			$mactmodulename = (isset($ary[0])?$ary[0]:'');
			if (!strcasecmp($mactmodulename,$params['module']))
			{
				$checkid = (isset($ary[1])?$ary[1]:'');
				$mactaction = (isset($ary[2])?$ary[2]:'');
			}
			$mactinline = (isset($ary[3]) && $ary[3] == 1?true:false);

			if ($checkid == $id)
			{
				// the action is for this instance of the module
				$inline = $mactinline;
				if( $inline == true )
				{
					// and we're inline (the results are supposed to replace
					// the tag, not {content}
					$action = $mactaction;
				}
			}
		}

		if( $action == '' ) $action = 'default'; // probably not needed, but safe

		$module_name = $params['module'];

		if (isset($module_name))
		{
			$module = CmsModuleLoader::get_module_class($module_name);
			if ($module)
			{
				@ob_start();

				$result = '';
				if (CmsModuleLoader::get_module_info($module_name, 'old_module'))
				{
					$result = $module->DoActionBase($action, $id, $params, $returnid);
				}
				else
				{
					$module->set_id($id, $returnid);
					$result = $module->run_action($action, $params);
				}
				if ($result !== FALSE)
				{
					echo $result;
				}
				$modresult = @ob_get_contents();
				@ob_end_clean();

				if( isset($params['assign']) )
				{
					$smarty->assign(trim($params['assign']), $modresult);
					return;
				}
				return $modresult;
			}
			else
			{
				return "<!-- Not a tag module -->\n";
			}
		}
	}
}

# vim:ts=4 sw=4 noet
?>