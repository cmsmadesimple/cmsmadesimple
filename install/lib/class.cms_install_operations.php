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
		return $bool ? '<span class="Yes">'.self::_('Yes').'</span>' : '<span class="Yes">'.self::_('No').'</span>';
	}
	
	static function _()
	{
		$args = func_get_args();
		return count($args[0]) > 0 ? $args[0] : '';
	}
}

# vim:ts=4 sw=4 noet
?>