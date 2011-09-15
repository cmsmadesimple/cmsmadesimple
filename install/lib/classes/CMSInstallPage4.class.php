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
#$Id: CMSInstallPage4.class.php 296 2010-10-17 22:31:18Z calguy1000 $

class CMSInstallerPage4 extends CMSInstallerPage
{
	function assignVariables()
	{
		$values = array();
		$values['username'] = isset($_POST['adminusername']) ? $_POST['adminusername'] : '';
		$values['email'] = isset($_POST['adminemail']) ? $_POST['adminemail'] : '';
		$values['salt'] = isset($_POST['adminsalt']) ? $_POST['adminsalt'] : '1';
		$values['email_accountinfo'] = isset($_POST['email_accountinfo']) ? $_POST['email_accountinfo'] : false;

		$values['umask'] = isset($_POST['umask']) ? $_POST['umask'] : '';

		$this->smarty->assign('values', $values);
		$this->smarty->assign('mail_function_exists', function_exists('mail'));

		$this->smarty->assign('errors', $this->errors);
	}
}
?>
