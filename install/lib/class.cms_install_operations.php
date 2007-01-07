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

class CmsInstallOperations extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	static function create_table($db, $table, $fields)
	{
		$db = cms_db();
		
		$dbdict = NewDataDictionary($db);
		$taboptarray = array('mysql' => 'TYPE=MyISAM');

		$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix().$table, $fields, $taboptarray);
		$dbdict->ExecuteSQLArray($sqlarray);
	}
	
	static function create_index($db, $table, $field)
	{
		$db = cms_db();
		
		$dbdict = NewDataDictionary($db);

		$sqlarray = $dbdict->CreateIndexSQL($field, $db_prefix.$table, $field);
		$dbdict->ExecuteSQLArray($sqlarray);
	}
	
	static function get_action()
	{
		$value = CmsRequest::get('action');
		return $value != '' ? $value : 'intro';
	}
	
	static function get_language_list()
	{
		return array('en_US' => 'English/US', 'fr_FR' => 'French');
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
	
	static function set_language_cookie($value)
	{
		CmsRequest::set_cookie('cms_install_lang', $value);
	}
	
	static function required_setting_output($bool)
	{
		return $bool ? '<span class="yes">'.self::_('Yes').'</span>' : '<span class="no">'.self::_('No').'</span>';
	}
	
	static function recommended_setting_output($state, $opposite = false)
	{
		return $state ? '<span class="yes">'.(!$opposite ? self::_('On') : self::_('Off')).'</span>' : '<span class="no">'.(!$opposite ? self::_('Off') : self::_('On')).'</span>';
	}
	
	static function required_checks()
	{
		$result = array();
		
		$result['php_version'] = version_compare(phpversion(), "5.0.4", ">=");
		$result['has_database'] = count(self::get_loaded_database_modules()) > 0;
		$result['which_database'] = self::_('No Drivers Loaded');
		if ($result['has_database'])
		{
			$result['which_database'] = implode(',', self::get_loaded_database_modules());
		}
		$result['has_xml'] = extension_loaded('xml');
		$result['has_simplexml'] = extension_loaded('SimpleXML');
		$result['canwrite_templates'] = is_writable(cms_join_path(dirname(dirname(dirname(__FILE__))),'tmp','templates_c'));
		$result['canwrite_cache'] = is_writable(cms_join_path(dirname(dirname(dirname(__FILE__))),'tmp','cache'));
		
		$count = count(array_keys($result, true, true));
		$result['failure'] = ((count($result) - 1) != $count);
		
		return $result;
	}
	
	static function recommended_checks()
	{
		$result = array();

		$result['file_uploads'] = (ini_get('file_uploads') == '1');
		$result['safe_mode'] = (ini_get('safe_mode') != '1');
		$result['magic_quotes_runtime'] = (ini_get('magic_quotes_runtime') != '1');
		$result['register_globals'] = (ini_get('register_globals') != '1');
		$result['output_buffering'] = (ini_get('output_buffering') != '1');
		$result['canwrite_uploads'] = is_writable(cms_join_path(dirname(dirname(dirname(__FILE__))),'uploads'));
		$result['canwrite_modules'] = is_writable(cms_join_path(dirname(dirname(dirname(__FILE__))),'modules'));
		
		$count = count(array_keys($result, true, true));
		$result['failure'] = (count($result) != $count);
		
		return $result;
	}
	
	static function get_loaded_database_modules()
	{
		$which = array();
		if (extension_loaded('mysql'))
			$which[] = 'mysql';
		if (extension_loaded('mysqli'))
			$which[] = 'mysqli';
		if (extension_loaded('pgsql'))
			$which[] = 'pgsql';
		if (extension_loaded('SQLite'))
			$which[] = 'sqlite';
		return $which;
	}
	
	static function _()
	{
		$args = func_get_args();
		return count($args[0]) > 0 ? $args[0] : '';
	}
}

# vim:ts=4 sw=4 noet
?>