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

class CmsInstallOperations extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}


	/* LANGUAGES */
	static function _()
	{
		$args = func_get_args();
		return count($args[0]) > 0 ? $args[0] : '';
	}

	static function get_language_list()
	{
		return CmsLanguage::get_language_list();
	}

	static function get_language_cookie()
	{
		if (CmsRequest::has('select_language'))
		{
			$value = CmsRequest::get('select_language');
			self::set_language_cookie($value);
		}
		else
		{
			$value = CmsRequest::get_cookie('cms_install_lang');
		}
		return $value != '' ? $value : 'en_US';
	}


	/* SQL */
	static function create_table($db, $table, $fields)
	{	
		$dbdict = NewDataDictionary($db);
		$taboptarray = array('mysql' => 'ENGINE=InnoDB DEFAULT CHARSET=utf8', 'mysqli' => 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

		$sqlarray = $dbdict->CreateTableSQL(CmsDatabase::get_prefix().$table, $fields, $taboptarray);
		if (count($sqlarray))
		{
#			$sqlarray[0] .= "\n/*!40100 DEFAULT CHARACTER SET UTF8 */";
		}
		$_return = $dbdict->ExecuteSQLArray($sqlarray);
		$_ado_ret = ($_return == 2) ? lang('done') : lang('failed');

		return lang('install_creating_table', $table, $_ado_ret);
	}

	static function create_index($db, $table, $name, $field)
	{	
		$dbdict = NewDataDictionary($db);

		$sqlarray = $dbdict->CreateIndexSQL($name, CmsDatabase::get_prefix().$table, $field);
		$_return = $dbdict->ExecuteSQLArray($sqlarray);
		$_ado_ret = ($_return == 2) ? lang('done') : lang('failed');

		return lang('install_creating_index', $table, $_ado_ret);
	}

	static function drop_table($db, $table)
	{
		$dbdict = NewDataDictionary($db);

		$sqlarray = $dbdict->DropTableSQL(CmsDatabase::get_prefix().$table);
		$_return = $dbdict->ExecuteSQLArray($sqlarray);
		$_ado_ret = ($_return == 2) ? lang('done') : lang('failed');

		return lang('install_creating_index', $table, $_ado_ret);
	}


	/* DISPLAY */
	static function show_error_msg($error)
	{
		include cms_join_path(CMS_INSTALL_BASE, 'templates', 'installer_start.tpl');
		echo '<p class="error">' . $error . '</p>';
		include cms_join_path(CMS_INSTALL_BASE, 'templates', 'installer_end.tpl');
		exit;
	}

	static function displayContent($process, $current_page, $number_of_pages, $debug = '')
	{
		$smarty = CmsSmarty::get_instance(false);
		$smarty->compile_dir = TMP_TEMPLATES_C_LOCATION;
		$smarty->cache_dir = TMP_CACHE_LOCATION;
		$smarty->template_dir = cms_join_path(CMS_INSTALL_BASE, 'templates'.DS);
		$smarty->caching = false;
		$smarty->debugging = false;
		$smarty->force_compile = true;
		$smarty->plugins_dir = array(cms_join_path(CMS_BASE, 'lib', 'smarty', 'plugins'.DS), cms_join_path(CMS_INSTALL_BASE, 'plugins'.DS));

		if(! empty($current_page))
		{
			if( (CmsRequest::get('check')) || (CmsRequest::get('recheck')) )
				$current_page--;

			// Assign smarty variables
			$smarty->assign('number_of_pages', $number_of_pages);
			$smarty->assign('current_page', $current_page);
			$smarty->assign('cms_version', CMS_VERSION);
			$smarty->assign('cms_version_name', CMS_VERSION_NAME);

			require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'class.cms_'.$process.'page'.$current_page.'.php');
			$errors = CmsInstallPage::process_content($smarty, $debug);
			$smarty->assign_by_ref('errors', $errors);

			$xajax = new CmsAjax();
			$xajax->register_function('test_connection');
			$xajax->process_requests();
			$smarty->assign('xajax_header', $xajax->get_javascript());

			$smarty->assign('include_file', $process.$current_page.'.tpl');
			$smarty->display($process . 'page.tpl');
		}
		else
		{
			$smarty->assign('languages', self::get_language_list());
			$smarty->assign('selected_language', self::get_language_cookie());
			$smarty->display('installer_start.tpl');
			$smarty->display('installer_body.tpl');
			$smarty->display('installer_end.tpl');
		}
	}
















	static function get_action()
	{
		$value = CmsRequest::get('action');
		return $value != '' ? $value : 'intro';
	}

	static function required_setting_output($bool)
	{
		return $bool ? '<span class="yes">'.self::_('Yes').'</span>' : '<span class="no">'.self::_('No').'</span>';
	}
	
	static function recommended_setting_output($state, $opposite = false)
	{
		return $state ? '<span class="yes">'.(!$opposite ? self::_('On') : self::_('Off')).'</span>' : '<span class="no">'.(!$opposite ? self::_('Off') : self::_('On')).'</span>';
	}

	
	
	static function test_database_connection($driver = '', $hostname = '', $username = '', $password = '', $dbname = '')
	{
		$drivers = self::get_loaded_database_modules();
		$driver = $drivers[$driver];

		$have_connection = false;
		$have_create_ability = false;
		$have_existing_db = false;
		if ($username != '' && $hostname != '')
		{
			$db = self::get_database_connection($driver, $hostname, $username, $password);
			if ($db != null && $db->IsConnected())
			{
				$have_connection = true;
				//TODO: Get permissions here...
				$db->Close();

				//Ok, we have a connection.  Now, what about with the db name?
				if ($dbname != '')
				{
					$db = self::get_database_connection($driver, $hostname, $username, $password, $dbname);
					if ($db != null && $db->IsConnected())
					{
						$have_existing_db = true;
						$db->Close();
					}
				}
			}
		}
		
		return array('have_connection' => $have_connection, 'have_create_ability' => $have_create_ability, 'have_existing_db' => $have_existing_db);
	}
	
	static function get_database_connection($driver = '', $hostname = '', $username = '', $password = '', $dbname = '')
	{
		return CmsDatabase::connect($driver, $hostname, $username, $password, $dbname, false, false);
	}
	
	static function create_database($driver = '', $hostname = '', $username = '', $password = '', $dbname = '')
	{
		$drivers = self::get_loaded_database_modules();
		$driver = $drivers[$driver];

		if ($username != '' && $hostname != '')
		{
			$db = CmsDatabase::connect($driver, $hostname, $username, $password, '', false, false);
			if ($db != null && $db->IsConnected())
			{
				$dict = NewDataDictionary($db);
				if ($dict)
				{
					$dict->CreateNewDatabase($dbname);
					$db->Close();
					$db = CmsDatabase::connect($driver, $hostname, $username, $password, $dbname, false, false);
					if ($db != null && $db->IsConnected())
					{
						return true;
					}
				}
			}
		}

		return false;
	}
	
	static function install_config($conn,$url,&$file_data)
	{
		//Get an instance of CMSConfig(we need a list of all the variables plus defaults)
		$config = CmsConfig::get_instance();
		$config_data = $config->params;
		//Update the defaults with what we have. Start with database
		$drivers = CmsInstallOperations::get_loaded_database_modules();
		$config_data['dbms'] = $drivers[$conn['driver']];
		$config_data['db_username'] = $conn['username'];
		$config_data['db_hostname'] = $conn['hostname'];
		$config_data['db_password'] = $conn['password'];
		$config_data['db_name'] = $conn['dbname'];
		$config_data['db_prefix'] = $conn['table_prefix'];
		//Now location stuff
		$config_data['root_path'] = $url['rootpath'];
		$config_data['root_url'] = $url['rooturl'];
		$config_data["previews_path"] = $config_data["root_path"] . "/tmp/cache";
		$config_data["uploads_path"] = $config_data["root_path"] . "/uploads";
		$config_data["uploads_url"] = $config_data["root_url"] . "/uploads";
		$config["image_uploads_path"] = $config_data["root_path"] . "/uploads/images";
		$config["image_uploads_url"] = $config_data["root_url"] . "/uploads/images";
		//Generate the file
		$file_data = "<?php\n";
		$file_data .= $config->config_text($config_data);
		$file_data .= "\n?>";
		//Write the file
		return @file_put_contents(CONFIG_FILE_LOCATION,$file_data) > 0;
	}
	
	static function install_schema($driver = '', $hostname = '', $username = '', $password = '', $dbname = '', $prefix = '', $drop_tables = false)
	{
		$drivers = self::get_loaded_database_modules();
		$driver = $drivers[$driver];

		if ($username != '' && $hostname != '')
		{
			$db = CmsDatabase::connect($driver, $hostname, $username, $password, $dbname, false, false, $prefix);
			if ($db != null && $db->IsConnected())
			{
				try
				{
					if ($drop_tables)
					{
						include_once(cms_join_path(dirname(dirname(__FILE__)), 'schemas', 'droptables.php'));
					}
					include_once(cms_join_path(dirname(dirname(__FILE__)), 'schemas', 'schema.php'));
					
					//Install the core events after the database
					//is created.
					CmsEventOperations::setup_core_events();
					
					return true;
				}
				catch (Exception $e)
				{
					var_dump('There was an error creating the database');
					echo $db->ErrorMsg().'<br/>';
				}
			}
		}
		
		return false;
	}
	
	static function install_account($driver = '', $hostname = '', $dbusername = '', $dbpassword = '', $dbname = '', $prefix = '', $username = '', $password = '')
	{
		$drivers = self::get_loaded_database_modules();
		$driver = $drivers[$driver];

		if ($username != '' && $hostname != '')
		{
			$db = CmsDatabase::connect($driver, $hostname, $dbusername, $dbpassword, $dbname, true, false, $prefix);
			if ($db != null && $db->IsConnected())
			{
				$user = new CmsUser();
				$user->name = $username;
				$user->set_password($password);
				$user->active = true;
				$user->admin_access = true;
				if ($user->save())
				{
					$group = cms_orm()->cms_group->find_by_id(2);
					if ($group != null)
					{
						return $group->add_user($user) !== false;
					}
				}
			}
		}
		
		return false;
	}
	
	static function load_basic_schema($driver = '', $hostname = '', $dbusername = '', $dbpassword = '', $dbname = '', $prefix = '')
	{
		$drivers = self::get_loaded_database_modules();
		$driver = $drivers[$driver];

		$db = CmsDatabase::connect($driver, $hostname, $dbusername, $dbpassword, $dbname, false, false, $prefix);
		if ($db != null && $db->IsConnected())
		{
			$handle = fopen(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'schemas'.DIRECTORY_SEPARATOR.'basic.sql', 'r');

			if ($handle)
			{
				while (!feof($handle))
				{
					set_magic_quotes_runtime(false);
					$s = fgets($handle, 32768);
					if ($s != "")
					{
						$s = trim(preg_replace('/^INSERT INTO /', "INSERT INTO {$prefix}", $s));
						$s = str_replace("\\r\\n", "\r\n", $s);
						$s = str_replace("\\'", "''", $s);
						$s = str_replace('\\"', '"', $s);
						$result = $db->Execute($s);
						if (!$result)
						{
							die("Invalid query: $s");
						} ## if
					}
				}
				fclose($handle);
			}
		}
	}
	
}
# vim:ts=4 sw=4 noet
?>
