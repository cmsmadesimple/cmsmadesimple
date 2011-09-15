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
#$Id: CMSUpgradePage3.class.php 296 2010-10-17 22:31:18Z calguy1000 $

class CMSInstallerPage3 extends CMSInstallerPage
{
	function assignVariables()
	{
	  $config = cmsms()->GetConfig();
	  if( isset($config['use_hierarchy']) && $config['use_hierarchy'] == false )
	    {
	      $_SESSION['disable_hierarchy'] = 1;
	    }
	  $config->save();
	  $result = 1; // tempalte needs a result.
	  $this->smarty->assign('config_file', CONFIG_FILE_LOCATION);
	  $this->smarty->assign('error_fragment', 'config_php_error');
	  $this->smarty->assign('result', $result);
	  
	  $this->smarty->assign('errors', $this->errors);
	}
}
# vim:ts=4 sw=4 noet
?>
