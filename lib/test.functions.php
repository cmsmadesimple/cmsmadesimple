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
 * Handles test functions
 *
 * @package CMS
 */

/**
 * @return array
 * @var string  $property
*/
function getTestValues($property)
{
	$range = array(
		'phpversion'			=> array('minimum'=>'4.3.0', 'recommended'=>'5.0.4'),
		'gd_version'			=> array('minimum'=>2),
		'memory_limit'			=> array('minimum'=>'16M', 'recommended'=>'24M'),
		'max_execution_time'	=> array('minimum'=>30, 'recommended'=>60),
		'post_max_size'			=> array('minimum'=>'2M', 'recommended'=>'10M'),
		'upload_max_filesize'	=> array('minimum'=>'2M', 'recommended'=>'10M'),
		'mysqlversion'			=> array('minimum'=>'3.23', 'recommended'=>'4.1'),
		'pgsqlversion'			=> array('minimum'=>'7.4', 'recommended'=>'8'),
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
 * @return boolean
 * @var string  $test
*/
function extension_loaded_or($test)
{
	$a = extension_loaded(strtolower($test));
	$b = extension_loaded(strtoupper($test));
	return (bool)($a | $b);
}

/**
 * @return string
 * @var string  $return
*/
function getTestReturn($return)
{
	switch($return)
	{
		case 'true':	return lang('success'); break;
		case 'green':	return lang('success'); break;
		case 'yellow':	return lang('caution'); break;
		case 'false':	return lang('failure'); break;
		case 'red':		return lang('failure'); break;
	}

	return '';
}

/**
 * @return array
 * @var boolean $result
 * @var boolean $set
*/
function testGlobal($result, $set = false)
{
	static $continueon = true;
	static $special_failed = false;
	if( ($set) && (is_array($result)) )
	{
		$return = array($continueon, $special_failed);
		list($continueon, $special_failed) = $result;
		return $return;
	}
	if($result == true)
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
 * @return object
 * @var string  $title
 * @var string  $value
 * @var string  $return
 * @var string  $message
 * @var string  $error
*/
function & testDummy($title, $value, $return, $message = '', $error = '')
{
	$test =&new StdClass();
	$test->title = $title;
	$test->value = $value;
	$test->secondvalue = null;
	if(trim($message) != '')
	{
		$test->message = $message;
	}
	if(trim($error) != '')
	{
		$test->error = $error;
	}

	$test->res = $return;
	$test->res_text = getTestReturn($return);

	return $test;
}

/**
 * @return object
 * @var string  $title
 * @var string  $varname
 * @var string  $testfunc
 * @var string  $message
*/
function & testConfig($title, $varname, $testfunc = '', $message = '')
{
	global $gCms;
	$config = $gCms->config;

	if( (isset($config[$varname])) && (is_bool($config[$varname])) )
	{
		$value = (true == $config[$varname]) ? 'true' : 'false';
	}
	else if(! empty($config[$varname]))
	{
		$value = $config[$varname];
		if(! empty($testfunc))
		{
			$test = $testfunc(0, $title, $value);
		}
	}
	else
	{
		$value = '';
	}

	if(! isset($test))
	{
		$test =&new StdClass();
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
 * @var object  $test
 * @var string  $varname
 * @var string  $type
*/
function testIni(&$test, $varname, $type)
{
	$error = null;
	$str = ini_get($varname);

	switch($type)
	{
		case 'boolean':
			$str = (bool) $str;
			break;
		case 'integer':
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

			if($type == 'integer')
			{
				$str = (int) $str;
			}
			break;
	}

	$test->ini_val = $str;
	$test->error = $error;
	return true;
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var mixed   $var
 * @var string  $message
 * @var boolean $ini
 * @var boolean $negative_test
*/
function & testBoolean($required, $title, $var, $message = '', $ini = true, $negative_test = false)
{
	$test =&new StdClass();
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
		list($test->continueon, $test->special_failed) = testGlobal($required);
		if(trim($message) != '')
		{
			$test->message = $message;
		}

		$test->value = $negative_test ? 'On' : 'Off';
		$test->secondvalue = $negative_test ? lang('true') : lang('false');
		if($required)
		{
			$test->res = 'false';
			$test->res_text = getTestReturn($test->res);
		}
		else
		{
			$test->res = 'yellow';
			$test->res_text = getTestReturn($test->res);
		}
	}
	else
	{
		$test->value = $negative_test ? 'Off' : 'On';
		$test->secondvalue = $negative_test ? lang('false') : lang('true');
		if($required)
		{
			$test->res = 'true';
			$test->res_text = getTestReturn($test->res);
		}
		else
		{
			$test->res = 'green';
			$test->res_text = getTestReturn($test->res);
		}
	}

	return $test;
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var mixed   $var
 * @var string  $message
 * @var mixed   $minimum
 * @var mixed   $recommended
 * @var boolean $ini
 * @var int     $unlimited
*/
function & testVersionRange($required, $title, $var, $message = '', $minimum, $recommended, $ini = true, $unlimited = null)
{
	$test =& new StdClass();
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
		$test->res = 'green';
		$test->res_text = getTestReturn($test->res);
		$test->value = lang('unlimited');
	}
	elseif(version_compare($test->ini_val, $minimum) < 0)
	{
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		$test->res_text = getTestReturn($test->res);
		if(trim($message) != '')
		{
			$test->message = $message;
		}
	}
	elseif(version_compare($test->ini_val, $recommended) < 0)
	{
		$test->res = 'yellow';
		$test->res_text = getTestReturn($test->res);
		if(trim($message) != '')
		{
			$test->message = $message;
		}
	}
	else
	{
		$test->res = 'green';
		$test->res_text = getTestReturn($test->res);
	}

	return $test;
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var mixed   $var
 * @var string  $message
 * @var mixed   $minimum
 * @var mixed   $recommended
 * @var boolean $ini
 * @var boolean $test_as_bytes
 * @var int     $unlimited
*/
function & testRange($required, $title, $var, $message = '', $minimum, $recommended, $ini = true, $test_as_bytes = false, $unlimited = null)
{
	$test =& new StdClass();
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

	if($test_as_bytes)
	{
		$test->ini_val = returnBytes($test->ini_val);
		$minimum = returnBytes($minimum);
		$recommended = returnBytes($recommended);
	}

	if( (! is_null($unlimited)) && ((int) $test->ini_val == (int) $unlimited) )
	{
		$test->res = 'green';
		$test->res_text = getTestReturn($test->res);
		$test->value = lang('unlimited');
	}
	elseif((int) $test->ini_val < $minimum)
	{
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		$test->res_text = getTestReturn($test->res);
	}
	elseif((int) $test->ini_val < $recommended)
	{
		$test->res = 'yellow';
		$test->res_text = getTestReturn($test->res);
	}
	else
	{
		$test->res = 'green';
		$test->res_text = getTestReturn($test->res);
	}

	if((int) $test->ini_val < $recommended && trim($message) != '')
	{
		$test->message = $message;
	}

	return $test;
}

/**
 * @return int
 * @var string $val
 */
function returnBytes($val)
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
 * @var boolean $required
 * @var string  $title
 * @var string  $umask
 * @var string  $message
 * @var boolean $debug
 * @var string  $dir
 * @var string  $file
 * @var string  $data
 */
function & testUmask($required, $title, $umask, $message = '', $debug = false, $dir = '', $file = '_test_umask_', $data = 'this is a test')
{
	$test =& new StdClass();
	$test->title = $title;

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
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		$test->res_text = getTestReturn($test->res);
		$test->error = lang('errordirectorynotwritable') .' ('. $dir . ')';
		if(trim($message) != '')
		{
			$test->message = $message;
		}
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
			list($test->continueon, $test->special_failed) = testGlobal($required);
			$test->res = 'red';
			$test->res_text = getTestReturn($test->res);
			$test->error = lang('errorcantcreatefile') .' ('. $test_file . ')';
			if(trim($message) != '')
			{
				$test->message = $message;
			}
			return $test;
		}

		$test->opt = $_opt;

		if($debug) unlink($test_file);
		else      @unlink($test_file);
		if(! empty($_return))
		{
			$test->res = 'green';
			$test->res_text = getTestReturn($test->res);
			return $test;
		}
	}

	list($test->continueon, $test->special_failed) = testGlobal($required);
	$test->res = 'red';
	$test->res_text = getTestReturn($test->res);
	$test->error = lang('errorcantcreatefile') .' ('. $test_file .')';
	return $test;
}



/**
 * @return array
 * @var string  $file
 * @var boolean $debug
 */
function permission_stat($file, $debug = false)
{
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
		$opt['username'] = isset($userinfo['name']) ? $userinfo['name'] : lang('unknown');
		$opt['usergroup'] = isset($groupinfo['name']) ? $groupinfo['name'] : lang('unknown');
	}
	else
	{
		$opt['username'] = lang('unknown');
		$opt['usergroup'] = lang('unknown');
	}

	return $opt;
}

/**
 * @var octal $mode
 * @return string
 */
function permission_octal2string($mode)
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
 * @var boolean $required
 * @var string  $title
 * @var string  $message
 * @var boolean $debug
 * @var string  $dir
 * @var string  $file
*/
function & testCreateDirAndFile($required, $title, $message='', $debug = false, $dir = '_test_dir_file_', $file = '_test_dir_file_')
{
	$test =& new StdClass();
	$test->title = $title;
	$dir = cms_join_path(TMP_CACHE_LOCATION, $dir);
	$file = cms_join_path($dir, $file);

	if($debug)
	{
		if(file_exists($file)) unlink($file);
		if( (is_dir($dir)) && (false !== strpos($dir, dirname(dirname(__FILE__)))) ) rmdir($dir);

		if(! mkdir($dir)) $test->error = lang('errordirectorynotwritable') .' ('. $dir . ')';
		if(! touch($file)) $test->error = lang('errorcantcreatefile') .' ('. $file . ')';
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
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->continueon = false;
		$test->special_failed = true;
		$test->res = 'false';
		if(trim($message) != '')
		{
			$test->message = $message;
		}
	}
	else
	{
		$test->res = 'true';
	}
	$test->res_text = getTestReturn($test->res);
	return $test;
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var string  $dir
 * @var string  $message
 * @var boolean $quick
 * @var boolean $debug
 * @var string  $file
 * @var string  $data
*/
function & testDirWrite($required, $title, $dir, $message = '', $quick = 0, $debug = false, $file = '_test_dir_write_', $data = 'this is a test')
{
	$test =& new StdClass();
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
			$test->res_text = getTestReturn($test->res);
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
				$test->res_text = getTestReturn($test->res);
				return $test;
			}
		}
	}

	list($test->continueon, $test->special_failed) = testGlobal($required);
	$test->res = 'red';
	$test->res_text = getTestReturn($test->res);
	$test->error = lang('errordirectorynotwritable') .' ('. $dir . ')';
	if(trim($message) != '')
	{
		$test->message = $message;
	}

	return $test;
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var string  $file
 * @var string  $message
 * @var boolean $debug
*/
function & testFileWritable($required, $title, $file, $message = '', $debug = false)
{
	$test =& new StdClass();
	$test->title = $title;

	if(empty($file))
	{
		$test->error = lang('errorfilenot');
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
			$test->res_text = getTestReturn($test->res);
			return $test;
		}
	}

	list($test->continueon, $test->special_failed) = testGlobal($required);
	$test->res = 'red';
	$test->res_text = getTestReturn($test->res);
	$test->error = lang('errorfilenotwritable') .' ('. $file . ')';
	if(trim($message) != '')
	{
		$test->message = $message;
	}

	return $test;
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var string  $dir
 * @var string  $message
 * @var boolean $debug
 * @var int     $timeout
 * @var string  $search
*/
function & testRemoteFile($required, $title, $url = '', $message = '', $debug = false, $timeout = 10, $search = 'cmsmadesimple')
{
	if(empty($url))
	{
		$url = 'http://dev.cmsmadesimple.org/latest_version.php';
	}

	$test =& new StdClass();
	$test->title = $title;
	//$test->value = $url;

	if(! $url_info = parse_url($url))
	{
		// Relative or invalid URL?
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->error = lang('invalid_test');
		$test->res = 'red';
		$test->res_text = getTestReturn($test->res);
		return $test;
	}

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
	if(false !== $handle)
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
			$test->opt['fsockopen']['res_text'] = getTestReturn($test->opt['fsockopen']['res']);
			$test->opt['fsockopen']['message'] = lang('remote_connection_timeout');
		}
		else
		{
			$test->opt['fsockopen']['ok'] = 0;
		}
	}
	else
	{
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->error = lang('connection_error');
		$test->res = 'red';
		$test->res_text = getTestReturn($test->res);
		return $test;
	}

	if($test->opt['fsockopen']['ok'] < 1)
	{
		if( (! empty($search)) && (false !== strpos($content_fsockopen, $search)) )
		{
			$test->opt['fsockopen']['res'] = 'green';
			$test->opt['fsockopen']['res_text'] = getTestReturn($test->opt['fsockopen']['res']);
			$test->opt['fsockopen']['message'] = lang('search_string_find');
		}
		elseif(false !== strpos($content_fsockopen, '200 OK'))
		{
			$test->opt['fsockopen']['ok'] = 1;
			$test->opt['fsockopen']['res'] = 'yellow';
			$test->opt['fsockopen']['res_text'] = getTestReturn($test->opt['fsockopen']['res']);
			$test->opt['fsockopen']['message'] = lang('remote_response_ok');
		}
		elseif(false !== strpos($content_fsockopen, '404 Not Found'))
		{
			$test->opt['fsockopen']['ok'] = 1;
			$test->opt['fsockopen']['res'] = 'yellow';
			$test->opt['fsockopen']['res_text'] = getTestReturn($test->opt['fsockopen']['res']);
			$test->opt['fsockopen']['message'] = lang('remote_response_404');
		}
		else
		{
			$test->opt['fsockopen']['ok'] = 2;
			$test->opt['fsockopen']['res'] = 'red';
			$test->opt['fsockopen']['res_text'] = getTestReturn($test->opt['fsockopen']['res']);
			$test->opt['fsockopen']['message'] = lang('remote_response_error');
		}
	}



	// TEST FOPEN
	$test->opt['fopen']['ok'] = 2;
	$result = testBoolean(0, '', 'allow_url_fopen', lang('test_allow_url_fopen_failed'), false, false);
	if($result->value == 'Off')
	{
		$test->opt['fopen']['res'] = 'red';
		$test->opt['fopen']['res_text'] = getTestReturn($test->opt['fopen']['res']);
		$test->opt['fopen']['message'] = lang('test_allow_url_fopen_failed');
	}
	else
	{
		if($debug) $handle = fopen($url, 'rb');
		else       $handle = @fopen($url, 'rb');
		if(false == $handle)
		{
			$test->opt['fopen']['ok'] = 2;
			$test->opt['fopen']['res'] = 'red';
			$test->opt['fopen']['res_text'] = getTestReturn($test->opt['fopen']['res']);
			$test->opt['fopen']['message'] = lang('connection_failed');
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
				$test->opt['fopen']['res_text'] = getTestReturn($test->opt['fopen']['res']);
				$test->opt['fopen']['message'] = lang('remote_connection_timeout');
			}
			else
			{
				$test->opt['fopen']['ok'] = 0;
			}
		}
	}

	if($test->opt['fopen']['ok'] < 1)
	{
		if( (! empty($search)) && (false !== strpos($content_fopen, $search)) )
		{
			$test->opt['fopen']['res'] = 'green';
			$test->opt['fopen']['res_text'] = getTestReturn($test->opt['fopen']['res']);
			$test->opt['fopen']['message'] = lang('search_string_find');
		}
		elseif(false !== strpos($content_fopen, '200 OK'))
		{
			$test->opt['fopen']['ok'] = 1;
			$test->opt['fopen']['res'] = 'yellow';
			$test->opt['fopen']['res_text'] = getTestReturn($test->opt['fopen']['res']);
			$test->opt['fopen']['message'] = lang('remote_response_ok');
		}
		elseif(false !== strpos($content_fopen, '404 Not Found'))
		{
			$test->opt['fopen']['ok'] = 1;
			$test->opt['fopen']['res'] = 'yellow';
			$test->opt['fopen']['res_text'] = getTestReturn($test->opt['fopen']['res']);
			$test->opt['fopen']['message'] = lang('remote_response_404');
		}
		else
		{
			$test->opt['fopen']['ok'] = 2;
			$test->opt['fopen']['res'] = 'red';
			$test->opt['fopen']['res_text'] = getTestReturn($test->opt['fopen']['res']);
			$test->opt['fopen']['message'] = lang('remote_response_error');
		}
	}


	$result = $test->opt['fsockopen']['ok'] + $test->opt['fopen']['ok'];
	switch($result)
	{
		case 0:
				$test->res = 'green';
				$test->res_text = getTestReturn($test->res);
				break;
		case 1:
		case 2:
		case 3:
				$test->res = 'yellow';
				$test->res_text = getTestReturn($test->res);
				break;
		default:
				list($test->continueon, $test->special_failed) = testGlobal($required);
				$test->res = 'red';
				$test->res_text = getTestReturn($test->res);
				if(trim($message) != '')
				{
					$test->message = $message;
				}
	}

	return $test;
}


