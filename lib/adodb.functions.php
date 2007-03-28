<?php

function load_adodb() 
{
	global $config, $ADODB_vers;

	// @TODO: Remove dependence on PEAR for error handling	
	if ($config['debug'] == true)
	{
		#@include_once(cms_join_path(dirname(__FILE__), $config['use_adodb_lite'] == true ? 'adodb_lite' : 'adodb', 'adodb-errorpear.inc.php'));
	}
	
	define('ADODB_OUTP', 'debug_sql');
	
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

function & adodb_connect()
{
	global $config;
	
	$dbinstance =& ADONewConnection($config['dbms'], 'pear:date:extend:transaction');
	$conn_func = (isset($config['persistent_db_conn']) && $config['persistent_db_conn'] == true) ? 'PConnect' : 'Connect';
	$connect_result = $dbinstance->$conn_func($config['db_hostname'], $config['db_username'], $config['db_password'], $config['db_name']);
	
	if (FALSE == $connect_result)
	{
		die('Database Connection failed');
	}
	
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
	
	$db =& $dbinstance;
	return $db;
}

?>
