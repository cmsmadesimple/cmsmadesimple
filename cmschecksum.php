<?php

// file: install/lang/en_US.php
$lang['nofiles'] = 'This resource is not exist!';
$lang['is_directory'] = 'This resource is a directory!';
$lang['is_readable_false'] = 'This resource is not readable!';
$lang['checksum_match'] = 'Checksum match!';
$lang['checksum_not_match'] = 'Checksum not match!';
$lang['not_checksum'] = 'No checksum retrieve!';
$lang['format_datetime'] = '%c';
$lang['no_checksum_file'] = 'No checksum file. Upload checksum file in root of CMS!';
$lang['checksum_file_no_readable'] = 'Checksum file is not readable!';


// file: install/translation.functions.php
function lang()
{
	global $lang;

	$name = '';
	$params = array();

	if (func_num_args() > 0)
	{
		$name = func_get_arg(0);
		if (func_num_args() == 2 && is_array(func_get_arg(1)))
		{
			$params = func_get_arg(1);
		}
		else if (func_num_args() > 1)
		{
			$params = array_slice(func_get_args(), 1);
		}
	}
	else
	{
		return '';
	}

	$result = '';

	if (isset($lang[$name]))
	{
		if (count($params))
		{
			$result = vsprintf($lang[$name], $params);
		}
		else
		{
			$result = $lang[$name];
		}
	}
	else
	{
		$result = "--Add Me - $name --";
	}

	return $result;
}


// file: lib/test.functions.php
/**
 * @return object
 * @var boolean $required
 * @var string  $title
 * @var string  $file
 * @var string  $checksum
 * @var string  $message
 * @var string  $formattime
*/
function & testFileChecksum($required, $title, $file, $checksum, $message = '', $formattime = '%c')
{
	$test =& new StdClass();
	$test->title = $title;
	$test->value = $file;

	if(is_dir($file))
	{
		$test->res = 'yellow';
		#$test->res_text = getTestReturn($test->res);
		$test->error = lang('is_directory') .' ('. $file . ')';
		return $test;
	}

	if(! file_exists($file))
	{
		#list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		#$test->res_text = getTestReturn($test->res);
		$test->error = lang('nofiles') .' ('. $file . ')';
		if(trim($message) != '')
		{
			$test->message = $message;
		}
		return $test;
	}

	if(!is_readable($file))
	{
		$test->res = 'yellow';
		#$test->res_text = getTestReturn($test->res);
		$test->error = lang('is_readable_false') .' ('. $file . ')';
		return $test;
	}


	$file_checksum = @md5_file($file);
	if(false == $file_checksum)
	{
		$test->res = 'yellow';
		#$test->res_text = getTestReturn($test->res);
		$test->error = lang('not_checksum') .' ('. $file . ')';
		return $test;
	}

	if($file_checksum == $checksum)
	{
		$test->secondvalue = lang('checksum_match');
		$test->res = 'green';
		#$test->res_text = getTestReturn($test->res);
		return $test;
	}

	$test->secondvalue = lang('checksum_not_match');
	$test->opt['file_timestamp'] = @filemtime($file);
	$test->opt['format_timestamp'] = $formattime;

	#list($test->continueon, $test->special_failed) = testGlobal($required);
	$test->res = 'red';
	#$test->res_text = getTestReturn($test->res);
	if(trim($message) != '')
	{
		$test->message = $message;
	}

	return $test;
}




// INIT
$error = '';
$checksum_file = '';
$config_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
$checksum_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;


if(! file_exists($config_file))
{
	@include_once $config_file;
	$root_path = $config['root_path'];
}

if( (! isset($root_path)) || (! is_dir($root_path)) )
{
	$root_path = dirname(__FILE__);
}


if ($handle = @opendir($checksum_dir))
{
	while (false !== ($file = readdir($handle)))
	{
		if ( ($file != '..') && ($file != '.') && ($file != basename($file, '.dat')) &&
					(is_file($checksum_dir . $file)) )
		{
			$pos = strpos($file, '-checksum.dat');
			if(false !== $pos)
			{
				$version = substr($file, 0, $pos);
				$checksum_file = $checksum_dir . $file;
				break;
			}
		}
	}
	closedir($handle);
}


if(! file_exists($checksum_file))
{
	$error = lang('no_checksum_file');
}
elseif(! is_readable($checksum_file))
{
	$error = lang('checksum_file_no_readable');
}
else
{
	$handle = @fopen($checksum_file, 'rb');
	if(false == $handle)
	{
		$error = lang('checksum_file_no_readable');
	}
}


if(empty($error))
{
	$results = array();
	while(!feof($handle))
	{
		$line = @fgets($handle, 4096);
		$line = trim($line); // clean

		if(empty($line)) continue; // skip empty line

		$pos = strpos($line, '#');
		if($pos) $line = substr($line, 0, $pos); // strip out comments

		list($md5sum, $file) = explode(' *./', $line, 2); // split it into fields
		$md5sum = trim($md5sum);
		$file = trim($file);
		$file = str_replace('/', DIRECTORY_SEPARATOR, $file);

		$test_file = $root_path . DIRECTORY_SEPARATOR . $file;
		$test = testFileChecksum(0, 'Checksum', $test_file, $md5sum, '', lang('format_datetime'));
		if($test->res == 'green') continue; // ok, skip

		$results[] = $test;
	}
	@fclose($handle);
}




// PRESENTATION
if(!empty($error)) die($error);

echo $version.'<hr>';

if(count($results) > 0)
{
	foreach($results as $result)
	{
		echo $result->value.' ('. $result->res .')<br />';
		if(isset($result->error)) echo $result->error.'<br />';
		if(isset($result->secondvalue)) echo $result->secondvalue.'<br />';
		if(isset($result->opt)) echo strftime($result->opt['format_timestamp'], $result->opt['file_timestamp']).'<br />';
		if(isset($result->message)) echo $result->message;
		echo '<hr>';
	}
}
?>
