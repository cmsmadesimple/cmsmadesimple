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

class CMSInstallerPage
{
	var $number;
	var $smarty;
	var $errors;
	var $content;
	
	/**
	 * Class constructor
	*/
	function CMSInstallerPage($number, &$smarty, $errors)
	{
		$this->number = $number;
		$this->smarty = $smarty;
		$this->errors = $errors;
	}
	
	function displayHeader()
	{
		$this->smarty->display('header.tpl');
	}
	
	function displayContent()
	{
		echo $this->fetchContent();
	}
	
	/**
	 * Fetches the page content - assigns smarty variables, fetches content HTML
	*/
	function fetchContent()
	{
		$this->assignVariables();
		return $this->smarty->fetch('page' . $this->number . '.tpl');
	}
	
	/**
	 * Should be overridden by subpages - used to assing Smarty variables
	*/
	function assignVariables()
	{
		
	}
	
	/**
	 * Executed before the page smarty content is displayed
	*/
	function preContent()
	{
		
	}
}

?>
