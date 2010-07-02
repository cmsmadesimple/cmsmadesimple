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
#
#$Id$

/**
 * Functions for loading modules
 * @package  CMS
 * @license GPL
 */

/**
 * Include the module class definition
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.module.inc.php');

/**
 * Class to load modules
 *
 * @since 1.0
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class ModuleLoader
{
	/**
	 * Loads modules from the filesystem.  If loadall is true, then it will load all
	 * modules whether they're installed, or active.  If it is false, then it will
	 * only load modules which are installed and active.
	 *
	 * @param boolean $loadall Should be load all modules?
	 * @param boolean $noadmin Should we skip all modules marked as admin only?
	 * @return void
	 */
	function LoadModules($loadall = false, $noadmin = false)
	{
		global $gCms;
		$db =& $gCms->GetDb();
		$cmsmodules = &$gCms->modules;

		$dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";

		if ($loadall == true)
		{
			if ($handle = @opendir($dir))
			{
				while (($file = readdir($handle)) !== false)
				{
					if (@is_file("$dir/$file/$file.module.php"))
					{
						include_once("$dir/$file/$file.module.php");
					}
					else
					{
						unset($cmsmodules[$file]);
					}
				}
				closedir($handle);
			}

			//Find modules and instantiate them
			$allmodules = $this->FindModules();
			foreach ($allmodules as $onemodule)
			{
				if (class_exists($onemodule))
				{
					$newmodule = new $onemodule;
					$name = $newmodule->GetName();
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
			$where = array();
			if ($noadmin)
			  {
			    $where[] = 'admin_only = 0';
			  }
			if( $loadall != true )
			  {
			    $where[] = 'active = 1';
			  }
			$query = 'SELECT * FROM '.cms_db_prefix().'modules ';
			if( count($where) )
			  {
			    $query.= 'WHERE '.implode(' AND ',$where);
			  }
                        $query .= ' ORDER by module_name';

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
										$name = $newmodule->GetName();

										global $CMS_VERSION;
										$dbversion = $result->fields['version'];

										#Check to see if there is an update and wether or not we should perform it
										if (version_compare($dbversion, $newmodule->GetVersion()) == -1 && $newmodule->AllowAutoUpgrade() == TRUE)
										{
											$newmodule->Upgrade($dbversion, $newmodule->GetVersion());
											$query = "UPDATE ".cms_db_prefix()."modules SET version = ? WHERE module_name = ?";
											$db->Execute($query, array($newmodule->GetVersion(), $name));
											Events::SendEvent('Core', 'ModuleUpgraded', array('name' => $name, 'oldversion' => $dbversion, 'newversion' => $newmodule->GetVersion()));
											$dbversion = $newmodule->GetVersion();
										}

										#Check to see if version in db matches file version
										if ($dbversion == $newmodule->GetVersion() && version_compare($newmodule->MinimumCMSVersion(), $CMS_VERSION) != 1)
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
	}

	/**
	 * Finds all classes extending cmsmodule for loading
	 *
	 * @return array list of class names
	 */
	function FindModules()
	{
		$result = array();

		foreach (get_declared_classes() as $oneclass)
		{
		  $parent = get_parent_class($oneclass);
		  while( $parent !== FALSE )
		    {
		      $str = strtolower($parent);
		      if( $str == 'cmsmodule' ) 
			{
			  $result[] = strtolower($oneclass);
			  break;
			}
		      $parent = get_parent_class($parent);
		    }
		}

		sort($result);

		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>
