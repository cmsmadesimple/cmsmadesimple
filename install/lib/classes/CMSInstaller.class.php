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
#$Id: CMSInstaller.class.php 226 2009-08-09 20:01:18Z alby $

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
	 * @var boolean debug
	 */
	var $debug;

	/**
	 * Class constructor
	 */
	function CMSInstaller($maxpages = 7, $debug = false)
	{
		$this->numberOfPages = $maxpages;
		$this->currentPage = (isset($_POST['page']) && (int) $_POST['page'] <= $this->numberOfPages) ? (int) $_POST['page'] : 1;

		$this->smarty   = NULL;
		$this->errors   = array();
		$this->debug	= $debug;
	}

	/**
	 * Load language dropdown
	 */
	function dropdown_lang()
	{
		$base = cms_join_path(CMS_INSTALL_BASE, 'lang');
		$ext = cms_join_path($base, 'ext');
		$languages = array();

		if ($this->debug) $handle = opendir($ext);
		else              $handle = @opendir($ext);
		if ($handle)
		{
			while (false !== ($file = readdir($handle)))
			{
				if ( ($file != '..') && ($file != '.') && ($file != basename($file, '.php')) &&
					(is_file(cms_join_path($ext, $file))) )
				{
					$languages[] = basename($file, '.php');
				}
			}
			closedir($handle);
		}
		natsort($languages);

		if (is_readable(cms_join_path($base, 'en_US.php')))
		{
			array_unshift($languages, 'en_US');
		}

		return $languages;
	}

	/**
	 * Runs the installer
	 */
	function run($process = 'install')
	{
		global $gCms;

		$this->smarty = cms_smarty();
		$this->smarty->template_dir = cms_join_path(CMS_INSTALL_BASE, 'templates');
		$this->smarty->caching = false;
		$this->smarty->force_compile = true;
		$this->smarty->debugging = false;

		// Process submitted data
		$db = $this->processSubmit($process);

		// Create the (current) page object
		require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'classes', 'CMS'.ucfirst($process).'Page'.$this->currentPage.'.class.php');
		$classname = 'CMSInstallerPage' . $this->currentPage;
		$page = new $classname($this->smarty, $this->errors, $this->debug);

		// Assign smarty variables
		$this->smarty->assign('number_of_pages', $this->numberOfPages);
		$this->smarty->assign('current_page', $this->currentPage);
		$this->smarty->assign('cms_version', CMS_VERSION);
		$this->smarty->assign('cms_version_name', CMS_VERSION_NAME);

		// Output HTML
		$this->smarty->display($process .'header.tpl');	// display header
		$page->preContent($db);				// pre-content
		$page->displayContent($process);		// display page content
		$this->smarty->display('installer_end.tpl');	// display page end
	}

	/**
	 * Processes submitted forms, redirects to previous page if needed
	 * @return mixed Returns a ADOdb Connection object (for re-use) if created
	 */
	function processSubmit($process = 'install')
	{
		if ($process == 'install')
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
					if (isset($_POST['recheck']))
					{
						$this->currentPage = 2;
					}
					break;
				case 4:
					if (isset($_POST['umask']) && trim($_POST['umask']) == '')
					{
						$this->errors[] = lang('test_umask_not_given');
						$this->currentPage = 3;
					}
					if (isset($_POST['recheck']))
					{
						$this->currentPage = 3;
					}
					break;
				case 5:
					$_POST['adminusername'] = cleanValue(trim($_POST['adminusername']));
					if ($_POST['adminusername'] == '')
					{
						$this->errors[] = lang('test_username_not_given');
					}
					elseif ( !preg_match("/^[a-zA-Z0-9\._ ]+$/", $_POST['adminusername']) )
					{
						$this->errors[] = lang('test_username_illegal');
					}

					if (trim($_POST['adminpassword']) == '' || trim($_POST['adminpasswordagain']) == '')
					{
						$this->errors[] = lang('test_not_both_passwd');
					}
					elseif ($_POST['adminpassword'] != $_POST['adminpasswordagain'])
					{
						$this->errors[] = lang('test_passwd_not_match');
					}

					$_POST['adminemail'] = trim($_POST['adminemail']);
					if (!empty($_POST['adminemail']) && !is_email($_POST['adminemail']))
					{
						$this->errors[] = lang('invalidemail');
					}
					if (isset($_POST['email_accountinfo']) && empty($_POST['adminemail']))
					{
						$this->errors[] = lang('test_email_accountinfo');
					}

					if (count($this->errors) > 0)
					{
						$this->currentPage = 4;
					}
					break;
				case 6:
					if( isset($_POST['prefix'])
					    && $_POST['prefix'] != ''
					    && !preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['prefix'])) )
					{
						$this->errors[] = lang('test_database_prefix');
						$this->currentPage = 5;
						return;
					}
					if (trim($_POST['dbms']) == '')
					{
						$this->errors[] = lang('test_no_dbms');
						$this->currentPage = 5;
						return;
					}

					$dsn = $_POST['dbms'].'://';
					if( $_POST['dbms'] == 'sqlite' )
					{
						$dsn .= urlencode(cms_join_path(CMS_BASE,'tmp',$_POST['database'])) .'/';
					}
					else
					{
						$dsn .= $_POST['username'].':'.rawurlencode($_POST['password']).'@'.$_POST['host'].'/'.$_POST['database'];
						if(! empty($_POST['db_port']))
						{
							$dsn .= '?port='.$_POST['db_port'];
						}
						elseif( (! empty($_POST['db_socket'])) && ($_POST['dbms'] == 'mysqli') )
						{
							$dsn .= '?socket='.$_POST['db_socket'];
						}
					}
					$db = ADONewConnection( $dsn );
					if (! $db)
					{
						$this->errors[] = lang('test_could_not_connect_db');
						$this->currentPage = 5;
						return;
					}
					//Try to create and drop a dummy table (with appropriate prefix)
					$db_prefix = trim($_POST['prefix']);
					@$db->Execute('DROP TABLE ' . $db_prefix . 'dummyinstall');
					@$result = $db->Execute('CREATE TABLE ' . $db_prefix . 'dummyinstall (i int)');
					if ($result)
					{
						$result = $db->Execute('DROP TABLE ' . $db_prefix . 'dummyinstall');
						if (!$result)
						{
							//could not drop table
							$this->errors[] = lang('test_could_not_drop_table');
							$this->currentPage = 5;
							return;
						}
					}
					else
					{
						//could not create table
						$this->errors[] = lang('test_could_not_create_table');
						$this->currentPage = 5;
						return;
					}
					return $db;
					break;
			}

		}
		elseif ($process == 'upgrade')
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
					if (isset($_POST['recheck']))
					{
						$this->currentPage = 2;
					}
					break;
				case 4:
					if (isset($_POST['recheck']))
					{
						$this->currentPage = 3;
					}
					break;
				case 5:
					if (isset($_POST['recheck']))
					{
						$this->currentPage = 4;
						return;
					}

					global $gCms;
					$db =& $gCms->GetDB();
					return $db;
					break;
				case 6:
					if (isset($_POST['recheck']))
					{
						$this->currentPage = 5;
						return;
					}

					global $gCms;
					$db =& $gCms->GetDB();
					return $db;
					break;
				 case 7:
					if (isset($_POST['recheck']))
					{
						$this->currentPage = 6;
					}
					break;
			}

		}

		return NULL;
	}
}
?>