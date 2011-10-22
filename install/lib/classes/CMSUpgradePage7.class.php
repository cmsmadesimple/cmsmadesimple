<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: CMSUpgradePage7.class.php 296 2010-10-17 22:31:18Z calguy1000 $

class CMSInstallerPage7 extends CMSInstallerPage
{
	function assignVariables()
	{
		$gCms = cmsms();
		$config =& $gCms->GetConfig();

		$test =new StdClass();

		$test->error = false;
		$test->messages = array();

		if (file_exists(TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . 'SITEDOWN'))
		{
			if($this->debug) $_test = unlink(TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . 'SITEDOWN');
			else             $_test = @unlink(TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . 'SITEDOWN');

			if (! $_test)
			{
				$test->messages[] = ilang('sitedown_not_removed');
				$test->error = true;
			}
		}

		$test->messages[] = ilang('upgrade_ok');
		$test->messages[] = ilang('upgrade_end', '<a href="../index.php">'.ilang('here').'</a>', '<a href="../'.$config['admin_dir'].'">'.ilang('go_to_admin').'</a>');
		$this->smarty->assign('test', $test);

		$this->smarty->assign('errors', $this->errors);
	}
}
# vim:ts=4 sw=4 noet
?>
