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
#$Id: CMSUpgradePage5.class.php 150 2009-03-17 23:01:15Z alby $

class CMSInstallerPage5 extends CMSInstallerPage
{
	/**
	 * Class constructor
	*/
	function CMSInstallerPage5(&$smarty, $errors, $debug)
	{
		$this->CMSInstallerPage(5, $smarty, $errors, $debug);
	}

	function assignVariables()
	{
		$this->smarty->assign('errors', $this->errors);
	}

	function preContent(&$db)
	{
		$test =&new StdClass();

		$test->error = false;
		$test->messages = array();

		$db->SetFetchMode(ADODB_FETCH_ASSOC);

		$current_version = 1;

		$query = "SELECT version from " . cms_db_prefix() . "version";
		$dbresult = $db->Execute($query);
		if(! $dbresult)
		{
			$test->messages[] = lang('invalid_query', $query);
			$test->error = true;
		}
		else
		{
			while($row = $dbresult->FetchRow())
			{
				$current_version = $row["version"];
			}
			if ($current_version == 1)
			{
				$test->messages[] = lang('empty_query', $query);
				$test->error = true;
			}
		}

		if ( (!$test->error) && ($current_version < CMS_SCHEMA_VERSION) )
		{
			$test->messages[] = lang('need_upgrade_schema', $current_version, CMS_SCHEMA_VERSION);
			while ($current_version < CMS_SCHEMA_VERSION)
			{
				$filename = cms_join_path(CMS_INSTALL_BASE, 'upgrades', "upgrade.{$current_version}.to." . ($current_version+1) . '.php');
				if (file_exists($filename))
				{
					if($this->debug) include($filename);
					else            @include($filename);
				}
				else
				{
					$test->messages[] = lang('nofiles') . ": $filename";
				}
				$current_version++;
			}
			$test->messages[] = lang('schema_ok',$current_version);
		}
		elseif (!$test->error)
		{
			$test->messages[] = lang('noneed_upgrade_schema', CMS_SCHEMA_VERSION);
		}

		$this->smarty->assign('test', $test);
	}
}
?>
