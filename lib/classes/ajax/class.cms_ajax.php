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
		if (cms_request('request:cms_ajax_function_name') != null)
		{
			//while(@ob_end_clean());
			$function_name = cms_request('request:cms_ajax_function_name');
			
			$json = json_decode(cms_request('request:cms_ajax_args'));
			$args = array();
			if (is_array($json))
			{
				foreach ($json as $ary)
				{
					if (is_array($ary) && $ary[0] == 'sf')
					{
						$result = array();
						parse_str($ary[1], $result);
						$args[] = $result;
					}
					else
					{
						$args[] = $ary;
					}
				}
			}
			
			cms_response()->add_header('Content-Type', "text/html; charset=utf-8");
			cms_response()->body(call_user_func_array($function_name, $args));
			cms_response()->render();
			exit;
		}
	}
	
	function get_javascript()
	{
		$result = '';
		$result .= "var cms_ajax_callback_url = \"" . CmsRequest::get_requested_uri() . "\";\n";

		foreach ($this->registered_functions as $function)
		{
			$result .= 'function cms_ajax_' . $function . '(){ return cms_ajax_call("' . $function . '", arguments, {}); }' . "\n";
		}
		
		return "<script type=\"text/javascript\">\n<!--\n" . $result . "-->\n</script>\n<script type=\"text/javascript\" src=\"" . CmsRequest::get_calculated_url_base(true) . "/lib/jquery/jquery.cmsms.js\"></script>\n";
	}
}

# vim:ts=4 sw=4 noet
?>