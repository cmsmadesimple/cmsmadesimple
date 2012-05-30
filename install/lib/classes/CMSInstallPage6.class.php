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
#$Id: CMSInstallPage6.class.php 296 2010-10-17 22:31:18Z calguy1000 $

class CMSInstallerPage6 extends CMSInstallerPage
{
  function assignVariables()
  {
    $values = array();
    $values['db']['dbms'] = isset($_POST['dbms']) ? $_POST['dbms'] : '';
    $values['db']['host'] = isset($_POST['host']) ? $_POST['host'] : '';
    $values['db']['database'] = isset($_POST['database']) ? $_POST['database'] : '';
    $values['db']['username'] = isset($_POST['username']) ? $_POST['username'] : '';
    $values['db']['password'] = isset($_POST['password']) ? $_POST['password'] : '';
    $values['db']['prefix'] = isset($_POST['prefix']) ? $_POST['prefix'] : '';
    if( isset($_POST['db_port']) ) {
      {
	$values['db']['db_port'] = (int)$_POST['db_port'];
      }
      // $values['db']['db_socket'] = isset($_POST['db_socket']) ? $_POST['db_socket'] : '';

      $values['timezone'] = isset($_POST['timezone']) ? $_POST['timezone'] : '';
      $values['umask'] = isset($_POST['umask']) ? $_POST['umask'] : '';
      $values['admininfo']['username'] = $_POST['adminusername'];
      $values['admininfo']['email'] = $_POST['adminemail'];
      $values['admininfo']['salt'] = $_POST['adminsalt'];
      $values['admininfo']['password'] = $_POST['adminpassword'];
      $values['admininfo']['email_accountinfo'] = $_POST['email_accountinfo'];
      $values['createtables'] = isset($_POST['createtables']) ? 1 : (isset($_POST['sitename']) ? 0 : 1);

      $this->smarty->assign('docroot', 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - 18));
      $this->smarty->assign('docpath', CMS_BASE);

      $this->smarty->assign('values', $values);

      $this->smarty->assign('errors', $this->errors);
    }
  }

  function preContent(&$db)
  {
    $db_prefix = $_POST['prefix'];

    if(isset($_POST['createtables']))
      {
	$db->SetFetchMode(ADODB_FETCH_ASSOC);

	$CMS_INSTALL_DROP_TABLES=1;
	$CMS_INSTALL_CREATE_TABLES=1;

	include_once(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'schema.php'));

	echo "<p>" . ilang('install_admin_importing');

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
	  while(!feof($handle)) {
	    @set_magic_quotes_runtime(false);
	    $s = fgets($handle, 32768);
	    if ($s != "") {
	      $s = trim(str_replace("{DB_PREFIX}", $db_prefix, $s));
	      $s = str_replace("\\r\\n", "\r\n", $s);
	      $s = str_replace("\\'", "''", $s);
	      $s = str_replace('\\"', '"', $s);
	      $result = $db->Execute($s);
	      if (!$result) {
		die(ilang('invalid_query', $s).' -- '.$db->ErrorMsg());
	      }
	    }
	  }
	  fclose($handle);
	  echo " [" . ilang('done') . "]</p>";
	}
	else
	  {
	    echo ilang('install_admin_error_schema') . "</p>";
	  }


	echo "<p>" . ilang('install_admin_set_account');

	$sql_error = false;
	if ($_POST['adminsalt'] == '1') 
	  {
	    $salt = substr(str_shuffle("23456789ABCDEFGHJKMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz@#$%^&*"),0,16);
	  }
	else
	  {
	    $salt = '';
	  }
	$sql = 'UPDATE ' . $db_prefix . 'users SET username = ?, password = ?, email = ? WHERE user_id = 1';
	$dbresult = $db->Execute($sql, array($_POST['adminusername'], md5($salt.$_POST['adminpassword']), $_POST['adminemail']));
	if (!$dbresult)
	  {
	    echo ilang('invalid_query', $db->$sql) ."</p>";
	    $sql_error = true;
	  }
	else
	  {
	    echo " [" . ilang('done') . "]</p>";
	  }

	echo "<p>" . ilang('install_admin_set_sitename');

	$query = "INSERT INTO ". $db_prefix ."siteprefs (sitepref_name, sitepref_value) VALUES (?,?)";
	$dbresult = $db->Execute($query, array('sitename', htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8')));
	if (!$dbresult)
	  {
	    echo ilang('invalid_query', $db->sql) ."</p>";
	    $sql_error = true;
	  }
	else
	  {
	    echo " [" . ilang('done') . "]</p>";
	  }
	$dbresult = $db->Execute($query, array('sitemask', $salt));
	if (!$dbresult)
	  {
	    echo ilang('invalid_query', $db->sql) ."</p>";
	    $sql_error = true;
	  }


	include_once(cms_join_path(CMS_INSTALL_BASE, 'schemas', 'createseq.php'));

	$db->Close();

	if (!$sql_error)
	  {
	    echo '<p class="success">' . ilang('success') . '!</p>';
	  }
	else
	  {
	    echo '<p class="error">' . ilang('invalid_querys') . '!</p>';
	  }
      }
  }
}
?>
