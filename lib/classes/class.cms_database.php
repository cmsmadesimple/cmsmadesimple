<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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
define('ADODB_OUTP', 'debug_sql');
define('CMS_ADODB_DT', CmsConfig::get('use_adodb_lite') ? 'DT' : 'T');

class CmsDatabase extends CmsObject
{
	static private $instance = NULL;

	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = CmsDatabase::start();
		}
		return self::$instance;
	}
	
	static function start()
	{
		$gCms = cmsms();
		$config = cms_config();

		global $USE_OLD_ADODB;
		
		$loaded_adodb = false;

		if ($config['use_adodb_lite'] == false || (isset($USE_OLD_ADODB) && $USE_OLD_ADODB == 1))
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
		        require($adodb_light);
		    }
		    else
		    {
		        # ADOdb cannot be found, show a message and stop the script execution
		        echo "The ADOdb Lite database abstraction library cannot be found, CMS Made Simple cannot load.";
		        die();
		    }
		}
		
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
		}
		
		//$dbinstance->debug = true;
		if ($config['debug'] == true)
		{
			$dbinstance->debug = true;
			#$dbinstance->LogSQL();
		}
		
	    if ($config['dbms'] == 'sqlite')
	    {
	        sqlite_create_function($dbinstance->_connectionID,'now','time',0);
	    }

		return $dbinstance;
	}
}

# vim:ts=4 sw=4 noet
?>