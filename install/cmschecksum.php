<?php

$debug = false;


/*********************
 CODE FROM CMSMS FILES
 *********************/

/**********************
 install/lang/en_US.php
 **********************/
$lang['nofiles'] = 'This resource is not exist!';
$lang['is_directory'] = 'This resource is a directory!';
$lang['is_readable_false'] = 'This resource is not readable!';
$lang['checksum_match'] = 'Checksum match!';
$lang['checksum_not_match'] = 'Checksum not match!';
$lang['not_checksum'] = 'No checksum retrieve!';
$lang['format_datetime'] = '%c';
$lang['upload_err_ini_size'] = 'The uploaded file exceeds the upload_max_filesize directive in php.ini!';
$lang['upload_err_form_size'] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
$lang['upload_err_partial'] = 'The uploaded file was only partially uploaded.';
$lang['upload_err_no_file'] = 'No file was uploaded.';
$lang['upload_err_no_tmp_dir'] = 'Missing a temporary folder.';
$lang['upload_err_cant_write'] = 'Failed to write file to disk.';
$lang['upload_err_extension'] = 'File upload stopped by extension.';
$lang['upload_err_empty'] = 'File has size zero';
$lang['upload_err_unknown'] = 'File upload error unknown.';
$lang['function_file_uploads_off'] = 'file_uploads is off in your php!';
$lang['upload_file_no_readable'] = 'File upload no readable!';
$lang['upload_file_multiple'] = 'Multiple file uploads not allowed!';
$lang['checksum'] = 'Checksum test';
$lang['checksum_file'] = 'Checksum file';
$lang['install_test_checksum'] = 'You can validate the integrity of your CMS files by comparing against original CMS checksum. It can assist in finding problems with uploads.';
$lang['checksum_passed'] = 'All checksums match!';


/*********************************
 install/translation.functions.php
 *********************************/
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
		$result = "-- Missing Languagestring - $name --";
	}

	return $result;
}


/**********************
 lib/test.functions.php
 **********************/
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
	$test = new StdClass();
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
		#list($test->continueon, $test->special_failed) = testGlobal($required);
		if(trim($message) != '')
		{
			$test->message = $message;
		}

		$test->value = $negative_test ? 'On' : 'Off';
		$test->secondvalue = $negative_test ? lang('true') : lang('false');
		if($required)
		{
			$test->res = 'false';
			#$test->res_text = getTestReturn($test->res);
		}
		else
		{
			$test->res = 'yellow';
			#$test->res_text = getTestReturn($test->res);
		}
	}
	else
	{
		$test->value = $negative_test ? 'Off' : 'On';
		$test->secondvalue = $negative_test ? lang('false') : lang('true');
		if($required)
		{
			$test->res = 'true';
			#$test->res_text = getTestReturn($test->res);
		}
		else
		{
			$test->res = 'green';
			#$test->res_text = getTestReturn($test->res);
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
	$test = new StdClass();
	$test->title = $title;
	$test->value = $file;

	if(is_dir($file))
	{
		$test->res = 'yellow';
		#$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('is_directory');
		$test->error = lang('is_directory') .' ('. $file . ')';
		return $test;
	}

	if(! file_exists($file))
	{
		#list($test->continueon, $test->special_failed) = testGlobal($required);
		$test->res = 'red';
		#$test->res_text = getTestReturn($test->res);
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
		#$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('is_readable_false');
		$test->error = lang('is_readable_false') .' ('. $file . ')';
		return $test;
	}


	if($debug) $file_checksum = md5_file($file);
	else       $file_checksum = @md5_file($file);
	if(false == $file_checksum)
	{
		$test->res = 'yellow';
		#$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('not_checksum');
		$test->error = lang('not_checksum') .' ('. $file . ')';
		return $test;
	}

	if($file_checksum == $checksum)
	{
		$test->res = 'green';
		#$test->res_text = getTestReturn($test->res);
		$test->secondvalue = lang('checksum_match');
		return $test;
	}

	if($debug) $test->opt['file_timestamp'] = filemtime($file);
	else       $test->opt['file_timestamp'] = @filemtime($file);
	$test->opt['format_timestamp'] = $formattime;

	#list($test->continueon, $test->special_failed) = testGlobal($required);
	$test->res = 'red';
	#$test->res_text = getTestReturn($test->res);
	$test->secondvalue = lang('checksum_not_match');
	$test->error = lang('checksum_not_match');
	if(trim($message) != '')
	{
		$test->message = $message;
	}

	return $test;
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

	$test = new StdClass();
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




// INIT
$config_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
if(file_exists($config_file))
{
	@include_once $config_file;
	$root_path = $config['root_path'];
}
if( (! isset($root_path)) || (! is_dir($root_path)) )
{
	$root_path = dirname(__FILE__);
}


/*********************************************
 install/lib/classes/CMSInstallPage1.class.php
 *********************************************/
		if (isset($_POST['recheck']))
		{
			$error = '';

			$test = testFileUploads('cksumdat');
			if(isset($test->error))
			{
				$error = $test->error;
			}
			elseif(count($test->files) > 1)
			{
				$error = lang('upload_file_multiple');
			}
			else
			{
				if(isset($test->files[0]['error_string']))
				{
					$error = $test->files[0]['error_string'];
				}
				else
				{
					$checksum_file = $test->files[0]['tmp_name'];
					if ($debug) $handle = fopen($checksum_file, 'rb');
					else $handle = @fopen($checksum_file, 'rb');
					if (! $handle)
					{
						$error = lang('upload_file_no_readable');
					}
				}
			}

			if (empty($error))
			{
				$results = array();
				while (!feof($handle))
				{
					$line = @fgets($handle, 4096);
					$line = trim($line); // clean

					if (empty($line)) continue; // skip empty line

					$pos = strpos($line, '#');
					if ($pos) $line = substr($line, 0, $pos); // strip out comments

					list($md5sum, $file) = explode(' *./', $line, 2); // split it into fields
					$md5sum = trim($md5sum);
					$file = trim($file);
					$file = str_replace('/', DIRECTORY_SEPARATOR, $file); // avoid windows suck

					$test_file = $root_path . DIRECTORY_SEPARATOR . $file;
					$test = testFileChecksum(0, 'Checksum', $test_file, $md5sum, '', lang('format_datetime'), $debug);
					if ($test->res == 'green') continue; // ok, skip

					$results[] = $test;
				}
				@fclose($handle);
			}
		}




// PRESENTATION
echo '<h2>'.lang('checksum').'</h2>';
if(!empty($error)) die($error);

if(isset($results))
{
	if(count($results) > 0)
	{
		foreach($results as $result)
		{
			echo '<strong>'.$result->value .'</strong><br />';
			if(isset($result->secondvalue)) echo '<span style="color:red;">'.$result->secondvalue .'</span><br />';
			if(isset($result->opt)) echo strftime($result->opt['format_timestamp'], $result->opt['file_timestamp']) .'<br />';
			if(isset($result->message)) echo $result->message;
			echo '<hr>';
		}
	}
	else
	{
		echo lang('checksum_passed').'<hr>';
	}	
}

echo '<h4 style="color:navy;">'.lang('install_test_checksum').'</h4>';
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" name="page1form" id="page1form" enctype="multipart/form-data">
	<input type="file" name="cksumdat" id="cksumdat" maxlength="255" /><br />
	<input type="submit" name="recheck" value=" Check " />
</form>
