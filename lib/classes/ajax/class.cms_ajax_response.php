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

class CmsAjaxResponse extends CmsObject
{
	var $result = null;

	function __construct()
	{
		parent::__construct();
		$this->result = array();
	}
	
	function replace_html($selector, $text)
	{
		$text = $this->json_safe($text);
		$this->script("jQuery('{$selector}').html('{$text}')");
	}
	
	function replace($selector, $attribute, $text)
	{
		$text = $this->json_safe($text);
		$this->script("jQuery('{$selector}').attr('{$attribute}', '{$text}')");
	}
	
	function insert($selector, $text, $position = "append")
	{
		$text = $this->json_safe($text);
		$position = trim(strtolower($position));
		if (in_array($position, array('before', 'after', 'prepend', 'append')))
		{
			$this->script("jQuery('{$selector}').{$position}('{$text}')");
		}
	}
	
	function remove($selector)
	{
		$this->script("jQuery('{$selector}').remove()");
	}
	
	function show($selector)
	{
		$this->script("jQuery('{$selector}').show()");
	}
	
	function hide($selector)
	{
		$this->script("jQuery('{$selector}').hide()");
	}
	
	function toggle($selector)
	{
		$this->script("jQuery('{$selector}').toggle()");
	}
	
	function script($text)
	{
		$this->result[] = array("sc", $text);
	}
	
	function json_safe($text)
	{
		$text = str_replace("'", "\\'", $text);
		$text = str_replace("\r", "\\r", $text);
		$text = str_replace("\n", "\\n", $text);
		return $text;
	}
	
	function get_result()
	{
		while(@ob_end_clean());
		header("Content-Type: application/json; charset=utf-8");
		if (in_debug())
		{
			$this->result[] = array('__debug', CmsProfiler::get_instance()->report(true, true, '', false));
		}
		return json_encode($this->result);
	}
}

# vim:ts=4 sw=4 noet
?>