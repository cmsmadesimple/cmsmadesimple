<?php

function load_adodb() {
	global $config;
	
	if ($config['debug'] == true)
	{
		require_once(cms_join_path(dirname(__FILE__), $config['use_adodb_lite'] == true ? 'adodb_lite' : 'adodb', 'adodb-errorpear.inc.php'));
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
			echo "The ADOdb Lite database abstraction library cannot be found, CMS Made Simple cannot load.";
			die();
		}
	}
	
	#Define the CMS_ADODB_DT constant
	define('CMS_ADODB_DT', $config['use_adodb_lite'] ? 'DT' : 'T');
}

function & adodb_connect()
{
	global $db, $config;
	
	$dbinstance = &ADONewConnection($config['dbms'], 'pear:date:extend:transaction');
	if (isset($config['persistent_db_conn']) && $config['persistent_db_conn'] == true)
	{
		$connect_result = $dbinstance->PConnect($config["db_hostname"],$config["db_username"],$config["db_password"],$config["db_name"]);
	}
	else
	{
		$connect_result = $dbinstance->Connect($config["db_hostname"],$config["db_username"],$config["db_password"],$config["db_name"]);
	}
	if (FALSE == $connect_result)
	{
		die('Database Connection failed');
	}
	$dbinstance->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if ($config['dbms'] == 'sqlite')
	{
		$dbinstance->Execute("PRAGMA short_column_names = 1;");
		sqlite_create_function($cmsdb->_connectionID,'now','time',0);
	}
	
	//$dbinstance->debug = true;
	if ($config['debug'] == true)
	{
		$dbinstance->debug = true;
		#$dbinstance->LogSQL();
	}
	$db =& $dbinstance;
	return $db;
}

?>