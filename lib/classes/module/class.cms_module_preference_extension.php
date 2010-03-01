<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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

class CmsModulePreferenceExtension extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	* Returns a module preference if it exists.
	*
	* @param string The name of the preference to check
	* @param string The default value, just in case it doesn't exist
	* @return string The preference, or default_value if it's not set
	*/
	public function get($preference_name, $default_value = '')
	{
		return CmsApplication::get_preference($this->module->get_name() . "_mapi_pref_" . $preference_name, $default_value);
	}

	/**
	* Sets a module preference.
	*
	* @param string The name of the preference to set
	* @param string The value to set it to
	*/
	public function set($preference_name, $value)
	{
		CmsApplication::set_preference($this->module->get_name() . "_mapi_pref_" . $preference_name, $value);
	}

	/**
	* Removes a module preference.  If no preference name
	* is specified, removes all module preferences.
	*
	* @param string The name of the preference to remove
	*/
	public function remove($preference_name = '')
	{
		if ($preference_name == '')
		{
			CmsApplication::remove_preference("^" . $this->module->get_name() . "_mapi_pref_", true);
		}
		else
		{
			CmsApplication::remove_preference($this->module->get_name() . "_mapi_pref_" . $preference_name);
		}
	}
}

# vim:ts=4 sw=4 noet
?>