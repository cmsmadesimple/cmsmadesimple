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
#$Id: CMSInstallPage7.class.php 311 2011-01-16 22:55:19Z wishy $

class CMSInstallerPage7 extends CMSInstallerPage
{
	function assignVariables()
	{
		$values = array();
		$values['umask'] = isset($_POST['umask']) ? $_POST['umask'] : '';
		$values['admininfo']['username'] = $_POST['adminusername'];
		$values['admininfo']['email'] = $_POST['adminemail'];
		$values['admininfo']['password'] = $_POST['adminpassword'];

		$base_url = 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - 18);
		if( $_POST['docroot'] ) {
		  $base_url = str_replace(" ", "%20", rtrim($_POST['docroot'], '/'));
		}
		$link = '<a href="'.$base_url.'">'.ilang('cms_site').'</a>';

		$this->smarty->assign('base_url', $base_url);
		$this->smarty->assign('values', $values);
		$this->smarty->assign('site_link', $link);

		$this->smarty->assign('errors', $this->errors);
	}

	function preContent(&$db)
	{
		// check if db info is correct as it should at this point to prevent an undeleted installation dir
		// to be used for sending spam by messing up $_POST variables
		$db = ADONewConnection($_POST['dbms'], 'pear:date:extend:transaction');
		if (! empty($_POST['db_port'])) $db->port = $_POST['db_port'];
		if (! $db->Connect($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database']))
		{
			$this->errors[] = ilang('could_not_connect_db');
			return;
		}

		$newconfig = cmsms()->GetConfig();
		$newconfig['dbms'] = trim($_POST['dbms']);
		$newconfig['db_hostname'] = trim($_POST['host']);
		$newconfig['db_username'] = trim($_POST['username']);
		$newconfig['db_password'] = trim($_POST['password']);
		$newconfig['db_name'] = trim($_POST['database']);
		$newconfig['db_prefix'] = trim($_POST['prefix']);

		$n = (int)$_POST['db_port'];
		if( $n > 0 ) {
		  $newconfig['db_port'] = $n;
		}

		$t = trim($_POST['docroot']);
		if( $t ) {
		  $newconfig['root_url'] = trim($t,'/');
		}

		$tmp = trim($_POST['querystr']);
		if( $tmp != 'page' )
		  {
		    $newconfig['query_var'] = $_POST['querystr'];
		  }
		$newconfig['timezone'] = $_POST['timezone'];
		$newconfig->save();

		if (file_exists(cms_join_path(TMP_CACHE_LOCATION, 'SITEDOWN')))
		{
			if (!unlink(cms_join_path(TMP_CACHE_LOCATION, 'SITEDOWN')))
			{
				echo ilang('install_admin_sitedown');
			}
		}

		#Make sure $gCms->db is set
		#Do module installation
		if (isset($_POST["createtables"]) && $_POST['createtables'] != 0 )
		{
			$gCms = cmsms();
			global $DONT_LOAD_DB;
			$DONT_LOAD_DB = 'force';
			$db = $gCms->GetDb();
			$db->SetFetchMode(ADODB_FETCH_ASSOC);

			echo '<p>' . ilang('install_admin_update_hierarchy');
			$contentops = cmsms()->GetContentOperations();
			$contentops->SetAllHierarchyPositions();
			echo " [" . ilang('done') . "]</p>";
			echo '<p>' . ilang('install_admin_set_core_event');

			Events::SetupCoreEvents();

			echo " [" . ilang('done') . "]</p>";
			echo '<p>' . ilang('install_admin_install_modules');
			$modops = $gCms->GetModuleOperations();
			$modops->LoadModules(TRUE);
			$allmodules = $modops->GetAllModuleNames();
			if( is_array($allmodules) && count($allmodules) )
			  {
			    foreach( $allmodules as $module_name )
			      {
				$obj = $modops->get_module_instance($module_name,'',TRUE);
			      }
			  }
			echo " [" . ilang('done') . "]</p>";

			echo '<p>' . ilang('install_admin_clear_cache');
			$contentops->ClearCache();
			echo " [" . ilang('done') . "]</p>";

			// Insert new site preferences
			set_site_preference('global_umask', $_POST['umask']);
			set_site_preference('frontendlang', $_POST['frontendlang']);
			set_preference(1, 'default_cms_language', $_POST['default_cms_lang']);
		}
		else
		{
		  $this->smarty->assign('tables_notinstalled',1);
		}
		  

		$link = str_replace(" ", "%20", $_POST['docroot']);

		if ( ($_POST['email_accountinfo'] == 1) && (! empty($_POST['adminemail'])) )
		{

			echo '<p>' . ilang('install_admin_emailing');
			$to      = $_POST['adminemail'];
			$subject = html_entity_decode(ilang('email_accountinfo_subject'));
			$message = ilang('email_accountinfo_message', $_POST['adminusername'], $_POST['adminpassword'], "$link/admin/");
			$message = html_entity_decode($message, ENT_QUOTES); // Encoded from TC
			echo (
				@mail($to, $subject, $message)
					? " [" . ilang('done') . "]"
					: "<strong>[" . ilang('failed') . "]</strong>"
			);
			echo "</p>";

		}
	}
}
?>
