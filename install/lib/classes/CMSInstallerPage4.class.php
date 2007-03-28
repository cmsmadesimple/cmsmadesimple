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

class CMSInstallerPage4 extends CMSInstallerPage
{
	/**
	 * Class constructor
	*/
	function CMSInstallerPage4(&$smarty, $errors)
	{
		$this->CMSInstallerPage(4, $smarty, $errors);
	}
	
	function assignVariables()
	{
		$values = array();
 		$values['db']['dbms'] = isset($_POST['dbms']) ? $_POST['dbms'] : '';
 		$values['db']['host'] = isset($_POST['host']) ? $_POST['host'] : '';
		$values['db']['database'] = isset($_POST['database']) ? $_POST['database'] : '';
		$values['db']['username'] = isset($_POST['username']) ? $_POST['username'] : '';
		$values['db']['password'] = isset($_POST['password']) ? $_POST['password'] : '';
		$values['db']['prefix'] = isset($_POST['prefix']) ? $_POST['prefix'] : '';
		$values['admininfo']['username'] = $_POST['adminusername'];
		$values['admininfo']['email'] = $_POST['adminemail'];
		$values['admininfo']['password'] = $_POST['adminpassword'];
		$values['email_accountinfo'] = (bool) $_POST['email_accountinfo'];
		$values['createtables'] = isset($_POST['createtables']) ? 1 : (isset($_POST['sitename']) ? 0 : 1);
		$this->smarty->assign('docroot', 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - 18));
		$this->smarty->assign('docpath', CMS_BASE); 
		
		$this->smarty->assign('errors', $this->errors);
		$this->smarty->assign('values', $values);
		
	}
	
	function preContent(&$db)
	{
		$db_prefix = $_POST['prefix'];

		if (isset($_POST['createtables']))
		{
			$db->SetFetchMode(ADODB_FETCH_ASSOC);

			$CMS_INSTALL_DROP_TABLES=1;
			$CMS_INSTALL_CREATE_TABLES=1;
			
			include_once(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'schema.php'));
			
			echo "<p>Importing sample data...";
			
					$handle = '';

			if (isset($_POST["createextra"]))
			{
				$handle = fopen(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'extra.sql'), 'r');
			}
			else
			{
				$handle = fopen(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'initial.sql'), 'r');
			}
	
			if ($handle) {
				while (!feof($handle)) {
					set_magic_quotes_runtime(false);
					$s = fgets($handle, 32768);
					if ($s != "") {
						$s = trim(str_replace("{DB_PREFIX}", $db_prefix, $s));
						$s = str_replace("\\r\\n", "\r\n", $s);
						$s = str_replace("\\'", "''", $s);
						$s = str_replace('\\"', '"', $s);
						$result = $db->Execute($s);
						if (!$result) {
							die("Invalid query: $s");
						} ## if
					}
				}
			}
	
			fclose($handle);
	
			echo "[done]</p>";
	
			echo "<p>Setting admin account information...";
	
			$sql = 'UPDATE ' . $db_prefix . 'users SET username = ?, password = ?, email = ? WHERE user_id = 1';
			$db->Execute($sql, array($_POST['adminusername'], md5($_POST['adminpassword']), $_POST['adminemail']));
	
			echo "[done]</p>";
			
			echo "<p>Setting sitename...";
	
			$query = "INSERT INTO ". $db_prefix ."siteprefs (sitepref_name, sitepref_value) VALUES (?,?)";
			$db->Execute($query, array('sitename', htmlentities($_POST['sitename'])));
	
			echo "[done]</p>";
	
			include_once(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'createseq.php'));
	
			$db->Close();
			echo '<p class="success">Success!</p>';
		}
	}
}

?>
