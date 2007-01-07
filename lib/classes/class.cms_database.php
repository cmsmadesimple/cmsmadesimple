<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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
define('ADODB_OUTP', 'mark_sql');
define('CMS_ADODB_DT', CmsConfig::get('use_adodb_lite') ? 'DT' : 'T');

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

	static public function get_instance($dbms = '', $hostname = '', $username = '', $password = '', $dbname = '', $debug = false)
	{
		if (self::$instance == NULL)
		{
			self::$instance = CmsDatabase::start($dbms, $hostname, $username, $password, $dbname, $debug);
		}
		return self::$instance;
	}
	
	static public function close()
	{
		if (self::$instance != NULL)
		{
			if (self::$instance->IsConnected())
			{
				self::$instance->Close();
			}
		}
	}
	
	static private function start($dbms = '', $hostname = '', $username = '', $password = '', $dbname = '', $debug = false)
	{
		$gCms = cmsms();
		$use_adodb_lite = true;
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
			$use_adodb_lite = $config['use_adodb_lite'];
		}

		global $USE_OLD_ADODB;
		
		$loaded_adodb = false;
		$dbinstance = null;

		if (!$use_adodb_lite || (isset($USE_OLD_ADODB) && $USE_OLD_ADODB == 1))
		{
			# CMSMS is configured to use full ADOdb
		    $full_adodb = cms_join_path(dirname(dirname(__FILE__)),'adodb','adodb.inc.php');
		    if (! file_exists($full_adodb))
		    {
		        # Full ADOdb cannot be found, show a debug error message
		        $gCms->errors[] = 'CMS Made Simple is configured to use the full ADOdb Database Abstraction library, but it\'s not in the lib' .DIRECTORY_SEPARATOR. 'adodb directory. Switched back to ADOdb Lite.';
		    }
		    else
		    {
		        # Load (full) ADOdb
		        require($full_adodb);
				$dbinstance = &ADONewConnection($dbms);
		        $loaded_adodb = true;
		    }
		}
		if (!$loaded_adodb)
		{
		    $adodb_light = cms_join_path(dirname(dirname(__FILE__)),'adodb_lite','adodb.inc.php');
		    # The ADOdb library is not yet included, try ADOdb Lite
		    if (file_exists($adodb_light))
		    {
		        # Load ADOdb Lite
		        require_once($adodb_light);
				$dbinstance = &ADONewConnection($dbms, 'pear:date:extend:transaction');
				$loaded_adodb = true;
		    }
		    else
		    {
		        # ADOdb cannot be found, show a message and stop the script execution
		        echo "The ADOdb Lite database abstraction library cannot be found, CMS Made Simple cannot load.";
		        die();
		    }
		}
	
		if ($persistent)
		{
			$connect_result = $dbinstance->PConnect($hostname,$username,$password,$dbname);
		}
		else
		{
			$connect_result = $dbinstance->Connect($hostname,$username,$password,$dbname);
		}
		if (FALSE == $connect_result)
		{
			die('Database Connection failed');
		}
		$dbinstance->SetFetchMode(ADODB_FETCH_ASSOC);
		
		//$dbinstance->debug = true;
		#if ($debug == true)
		#{
			$dbinstance->debug = true;
		#}
		
	    if ($dbms == 'sqlite')
	    {
			$dbinstance->Execute("PRAGMA short_column_names = 1;");
	        sqlite_create_function($dbinstance->_connectionID,'now','time',0);
	    }

		return $dbinstance;
	}
}

/**
 * Function for adodb to call back to log sql queries and errors.
 **/
function mark_sql($sql, $newline)
{
	CmsProfiler::get_instance()->mark($sql);
}

# vim:ts=4 sw=4 noet
?>