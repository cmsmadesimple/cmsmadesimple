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

class CMSInstallerPage1 extends CMSInstallerPage
{
	var $continueon;
	var $special_failed;
	var $images;
	
	/**
	 * Class constructor
	 * @var object $smarty
	 * @var array  $errors
	 */
	function CMSInstallerPage1(&$smarty, $errors)
	{
		$this->CMSInstallerPage(1, $smarty, $errors);
		$this->continueon = true;
		$this->special_failed = false;
		$this->images = array();
		$this->images['true']   = '<img src="images/true.gif" alt="Success" height="16" width="16" border="0" />';
		$this->images['false']  = '<img src="images/false.gif" alt="Failure" height="16" width="16" border="0" />';
		$this->images['red']    = '<img src="images/red.gif" alt="Failure" height="16" width="16" border="0" />';
		$this->images['yellow'] = '<img src="images/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
		$this->images['green']  = '<img src="images/green.gif" alt="Success" height="16" width="16" border="0" />';
	}
	
	function assignVariables()
	{
		$settings = array('required' => array(), 'recommended' => array());
		
		$settings['required'][] = $this->testBoolean(1, 'Checking for PHP version 4.3+', (@version_compare(phpversion(), '4.3.0') > -1));
		$settings['required'][] = $this->testBoolean(1, 'Checking for Session Functions', function_exists('session_start'));
		$settings['required'][] = $this->testBoolean(1, 'Checking for md5 Function', function_exists('md5'));
		
		
		/*
		 * Required Settings
		 */

		$settings['required'][] = 
		  $this->testBoolean(1,
				     'Checking write permission on '.
				     TMP_CACHE_LOCATION,
				     is_writable(TMP_CACHE_LOCATION));
		$settings['required'][] = 
		  $this->testBoolean(1,
				     'Checking write permission on '.
				     TMP_TEMPLATES_C_LOCATION,
				     is_writable(TMP_TEMPLATES_C_LOCATION));
		$settings['required'][] = 
		  $this->testBoolean(1,
				     'Checking write permission on '.
				     CONFIG_FILE_LOCATION,
				     is_writable(CONFIG_FILE_LOCATION),
		  'This file may not exist yet.  If it does not, you should
                   create an empty file with this name. Please also ensure 
                   that this file writable by the web server process.' );

/* old code? */
/*
		foreach ($files as $file)
		{
			$settings['required'][] = $this->testBoolean(1, "Checking write permission on $file", is_writable($file));
		}
*/
		
		/*
		 * Recommended Settings
		 */
		$settings['recommended'][] = $this->testBoolean(0, 'Checking for basic XML (expat) support', function_exists('xml_parser_create'), 'XML support is not compiled into your php install.  You can still use the system, but will not be able to use any of the remote module installation functions.');
		$settings['recommended'][] = $this->testIniBoolean(0, 'Checking file uploads', 'file_uploads', 'When file uploads are disabled you will not be able to use any of the file uploading facilities included in CMS Made Simple.  If possible, this restriction should be lifted by your system admin to properly use all file management features of the system.  Proceed with caution.');
		
		$settings['recommended'][] = $this->testIniRange(0, 'Checking PHP memory limit', 'memory_limit', '16M', '24M', 'You may not have enough memory to run CMSMS correctly, or with all of your desired addons. If possible, you should try to get your system admin to raise this value. Proceed with caution.');
		$settings['recommended'][] = $this->testIniRange(0, 'Checking max upload file size', 'upload_max_filesize', '2M', '10M', 'You will probably not be able to upload (larger) files using the included file management functions.  Please be aware of this restriction.');
		$f = cms_join_path(CMS_BASE, 'uploads');
		$settings['recommended'][] = $this->testBoolean(0, "Checking if $f is writable", is_writable($f), 'The uploads folder is not writable. You can still install the system, but you will not be able to upload files via the Admin Panel.');
		$f = cms_join_path(CMS_BASE, 'modules');
		$settings['recommended'][] = $this->testBoolean(0, "Checking if $f is writable", is_writable($f), 'The modules folder is not writable. You can still install the system, but you will not be able to upload modules via the Admin Panel.');
		$settings['recommended'][] = $this->testBoolean(0, 'Checking for file_get_contents', function_exists('file_get_contents'), 'The file_get_contents function was added in PHP 4.3 and although a workaround has been added that should allow most functionality that uses this function to work properly in PHP 4.2, it may be advisable to upgrade to PHP 4.3 or greater.');
		$settings['recommended'][] = $this->testBoolean(0, 'Checking if session.save_path is writable', is_writable(session_save_path()), 'Your session.save_path is "'.session_save_path().'". Not having this as writable may make logins to the Admin Panel not work. You may want to look into making this path writable if you have trouble logging into the Admin Panel.  This test may fail if safe_mode is enabled (see below).');
		ini_set('max_execution_time', '123');
		$result = (ini_get('max_execution_time') == 123);
		$settings['recommended'][] = $this->testBoolean(0, 'Checking if ini_set works', $result, 'Although the ability to override php ini settings is not mandatory, some addon (optional) functionality uses ini_set to extend timeouts, and allow uploading of larger files, etc.  You may have difficulty with some addon functionality without this capability.  This test may fail if safe_mode is enabled (see below).');
		$settings['recommended'][] = $this->testBoolean(0, 'Checking if sessions are enabled', isset($_GET['sessiontest']) && isset($_SESSION['test']), 'Although the PHP support for sessions is not mandatory, it is highly recommended. Logins and other things may slow down and you may have difficulty with some addon functionality without this capability.');
		$settings['recommended'][] = $this->testBoolean(0, 'Checking for tokenizer functions', function_exists('token_get_all'), 'Not having the tokenizer could cause pages to render as purely white.  We recommend you have this installed, but your website may work fine without it.');
		$settings['recommended'][] = $this->testIniBoolean(0, 'Checking for Safe mode', 'safe_mode', 'PHP Safe mode could create some problems with uploading files and other functions. It all depends on how strict your server safe mode settings are.', true);
		
		// assign settings
		$this->smarty->assign('images', $this->images);
		$this->smarty->assign('settings', $settings);
		$this->smarty->assign('continueon', $this->continueon);
		$this->smarty->assign('special_failed', $this->special_failed);
	}
	
	/**
	 * @return object 
	 * @var boolean $required
	 * @var string  $title
	 * @var boolean $result
	 * @var string  $message
	*/
	function & testBoolean($required, $title, $result, $message = '')
	{
		$test =&new StdClass();
		$test->title = $title;
		if ((bool) $result == false)
		{
			$required == true ? $this->continueon = false : $this->special_failed = true;
			
			if (trim($message) != '')
			{
				$test->message = $message;
			}
			$test->resultimage = $required ? $this->images['false'] : $this->images['yellow'];
		}
		else
		{
			$test->resultimage = $required ? $this->images['true'] : $this->images['green'];
		}
		return $test;
	}
	
	/**
	 * @return object 
	 * @var boolean $required
	 * @var string  $title
	 * @var boolean $result
	 * @var string  $message
	 * @var boolean $negative_test
	*/
	function & testIniBoolean($required, $title, $varname, $message = '', $negative_test = false)
	{
		$str = ini_get($varname);
		$result = $negative_test ? (! (bool) $str) : (bool) $str;
		return $this->testBoolean($required, $title, $result, $message);
	}
	
	/**
	 * @return object 
	 * @var boolean $required
	 * @var string  $title
	 * @var mixed   $value
	 * @var mixed   $minimum
	 * @var mixed   $recommended
	 * @var string  $message
	 * @var boolean $test_as_bytes
	*/
	function & testRange($required, $title, $value, $minimum, $recommended, $message = '', $test_as_bytes = false)
	{
		$test =& new StdClass();
		
		$test->title = $title .  " (min $minimum, recommend $recommended)";
		$test->value = $value;
		
		if ($test_as_bytes)
		{
			$value = $this->returnBytes($value);
			$minimum = $this->returnBytes($minimum);
			$recommended = $this->returnBytes($recommended);
		}
		
		if ($value < $minimum)
		{
			$test->resultimage = $this->images['red'];
			$required == true ? $this->continueon = false : $this->special_failed = true;
		}
		elseif ($value < $recommended)
		{
			$test->resultimage = $this->images['yellow'];
		}
		else
		{
			$test->resultimage = $this->images['green'];
		}
		
		if ($value < $recommended && trim($message) != '')
		{
			$test->message = $message;
		}
		
		return $test;
	}
	
	/**
	 * @return object 
	 * @var boolean $required
	 * @var string  $title
	 * @var string  $varname
	 * @var string  $minimum
	 * @var string  $recommended
	 * @var string  $message
	 * @var boolean $test_as_bytes
	*/
	function & testIniRange($required, $title, $varname, $minimum, $recommended, $message = '', $test_as_bytes = true)
	{
		$str = ini_get($varname);
		if ($str == '')
		{
			$error = 'Could not retrieve a value.... passing anyways.';
			$required = false;
			if ((string) get_cfg_var($varname) != '')
			{
				$str = (string) get_cfg_var($varname);
				$error .= '<br />Displaying the value originally set in the config file (this may not be accurate).';
			}
		}
		$test =& $this->testRange($required, $title, $str, $minimum, $recommended, $message, $test_as_bytes);
		if (isset($error))
		{
			$test->error = $error;
		}
		return $test;
	}
	
	/**
	 * @var string $val
	 * @return int
	 */
	function returnBytes($val) 
	{
		if (is_string($val) && $val != '')
		{
			$val = trim($val);
			$last = strtolower($val{strlen($val)-1});
			switch($last) 
			{
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
		}
		return (int) $val;
	}
}

?>
