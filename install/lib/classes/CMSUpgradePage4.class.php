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
#$Id: CMSUpgradePage4.class.php 149 2009-03-17 22:22:13Z alby $

class CMSInstallerPage4 extends CMSInstallerPage
{
	/**
	 * Class constructor
	 * @var object $smarty
	 * @var array  $errors
	 * @var bool   $debug
	 */
	function CMSInstallerPage4(&$smarty, $errors, $debug)
	{
		$this->CMSInstallerPage(4, $smarty, $errors, $debug);
	}

	function assignVariables()
	{
		$result = true;
		@clearstatcache();

		//Clear cache dirs
		$cpaths = array(TMP_TEMPLATES_C_LOCATION, TMP_CACHE_LOCATION);
		foreach($cpaths as $cpath)
		{
			$handle = opendir($cpath);
			if ($handle)
			{
				while ($cfile = readdir($handle))
				{
					if ( ($cfile != '.') && ($cfile != '..') && (is_file($cpath . DIRECTORY_SEPARATOR . $cfile)) )
					{
						if($this->debug) unlink($cpath . DIRECTORY_SEPARATOR . $cfile);
						else            @unlink($cpath . DIRECTORY_SEPARATOR . $cfile);
					}
				}
			}
			else
			{
				$result = false;
			}
        }

		$this->smarty->assign('result', $result);

		$this->smarty->assign('errors', $this->errors);
	}
}
# vim:ts=4 sw=4 noet
?>
