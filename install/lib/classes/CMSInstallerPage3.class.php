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
#$Id$

class CMSInstallerPage3 extends CMSInstallerPage
{
	/**
	 * Class constructor
	*/
	function CMSInstallerPage3(&$smarty, $errors)
	{
		$this->CMSInstallerPage(3, $smarty, $errors);
	}
	
	function assignVariables()
	{
		$values = array();
		$values['sitename'] = isset($_POST['sitename'])? htmlentities($_POST['sitename']):'CMS Made Simple Site';
 		$values['db']['dbms'] = isset($_POST['dbms']) ? $_POST['dbms'] : 'mysql';
 		$values['db']['host'] = isset($_POST['host']) ? $_POST['host'] : 'localhost';
		$values['db']['database'] = isset($_POST['database']) ? $_POST['database'] : 'cms';
		$values['db']['username'] = isset($_POST['username']) ? $_POST['username'] : '';
		$values['db']['password'] = isset($_POST['password']) ? $_POST['password'] : '';
		$values['db']['prefix'] = isset($_POST['prefix']) ? $_POST['prefix'] : 'cms_';
		$values['admininfo']['username'] = $_POST['adminusername'];
		$values['admininfo']['email'] = $_POST['adminemail'];
		$values['admininfo']['password'] = $_POST['adminpassword'];
		$values['email_accountinfo'] = isset($_POST['email_accountinfo']) ? 1 : 0;
		$values['createtables'] = isset($_POST['createtables']) ? 1 : (isset($_POST['sitename']) ? 0 : 1);
		$values['createextra'] = isset($_POST['createextra']) ? 1 : (isset($_POST['sitename']) ? 0 : 1);
		$databases = array(
			array('name' => 'mysql', 'title' => 'MySQL'),
			array('name' => 'mysqli', 'title' => 'MySQL (4.1+)'),
			array('name' => 'postgres7', 'title' => 'PostgreSQL 7/8', 'extension' => 'pgsql')
			// array('name' => 'sqlite', 'title' => 'SQLite')
		);
		$dbms_options = array();
		foreach ($databases as $db)
		{
			$extension = isset($db['extension']) ? $db['extension'] : $db['name'];
			if (extension_loaded($extension))
			{
				$dbms_options[] = $db;
			}
		}
		$this->smarty->assign('extra_sql', is_file(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'extra.sql')));
		$this->smarty->assign('dbms_options', $dbms_options);
		$this->smarty->assign('errors', $this->errors);
		$this->smarty->assign('values', $values);
	}
}
?>