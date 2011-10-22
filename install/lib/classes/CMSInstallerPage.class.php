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
#$Id: CMSInstallerPage.class.php 296 2010-10-17 22:31:18Z calguy1000 $

/**
 * CMS Made Simple installer page base class
*/
class CMSInstallerPage
{
	var $number;
	var $smarty;
	var $errors;
	var $debug;
	private $_installer;

	/**
	 * Class constructor
	*/
	function CMSInstallerPage(CMSInstaller& $installer, $number, &$smarty, $errors, $debug)
	{
	  $this->_installer = $installer;
	  $this->number = $number;
	  $this->smarty =& $smarty;
	  $this->errors = $errors;
	  $this->debug  = $debug;
	}

	/**
	 * Get the instance of the installer object
	 */
	public function &get_installer()
	{
	  return $this->_installer;
	}

	/**
	 * Displays the page content, assigns smarty variables and displays the template
	*/
	function displayContent($process = 'install')
	{
		$this->assignVariables();
		$this->smarty->display($process . $this->number . '.tpl');
	}

	/**
	 * Should be overridden by subpages - used to assing Smarty variables
	*/
	function assignVariables()
	{

	}

	/**
	 * Should be overridden by subpages - executed before the page smarty content is displayed
	 * @param mixed ADOdb object when created in CMSInstaller::processSubmit or NULL
	*/
	function preContent(&$db)
	{

	}
}
?>
