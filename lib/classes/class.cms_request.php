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

class CmsRequest extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Sets up various things important for incoming requests
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public static function setup()
	{
		#magic_quotes_runtime is a nuisance...  turn it off before it messes something up
		set_magic_quotes_runtime(false);
		
		# sanitize $_GET
		array_walk_recursive($_GET, array('CmsRequest', 'sanitize_get_var'));
		
		CmsRequest::strip_slashes_from_globals();
		
		#Fix for IIS (and others) to make sure REQUEST_URI is filled in
		if (!isset($_SERVER['REQUEST_URI']))
		{
		    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		    if(isset($_SERVER['QUERY_STRING']))
		    {
		        $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
		    }
		}
	}
	
	/**
	 * Removes possible javascript from a string
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public static function sanitize_get_var(&$value, $key)
	{
		$value = eregi_replace('\<\/?script[^\>]*\>', '', $value);
	}
	
	/**
	 * Strips the slashes from all incoming superglobals,
	 * if necessary.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public static function strip_slashes_from_globals()
	{
		if (get_magic_quotes_gpc())
		{
		    CmsRequest::stripslashes_deep($_GET);
		    CmsRequest::stripslashes_deep($_POST);
		    CmsRequest::stripslashes_deep($_REQUEST);
		    CmsRequest::stripslashes_deep($_COOKIE);
		    CmsRequest::stripslashes_deep($_SESSION);
		}
	}
	
	function stripslashes_deep($value)
	{
		if (is_array($value))
		{
			$value = array_map(array('CmsRequest', 'stripslashes_deep'), $value);
		}
		elseif (!empty($value) && is_string($value))
		{
			$value = stripslashes($value);
		}
		return $value;
	}
	
	/**
	 * Sanitize input to prevent against XSS and other nasty stuff.
	 * Taken from cakephp (http://cakephp.org)
	 * Licensed under the MIT License
	 *
	 * @return string The cleansed string
	 **/
	public static function clean_value($val)
	{
		if ($val == "")
		{
			return $val;
		}
		//Replace odd spaces with safe ones
		$val = str_replace(" ", " ", $val);
		$val = str_replace(chr(0xCA), "", $val);
		//Encode any HTML to entities (including \n --> <br />)
		$val = CmsRequest::clean_html($val);
		//Double-check special chars and remove carriage returns
		//For increased SQL security
		$val = preg_replace("/\\\$/", "$", $val);
		$val = preg_replace("/\r/", "", $val);
		$val = str_replace("!", "!", $val);
		$val = str_replace("'", "'", $val);
		//Allow unicode (?)
		$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val);
		//Add slashes for SQL
		//$val = $this->sql($val);
		//Swap user-inputted backslashes (?)
		$val = preg_replace("/\\\(?!&amp;#|\?#)/", "\\", $val);
		return $val;
	}
	
	/**
	 * Method to sanitize incoming html.
	 * Take from cakephp (http://cakephp.org)
	 * Licensed under the MIT License
	 *
	 * @return string The cleansed string
	 **/
	public static function clean_html($string, $remove = false)
	{
		if ($remove)
		{
			$string = strip_tags($string);
		}
		else
		{
			$patterns = array("/\&/", "/%/", "/</", "/>/", '/"/', "/'/", "/\(/", "/\)/", "/\+/", "/-/");
			$replacements = array("&amp;", "&#37;", "&lt;", "&gt;", "&quot;", "&#39;", "&#40;", "&#41;", "&#43;", "&#45;");
			$string = preg_replace($patterns, $replacements, $string);
		}
		return $string;
	}
}

# vim:ts=4 sw=4 noet
?>