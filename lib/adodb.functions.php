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
  global $gCms;
  global $ADODB_vers;
  $config = $gCms->GetConfig();

	// @TODO: Remove dependence on PEAR for error handling	
	if ($config['debug'] == true)
	{
		#@include_once(cms_join_path(dirname(__FILE__), $config['use_adodb_lite'] == true ? 'adodb_lite' : 'adodb', 'adodb-errorpear.inc.php'));
	}
	
	define('ADODB_OUTP', 'debug_sql');
	//define('ADODB_ERROR_HANDLER', 'adodb_error');
	
	$loaded_adodb = false;
	
	if ($config['use_adodb_lite'] == false || (isset($USE_OLD_ADODB) && $USE_OLD_ADODB == 1))
	{
	# CMSMS is configured to use full ADOdb
		$full_adodb = cms_join_path(dirname(__FILE__),'adodb','adodb.inc.php');
		if (! file_exists($full_adodb))
		{
			# Full ADOdb cannot be found, show a debug error message
			$gCms->errors[] = 'CMS Made Simple is configured to use the full ADOdb Database Abstraction library, but it\'s not in the lib' .DIRECTORY_SEPARATOR. 'adodb directory. Switched back to ADOdb Lite.';
		}
		else
		{
			# Load (full) ADOdb
			require($full_adodb);
			$loaded_adodb = true;
		}
	}
	if (!$loaded_adodb)
	{
		$adodb_light = cms_join_path(dirname(__FILE__),'adodb_lite','adodb.inc.php');
		# The ADOdb library is not yet included, try ADOdb Lite
		if (file_exists($adodb_light))
		{
			# Load ADOdb Lite
			require($adodb_light);
		}
		else
		{
			# ADOdb cannot be found, show a message and stop the script execution
			die('The ADOdb Lite database abstraction library cannot be found, CMS Made Simple cannot load.');
		}
	}
	
	define('CMS_ADODB_DT', $config['use_adodb_lite'] ? 'DT' : 'T');
}

/**
 * A method to re-initialize connections to the CMSMS configured database.
 * This method should be used by any UDT's or other plugins that use any other method
 * than the standard CMSMS supplied database object to connect to a database.
 *
 */
function & adodb_connect()
{
	global $gCms;
	$config = $gCms->GetConfig();
	
	$dbinstance =& ADONewConnection($config['dbms'], 'pear:date:extend:transaction');
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
		#$dbinstance->LogSQL();
	}
	
	if ($config['dbms'] == 'sqlite')
	{
		$dbinstance->Execute('PRAGMA short_column_names = 1;');
		sqlite_create_function($config['use_adodb_lite'] ? $db->connectionId : $db->_connectionID, 'now', 'time', 0);
	}
	else
	{
		if(!empty($config['default_encoding']) && $config['default_encoding'] == 'utf-8' && $config['set_names'] == true)
		{
			$dbinstance->Execute("SET NAMES 'utf8'");
		}
	}
	
	$db =& $dbinstance;
	return $db;
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
		echo "Host/DB: {$host}/{$database}<br />";
		echo "Database Type: {$dbtype}<br />";
	}
}

?>
