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
#$Id: CMSUpgradePage3.class.php 149 2009-03-17 22:22:13Z alby $

class CMSInstallerPage3 extends CMSInstallerPage
{
	/**
	 * Class constructor
	 * @var object $smarty
	 * @var array  $errors
	 * @var bool   $debug
	 */
	function CMSInstallerPage3(&$smarty, $errors, $debug)
	{
		$this->CMSInstallerPage(3, $smarty, $errors, $debug);
	}

	function assignVariables()
	{
		//cms_config_upgrade();
		CmsConfig::load(true, true);
		$result = CmsConfig::save();
		if($result)
		{
			// Delete backup config file
			@unlink(CONFIG_FILE_LOCATION .'.bakupg');
		}
		else
		{
			$this->smarty->assign('config_file', CONFIG_FILE_LOCATION);
			$this->smarty->assign('error_fragment', 'config_php_error');
		}

		$this->smarty->assign('result', $result);

		$this->smarty->assign('errors', $this->errors);
	}
}
# vim:ts=4 sw=4 noet
?>
