<?php

/**
 * @package CMS
 */

/**
 * A method to initialize the connection with the CMSMS configured database.
 *
 * @internal
 * @access private
 */
function load_adodb() 
{
  $gCms = cmsms();
  $config = $gCms->GetConfig();

	// @TODO: Remove dependence on PEAR for error handling	
	if( !defined('ADODB_OUTP') )
	  {
	    define('ADODB_OUTP', 'debug_sql');
	  }
	//define('ADODB_ERROR_HANDLER', 'adodb_error');
	
	$adodb_light = cms_join_path(dirname(__FILE__),'adodb_lite','adodb.inc.php');
	if (file_exists($adodb_light))
	  {
	    // Load ADOdb Lite
	    require_once($adodb_light);
	  }
	else
	  {
	    // ADOdb cannot be found, show a message and stop the script execution
	    die('The ADOdb Lite database abstraction library cannot be found, CMS Made Simple cannot load.');
	  }
	
	if( !defined('CMS_ADODB_DT') ) define('CMS_ADODB_DT', $config['use_adodb_lite'] ? 'DT' : 'T');
}

/**
 * A method to re-initialize connections to the CMSMS configured database.
 * This method should be used by any UDT's or other plugins that use any other method
 * than the standard CMSMS supplied database object to connect to a database.
 *
 */
function &adodb_connect()
{
  $gCms = cmsms();
  $config = $gCms->GetConfig();
	
  $str = 'pear:date:extend';
  $dbinstance = ADONewConnection($config['dbms'], $str);
	$dbinstance->raiseErrorFn = "adodb_error";
	$conn_func = (isset($config['persistent_db_conn']) && $config['persistent_db_conn'] == true) ? 'PConnect' : 'Connect';
	if(!empty($config['db_port'])) $dbinstance->port = $config['db_port'];
	$connect_result = $dbinstance->$conn_func($config['db_hostname'], $config['db_username'], $config['db_password'], $config['db_name']);
	
	if (FALSE == $connect_result)
	{
	  $str = "Attempt to connect to database {$config['db_name']} on {$config['db_username']}@{$config['db_hostname']} failed";
	  trigger_error($str,E_USER_ERROR);
	  die($str);
	}

	$dbinstance->raiseErrorFn = null;
	
	$dbinstance->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if ($config['debug'] == true)
	{
		$dbinstance->debug = true;
	}
	
	$p1 = array();
	if($config['set_names'] == true) {
	  $p1[] = "NAMES 'utf8'";
	}
	if($config['set_db_timezone'] == true) {
	  $dt = new DateTime();
	  $dtz = new DateTimeZone($config['timezone']);
	  $offset = timezone_offset_get($dtz,$dt);
	  $symbol = ($offset < 0) ? '-' : '+';
	  $hrs = abs((int)($offset / 3600));
	  $mins = abs((int)($offset % 3600));
	  $p1[] = sprintf("time_zone = '%s%d:%02d'",$symbol,$hrs,$mins);
	}
	$dbinstance->Execute('SET '.implode(',',$p1));
	return $dbinstance;
}


/**
 * @ignore
 */
function adodb_error($dbtype, $function_performed, $error_number, $error_message, $host, $database, &$connection_obj)
{
	if(file_exists(cms_join_path(dirname(CONFIG_FILE_LOCATION), 'db_error.html')))
	{
		include_once cms_join_path(dirname(CONFIG_FILE_LOCATION), 'db_error.html');
		exit;
	}
	else
	{
		echo "<strong>Database Connection Failed</strong><br />";
		echo "Error: {$error_message} ({$error_number})<br />";
		echo "Function Performed: {$function_performed}<br />";
	}
}

?>