/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var string  $file
 * @var string  $checksum
 * @var string  $message
 * @var string  $formattime
 * @var boolean $debug
*/
function & testFileChecksum($required, $title, $file, $checksum, $message = '', $formattime = '%c', $debug = false)
{
	$test =& new StdClass();
	$test->title = $title;
	$test->value = $file;

	if(is_dir($file))
	{
		$test->res = 'yellow';
		$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('is_directory');
		$test->error = lang('is_directory') .' ('. $file . ')';
		return $test;
	}

	if(! file_exists($file))
	{
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('nofiles');
		$test->error = lang('nofiles') .' ('. $file . ')';
		if(trim($message) != '')
		{
			$test->message = $message;
		}
		return $test;
	}

	if($debug) $_test = is_readable($file);
	else       $_test = @is_readable($file);
	if(! $_test)
	{
		$test->res = 'yellow';
		$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('is_readable_false');
		$test->error = lang('is_readable_false') .' ('. $file . ')';
		return $test;
	}


	if($debug) $file_checksum = md5_file($file);
	else       $file_checksum = @md5_file($file);
	if(false == $file_checksum)
	{
		$test->res = 'yellow';
		$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('not_checksum');
		$test->error = lang('not_checksum') .' ('. $file . ')';
		return $test;
	}

	if($file_checksum == $checksum)
	{
		$test->res = 'green';
		$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('checksum_match');
		return $test;
	}

	if($debug) $test->opt['file_timestamp'] = filemtime($file);
	else       $test->opt['file_timestamp'] = @filemtime($file);
	$test->opt['format_timestamp'] = $formattime;

	list($test->continueon, $test->special_failed) = testGlobal($required);
	$test->res = 'red';
	$test->res_text = getTestReturn($test->res);
	$test->secondvalue = lang('checksum_not_match');
	$test->error = lang('checksum_not_match');
	if(trim($message) != '')
	{
		$test->message = $message;
	}

	return $test;
}

