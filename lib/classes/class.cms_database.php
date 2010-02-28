<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

//Database related defines
define('CMS_ADODB_DT', 'T');
define('ADODB_OUTP', 'adodb_outp');

/**
 * Singleton class to represent a connection to the database.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsDatabase extends CmsObject
{
	static private $instance = NULL;
	static private $prefix = NULL;

	static public function get_instance($dbms = '', $hostname = '', $username = '', $password = '', $dbname = '', $debug = false)
	{
		if (self::$instance == NULL)
		{
			CmsDatabase::connect($dbms, $hostname, $username, $password, $dbname, $debug);
			CmsEventManager::send_event('Core:database_opened');
			CmsEventManager::register_event_handler('Core:shutdown_now', array('CmsDatabase', 'close'));
		}
		return self::$instance;
	}
	
	static public function close()
	{
		if (self::$instance !== null)
		{
			if (self::$instance->IsConnected())
			{
				self::$instance->Close();
				CmsEventManager::send_event('Core:database_closed');
			}
		}
	}
	
	static public function unload()
	{
		if (self::$instance !== null)
			self::$instance->close();
		
		self::$instance = null;
	}
	
	static public function get_prefix()
	{
		if (self::$prefix === null)
		{
			self::$prefix = CmsConfig::get('db_prefix');
			define('CMS_DB_PREFIX', self::$prefix);
		}
		return self::$prefix;
	}
	
	static function connect($dbms = '', $hostname = '', $username = '', $password = '', $dbname = '', $debug = false, $die = true, $prefix = null, $make_global = true)
	{
		$gCms = cmsms();
		$persistent = false;
		
		if ($dbms == '')
		{
			$config = cms_config();
			$dbms = $config['dbms'];
			$hostname = $config['db_hostname'];
			$username = $config['db_username'];
			$password = $config['db_password'];
			$dbname = $config['db_name'];
			$debug = $config['debug'];
			$persistent = $config['persistent_db_conn'];
		}
		
		if ($prefix !== null)
		{
			self::$prefix = $prefix;
		}
		
		$dbinstance = null;

		$_GLOBALS['ADODB_CACHE_DIR'] = cms_join_path(ROOT_DIR, 'tmp', 'cache');

		//require_once(cms_join_path(ROOT_DIR, 'lib', 'adodb', 'adodb-exceptions.inc.php'));
		require_once(cms_join_path(ROOT_DIR, 'lib', 'adodb', 'adodb.inc.php'));

		try
		{
			$dbinstance = ADONewConnection($dbms);
			$dbinstance->fnExecute = 'pre_parse_query';
			$dbinstance->fnCacheExecute = 'count_cached_execs';
	
			if ($persistent)
			{
				$connect_result = $dbinstance->PConnect($hostname, $username, $password, $dbname);
			}
			else
			{
				$connect_result = $dbinstance->Connect($hostname, $username, $password, $dbname);
			}
		}
		catch (exception $e)
		{
			if ($die)
			{
				echo "<strong>Database Connection Failed</strong><br />";
				echo "Error: {$dbinstance->_errorMsg}<br />";
				echo "Function Performed: {$e->fn}<br />";
				echo "Host/DB: {$e->host}/{$e->database}<br />";
				echo "Database Type: {$dbms}<br />";
				die();
			}
			else
			{
				return null;
			}
		}

		$dbinstance->SetFetchMode(ADODB_FETCH_ASSOC);
		$dbinstance->debug = $debug;
		
	    if ($dbms == 'sqlite')
	    {
			$dbinstance->Execute("PRAGMA short_column_names = 1;");
	        sqlite_create_function($dbinstance->_connectionID,'now','time',0);
	    }
		else
		{
			//if(!empty($config['default_encoding']) && $config['default_encoding'] == 'utf-8' && $config['set_names'] == true)
			//{
				try
				{
					$dbinstance->Execute("SET NAMES 'utf8'");
				}
				catch (exception $e)
				{
					//Ignore for now
				}
			//}
			//$dbinstance->debug = true;
		}
	
		if ($make_global)
		{
			self::$instance = $dbinstance;
		}
		
		//Initialize the CMS_DB_PREFIX define
		self::get_prefix();
		
		if(class_exists('Memcache') && CmsConfig::get('use_memcache') == true)
		{
			self::$instance->memCache = true; /// should we use memCache instead of caching in files
			self::$instance->memCacheHost = array(CmsConfig::get('memcache_server')); /// 
			self::$instance->memCachePort = CmsConfig::get('memcache_port'); /// this is default memCache port
		}

		return $dbinstance;
	}
	
	public static function query_count()
	{
		if (method_exists(self::$instance, 'query_count'))
		{
			return self::$instance->query_count();
		}
		else
		{
			global $EXECS;
			global $CACHED;
			return $EXECS + $CACHED;
		}
	}
	
	public static function create_table($table, $fields)
	{	
		$dbdict = NewDataDictionary(self::get_instance());
		$taboptarray = array('mysql' => 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

		$sqlarray = $dbdict->CreateTableSQL(self::get_prefix().$table, $fields, $taboptarray);
		if (count($sqlarray))
		{
#			$sqlarray[0] .= "\n/*!40100 DEFAULT CHARACTER SET UTF8 */";
		}
		$dbdict->ExecuteSQLArray($sqlarray);
	}
	
	public static function create_index($table, $name, $field)
	{	
		$dbdict = NewDataDictionary(self::get_instance());

		$sqlarray = $dbdict->CreateIndexSQL($name, self::get_prefix().$table, $field);
		$dbdict->ExecuteSQLArray($sqlarray);
	}
	
	public static function drop_table($table)
	{
		$dbdict = NewDataDictionary(self::get_instance());

		$sqlarray = $dbdict->DropTableSQL(self::get_prefix().$table);
		$dbdict->ExecuteSQLArray($sqlarray);
	}
}

function adodb_outp($msg, $newline = true)
{
	if ($newline)
		$msg .= "<br>\n";

	$msg = str_replace('<hr />', '', $msg);

	CmsProfiler::get_instance()->mark($msg);
}

//TODO: Clean me up.  Globals?  Yuck!
function pre_parse_query($db, &$sql, $inputarray)
{
	//CmsProfiler::get_instance()->mark($sql);
	
	$sql = strtr($sql, array('{' => cms_db_prefix(), '}' => ''));

	global $EXECS;

	if (!is_array($inputarray))
		$EXECS++;
	else if (is_array(reset($inputarray)))
		$EXECS += sizeof($inputarray);
	else
		$EXECS++;

	$null = null;
	return $null;
}

//TODO: You too, slacker!
function count_cached_execs($db, $secs2cache, $sql, $inputarray)
{
	CmsProfiler::get_instance()->mark('CACHED:' . $sql);

	global $CACHED; $CACHED++;
}

# vim:ts=4 sw=4 noet
?>