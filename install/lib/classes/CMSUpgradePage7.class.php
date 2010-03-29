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
#$Id: CMSUpgradePage7.class.php 149 2009-03-17 22:22:13Z alby $

class CMSInstallerPage7 extends CMSInstallerPage
{
	/**
	 * Class constructor
	*/
	function CMSInstallerPage7(&$smarty, $errors, $debug)
	{
		$this->CMSInstallerPage(7, $smarty, $errors, $debug);
	}

	function assignVariables()
	{
		global $gCms;
		$config =& $gCms->GetConfig();

		$test =&new StdClass();

		$test->error = false;
		$test->messages = array();

		if (file_exists(TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . 'SITEDOWN'))
		{
			if($this->debug) $_test = unlink(TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . 'SITEDOWN');
			else             $_test = @unlink(TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . 'SITEDOWN');

			if (! $_test)
			{
				$test->messages[] = lang('sitedown_not_removed');
				$test->error = true;
			}
		}

		$test->messages[] = lang('upgrade_ok');
		$test->messages[] = lang('upgrade_end', '<a href="../index.php">'.lang('here').'</a>', '<a href="../'.$config['admin_dir'].'">'.lang('go_to_admin').'</a>');
		$this->smarty->assign('test', $test);

		$this->smarty->assign('errors', $this->errors);
	}
}
# vim:ts=4 sw=4 noet
?>
