<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
		$this->currentPage = isset($_POST['page']) && (int) $_POST['page'] <= $this->numberOfPages ? (int) $_POST['page'] : 1;

		$this->smarty   = NULL;
		$this->errors   = array();
	}
	
	/**
	 * Loads smarty
	 * @return boolean whether loading succeeded
	 */
	function loadSmarty()
	{
		if (! is_readable(cms_join_path(CMS_BASE, 'lib', 'smarty', 'Smarty.class.php')))
		{
			$this->showErrorPage(cms_join_path(CMS_BASE, 'lib', 'smarty', 'Smarty.class.php') . 'cannot be found, exiting');
			return false;
		}
		require_once cms_join_path(CMS_BASE, 'lib', 'smarty', 'Smarty.class.php');
		$smarty =& new Smarty();
		
		$smarty->compile_dir = cms_join_path(CMS_BASE, 'tmp', 'templates_c');
		if (! is_writable($smarty->compile_dir))
		{
			$this->showErrorPage($smarty->compile_dir . ' is not writable, exiting');
			return false;
		}
		$smarty->cache_dir = cms_join_path(CMS_BASE, 'tmp', 'cache');
		if (! is_writable($smarty->cache_dir))
		{
			$this->showErrorPage($smarty->cache_dir . ' is not writable, exiting');
			return false;
		}
		$this->smarty =& $smarty;
		return true;
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
		// Load smarty, exit if failed
		if (! $this->loadSmarty())
		{
			return;
		}
		
		// Process submitted data
		$db = $this->processSubmit();
		
		// Test for sessions on the first page
		if ($this->currentPage == 1)
		{
			@session_start();
			if (! isset($_GET['sessiontest']))
			{
				$_SESSION['test'] = TRUE;
				$scheme = ((! isset($_SERVER['HTTPS'])) || strtolower($_SERVER['HTTPS']) != 'on') ? 'http' : 'https';
				$redirect = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?sessiontest=1&' . SID;
				header("Location: $redirect");
			}
		}
		
		// Create the (current) page object
		require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'classes', 'CMSInstallerPage' . $this->currentPage . '.class.php');
		$classname = 'CMSInstallerPage' . $this->currentPage;
		$page =& new $classname($this->smarty, $this->errors);
		
		// Assign smarty variables (used in header)
		$this->smarty->assign('number_of_pages', $this->numberOfPages);
		$this->smarty->assign('current_page', $this->currentPage);
		
		// Output HTML
		$this->smarty->display('installer_start.tpl'); // display page start
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
					$this->errors[] = 'Username not given!';
					$this->currentPage = 2;
				}
				else if ( !preg_match( "/^[a-zA-Z0-9]+$/", trim($_POST['adminusername']) ) )
				{
					$this->errors[] = 'Username contains illegal characters!';
					$this->currentPage = 2;
				}
				elseif (trim($_POST['adminpassword']) == '' || trim($_POST['adminpasswordagain']) == '')
				{
					$this->errors[] = 'Not both password fields given!';
					$this->currentPage = 2;
				}
				elseif ($_POST['adminpassword'] != $_POST['adminpasswordagain'])
				{
					$this->errors[] = 'Password fields do not match!';
					$this->currentPage = 2;	
				}
				if (isset($_POST['email_accountinfo']) && trim($_POST['adminemail'] == ''))
				{
					$this->errors[] = 'E-mail accountinfo selected, but no E-mail address given!';
					$this->currentPage = 2;	
				}
				break;
			case 4:
				if( isset($_POST['prefix']) 
				    && $_POST['prefix'] != '' 
				    && !preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['prefix'])) )
				{
				  $this->errors[] = 'Database prefix contains invalid characters'; 
				  $this->currentPage = 3;
				}
				if ($_POST['dbms'] == '')
				{
					$this->errors[] = 'No dbms selected!';
					$this->currentPage = 3;
					return;
				}
				
				$db =& ADONewConnection($_POST['dbms'], 'pear:date:extend:transaction');
				$result = $db->Connect($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database']);
				if (! $result)
				{
					$this->errors[] = 'Could not connect to the database. Verify that username and password are correct, and that the user has access to the given database.';
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
						$this->errors[] = 'Could not drop a table.  Verify that the user has privileges to drop tables in the given database.';
						$this->currentPage = 3;
						return;
					}
				}
				else
				{
					//could not create table
					$this->errors[] = 'Could not create a table.  Verify that the user has privileges to create tables in the given database.';
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
