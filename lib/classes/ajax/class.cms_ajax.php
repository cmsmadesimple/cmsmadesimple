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

class CmsAjax extends CmsObject
{
	var $registered_funtions = array();

	function __construct()
	{
		parent::__construct();
	}
	
	function register_function($function)
	{
		$this->registered_functions[] = $function;
	}
	
	function process_requests()
	{
		if (isset($_REQUEST['cms_ajax_function_name']))
		{
			header("Content-Type: text/xml; charset=utf-8");
			$function_name = $_REQUEST['cms_ajax_function_name'];
			
			$xml = new SimpleXMLElement($_REQUEST['cms_ajax_args']);
			$args = array();
			if ($xml)
			{
				foreach ($xml->xpath('/ajaxarray/*') as $element)
				{
					switch($element->getName())
					{
						case 'e':
							$args[] = (string)$element;
							break;
						case 'sf':
							$str = (string)$element;
							$result = array();

							parse_str($str, $result);
							
							$args[] = $result;
							break;
					}
				}
			}
			
			echo call_user_func_array($function_name, $args);
			exit;
		}
	}
	
	function get_javascript()
	{
		$result = '';
		$result .= "var cms_ajax_callback_url = \"" . CmsRequest::get_requested_uri() . "\";\n";

		foreach ($this->registered_functions as $function)
		{
			$result .= 'function cms_ajax_' . $function . '(){ return cms_ajax_call("' . $function . '", arguments, 1); }' . "\n";
		}
		
		return "<script type=\"text/javascript\">\n<!--\n" . $result . "-->\n</script>\n<script type=\"text/javascript\" src=\"" . CmsConfig::get('root_url') . "/lib/js/cms_ajax.js\"></script>\n";
	}
}

# vim:ts=4 sw=4 noet
?>