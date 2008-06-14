<?php
$range_phpversion = array('minimum'=> '4.3.0', 'recommended'=> '5.2');
$range_memory_limit = array('minimum'=> 16, 'recommended'=> 24);
$range_post_max_size = array('minimum'=> 2, 'recommended'=> 5);
$range_upload_max_filesize = array('minimum'=> 2, 'recommended'=> 5);
$range_max_execution_time = array('minimum'=> 30, 'recommended'=> 60);
$range_gd_version = array('minimum'=> 2, 'recommended'=> 2);





function systeminfo_check_range( $value, $range )
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

function systeminfo_check_php( $range )
{
	if (version_compare(PHP_VERSION, $range['minimum'], '<'))
	{
		return 'red';
	}
	elseif (version_compare(PHP_VERSION, $range['recommended'], '<'))
	{
		return 'yellow';
	}
	else
	{
		 return 'green';
	}
}

function systeminfo_session_save_path( $dir, $file='this_is_a_test', $sess_data = 'this is a test' )
{
	if(empty($dir)) return 'red';
	$sess_file = "$dir/file";
	if ($fp = @fopen($sess_file, "w")) {
		flock($fp,LOCK_EX);
		$return = fwrite($fp, $sess_data);
		flock($fp,LOCK_UN);
		fclose($fp);
		if(!empty($return)) return 'green';
		return 'red';
	} else {
		return 'red';
	}
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
