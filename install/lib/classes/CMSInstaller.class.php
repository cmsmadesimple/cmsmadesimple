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

/**
 * Uses the CMSInstallerPage class
 */
require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'classes', 'CMSInstallerPage.class.php');

/**
 * CMS Made Simple Installer
 */
class CMSInstaller
{
	/**
	 * @var int the total number of installer pages
	 */
	var $numberOfPages;
	/**
	 * @var int the current page
	 */
	var $currentPage;
	/**
	 * @var object the Smarty object
	 */
	var $smarty;
	/**
	 * @var array errors to be shown
	 */
	var $errors;
	
	/**
	 * Class constructor
	 */
	function CMSInstaller()
	{
		$this->numberOfPages = 5;
		$this->currentPage = (isset($_POST['page']) && (int) $_POST['page'] <= $this->numberOfPages) ? (int) $_POST['page'] : 1;

		$this->smarty   = NULL;
		$this->errors   = array();
	}

	/**
	 * Load language dropdown
	 */
	function dropdown_lang()
	{
		$base = CMS_INSTALL_BASE . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
		$languages = array();

		if ($handle = @opendir($base . 'ext' . DIRECTORY_SEPARATOR))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ( ($file != '..') && ($file != '.') && (is_file($base . 'ext' . DIRECTORY_SEPARATOR . $file)) )
				{
					$languages[] = basename($file, '.php');
				}
			}
			closedir($handle);
		}
		natsort($languages);

		if (is_readable($base . 'en_US.php'))
		{
			array_unshift($languages, 'en_US');
		}

		return $languages;
	}

	/**
	 * Shows an error page (without use of Smarty)
	 * @var string $error
	 */
	function showErrorPage($error)
	{
		include cms_join_path(CMS_INSTALL_BASE, 'templates', 'installer_start.tpl');
		echo '<p class="error">' . $error . '</p>';
		include cms_join_path(CMS_INSTALL_BASE, 'templates', 'installer_end.tpl');
	}

	/**
	 * Runs the installer
	 */
	function run()
	{
		global $gCms;

		$this->smarty = &$gCms->GetSmarty();
		$this->smarty->template_dir = CMS_INSTALL_BASE . DIRECTORY_SEPARATOR . 'templates';
		$this->smarty->caching = false;
		$this->smarty->force_compile = true;

		// Process submitted data
		$db = $this->processSubmit();

		// Create the (current) page object
		require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'classes', 'CMSInstallerPage' . $this->currentPage . '.class.php');
		$classname = 'CMSInstallerPage' . $this->currentPage;
		$page =& new $classname($this->smarty, $this->errors);

		// Assign smarty variables
		$this->smarty->assign('number_of_pages', $this->numberOfPages);
		$this->smarty->assign('current_page', $this->currentPage);
		$this->smarty->assign('cms_version', CMS_VERSION);
		$this->smarty->assign('cms_version_name', CMS_VERSION_NAME);

		// Output HTML
		$this->smarty->display('header.tpl');          // display header
		$page->preContent($db);		                   // pre-content
		$page->displayContent();                       // display page content
		$this->smarty->display('installer_end.tpl');   // display page end
	}

	/**
	 * Processes submitted forms, redirects to previous page if needed
	 * @return mixed Returns a ADOdb Connection object (for re-use) if created
	 */
	function processSubmit()
	{
		switch ($this->currentPage)
		{
			case 2:
				if (isset($_POST['recheck']))
				{
					$this->currentPage = 1;
				}
				break;
			case 3:
				if (trim($_POST['adminusername']) == '')
				{
					$this->errors[] = lang('test_username_not_given');
				}
				else if ( !preg_match( "/^[a-zA-Z0-9]+$/", trim($_POST['adminusername']) ) )
				{
					$this->errors[] = lang('test_username_illegal');
				}
				elseif (trim($_POST['adminpassword']) == '' || trim($_POST['adminpasswordagain']) == '')
				{
					$this->errors[] = lang('test_not_both_passwd');
				}
				elseif ($_POST['adminpassword'] != $_POST['adminpasswordagain'])
				{
					$this->errors[] = lang('test_passwd_not_match');
				}
				if (isset($_POST['email_accountinfo']) && trim($_POST['adminemail'] == ''))
				{
					$this->errors[] = lang('test_email_accountinfo');
				}

				if (count($this->errors) > 0)
				{
					$this->currentPage = 2;
				}
				break;
			case 4:
				if( isset($_POST['prefix'])
				    && $_POST['prefix'] != ''
				    && !preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['prefix'])) )
				{
				  $this->errors[] = lang('test_database_prefix');
				  $this->currentPage = 3;
				}
				if ($_POST['dbms'] == '')
				{
					$this->errors[] = lang('test_no_dbms');
					$this->currentPage = 3;
					return;
				}

				$db =& ADONewConnection($_POST['dbms'], 'pear:date:extend:transaction');
				$result = $db->Connect($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database']);
				if (! $result)
				{
					$this->errors[] = lang('test_could_not_connect_db');
					$this->currentPage = 3;
					return;
				}

				//Try to create and drop a dummy table (with appropriate prefix)
				$db_prefix = $_POST['prefix'];
				@$db->Execute('DROP TABLE ' . $db_prefix . 'dummyinstall');
				$result = $db->Execute('CREATE TABLE ' . $db_prefix . 'dummyinstall (i int)');
				if ($result)
				{
					$result = $db->Execute('DROP TABLE ' . $db_prefix . 'dummyinstall');
					if (!$result)
					{
						//could not drop table
						$this->errors[] = lang('test_could_not_drop_table');
						$this->currentPage = 3;
						return;
					}
				}
				else
				{
					//could not create table
					$this->errors[] = lang('test_could_not_create_table');
					$this->currentPage = 3;
					return;
				}
				return $db;
				break;
		}
		return NULL;
	}
}
?>