/**
 * @return string
 * @var string  $sess_path
*/
function testSessionSavePath($sess_path)
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
 * @var string  $inputname
*/
function & testFileUploads($inputname)
{
	$_errors = array(
		UPLOAD_ERR_INI_SIZE => lang('upload_err_ini_size'),
		UPLOAD_ERR_FORM_SIZE => lang('upload_err_form_size'),
		UPLOAD_ERR_PARTIAL => lang('upload_err_partial'),
		UPLOAD_ERR_NO_FILE => lang('upload_err_no_file'),
		UPLOAD_ERR_NO_TMP_DIR => lang('upload_err_no_tmp_dir'), //at least PHP 4.3.10 or PHP 5.0.3
		UPLOAD_ERR_CANT_WRITE => lang('upload_err_cant_write'), //at least PHP 5.1.0
		UPLOAD_ERR_EXTENSION => lang('upload_err_extension'), //at least PHP 5.2.0
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

	$test =& new StdClass();
	$test->files = array();

	$_file_uploads = testBoolean(0, '', 'file_uploads', '', true, false);
	if($_file_uploads->value == 'Off')
	{
		$test->error = lang('function_file_uploads_off');
		return $test;
	}

	if(! isset($_FILES['cksumdat']))
	{
		$test->error = lang('error_nofileuploaded');
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
				$_data['error_string'] = lang('upload_err_empty');
			}
			elseif(! is_readable($_file['tmp_name']))
			{
				$_data['error_string'] = lang('upload_file_no_readable');
            }
			else
			{
				$_data['error_string'] = lang('upload_err_unknown');
			}
		}

		$test->files["$i"] = $_data;
	}

	return $test;
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var string  $minimum
 * @var string  $message
*/
function & testGDVersion($required, $title, $minimum, $message = '')
{
	$test =& new StdClass();
	$test->title = $title;

	$gd_version_number = GDVersion();
	$test->value = $gd_version_number;
	$test->secondvalue = null;

	if($gd_version_number < $minimum)
	{
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		$test->res_text = getTestReturn($test->res);
		if(trim($message) != '')
		{
			$test->message = $message;
		}
	}
	else
	{
		$test->res = 'green';
		$test->res_text = getTestReturn($test->res);
	}

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
