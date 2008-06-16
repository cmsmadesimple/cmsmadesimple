<?php
$range_phpversion = array('minimum'=> '4.3.0', 'recommended'=> '5.2');
$range_memory_limit = array('minimum'=> 16, 'recommended'=> 24);
$range_post_max_size = array('minimum'=> 2, 'recommended'=> 10);
$range_upload_max_filesize = array('minimum'=> 2, 'recommended'=> 10);
$range_max_execution_time = array('minimum'=> 30, 'recommended'=> 60);
$range_gd_version = array('minimum'=> 2, 'recommended'=> 2);
$range_mysqlversion = array('minimum'=> '3.23', 'recommended'=> '4.1');
$range_pgsqlversion = array('minimum'=> '7.4', 'recommended'=> '8');



function systeminfo_range_numeric( $value, $range )
{
	$value = intval($value);
	if(empty($value)) return 'red'; //Can be 0 in our case?
	if( (empty($range)) || (!is_array($range)) ) return 'green';

	if ($value < $range['minimum'])
	{
		return 'red';
	}
	elseif ($value < $range['recommended'])
	{
		return 'yellow';
	}
	else
	{
		return 'green';
	}
}

function systeminfo_version_compare( $value, $range )
{
	if (version_compare($value, $range['minimum'], '<'))
	{
		return 'red';
	}
	elseif (version_compare($value, $range['recommended'], '<'))
	{
		return 'yellow';
	}
	else
	{
		 return 'green';
	}
}

function systeminfo_dir_write( $dir, $file='file_test', $data='this is a test' )
{
	if(empty($dir)) return 'red';
	$test_file = "$dir/$file";
	if ($fp = @fopen($test_file, "w")) {
		flock($fp,LOCK_EX);
		$return = @fwrite($fp, $data);
		flock($fp,LOCK_UN);
		fclose($fp);
		@unlink($test_file);
		if(!empty($return)) return 'green';
	}
	return 'red';
}

function systeminfo_gd_version()
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
