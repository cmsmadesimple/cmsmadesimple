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

/**
 * Class to handle details of storing multilanguage content and properties
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsMultiLanguage extends CmsObject
{
	public static $current_language = '';

	public static function get_client_language()
	{
		//TODO: 
		//1. See if there is a forced language from the url (in $current_language)
		//2. Try to grab a usable language from the browser
		//3. Use the default language of the site
		if (self::$current_language != '')
		{
			return self::$current_language;
		}

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$browser_languages = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$cms_languages = self::get_enabled_languages();
			
			//Hackish because of the case insensitivity needed
			foreach ($cms_languages as $cms_lang)
			{
				foreach ($browser_languages as $browser_lang)
				{
					if (strtolower($cms_lang) == strtolower(str_replace('-', '_', $browser_lang)))
					{
						return $cms_lang;
					}
					//TODO: Logic to handle browser sending a 2 char code?
				}
			}
		}

		return self::get_default_language();
	}
	
	public static function get_default_language()
	{
		return CmsApplication::get_preference('default_language', 'en_US');
	}
	
	public static function get_enabled_languages()
	{
		return explode(',', CmsApplication::get_preference('enabled_languages', 'en_US'));
	}
	
	public static function get_enabled_languages_as_hash()
	{
		$enabled = CmsMultiLanguage::get_enabled_languages();

		$result = array();
		foreach (CmsLanguage::get_language_list(true) as $k=>$v)
		{
			if (in_array($k, $enabled))
			{
				$result[$k] = $v;
			}
		}

		return $result;
	}
	
	/**
	 * Get a list of languages that this content is currently translated to
	 *
	 * @param string The name of the module ('Core' is a valid option)
	 * @param string The type of content
	 * @param integer The optional id of the object
	 * @param string The name of the property
	 * @return array List of languages
	 * @author Ted Kulp
	 **/
	public static function get_existing_translations($module_name, $content_type, $object_id = -1, $property_name = '')
	{
		$db = cms_db();
		
		$result = array();
		
		$dbresult = $db->Execute('SELECT language FROM '.cms_db_prefix().'multilanguage WHERE module_name = ? and content_type = ? and object_id = ? and property_name = ?', array($module_name, $content_type, $object_id, $property_name));
		while ($dbresult && !$dbresult->EOF)
		{
			$result[] = $dbresult->fields['language'];
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();
		
		return $result;
	}
	
	/**
	 * Pull content from the mutlilanguage subsystem.  If nothing is found, then return the original_content
	 * if it's sent along.
	 *
	 * @param string The name of the module ('Core' is a valid option)
	 * @param string The type of content
	 * @param integer The optional id of the object
	 * @param string The name of the property
	 * @param string The original content.  This is returned if no lookup is found.
	 * @param string The language to return.  No language sent means we try to figure it out first.
	 * @return string The new content if the lookup is successful, or the original content if not.
	 * @author Ted Kulp
	 **/
	public static function get_content($module_name, $content_type, $object_id = -1, $property_name = '', $original_content = '', $language = '')
	{
		//TODO: Run through CmsCache function caching
		return self::get_content_impl($module_name, $content_type, $object_id, $property_name, $original_content);
	}
	
	/**
	 * Private implementation of the get_content method.  This is the one that will get cached.
	 *
	 * @param string The name of the module ('Core' is a valid option)
	 * @param string The type of content
	 * @param integer The optional id of the object
	 * @param string The name of the property
	 * @param string The original content.  This is returned if no lookup is found.
	 * @param string The language to return.  No language sent means we try to figure it out first.
	 * @return string The new content if the lookup is successful, or the original content if not.
	 * @author Ted Kulp
	 **/
	private static function get_content_impl($module_name, $content_type, $object_id = -1, $property_name = '', $original_content = '', $language = '')
	{
		$db = cms_db();
		$language = ($language != '' ? $language : self::get_client_language());
		
		$result = $db->GetRow('SELECT content FROM ' . cms_db_prefix() . 'multilanguage WHERE module_name = ? AND content_type = ? and object_id = ? and property_name = ? and language = ?', array($module_name, $content_type, $object_id, $property_name, $language));
		if ($result)
		{
			if ($result['content'])
			{
				return $result['content'];
			}
		}

		return $original_content;
	}
	
	public static function get_all_content($module_name, $content_type, $object_id)
	{
		$content = array();
		$db = cms_db();
		
		$dbresult = $db->Execute('SELECT property_name, language, content FROM ' . cms_db_prefix() . 'multilanguage WHERE module_name = ? AND content_type = ? and object_id = ?', array($module_name, $content_type, $object_id));
		while ($dbresult && !$dbresult->EOF)
		{
			$content[$dbresult->fields['property_name']][$dbresult->fields['language']] = $dbresult->fields['content'];
			$dbresult->MoveNext();
		}

		return $content;
	}
	
	/**
	 * Save content translations to the multilanguage subsystem.
	 *
	 * @param string The name of the module ('Core' is a valid option)
	 * @param string The type of content
	 * @param integer The optional id of the object
	 * @param string The name of the property
	 * @param string The content to save.
	 * @param string The language to save.  No language sent means we try to figure it out first.
	 * @return boolean Whether or not the save was successful
	 * @author Ted Kulp
	 **/
	public static function set_content($module_name, $content_type, $object_id = -1, $property_name = '', $content = '', $language = '')
	{
		$language = ($language != '' ? $language : self::get_client_language());
		$db = cms_db();
		$time = $db->DBTimeStamp(time());
		
		//Call the non-cached version to see if it exists.  Better safe than sorry.
		if (self::get_content_impl($module_name, $content_type, $object_id, $property_name, null, $language) == null)
		{
			//It's a new record.  Do the insert.
			return $db->Execute("INSERT INTO ".cms_db_prefix()."multilanguage (module_name, content_type, object_id, property_name, language, content, create_date, modified_date) VALUES (?, ?, ?, ?, ?, ?, {$time}, {$time})", array($module_name, $content_type, $object_id, $property_name, $language, $content));
		}
		else
		{
			//Else, do the update
			return $db->Execute("UPDATE ".cms_db_prefix()."multilanguage SET content = ?, modified_date = {$time} WHERE module_name = ? AND content_type = ? and object_id = ? and property_name = ? and language = ?", array($content, $module_name, $content_type, $object_id, $property_name, $language));
		}
	}
	
	/**
	 * Deletes content from the multilanguage subsystem
	 *
	 * @param string The name of the module ('Core' is a valid option)
	 * @param string The type of content
	 * @param integer The optional id of the object
	 * @param string The name of the property
	 * @param string The language to remove.  If no language is sent, all languages are removed.
	 * @return boolean
	 * @author Ted Kulp
	 **/
	public static function delete_content($module_name, $content_type, $object_id = -1, $property_name = '', $language = '')
	{
		$db = cms_db();
		
		$query = "DELETE FROM " . cms_db_prefix() . "multilanguage WHERE module_name = ? and content_type = ? and object_id = ? and property_name = ?";
		$params = array($module_name, $content_type, $object_id, $property_name);
		
		if ($language != '')
		{
			$query .= ' AND language = ?';
			$params[] = $language;
		}

		return $db->Execute($query, $params);
	}
	
	public static function delete_all_content($module_name, $content_type, $object_id = -1)
	{
		$db = cms_db();
		
		$query = "DELETE FROM " . cms_db_prefix() . "multilanguage WHERE module_name = ? and content_type = ? and object_id = ?";
		$params = array($module_name, $content_type, $object_id);

		return $db->Execute($query, $params);
	}
}

?>