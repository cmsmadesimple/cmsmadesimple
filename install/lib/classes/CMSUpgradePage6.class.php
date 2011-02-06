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
#
#$Id: CMSUpgradePage6.class.php 296 2010-10-17 22:31:18Z calguy1000 $

class CMSInstallerPage6 extends CMSInstallerPage
{
	function assignVariables()
	{
		$test = $this->module_autoupgrade();
		$test->messages[] = ilang('noneed_upgrade_modules');
		$this->smarty->assign('test', $test);

		$this->smarty->assign('errors', $this->errors);
	}


	//Do module autoupgrades 
	function module_autoupgrade()
	{
		global $gCms;
		$db =& $gCms->GetDB();
		$test =new StdClass();

		$test->error = false;
		$test->messages = array();

		foreach ($gCms->modules as $modulename=>$value)
		{
			if($gCms->modules[$modulename]['object']->AllowAutoUpgrade() == true)
			{
				//Check to see what version we currently have in the database (if it's installed)
				$module_version = false;

				$query = "SELECT version from " . cms_db_prefix() . "modules WHERE module_name = ?";
				$dbresult = $db->Execute($query, array($modulename));
				if(! $dbresult)
				{
					$test->messages[] = ilang('invalid_query', $query);
					$test->error = true;
				}
				else
				{
					while ($row = $dbresult->FetchRow())
					{
						$module_version = $row['version'];
					}
				}
				//Check to see what version we have in the file system
				$file_version = $gCms->modules[$modulename]['object']->GetVersion();

				if($module_version != false)
				{
					if(version_compare($file_version, $module_version) == 1)
					{
						$_msg = ilang('upgrade_sql_module_from_to', $modulename, $module_version, $file_version);
						$gCms->modules[$modulename]['object']->Upgrade($module_version, $file_version);
						$query = "UPDATE " . cms_db_prefix() . "modules SET version = ?, admin_only = ? WHERE module_name = ?";
						$dbresult = $db->Execute($query, array($file_version, ($gCms->modules[$modulename]['object']->IsAdminOnly()==true?1:0), $modulename));
						if(!$dbresult)
						{
							$_msg .= ilang('invalid_query', $query);
							$test->error = true;
						}
						else
						{
							$_msg .= " [" . ilang('done') . "]";
						}
						$test->message[] = $_msg; 

						$_msg = ilang('upgrade_event_module_from_to', $modulename, $module_version, $file_version);
						Events::SendEvent('Core', 'ModuleUpgraded', array('name' => $modulename, 'oldversion' => $module_version, 'newversion' => $file_version));
						$_msg .= " [" . ilang('done') . "]";
						$test->message[] = $_msg; 
					}
				}
			}
		}

		return $test;
	}
}
?>
