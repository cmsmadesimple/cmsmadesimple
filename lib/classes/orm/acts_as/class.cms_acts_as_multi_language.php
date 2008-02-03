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

class CmsActsAsMultiLanguage extends CmsActsAs
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function setup(&$obj)
	{
		$obj->multi_lang_content = array();
	}
	
	public function get_multi_language_content(&$obj, $field, $lang = 'en_US')
	{
		return isset($obj->multi_lang_content[$field][$lang]) ? $obj->multi_lang_content[$field][$lang] : '';
	}
	
	public function set_multi_language_content(&$obj, $field, $lang, $content)
	{
		$ary = $obj->multi_lang_content;
		$ary[$field][$lang] = $content;
		$obj->multi_lang_content = $ary;
	}
	
	public function after_load(&$obj)
	{
		//Load up any existing multi lang content
		$module = isset($obj->module_name) ? $obj->module_name : 'core';
		$content_type = isset($obj->content_type) ? $obj->content_type : get_class($obj);
		$obj->multi_lang_content = CmsMultiLanguage::get_all_content($module, $content_type, $obj->id);
	}
	
	public function before_save(&$obj)
	{
		
	}
	
	public function after_save(&$obj)
	{
		$module = isset($obj->module_name) ? $obj->module_name : 'core';
		$content_type = isset($obj->content_type) ? $obj->content_type : get_class($obj);
		$ary = $obj->multi_lang_content;
		foreach ($ary as $field=>$v)
		{
			foreach ($v as $lang=>$content)
			{
				CmsMultiLanguage::set_content($module, $content_type, $obj->id, $field, $content, $lang);
			}
		}
	}
	
	public function before_delete(&$obj)
	{
		
	}
	
	public function after_delete(&$obj)
	{
		$module = isset($obj->module_name) ? $obj->module_name : 'core';
		$content_type = isset($obj->content_type) ? $obj->content_type : get_class($obj);
		CmsMultiLanguage::delete_all_content($module, $content_type, $obj->id);
	}
}

# vim:ts=4 sw=4 noet
?>