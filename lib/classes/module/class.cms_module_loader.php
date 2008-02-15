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

/**
 * Class to load modules.
 *
 * @author Ted Kulp
 * @since 1.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsModuleLoader extends CmsObject
{
	/**
	* Loads modules from the filesystem.  If loadall is true, then it will load all
	* modules whether they're installed, or active.  If it is false, then it will
	* only load modules which are installed and active.
	*/
	public static function load_modules($loadall = false, $noadmin = false)
	{
		$gCms = cmsms();
		$db = cms_db();
		$cmsmodules = &$gCms->modules;

		$dir = cms_join_path(ROOT_DIR,'modules');
		
		$loaded_modules = array();

		if ($loadall == true)
		{
			$handle=opendir($dir);
			while ($file = readdir($handle))
			{
				if (@is_file("$dir/$file/$file.module.php"))
				{
					include("$dir/$file/$file.module.php");
				}
				else
				{
					unset($cmsmodules[$file]);
				}
			}
			closedir($handle);
			
			//Find modules and instantiate them
			$allmodules = CmsModuleLoader::find_modules();
			foreach ($allmodules as &$onemodule)
			{
				if (class_exists($onemodule))
				{
					$newmodule = new $onemodule;
					$name = $newmodule->get_name();
					$loaded_modules[] = $name;
					$cmsmodules[$name]['object'] = $newmodule;
					$cmsmodules[$name]['installed'] = false;
					$cmsmodules[$name]['active'] = false;
				}
				else
				{
					unset($cmsmodules[$name]);
				}
			}
		}

		#Figger out what modules are active and/or installed
		#Load them if loadall is false
		if (isset($db))
		{
			$query = '';
			if ($noadmin)
				$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE admin_only = 0 ORDER BY module_name";
			else
				$query = "SELECT * FROM ".cms_db_prefix()."modules ORDER BY module_name";
			$result = &$db->Execute($query);
			while ($result && !$result->EOF)
			{
				if (isset($result->fields['module_name']))
				{
					$modulename = $result->fields['module_name'];
					if (isset($modulename))
					{
						if ($loadall == true)
						{
							if (isset($cmsmodules[$modulename]))
							{
								$cmsmodules[$modulename]['installed'] = true;
								$cmsmodules[$modulename]['active'] = ($result->fields['active'] == 1?true:false);
							}
						}
						else
						{
							if ($result->fields['active'] == 1)
							{
								if (@is_file("$dir/$modulename/$modulename.module.php"))
								{
									#var_dump('loading module:' . $modulename);
									include_once("$dir/$modulename/$modulename.module.php");
									if (class_exists($modulename))
									{
										$newmodule = new $modulename;
										$name = $newmodule->get_name();
										$loaded_modules[] = $name;

										$dbversion = $result->fields['version'];

										#Check to see if there is an update and wether or not we should perform it
										if (version_compare($dbversion, $newmodule->get_version()) == -1 && $newmodule->allow_auto_upgrade())
										{
											$newmodule->Upgrade($dbversion, $newmodule->get_version());
											$query = "UPDATE ".cms_db_prefix()."modules SET version = ? WHERE module_name = ?";
											$db->Execute($query, array($newmodule->get_version(), $name));
											CmsEvents::SendEvent('Core', 'ModuleUpgraded', array('name' => $name, 'oldversion' => $dbversion, 'newversion' => $newmodule->get_version()));
											$dbversion = $newmodule->get_version();
										}

										#Check to see if version in db matches file version
										if ($dbversion == $newmodule->get_version() && version_compare($newmodule->minimum_core_version(), CMS_VERSION) != 1)
										{
											$cmsmodules[$name]['object'] = $newmodule;
											$cmsmodules[$name]['installed'] = true;
											$cmsmodules[$name]['active'] = ($result->fields['active'] == 1?true:false);
										}
										else
										{
											unset($cmsmodules[$name]);
										}
									}
									else //No point in doing anything with it
									{
										unset($cmsmodules[$modulename]);
									}
								}
								else
								{
									unset($cmsmodules[$modulename]);
								}
							}
						}
					}
					$result->MoveNext();
				}
			}
			
			if ($result) $result->Close();
		}
		
		CmsEventOperations::send_event('Core', 'AllModulesLoaded', array('loaded_modules' => $loaded_modules));
	}
	
	/**
	 * Deprecated.  Use load_modules instead.
	 **/
	public static function LoadModules($loadall = false, $noadmin = false)
	{
		return CmsModuleLoader::load_modules($loadall, $noadmin);
	}

	/**
	 * Finds all classes extending cmsmodule for loading
	 */
	public static function find_modules()
	{
		$result = array();

		foreach (get_declared_classes() as $oneclass)
		{
			$parent = get_parent_class($oneclass);
			while( $parent !== FALSE )
			{
				$str = strtolower($parent);
				if(starts_with($str, 'cmsmodule')) 
				{
					$str = strtolower($oneclass);
					if(!starts_with($str, 'cmsmodule'))
					{
						if (!in_array($oneclass, $result))
						{
							$result[] = $oneclass;
							break;
						}
					}
				}
				$parent = get_parent_class($parent);
			}
		}

		sort($result);

		return $result;
	}
	
	/**
	 * Deprecated.  Use find_modules instead.
	 **/
	public static function FindModules()
	{
		return CmsModuleLoader::find_modules();
	}
}

?>
