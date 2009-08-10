<?php

function load_adodb() 
{
  global $gCms;
  global $ADODB_vers;
  $config =& $gCms->GetConfig();

	// @TODO: Remove dependence on PEAR for error handling	
	define('ADODB_OUTP', 'debug_sql');
	
	# CMSMS is configured to use full ADOdb
	$full_adodb = cms_join_path(dirname(__FILE__),'adodb','adodb.inc.php');
	if (! file_exists($full_adodb))
	{
		# Full ADOdb cannot be found, show a debug error message
		$gCms->errors[] = 'CMS Made Simple is configured to use the full ADOdb Database Abstraction library, but it\'s not in the lib' .DIRECTORY_SEPARATOR. 'adodb directory!';
	}
	else
	{
		# Load (full) ADOdb
		require($full_adodb);
		$loaded_adodb = true;
	}
	
	define('CMS_ADODB_DT', 'T');
}

function & adodb_connect()
{
	global $gCms;
	$config =& $gCms->GetConfig();
	
	$dsn = $config['dbms'].'://';
	if( $config['dbms'] == 'sqlite' )
	{
		$path = dirname(dirname(__FILE__));
		$dsn .= urlencode(cms_join_path($path, 'tmp', $config['db_name'])) .'/';
	}
	else
	{
		$dsn .= $config['db_username'].':'.rawurlencode($config['db_password']).'@'.$config['db_hostname'].'/'.$config['db_name'];
		$opt = array();
		if(isset($config['persistent_db_conn']) && $config['persistent_db_conn'] == true) $opt[] = 'persist';
		if( !empty($config['db_port']) )
		{
			$opt[] = 'port='.$config['db_port'];
		}
		elseif( !empty($config['db_socket']) && $config['dbms'] == 'mysqli')
		{
			$opt[] = 'socket='.$config['db_socket'];
		}
		$dsn .= ((count($opt) > 0) ? '?'.implode('&', $opt) : '');
	}

	//define('ADODB_ERROR_HANDLER', 'adodb_error');
	$dbinstance = ADONewConnection( $dsn );
	$dbinstance->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if ($config['debug'] == true)
	{
		$dbinstance->debug = true;
	}
	
	if ($config['dbms'] == 'sqlite')
	{
		$dbinstance->Execute('PRAGMA short_column_names = 1;');
		sqlite_create_function($dbinstance->_connectionID, 'now', 'time', 0);
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

function adodb_error($dbtype, $function_performed, $error_number, $error_message, $host, $database, &$connection_obj)
{
	if(file_exists(cms_join_path(dirname(CONFIG_FILE_LOCATION), 'db_error.html')))
	{
		include_once cms_join_path(dirname(CONFIG_FILE_LOCATION), 'db_error.html');
	}
	else
	{
		echo "<strong>Database Connection Failed</strong><br />";
		echo "Error: {$error_message} ({$error_number})<br />";
		echo "Function Performed: {$function_performed}<br />";
		echo "Host/DB: {$host}/{$database}<br />";
		echo "Database Type: {$dbtype}<br />";
	}
	die();
}
?>
