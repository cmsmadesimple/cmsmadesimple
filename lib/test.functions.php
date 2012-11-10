<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id: test.functions.php 5128 2008-10-08 21:14:46Z alby $

/**
 * Handles test functions and values for CMSMS
 *
 * @package CMS
 * @ignore
 */

$lang_fn = 'lang';
global $CMS_INSTALL_PAGE;
if( isset($CMS_INSTALL_PAGE) )
	{
		$lang_fn = 'ilang';
	}

/**
 * A class for working with tests
 * 
 * @package CMS
 * @internal
 */
class CmsInstallTest
{
	public $title = null;
	public $res = null;
	public $value = null;
	public $secondvalue = null;
	public $message = null;
	public $ini_val = null;
	public $error = null;
    public $display_value = true;
}

/**
 * Array with PHP driver => DB server
 *
 * @return array
*/
function getSupportedDBDriver()
{
	return array('mysqli'=>'mysql', 'mysql'=>'mysql' );
}


/**
 * Array with minimum and recommended values
 *
 * @internal
 * @return array
 * @param string $property
 */
function getTestValues( $property )
{
	$range = array(
		'php_version'			=> array('minimum'=>'5.2.4', 'recommended'=>'5.3.2'),
		'gd_version'			=> array('minimum'=>2),
		'memory_limit'			=> array('minimum'=>'16M', 'recommended'=>'24M'),
		'max_execution_time'	=> array('minimum'=>30, 'recommended'=>60),
		'post_max_size'			=> array('minimum'=>'2M', 'recommended'=>'10M'),
		'upload_max_filesize'	=> array('minimum'=>'2M', 'recommended'=>'10M'),

		'mysql_version'			=> array('minimum'=>'3.23', 'recommended'=>'4.1'),
		'pgsql_version'			=> array('minimum'=>'7.4', 'recommended'=>'8'),
		'sqlite_version'		=> array('minimum'=>'', 'recommended'=>''),
	);

	if(array_key_exists($property, $range))
	{
		if(empty($range[$property]['recommended']))
		{
			$range[$property]['recommended'] = $range[$property]['minimum'];
		}
		return array($range[$property]['minimum'], $range[$property]['recommended']);
	}

	return array();
}

/**
 * Test a php global.

 * @return array
 * @param mixed   $result
 * @param boolean $set
 */
function testGlobal( $result, $set = false )
{
	static $continueon = true;
	static $special_failed = false;

	if( ($set) && (is_array($result)) )
	{
		$return = array($continueon, $special_failed);
		list($continueon, $special_failed) = $result;
		return $return;
	}

	if($result === '')
	{
		return array($continueon, $special_failed);
	}
	elseif($result == true)
	{
		$continueon = false;
	}
	elseif($result == false)
	{
		$special_failed = true;
	}

	return array($continueon, $special_failed);
}


/**
 * @return boolean
 * @param string $test
*/
function extension_loaded_or( $test )
{
	$a = extension_loaded(strtolower($test));
	$b = extension_loaded(strtoupper($test));
	return (bool)($a | $b);
}

