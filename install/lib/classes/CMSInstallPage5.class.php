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
#$Id: CMSInstallPage5.class.php 296 2010-10-17 22:31:18Z calguy1000 $

class CMSInstallerPage5 extends CMSInstallerPage
{
	function assignVariables()
	{
		$values = array();
		$values['sitename'] = isset($_POST['sitename'])? htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8'):'CMS Made Simple Site';
 		$values['db']['dbms'] = isset($_POST['dbms']) ? $_POST['dbms'] : 'mysqli';
 		$values['db']['host'] = isset($_POST['host']) ? $_POST['host'] : 'localhost';
		$values['db']['database'] = isset($_POST['database']) ? $_POST['database'] : 'cms';
		$values['db']['username'] = isset($_POST['username']) ? $_POST['username'] : '';
		$values['db']['password'] = isset($_POST['password']) ? $_POST['password'] : '';
		$values['db']['prefix'] = isset($_POST['prefix']) ? $_POST['prefix'] : 'cms_';
		$values['db']['db_port'] = isset($_POST['db_port']) ? $_POST['db_port'] : '';
		// $values['db']['db_socket'] = isset($_POST['db_socket']) ? $_POST['db_socket'] : '';

		if( isset($_SESSION['cms_orig_tz']) && $_SESSION['cms_orig_tz'] != '' )
		  {
		    $values['timezone'] = $_SESSION['cms_orig_tz'];
		    $this->smarty->assign('current_timezone',$_SESSION['cms_orig_tz']);
		  }
		if( isset($_POST['timezone']) )
		  {
		    $values['timezone'] = $_POST['timezone'];
		  }
		$values['umask'] = isset($_POST['umask']) ? $_POST['umask'] : '';
		$values['admininfo']['username'] = $_POST['adminusername'];
		$values['admininfo']['email'] = $_POST['adminemail'];
		if( isset($_POST['adminsalt']) )
		  {
		    $values['admininfo']['salt'] = $_POST['adminsalt'];
		  }
		$values['admininfo']['password'] = $_POST['adminpassword'];
		$values['email_accountinfo'] = empty($_POST['email_accountinfo']) ? 0 : 1;
		$values['createtables'] = isset($_POST['createtables']) ? 1 : (isset($_POST['sitename']) ? 0 : 1);
		$values['createextra'] = isset($_POST['createextra']) ? 1 : (isset($_POST['sitename']) ? 0 : 1);
		$databases = array(
			array('name' => 'mysqli', 'title' => 'MySQLi (4.1+)'),
			array('name' => 'mysql', 'title' => 'MySQL (compatibility)')
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

		$tmp = timezone_identifiers_list();
		if( is_array($tmp) )
		  {
		    $timezones = array();
		    $timezones[''] = ilang('none');
		    foreach( $tmp as $zone )
		      {
			$timezones[$zone] = $zone;
		      }
		    $this->smarty->assign('timezones',$timezones);
		  }

		$this->smarty->assign('extra_sql', is_file(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'extra.sql')));
		$this->smarty->assign('dbms_options', $dbms_options);
		$this->smarty->assign('values', $values);

		$this->smarty->assign('errors', $this->errors);
	}
}
?>
