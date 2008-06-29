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

	if (array_key_exists($property, $range))
	{
		if (empty($range[$property]['recommended']))
		{
			$range[$property]['recommended'] = $range[$property]['minimum'];
		}
		return array($range[$property]['minimum'], $range[$property]['recommended']);
	}

	return array();
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
	if ( ($set) && (is_array($result)) )
	{
		$return = array($continueon, $special_failed);
		list($continueon, $special_failed) = $result;
		return $return;
	}
	if ($result == true)
	{
		$continueon = false;
	}
	else if ($result == false)
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
*/
function & testDummy($title, $value, $return, $message = '')
{
	$test =&new StdClass();
	$test->title = $title;
	$test->value = $value;
	$test->message = $message;

	$test->res = $return;
	switch($return)
	{
		case 'true':	$test->res_text = lang('success'); break;
		case 'green':	$test->res_text = lang('success'); break;
		case 'yellow':	$test->res_text = lang('caution'); break;
		case 'false':	$test->res_text = lang('failure'); break;
		case 'red':	$test->res_text = lang('failure'); break;
	}

	return $test;
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
		list($test->continueon, $test->special_failed) = testGlobal($required);
		if (trim($message) != '')
		{
			$test->message = $message;
		}

		if ($required)
		{
			$test->res = 'false';
			$test->res_text = lang('failure');
		}
		else
		{
			$test->res = 'yellow';
			$test->res_text = lang('caution');
		}
	}
	else
	{
		if ($required)
		{
			$test->res = 'true';
			$test->res_text = lang('success');
		}
		else
		{
			$test->res = 'green';
			$test->res_text = lang('success');
		}
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
	return testBoolean($required, $title, $result, $message);
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var mixed   $value
 * @var mixed   $minimum
 * @var mixed   $recommended
 * @var string  $message
*/
function & testVersionRange($required, $title, $value, $minimum, $recommended, $message = '',$plus = 0)
{
	$test =& new StdClass();

	echo "DEBUG: $recommended,$message - $plus<br/>";
	$test->title = $title . sprintf(' '.lang('test_min_recommend'), $minimum, $recommended);
	if( $plus > 0 )
	  {
	    $test->title = $title . sprintf(' '.lang('test_min_recommend_plus'), $minimum, $recommended);
	  }
	$test->value = $value;

	if (version_compare($value,$minimum) < 0)
	{
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		$test->res_text = lang('failure');
		$test->message = $message;
	}
	elseif (version_compare($value,$recommended) < 0 )
	{
		$test->res = 'yellow';
		$test->res_text = lang('caution');
		$test->message = $message;
	}
	else
	{
		$test->res = 'green';
		$test->res_text = lang('success');
	}

	return $test;
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

	$test->title = $title . sprintf(' '.lang('test_min_recommend'), $minimum, $recommended);
	$test->value = $value;

	if ($test_as_bytes)
	{
		$value = returnBytes($value);
		$minimum = returnBytes($minimum);
		$recommended = returnBytes($recommended);
	}

	if ($value < $minimum)
	{
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		$test->res_text = lang('failure');
	}
	elseif ($value < $recommended)
	{
		$test->res = 'yellow';
		$test->res_text = lang('caution');
	}
	else
	{
		$test->res = 'green';
		$test->res_text = lang('success');
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
		$error = lang('could_not_retrieve_a_value');
		$required = false;
		if ((string) get_cfg_var($varname) != '')
		{
			$str = (string) get_cfg_var($varname);
			$error .= lang('displaying_the_value_originally');
		}
	}
	$test =& testRange($required, $title, $str, $minimum, $recommended, $message, $test_as_bytes);
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

/**
 * @return object
 * @var boolean $required
 * @var string  $dir
 * @var string  $title
 * @var string  $message
 * @var string  $file
 * @var string  $data
*/
function testDirWrite($required, $title, $dir, $message = '', $file = 'file_test', $data = 'this is a test')
{
	$test =& new StdClass();

	$test->title = $title;
	if (is_dir($dir))
	{
		$test->value = substr(sprintf('%o', fileperms($dir)), -4);
	}

	$return = '';
	$test_file = "$dir/$file";
	if ($fp = @fopen($test_file, "w")) {
//		flock($fp, LOCK_EX); //no on NFS filesystem
		$return = @fwrite($fp, $data);
//		flock($fp, LOCK_UN);
		fclose($fp);
		@unlink($test_file);

		if (!empty($return))
		{
			$test->res = 'green';
			$test->res_text = lang('success');
			return $test;
		}
	}

	list($test->continueon, $test->special_failed) = testGlobal($required);
	$test->res = 'red';
	$test->res_text = lang('failure');
	$test->message = $message;

	return $test;
}

/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var string  $minimum
 * @var string  $message
*/
function testGDVersion($required, $title, $minimum, $message = '')
{
	$test =& new StdClass();

	$test->title = $title;

	$gd_version_number = GDVersion();
	$test->value = $gd_version_number;

	if ($gd_version_number < $minimum)
	{
		list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		$test->res_text = lang('failure');
		$test->message = $message;
	}
	else
	{
		$test->res = 'green';
		$test->res_text = lang('success');
	}

	return $test;
}

/**
 * @return string
*/
function GDVersion()
{
	static $gd_version_number = null;

	if (is_null($gd_version_number)) {
		if (extension_loaded('gd')) {
			if (defined('GD_MAJOR_VERSION')) {
				$gd_version_number = GD_MAJOR_VERSION;
				return $gd_version_number;
			}
			$gdinfo = @gd_info();
			if (preg_match('/\d+/', $gdinfo['GD Version'], $gdinfo)) {
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
?>