/**
 * @return string
 * @param object  $test
 * @param boolean $required
 * @param string  $message
 * @param string  $error_fragment
 * @param string  $error
*/
function getTestReturn( &$test, $required, $message = '', $error_fragment = '', $error = '' )
{
	global $lang_fn;
	switch($test->res)
	{
		case 'green':
			list($test->continueon, $test->special_failed) = testGlobal('');
			$test->res_text = $lang_fn('success');
			break;
		case 'yellow':
		case 'red':
			list($test->continueon, $test->special_failed) = testGlobal($required);
			if($test->res == 'yellow')
			{
				$test->res_text = $lang_fn('caution');
			}
			elseif($test->res == 'red')
			{
				$test->res_text = $lang_fn('failure');
			}

			if(trim($message) != '')
			{
				$test->message = $message;
			}
			if(trim($error_fragment) != '')
			{
				$test->error_fragment = $error_fragment;
			}
			if(trim($error) != '')
			{
				$test->error = $error;
			}
			break;
	}

	return true;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param string  $db
 * @param string  $message
*/
function & testSupportedDatabase( $required, $title, $db = false, $message = '' )
{
	global $lang_fn;
	
	$drivers = getSupportedDBDriver();

//TODO?
	if($db)
	{
		$serverInfo = $db->ServerInfo();
		$_test = testConfig('', 'dbms');
		if(! empty($_test->value))
		{
			$dbms = $_test->value;
			list($minimum, $recommended) = getTestValues($drivers[$dbms].'_version');
			$test = testVersionRange('', $title, $serverInfo['version'], $message, $minimum, $recommended, false);
			$test->opt = $serverInfo['description'];
			$test->res = 'green';
			getTestReturn($test, $required);
			return $test;
		}
		$test = new CmsInstallTest();
		$test->title = $title;
		if($required)
		{
			$test->res = 'red';
		}
		else
		{
			$test->res = 'yellow';
		}
		getTestReturn($test, $required, $message);
		return $test;
	}
//TODO

	$test = new CmsInstallTest();
	$test->title = $title;

	if(count($drivers) > 0)
	{
		$return = array();
		foreach($drivers as $driver=>$server)
		{
			if(extension_loaded_or($driver))
			{
				$return[] = $driver;
			}
		}

		$test->value = implode(',', $return);
		$test->secondvalue = $return;

		if(count($return) > 0)
		{
			$test->res = 'green';
		}
		else
		{
			if($required)
			{
				$test->res = 'red';
			}
			else
			{
				$test->res = 'yellow';
			}
		}
		getTestReturn($test, $required, $message, 'DB_driver_missing');
	}
	else
	{
		$test->res = 'red';
		getTestReturn($test, $required, $message, 'DB_driver_missing', $lang_fn('no_db_driver'));
	}

	return $test;
}

/**
 * @return string
 * @param integer $info
*/
function getEmbedPhpInfo( $info = INFO_ALL )
{
	/**
	 * callback function to eventually add an extra space in passed <td class="v">...</td>
	 * after a ";" or "@" char to let the browser split long lines nicely
	 */
	function _sysinfo_phpinfo_v_callback($matches)
	{
		$matches[2] = preg_replace('%(?<!\s)([;@])(?!\s)%', "$1 ", $matches[2]);
		return $matches[1].$matches[2].$matches[3];
	}

	ob_start();
	phpinfo($info);
	$output = preg_replace(array('/^.*<body[^>]*>/is', '/<\/body[^>]*>.*$/is'), '', ob_get_clean(), 1);

	$output = preg_replace('/width="[0-9]+"/i', 'width="85%"', $output);
	$output = str_replace('<table border="0" cellpadding="3" width="85%">', '<table class="phpinfo">', $output);
	$output = str_replace('<hr />', '', $output);
	$output = str_replace('<tr class="h">', '<tr>', $output);
	$output = str_replace('<a name=', '<a id=', $output);
	$output = str_replace('<font', '<span', $output);
	$output = str_replace('</font', '</span', $output);
	$output = str_replace(',', ', ', $output);
	// match class "v" td cells an pass them to callback function
	return preg_replace_callback('%(<td class="v">)(.*?)(</td>)%i', '_sysinfo_phpinfo_v_callback', $output);
}

/**
 * @return mixed
 * @param string $module
*/
function getApacheModules( $module = false )
{
	if(function_exists('apache_get_modules'))
	{
		$modules = apache_get_modules();
		if($module)
		{
			if(in_array($module, $modules)) return true;
			else return false;
		}
		return $modules;
	}

	return false;
}

/**
 * @return object
 * @param string $title
 * @param string $value
 * @param string $return
 * @param string $message
 * @param string $error_fragment	
 * @param string $error
*/
function & testDummy( $title, $value, $return, $message = '', $error_fragment = '', $error = '' )
{
	$test = new CmsInstallTest();
	$test->title = $title;
	$test->value = $value;
	$test->secondvalue = null;
	$test->res = $return;

	getTestReturn($test, '', $message, $error_fragment, $error);
	return $test;
}

/**
 * @return object
 * @param string $title
 * @param string $varname
 * @param string $testfunc
 * @param string $message
*/
function & testConfig( $title, $varname, $testfunc = '', $message = '' )
{
	$gCms = cmsms();
	$config = $gCms->GetConfig();

	if( (isset($config[$varname])) && (is_bool($config[$varname])) )
	{
		$value = (true == $config[$varname]) ? 'true' : 'false';
	}
	else if(! empty($config[$varname]))
	{
		$value = $config[$varname];
		if(! empty($testfunc))
		{
			$test = $testfunc('', $title, $value);
		}
	}
	else
	{
		$value = '';
	}

	if(! isset($test))
	{
		$test = new CmsInstallTest();
		$test->title = $title;
		$test->value = $value;
		$test->secondvalue = null;
		if(trim($message) != '')
		{
			$test->message = $message;
		}
	}

	return $test;
}

/**
 * @return boolean
 * @param object $test
 * @param string $varname
 * @param string $type
*/
function testIni( &$test, $varname, $type, $opt = '' )
{
	$error = null;
	$str = ini_get($varname);

	switch($type)
	{
  	    case 'and':
			if($str === '') break;
			$str = (int) $str;
			if( empty($str) )
			{
				$str = (int)$str;
			}
			else
			{
				$str = (int)$str & (int)$opt;
			}
			break;
		case 'boolean':
			if( !is_numeric($str) )
			{
				$str2 = strtolower($str);
				switch( $str2 )
				{
				case 'on':
				case 'yes':
				case 'y':
					$str = TRUE;
					break;
				default:
					$str = FALSE;
				}
			}
			$str = (bool) $str;
			break;
		case 'integer':
			if($str === '') break;
			$str = (int) $str;
			break;
		case 'string':
			$str = (string) $str;
			if(empty($str))
			{
				#$error = lang('could_not_retrieve_a_value');
				if((string) get_cfg_var($varname) != '')
				{
					$str = (string) get_cfg_var($varname);
					#$error .= lang('displaying_the_value_originally');
				}
			}
			break;
	}

	$test->ini_val = $str;
	$test->error = $error;
	return true;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param mixed   $var
 * @param integer $bitmask
 * @param string  $message
 * @param boolean $ini
 * @param boolean $empty_is_ok
 * @param string  $error_fragment
*/
function & testIntegerMask($required,$title,$var,$mask,$message = '',$ini = true,$negate = false,$display_value = true,$error_fragment = '')
{
	$test = new CmsInstallTest();
	$test->title = $title;
	
	if( $ini )
	{
		testIni($test,$var,'and',$mask);
	}
	else
	{
		$test->ini_val = $var;
	}
	
	// did the ini test work.
	if( $test->ini_val === '' )
	{
		$test->value = 0;
	}
	else
	{
		$test->value = $test->ini_val;
	}

	$res = $test->value;
	if( $negate )
	{
		$res = !(int)$res;
	}

	if(empty($res))
	{
		if($required)
		{
			$test->res = 'red';
		}
		else
		{
			$test->res = 'yellow';
		}
	}
	else
	{
		$test->res = 'green';
	}

	$test->display_value = $display_value;
	getTestReturn($test, $required, $message, $error_fragment);
	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param mixed   $var
 * @param string  $message
 * @param boolean $ini
 * @param boolean $empty_is_ok
 * @param string  $error_fragment
*/
function & testInteger( $required, $title, $var, $message = '', $ini = true, $empty_is_ok = true, $error_fragment = '' )
{
	global $lang_fn;
	$test = new CmsInstallTest();
	$test->title = $title;

	if($ini)
	{
		testIni($test, $var, 'integer');
	}
	else
	{
		$test->ini_val = $var;
	}

	if($test->ini_val === '')
	{
		if($empty_is_ok) $test->value = $lang_fn('on');
		else $test->value = 0;
	}
	else
	{
		$test->value = $test->ini_val;
	}

	if(empty($test->value))
	{
		if($required)
		{
			$test->res = 'red';
		}
		else
		{
			$test->res = 'yellow';
		}
	}
	else
	{
		$test->res = 'green';
	}

	getTestReturn($test, $required, $message, $error_fragment);
	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param mixed   $var
 * @param string  $message
 * @param boolean $ini
 * @param boolean $code_empty
 * @param boolean $code_not_empty
 * @param string  $error_fragment
*/
function & testString( $required, $title, $var, $message = '', $ini = true, $code_empty = 'green', $code_not_empty = 'yellow', $error_fragment = '' )
{
	$test = new CmsInstallTest();
	$test->title = $title;

	if($ini)
	{
		testIni($test, $var, 'string');
	}
	else
	{
		$test->ini_val = $var;
	}

	$test->value = $test->ini_val;
	if(empty($test->value))
	{
		$test->res = $code_empty;
	}
	else
	{
		$test->value = str_replace(',', ', ', $test->value);
		$test->res = $code_not_empty;
	}

	getTestReturn($test, $required, $message, $error_fragment);
	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param mixed   $var
 * @param string  $message
 * @param boolean $ini
 * @param boolean $negative_test
 * @param string  $error_fragment
*/
function & testBoolean( $required, $title, $var, $message = '', $ini = true, $negative_test = false, $error_fragment = '' )
{
	global $lang_fn;
	$test = new CmsInstallTest();
	$test->title = $title;

	if($ini)
	{
		testIni($test, $var, 'boolean');
	}
	else
	{
		$test->ini_val = $var;
	}
	$test->ini_val = $negative_test ? (! (bool) $test->ini_val) : (bool) $test->ini_val;

	if($test->ini_val == false)
	{
		$test->value = $negative_test ? $lang_fn('on') : $lang_fn('off');
		$test->secondvalue = $negative_test ? $lang_fn('true') : $lang_fn('false');
		if($required)
		{
			$test->res = 'red';
		}
		else
		{
			$test->res = 'yellow';
		}
	}
	else
	{
		$test->value = $negative_test ? $lang_fn('off') : $lang_fn('on');
		$test->secondvalue = $negative_test ? $lang_fn('false') : $lang_fn('true');
		$test->res = 'green';
	}

	getTestReturn($test, $required, $message, $error_fragment);
	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param mixed   $var
 * @param string  $message
 * @param mixed   $minimum
 * @param mixed   $recommended
 * @param boolean $ini
 * @param int     $unlimited
 * @param string  $error_fragment
*/
function & testVersionRange( $required, $title, $var, $message = '', $minimum, $recommended, $ini = true, $unlimited = null, $error_fragment='' )
{
	$test = new CmsInstallTest();
	$test->title = $title;

	if($ini)
	{
		testIni($test, $var, 'string');
		if(isset($test->error))
		{
			$required = false;
		}
	}
	else
	{
		$test->ini_val = $var;
	}

	$test->value = $test->ini_val;
	$test->secondvalue = null;

	if( (! is_null($unlimited)) && ($test->ini_val == (string) $unlimited) )
	{
		$test->value = $lang_fn('unlimited');
		$test->res = 'green';
		getTestReturn($test, $required);
	}
	elseif(version_compare($test->ini_val, $minimum) < 0)
	{
		$test->res = 'red';
		getTestReturn($test, $required, $message, $error_fragment);
	}
	elseif(version_compare($test->ini_val, $recommended) < 0)
	{
		$test->res = 'yellow';
		getTestReturn($test, false, $message, $error_fragment);
	}
	else
	{
		$test->res = 'green';
		getTestReturn($test, $required);
	}

	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param mixed   $var
 * @param string  $message
 * @param mixed   $minimum
 * @param mixed   $recommended
 * @param boolean $ini
 * @param boolean $test_as_bytes
 * @param int     $unlimited
 * @param string  $error_fragment
*/
function & testRange( $required, $title, $var, $message = '', $minimum, $recommended, $ini = true, $test_as_bytes = false, $unlimited = null, $error_fragment = '' )
{
	$test = new CmsInstallTest();
	$test->title = $title;
	
	global $lang_fn;

	if($ini)
	{
		testIni($test, $var, 'string');
		if(isset($test->error))
		{
			$required = false;
		}
	}
	else
	{
		$test->ini_val = $var;
	}

	$test->value = $test->ini_val;
	$test->secondvalue = null;
	if($test_as_bytes)
	{
		$test->ini_val = returnBytes($test->ini_val);
		$minimum = returnBytes($minimum);
		$recommended = returnBytes($recommended);
	}

	if( (! is_null($unlimited)) && ((int) $test->ini_val == (int) $unlimited) )
	{
		$test->value = $lang_fn('unlimited');
		$test->res = 'green';
		getTestReturn($test, $required);
	}
	elseif((int) $test->ini_val < $minimum)
	{
		$test->res = 'red';
		getTestReturn($test, $required, $message, $error_fragment);
	}
	elseif((int) $test->ini_val < $recommended)
	{
		$test->res = 'yellow';
		getTestReturn($test, false, $message, $error_fragment);
	}
	else
	{
		$test->res = 'green';
		getTestReturn($test, $required);
	}

	return $test;
}

/**
 * @return int
 * @param string $val
 */
function returnBytes( $val )
{
	if(is_string($val) && $val != '')
	{
		$val = trim($val);
		$last = strtolower(substr($val, strlen($val/1), 1));
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

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param string  $umask
 * @param string  $message
 * @param boolean $debug
 * @param string  $dir
 * @param string  $file
 * @param string  $data
 */
function & testUmask( $required, $title, $umask, $message = '', $debug = false, $dir = '', $file = '_test_umask_', $data = 'this is a test' )
{
	$test = new CmsInstallTest();
	$test->title = $title;
	
	global $lang_fn;

	if(empty($dir))
	{
		$dir = TMP_CACHE_LOCATION;
	}

	$_test = true;
	if($debug)
	{
		if( (! is_dir($dir)) || (! is_writable($dir)) ) $_test = false;
	}
	else
	{
		if( (! @is_dir($dir)) || (! @is_writable($dir)) ) $_test = false;
	}


	if(! $_test)
	{
		$test->res = 'red';
		getTestReturn($test, $required, $message, 'Directory_not_writable', $lang_fn('errordirectorynotwritable').' ('.$dir .')');
		return $test;
	}
	$_test = true;

	clearstatcache();
	$test->value = $dir;
	$test->secondvalue = substr(sprintf('%o', @fileperms($dir)), -4);

	$test_file = $dir . DIRECTORY_SEPARATOR . $file;
	if(file_exists($test_file)) @unlink($test_file);

	if($debug)
	{
		umask(octdec($umask));
		$fp = fopen($test_file, "w");
	}
	else
	{
		@umask(octdec($umask));
		$fp = @fopen($test_file, "w");
	}

	if($fp !== false)
	{
		$_return = '';
		if($debug) $_return = fwrite($fp, $data);
		else       $_return = @fwrite($fp, $data);
		@fclose($fp);

		$_opt = permission_stat($test_file, $debug);
		if($_opt == false)
		{
			if($required)
			{
				$test->res = 'red';
			}
			else
			{
				$test->res = 'yellow';
			}
			getTestReturn($test, $required, $message, 'Can.27t_create_file', $lang_fn('errorcantcreatefile').' ('.$test_file.')');
			return $test;
		}

		$test->opt = $_opt;

		if($debug) unlink($test_file);
		else      @unlink($test_file);
		if(! empty($_return))
		{
			$test->res = 'green';
			getTestReturn($test, $required);
			return $test;
		}
	}

	$test->res = 'red';
	getTestReturn($test, $required, $message, 'Can.27t_create_file', $lang_fn('errorcantcreatefile').' ('.$test_file.')');
	return $test;
}



/**
 * @return array
 * @param string  $file
 * @param boolean $debug
 */
function permission_stat( $file, $debug = false )
{
	global $lang_fn;
	$opt = array();
	clearstatcache();
	$filestat = stat($file);
	if($filestat == false)
	{
		return false;
	}

	clearstatcache();
	if($debug) $mode = fileperms($file);
	else       $mode = @fileperms($file);

	$opt['permsdec'] = substr(sprintf('%o', $mode), -4);
	$opt['permsstr'] = permission_octal2string($mode);

	// functions not available on WAMP systems
	if( (function_exists('posix_getpwuid')) && (function_exists('posix_getgrgid')) )
	{
		if($debug)
		{
			$userinfo = posix_getpwuid($filestat[4]);
			$groupinfo = posix_getgrgid($filestat[5]);
		}
		else
		{
			$userinfo = @posix_getpwuid($filestat[4]);
			$groupinfo = @posix_getgrgid($filestat[5]);
		}
		$opt['username'] = isset($userinfo['name']) ? $userinfo['name'] : $lang_fn('unknown');
		$opt['usergroup'] = isset($groupinfo['name']) ? $groupinfo['name'] : $lang_fn('unknown');
	}
	else
	{
		$opt['username'] = $lang_fn('unknown');
		$opt['usergroup'] = $lang_fn('unknown');
	}

	return $opt;
}

/**
 * @return string
 * @param octal $mode
 */
function permission_octal2string( $mode )
{
	if(($mode & 0xC000) === 0xC000) {
		$type = 's';
	} elseif(($mode & 0xA000) === 0xA000) {
		$type = 'l';
	} elseif(($mode & 0x8000) === 0x8000) {
		$type = '-';
	} elseif(($mode & 0x6000) === 0x6000) {
		$type = 'b';
	} elseif(($mode & 0x4000) === 0x4000) {
		$type = 'd';
	} elseif(($mode & 0x2000) === 0x2000) {
		$type = 'c';
	} elseif(($mode & 0x1000) === 0x1000) {
		$type = 'p';
	} else{
		$type = '?';
	}

	$owner  = ($mode & 00400) ? 'r' : '-';
	$owner .= ($mode & 00200) ? 'w' : '-';
	if($mode & 0x800) {
		$owner .= ($mode & 00100) ? 's' : 'S';
	} else {
		$owner .= ($mode & 00100) ? 'x' : '-';
	}

	$group  = ($mode & 00040) ? 'r' : '-';
	$group .= ($mode & 00020) ? 'w' : '-';
	if($mode & 0x400) {
		$group .= ($mode & 00010) ? 's' : 'S';
	} else {
		$group .= ($mode & 00010) ? 'x' : '-';
	}

	$other  = ($mode & 00004) ? 'r' : '-';
	$other .= ($mode & 00002) ? 'w' : '-';
	if($mode & 0x200) {
		$other .= ($mode & 00001) ? 't' : 'T';
	} else {
		$other .= ($mode & 00001) ? 'x' : '-';
	}

	return $type . $owner . $group . $other;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param string  $message
 * @param boolean $debug
 * @param string  $dir
 * @param string  $file
*/
function & testCreateDirAndFile( $required, $title, $message = '', $debug = false, $dir = '_test_dir_file_', $file = '_test_dir_file_' )
{
	$test = new CmsInstallTest();
	$test->title = $title;
	$dir = cms_join_path(TMP_CACHE_LOCATION, $dir);
	$file = cms_join_path($dir, $file);
	
	global $lang_fn;

	if($debug)
	{
		if(file_exists($file)) unlink($file);
		if( (is_dir($dir)) && (false !== strpos($dir, dirname(dirname(__FILE__)))) ) rmdir($dir);

		if(! mkdir($dir)) $test->error = $lang_fn('errordirectorynotwritable') .' ('. $dir . ')';
		if(! touch($file)) $test->error = $lang_fn('errorcantcreatefile') .' ('. $file . ')';
		$_test = file_exists($file);
	}
	else
	{
		if(file_exists($file)) @unlink($file);
		if( (@is_dir($dir)) && (false !== strpos($dir, dirname(dirname(__FILE__)))) ) @rmdir($dir);

		@mkdir($dir);
		@touch($file);
		$_test = file_exists($file);
		@unlink($file);
		@rmdir($dir);
	}

	if(! $_test)
	{
		$test->res = 'red';
		getTestReturn($test, $required, $message, 'Can.27t_create_file');
	}
	else
	{
		$test->res = 'green';
		getTestReturn($test, $required);
	}

	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param string  $dir
 * @param string  $message
 * @param boolean $quick
 * @param boolean $debug
 * @param string  $file
 * @param string  $data
*/
function & testDirWrite( $required, $title, $dir, $message = '', $quick = 0, $debug = false, $file = '_test_dir_write_', $data = 'this is a test' )
{
	$test = new CmsInstallTest();
	$test->title = $title;

	if(empty($dir))
	{
		$dir = TMP_CACHE_LOCATION;
	}
	$test->value = $dir;
	$test->secondvalue = substr(sprintf('%o', @fileperms($dir)), -4);

	$_test = true;
	if($debug)
	{
		if( (! is_dir($dir)) || (! is_writable($dir)) ) $_test = false;
	}
	else
	{
		if( (! @is_dir($dir)) || (! @is_writable($dir)) ) $_test = false;
	}


	if($_test)
	{
		if($quick)
		{
			// we're only doing the quick test which sucks
			$test->res = 'green';
			getTestReturn($test, $required);
			return $test;
		}

		$test_file = $dir . DIRECTORY_SEPARATOR . $file;
		if(file_exists($test_file))
		{
			if($debug) unlink($test_file);
			else      @unlink($test_file);
		}

		if($debug) $fp = fopen($test_file, "w");
		else       $fp = @fopen($test_file, "w");
		if($fp !== false)
		{
			$_return = '';
//			flock($fp, LOCK_EX); //no on NFS filesystem
			if($debug) $_return = fwrite($fp, $data);
			else       $_return = @fwrite($fp, $data);
//			flock($fp, LOCK_UN);
			@fclose($fp);

			if(! $debug) @unlink($test_file);

			if(! empty($_return))
			{
				$test->res = 'green';
				getTestReturn($test, $required);
				return $test;
			}
		}
	}

	$test->res = 'red';
	global $lang_fn;
	getTestReturn($test, $required, $message, 'Directory_not_writable', $lang_fn('errordirectorynotwritable').' ('.$dir.')');
	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param string  $file
 * @param string  $message
 * @param boolean $debug
*/
function & testFileWritable( $required, $title, $file, $message = '', $debug = false )
{
	$test = new CmsInstallTest();
	$test->title = $title;
	
	global $lang_fn;

	if(empty($file))
	{
		$test->error = $lang_fn('errorfilenot');
		return $test;
	}

	$test->value = $file;
	$test->secondvalue = substr(sprintf('%o', @fileperms($file)), -4);

	$_test = true;
	if($debug)
	{
		if( (! is_file($file)) || (! is_writable($file)) ) $_test = false;
	}
	else
	{
		if( (! @is_file($file)) || (! @is_writable($file)) ) $_test = false;
	}


	if($_test)
	{
		$_test = file_exists($file);

		if($debug) $fp = fopen($file, "a");
		else       $fp = @fopen($file, "a");
		if($fp !== false)
		{
			@fclose($fp);
			if(! $_test) @unlink($file);

			$test->res = 'green';
			getTestReturn($test, $required);
			return $test;
		}
	}

	$test->res = 'red';
	getTestReturn($test, $required, $message, 'File_not_writable', $lang_fn('errorfilenotwritable').' ('.$file.')');
	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param string  $dir
 * @param string  $message
 * @param boolean $debug
 * @param int     $timeout
 * @param string  $search
*/
function & testRemoteFile( $required, $title, $url = '', $message = '', $debug = false, $timeout = 10, $search = 'cmsmadesimple' )
{
	global $lang_fn;
	if(empty($url))
	{
		$url = CMS_DEFAULT_VERSIONCHECK_URL;
	}

	$test = new CmsInstallTest();
	$test->title = $title;
	//$test->value = $url;

	if(! $url_info = parse_url($url))
	{
		// Relative or invalid URL?
		$test->res = 'red';
		getTestReturn($test, $required, '', '', $lang_fn('invalid_test'));
		return $test;
	}

	if( !isset($url_info['scheme']) || !isset($url_info['host']) )
	{
		$test->res = 'red';
		getTestReturn($test, $required, '', '', $lang_fn('invalid_url'));
		return $test;
	}
	else
	{
		switch($url_info['scheme'])
			{
			case 'https':
				$scheme = 'ssl://';
				$port = (isset($url_info['port'])) ? $url_info['port'] : 443;
				break;
			case 'http':
			default:
				$scheme = '';
				$port = (isset($url_info['port'])) ? $url_info['port'] : 80;
			}
		$complete_url  = (isset($url_info['path'])) ? $url_info['path'] : '/';
		$complete_url .= (isset($url_info['query'])) ? '?'.$url_info['query'] : '';


		// TEST FSOCKOPEN
		if($debug) $handle = fsockopen($scheme . $url_info['host'], $port, $errno, $errstr, $timeout);
		else       $handle = @fsockopen($scheme . $url_info['host'], $port, $errno, $errstr, $timeout);
		if($handle)
			{
				$out  = "GET " . $complete_url . " HTTP/1.1\r\n";
				$out .= "Host: " . $url_info['host'] . "\r\n";
				$out .= "Connection: Close\r\n\r\n";
				if($debug)
					{
						fwrite($handle, $out);
						stream_set_blocking($handle, 1);
						stream_set_timeout($handle, $timeout);
						$_info = stream_get_meta_data($handle);
					}
				else
					{
						@fwrite($handle, $out);
						@stream_set_blocking($handle, 1);
						@stream_set_timeout($handle, $timeout);
						$_info = @stream_get_meta_data($handle);
					}

				$content_fsockopen = '';
				while( (! feof($handle)) && (! $_info['timed_out']) )
					{
						if($debug) $content_fsockopen .= fgets($handle, 128);
						else       $content_fsockopen .= @fgets($handle, 128);
						$_info = stream_get_meta_data($handle);
					}
				@fclose($handle);

				if($_info['timed_out'])
					{
						$test->opt['fsockopen']['ok'] = 1;
						$test->opt['fsockopen']['res'] = 'yellow';
						$test->opt['fsockopen']['res_text'] = $lang_fn('caution');
						$test->opt['fsockopen']['message'] = $lang_fn('remote_connection_timeout');
					}
				else
					{
						$test->opt['fsockopen']['ok'] = 0;
					}
			}
		else
			{
				$test->res = 'red';
				getTestReturn($test, $required, '', '', $lang_fn('connection_error'));
				$test->continueon = 0;
				return $test;
			}

		if($test->opt['fsockopen']['ok'] < 1)
			{
				if( (! empty($search)) && (false !== strpos($content_fsockopen, $search)) )
					{
						$test->opt['fsockopen']['res'] = 'green';
						$test->opt['fsockopen']['res_text'] = $lang_fn('success');
						$test->opt['fsockopen']['message'] = $lang_fn('search_string_find');
					}
				elseif(false !== strpos($content_fsockopen, '200 OK'))
					{
						$test->opt['fsockopen']['ok'] = 1;
						$test->opt['fsockopen']['res'] = 'yellow';
						$test->opt['fsockopen']['res_text'] = $lang_fn('caution');
						$test->opt['fsockopen']['message'] = $lang_fn('remote_response_ok');
					}
				elseif(false !== strpos($content_fsockopen, '404 Not Found'))
					{
						$test->opt['fsockopen']['ok'] = 1;
						$test->opt['fsockopen']['res'] = 'yellow';
						$test->opt['fsockopen']['res_text'] = $lang_fn('caution');
						$test->opt['fsockopen']['message'] = $lang_fn('remote_response_404');
					}
				else
					{
						$test->opt['fsockopen']['ok'] = 2;
						$test->opt['fsockopen']['res'] = 'red';
						$test->opt['fsockopen']['res_text'] = $lang_fn('failure');
						$test->opt['fsockopen']['message'] = $lang_fn('remote_response_error');
					}
			}


		// TEST FOPEN
		$test->opt['fopen']['ok'] = 2;
		$result = testBoolean('', '', 'allow_url_fopen', '', true, false);
		if($result->res == 'green')
			{
				if($debug) $handle = fopen($url, 'rb');
				else       $handle = @fopen($url, 'rb');
				if(false == $handle)
					{
						$test->opt['fopen']['ok'] = 2;
						$test->opt['fopen']['res'] = 'red';
						$test->opt['fopen']['res_text'] = $lang_fn('failure');
						$test->opt['fopen']['message'] = $lang_fn('connection_failed');
					}
				else
					{
						if($debug)
							{
								stream_set_blocking($handle, 1);
								stream_set_timeout($handle, $timeout);
								$_info = stream_get_meta_data($handle);
							}
						else
							{
								@stream_set_blocking($handle, 1);
								@stream_set_timeout($handle, $timeout);
								$_info = @stream_get_meta_data($handle);
							}

						$content_fopen = '';
						while( (! feof($handle)) && (! $_info['timed_out']) )
							{
								if($debug) $content_fopen .= fgets($handle, 128);
								else       $content_fopen .= @fgets($handle, 128);
								$_info = stream_get_meta_data($handle);
							}
						@fclose($handle);

						if($_info['timed_out'])
							{
								$test->opt['fopen']['ok'] = 1;
								$test->opt['fopen']['res'] = 'yellow';
								$test->opt['fopen']['res_text'] = $lang_fn('caution');
								$test->opt['fopen']['message'] = $lang_fn('remote_connection_timeout');
							}
						else
							{
								$test->opt['fopen']['ok'] = 0;
							}
					}
			}
		else
			{
				$test->opt['fopen']['res'] = 'red';
				$test->opt['fopen']['res_text'] = $lang_fn('failure');
				$test->opt['fopen']['message'] = $lang_fn('test_allow_url_fopen_failed');
			}


		if($test->opt['fopen']['ok'] < 1)
			{
				if( (! empty($search)) && (false !== strpos($content_fopen, $search)) )
					{
						$test->opt['fopen']['res'] = 'green';
						$test->opt['fopen']['res_text'] = $lang_fn('success');
						$test->opt['fopen']['message'] = $lang_fn('search_string_find');
					}
				elseif(false !== strpos($content_fopen, '200 OK'))
					{
						$test->opt['fopen']['ok'] = 1;
						$test->opt['fopen']['res'] = 'yellow';
						$test->opt['fopen']['res_text'] = $lang_fn('caution');
						$test->opt['fopen']['message'] = $lang_fn('remote_response_ok');
					}
				elseif(false !== strpos($content_fopen, '404 Not Found'))
					{
						$test->opt['fopen']['ok'] = 1;
						$test->opt['fopen']['res'] = 'yellow';
						$test->opt['fopen']['res_text'] = $lang_fn('caution');
						$test->opt['fopen']['message'] = $lang_fn('remote_response_404');
					}
				else
					{
						$test->opt['fopen']['ok'] = 2;
						$test->opt['fopen']['res'] = 'red';
						$test->opt['fopen']['res_text'] = $lang_fn('failure');
						$test->opt['fopen']['message'] = $lang_fn('remote_response_error');
					}
			}


		$result = $test->opt['fsockopen']['ok'] + $test->opt['fopen']['ok'];
	}

	switch($result)
	{
		case 0:
				$test->res = 'green';
				getTestReturn($test, $required);
				break;
		case 1:
		case 2:
		case 3:
				$test->res = 'yellow';
				getTestReturn($test, false, '', 'Connection_error');
				break;
		default:
				$test->res = 'red';
				getTestReturn($test, $required, $message, 'Connection_error');
	}

	return $test;
}


/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param string  $file
 * @param string  $checksum
 * @param string  $message
 * @param string  $formattime
 * @param boolean $debug
*/
function & testFileChecksum( $required, $title, $file, $checksum, $message = '', $formattime = '%c', $debug = false )
{
	$test = new CmsInstallTest();
	$test->title = $title;
	$test->value = $file;
	
	global $lang_fn;

	if(is_dir($file))
	{
		$test->secondvalue = $lang_fn('is_directory');
		$test->res = 'yellow';
		getTestReturn($test, '', '', '', $lang_fn('is_directory').' ('.$file.')');
		return $test;
	}

	if(! file_exists($file))
	{
		$test->secondvalue = $lang_fn('nofiles');
		$test->res = 'red';
		getTestReturn($test, '', $message, '', $lang_fn('nofiles').' ('.$file.')');
		return $test;
	}

	if($debug) $_test = is_readable($file);
	else       $_test = @is_readable($file);
	if(! $_test)
	{
		$test->secondvalue = $lang_fn('is_readable_false');
		$test->res = 'yellow';
		getTestReturn($test, '', '', '', $lang_fn('is_readable_false').' ('.$file.')');
		return $test;
	}

	if($debug) $file_checksum = md5_file($file);
	else       $file_checksum = @md5_file($file);
	if(false == $file_checksum)
	{
		$test->secondvalue = $lang_fn('not_checksum');
		$test->res = 'yellow';
		getTestReturn($test, '', '', '', $lang_fn('not_checksum').' ('.$file.')');
		return $test;
	}

	if($file_checksum == $checksum)
	{
		$test->secondvalue = $lang_fn('checksum_match');
		$test->res = 'green';
		getTestReturn($test, $required);
		return $test;
	}

	if($debug) $test->opt['file_timestamp'] = filemtime($file);
	else       $test->opt['file_timestamp'] = @filemtime($file);
	$test->opt['format_timestamp'] = $formattime;

	$test->secondvalue = $lang_fn('checksum_not_match');
	$test->res = 'red';
	getTestReturn($test, $required, $message, '', $lang_fn('checksum_not_match'));
	return $test;
}

/**
 * @return string
 * @param string $sess_path
*/
function testSessionSavePath( $sess_path )
{
	if(empty($sess_path))
	{
		$sess_path = ini_get('session.save_path');
	}

	if('files' == ini_get('session.save_handler'))
	{
		if(empty($sess_path))
		{
			if(! function_exists('sys_get_temp_dir'))
			{
				if(! empty($_ENV['TMP'])) return realpath($_ENV['TMP']);
				if(! empty($_ENV['TMPDIR'])) return realpath($_ENV['TMPDIR']);
				if(! empty($_ENV['TEMP'])) return realpath($_ENV['TEMP']);
				if( ('1' != ini_get('safe_mode')) && ($tempfile = tempnam('', 'cms')) )
				{
					if(file_exists($tempfile))
					{
						@unlink($tempfile);
						return realpath(dirname($tempfile));
					}
				}
			}
			else
			{
				return rtrim(sys_get_temp_dir(), '\\/');
			}
		}
		else
		{
			if (strrpos($sess_path, ";") !== false) //Can be 5;777;/tmp
			{
				$sess_path = substr($sess_path, strrpos($sess_path, ";")+1);
			}
			return $sess_path;
		}
	}

	return '';
}

/**
 * @return object
 * @param string $inputname
*/
function & testFileUploads( $inputname )
{
	global $lang_fn;
	
	$_errors = array(
		UPLOAD_ERR_INI_SIZE => $lang_fn('upload_err_ini_size'),
		UPLOAD_ERR_FORM_SIZE => $lang_fn('upload_err_form_size'),
		UPLOAD_ERR_PARTIAL => $lang_fn('upload_err_partial'),
		UPLOAD_ERR_NO_FILE => $lang_fn('upload_err_no_file'),
		UPLOAD_ERR_NO_TMP_DIR => $lang_fn('upload_err_no_tmp_dir'), //at least PHP 4.3.10 or PHP 5.0.3
		UPLOAD_ERR_CANT_WRITE => $lang_fn('upload_err_cant_write'), //at least PHP 5.1.0
		UPLOAD_ERR_EXTENSION => $lang_fn('upload_err_extension'), //at least PHP 5.2.0
	);

	function orderFiles(&$file_upl)
	{
		$_ary = array();
		$_count = count($file_upl['name']);
		$_keys = array_keys($file_upl);
		for($i=0; $i<$_count; $i++)
		{
			foreach($_keys as $key) $_ary[$i][$key] = $file_upl[$key][$i];
		}

		return $_ary;
	}

	$test = new CmsInstallTest();
	$test->files = array();

	$result = testBoolean('', '', 'file_uploads', '', true, false);
	if($result->res != 'green')
	{
		$test->res = 'red';
		getTestReturn($test, '', '', 'Function_file_uploads_disabled', $lang_fn('function_file_uploads_off'));
		return $test;
	}

	if(! isset($_FILES["$inputname"]))
	{
		$test->res = 'red';
		getTestReturn($test, '', '', '', $lang_fn('error_nofileuploaded'));
		return $test;
	}

	$_files = array();
	if(is_array($_FILES["$inputname"]['name'])) $_files = orderFiles($_FILES["$inputname"]);
	else $_files[] = $_FILES["$inputname"];

	foreach($_files as $i=>$_file)
	{
		$_data = $_file;

		if( (! is_uploaded_file($_file['tmp_name'])) || ($_file['error'] !== UPLOAD_ERR_OK) )
		{
			if(isset($_errors[$_file['error']]))
			{
				$_data['error_string'] = $_errors[$_file['error']];
			}
			elseif($_file['size'] == 0)
			{
				$_data['error_string'] = $lang_fn('upload_err_empty');
			}
			elseif(! is_readable($_file['tmp_name']))
			{
				$_data['error_string'] = $lang_fn('upload_file_no_readable');
            }
			else
			{
				$_data['error_string'] = $lang_fn('upload_err_unknown');
			}
		}

		$test->files["$i"] = $_data;
	}

	return $test;
}

/**
 * @return object
 * @param boolean $required
 * @param string  $title
 * @param string  $minimum
 * @param string  $message
 * @param string  $error_fragment
*/
function & testGDVersion( $required, $title, $minimum, $message = '', $error_fragment = '' )
{
	$test = new CmsInstallTest();
	$test->title = $title;

	$gd_version_number = GDVersion();
	$test->value = $gd_version_number;
	$test->secondvalue = null;

	if($gd_version_number < $minimum)
	{
		if($required)
		{
			$test->res = 'red';
		}
		else
		{
			$test->res = 'yellow';
		}
	}
	else
	{
		$test->res = 'green';
	}

	getTestReturn($test, $required, $message, $error_fragment);
	return $test;
}

/**
 * @return string
*/
function GDVersion()
{
	static $gd_version_number = null;

	if(is_null($gd_version_number)) {
		if(extension_loaded('gd')) {
			if(defined('GD_MAJOR_VERSION')) {
				$gd_version_number = GD_MAJOR_VERSION;
				return $gd_version_number;
			}
			$gdinfo = @gd_info();
			if(preg_match('/\d+/', $gdinfo['GD Version'], $gdinfo)) {
				$gd_version_number = (int) $gdinfo[0];
			} else {
				$gd_version_number = 1;
			}
			return $gd_version_number;
		}
		$gd_version_number = 0;
	}

	return $gd_version_number;
}

# vim:ts=4 sw=4 noet
?>
