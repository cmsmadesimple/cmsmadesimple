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
#$Id: CMSInstallPage6.class.php 242 2009-10-27 02:54:30Z sjg $

class CMSInstallerPage6 extends CMSInstallerPage
{
	/**
	 * Class constructor
	*/
	function CMSInstallerPage6(&$smarty, $errors, $debug)
	{
		$this->CMSInstallerPage(6, $smarty, $errors, $debug);
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
		$values['db']['db_port'] = isset($_POST['db_port']) ? $_POST['db_port'] : '';
		// $values['db']['db_socket'] = isset($_POST['db_socket']) ? $_POST['db_socket'] : '';

		$values['umask'] = isset($_POST['umask']) ? $_POST['umask'] : '';
		$values['admininfo']['username'] = $_POST['adminusername'];
		$values['admininfo']['email'] = $_POST['adminemail'];
		$values['admininfo']['password'] = $_POST['adminpassword'];
		$values['admininfo']['email_accountinfo'] = $_POST['email_accountinfo'];
		$values['createtables'] = isset($_POST['createtables']) ? 1 : (isset($_POST['sitename']) ? 0 : 1);

		$this->smarty->assign('docroot', 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - 18));
		$this->smarty->assign('docpath', CMS_BASE);
		$this->smarty->assign('default_encoding', 'utf-8');

		$zones = DateTimeZone::listIdentifiers();
		$tzdef = date_default_timezone_get();		
		$this->smarty->assign('timezones',$zones);
		$this->smarty->assign('default_timezone',$tzdef);
		

		$this->smarty->assign('values', $values);

		$this->smarty->assign('errors', $this->errors);
	}

	function preContent(&$db)
	{
		$db_prefix = $_POST['prefix'];

		if(isset($_POST['createtables']))
		{
			$sql_error=false;
			$db->SetFetchMode(ADODB_FETCH_ASSOC);

			$CMS_INSTALL_DROP_TABLES=1;
			$CMS_INSTALL_CREATE_TABLES=1;


			$t=time();
			echo "<p>" . lang('install_sql_schema') ."</p>";

			include_once(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'schema.php'));

			echo "<p>[" . (($this->debug) ? lang('done_in_sec', (time()-$t)) : lang('done')) . "]</p>";
			flush();


			echo "<p>&nbsp;</p>";


			$t=time();
			echo "<p>" . lang('install_admin_importing') ."</p>";

			$handle = '';
			if(isset($_POST["createextra"]))
			{
				$_file = cms_join_path(CMS_INSTALL_BASE, 'schemas', 'extra.sql');
				if($this->debug) $handle = fopen($_file, 'r');
				else             $handle = @fopen($_file, 'r');
			}
			else
			{
				$_file = cms_join_path(CMS_INSTALL_BASE, 'schemas', 'initial.sql');
				if($this->debug) $handle = fopen($_file, 'r');
				else             $handle = @fopen($_file, 'r');
			}

			if($handle) {
				$db->StartTrans();

				while(!feof($handle)) {
					//set_magic_quotes_runtime(false);
					$s = fgets($handle, 32768);
					if ($s != "") {
						$s = trim(str_replace("{DB_PREFIX}", $db_prefix, $s));
						$s = str_replace("\\r", "\r", $s);
						$s = str_replace("\\n", "\n", $s);
						$s = str_replace("\\'", "''", $s);
						$s = str_replace('\\"', '"', $s);
						$result = $db->Execute($s);
						if (!$result) {
							die(lang('invalid_query', $s));
						}
					}
				}
				fclose($handle);

				if($this->debug && $db->HasFailedTrans()) echo "<p>" . lang('install_error_trans') . "</p>";
				$db->CompleteTrans();
				echo "<p>[" . (($this->debug) ? lang('done_in_sec', (time()-$t)) : lang('done')) . "]</p>";
			}
			else
			{
				echo "<p>" . lang('install_admin_error_schema') . "</p>";
			}
			flush();


			echo "<p>&nbsp;</p>";


			$t=time();
			echo "<p>" . lang('install_set') . "</p>";

			$sql = 'UPDATE ' . $db_prefix . 'users SET username = ?, password = ?, email = ? WHERE user_id = 1';
			try {
				$dbresult = $db->Execute($sql, array($_POST['adminusername'], md5($_POST['adminpassword']), $_POST['adminemail']));
				echo lang('install_set_account', lang('done'));
			} catch (exception $e) {
				echo lang('install_set_account', lang('failed').': '.$e->getMessage());
				$sql_error = true;
			}

			$sql = "INSERT INTO ". $db_prefix ."siteprefs (sitepref_name, sitepref_value) VALUES (?,?)";
			try {
				$dbresult = $db->Execute($sql, array('sitename', htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8')));
				echo lang('install_set_sitename', lang('done'));
			} catch (exception $e) {
				echo lang('install_set_sitename', lang('failed').': '.$e->getMessage());
				$sql_error = true;
			}

			echo "<p>[" . (($this->debug) ? lang('done_in_sec', (time()-$t)) : lang('done')) . "]</p>";
			flush();


			echo "<p>&nbsp;</p>";


			$t=time();
			echo "<p>" . lang('install_sequence') ."</p>";
			$db->StartTrans();

			include_once(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'createseq.php'));

			if($this->debug && $db->HasFailedTrans()) echo "<p>" . lang('install_error_trans') . "</p>";
			$db->CompleteTrans();
			echo "<p>[" . (($this->debug) ? lang('done_in_sec', (time()-$t)) : lang('done')) . "]</p>";


			$db->Close();
			if (!$sql_error)
			{
				echo '<p class="success">' . lang('success') . '!</p>';
			}
			else
			{
				echo '<p class="error">' . lang('invalid_querys') . '!</p>';
			}
		}
	}
}
?>