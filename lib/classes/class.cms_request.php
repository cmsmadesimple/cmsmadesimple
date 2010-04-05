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

/**
 * Static methods for handling web requests.
 *
 * @author Ted Kulp
 * @since 1.7
 * @license GPL
 **/
class CmsRequest extends CmsObject
{
	public $_get = array();
	public $_post = array();
	public $_cookie = array();
	public $_request = array();
	public $_env = array();
	
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();
		
		# sanitize $_GET
		array_walk_recursive($_GET, array('CmsRequest', 'sanitize_get_var'));
		
		#Fix for IIS (and others) to make sure REQUEST_URI is filled in
		if (!isset($_SERVER['REQUEST_URI']))
		{
		    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		    if(isset($_SERVER['QUERY_STRING']))
		    {
		        $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
		    }
		}
		
		$this->_get += $_GET;
		$this->_post += $_POST;
		$this->_cookie += $_COOKIE;
		
		$this->_env += (array)$_SERVER + (array)$_ENV;
		
		//Pull the module id into smarty if necessary
		$this->_request = $this->_get + $this->_post + $this->_cookie;
		if (isset($this->_request['mact']))
		{
			$ary = explode(',', cms_htmlentities($this->_request['mact']), 4);
			cms_smarty()->id = (isset($ary[1])?$ary[1]:'');
		}
		else
		{
			cms_smarty()->id = (isset($this->_request['id'])?intval($this->_request['id']):'');
		}
	}
	
	/**
	 * Returns an instnace of the CmsRequest singleton.
	 *
	 * @return CmsRequest The singleton CmsRequest instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsRequest();
		}
		return self::$instance;
	}
	
	public function has($name)
	{
		return $this->get($name, false) != null;
	}
	
	public function get($name, $clean = false)
	{
		$ret = null;
		
		list($var, $key) = explode(':', $name);
		if ($var && $key)
		{	
			if (in_array($var, array('request', 'get', 'post', 'cookie')))
			{
				$var = "_{$var}";
				$ret = isset($this->{$var}[$key]) ? $this->{$var}[$key] : null;
			}
			else if ($var == 'env')
			{
				$ret = env($key);
			}
		}
		else
		{
			$ret = isset($this->_request[$name]) ? $this->request[$name] : null;
		}
		
		if ($ret && $clean)
		{
			$ret = self::clean_value($ret);
		}
		
		return $ret;
	}
	
	public function env($name)
	{
		return isset($this->_env[$name]) ? $this->_env[$name] : null;
	}
	
	/**
	 * Removes possible javascript from a string
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public static function sanitize_get_var(&$value, $key)
	{
		$value = preg_replace('/\<\/?script[^\>]*\>/i', '', $value);
	}
	
	/**
	 * Get the internal id based on variables sent in from
	 * the request.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	static public function get_id_from_request()
	{
		$id = '';
		
		$smarty = cms_smarty();
		
		if (isset($params['mact']))
		{
			$ary = explode(',', cms_htmlentities($params['mact']), 4);
			$smarty->id = (isset($ary[1])?$ary[1]:'');
		}
		else
		{
			$smarty->id = (isset($params['id'])?intval($params['id']):'');
		}
		
		return $id;
	}
	
	public static function calculate_page_from_request()
	{
		$config = cms_config();
		$page = '';
		
		$id = cms_smarty()->id;

		if (isset($id) && isset($params[$id . 'returnid']))
		{
			$page = $_REQUEST[$id . 'returnid'];
		}
		else if (CmsConfig::exists("query_var") && $config['query_var'] != '' && isset($_GET[$config['query_var']]))
		{
			$page = $_GET[$config["query_var"]];

		    //trim off the extension, if there is one set
		    if ($config['page_extension'] != '' && endswith($page, $config['page_extension']))
		    {   
		        $page = substr($page, 0, strlen($page) - strlen($config['page_extension']));
		    }
		}
		else
		{
			$calced = self::cms_calculate_url();
			if ($calced != '')
				$page = $calced;
		}
		
		//$page = self::strip_language_from_page($page);
		
		return rtrim($page, '/');
	}
	
	public static function strip_language_from_page($page)
	{
		$enabled_languages = CmsMultiLanguage::get_enabled_languages();
		$exploded = explode('/', $page);
		if (count($exploded) > 1)
		{
			if(in_array($exploded[0], $enabled_languages))
			{
				//TODO: Set $exploded[0] as the current language
				$page = str_replace($exploded[0] . '/', '', $page);
				CmsMultiLanguage::$current_language = $exploded[0];
			}
		}
		else if (isset($_REQUEST['lang']))
		{
			//TODO: Set $_REQUEST['lang'] as the current language
			CmsMultiLanguage::$current_language = $_REQUEST['lang'];
		}
		return $page;
	}
	
	/**
	 * Figures out the page name from the uri string.  Has to use different logic
	 * based on the type of httpd server.
	 */
	public static function cms_calculate_url()
	{
		$result = '';

	    $config = cms_config();

		//Apache
		/*
		if (isset($_SERVER["PHP_SELF"]) && !endswith($_SERVER['PHP_SELF'], 'index.php'))
		{
			$matches = array();

			//Seems like PHP_SELF has whatever is after index.php in certain situations
			if (strpos($_SERVER['PHP_SELF'], 'index.php') !== FALSE) {
				if (preg_match('/.*index\.php\/(.*?)$/', $_SERVER['PHP_SELF'], $matches))
				{
					$result = $matches[1];
				}
			}
			else
			{
				$result = $_SERVER['PHP_SELF'];
			}
		}
		*/
		//lighttpd
		#else if (isset($_SERVER["REQUEST_URI"]) && !endswith($_SERVER['REQUEST_URI'], 'index.php'))

		//apache and lighttpd
		if (isset($_SERVER["REQUEST_URI"]) && !endswith($_SERVER['REQUEST_URI'], 'index.php'))
		{
			$matches = array();
			if (preg_match('/.*index\.php\/(.*?)$/', $_SERVER['REQUEST_URI'], $matches))
			{
				$result = $matches[1];
			}
		}

		//trim off the extension, if there is one set
		if ($config['page_extension'] != '' && endswith($result, $config['page_extension']))
		{
			$result = substr($result, 0, strlen($result) - strlen($config['page_extension']));
		}

		return $result;

	}
	
	/**
	 * Determines the uri that was requested
	 */
	public static function get_requested_uri()
	{
		if (!function_exists('_strleft'))
		{
			function _strleft($str,$substr)
			{
				$pos = strpos($str,$substr);
				if( $pos !== FALSE )
				{
					return substr($str,0,$pos);
				}
				return $str;
			}
		}

		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = _strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		
		$result = '';
		if (isset($_SERVER['HTTP_HOST']))
		{
			$result .= $protocol . '://' . $_SERVER['HTTP_HOST'] . $port;
		}
		
		if (isset($_SERVER['REQUEST_URI']))
		{
			$result .= $_SERVER['REQUEST_URI'];
		}
		else if (isset($_SERVER['SCRIPT_NAME']))
		{
			$result .= $_SERVER['SCRIPT_NAME'];
		}
		
		return $result;
	}
	
	public static function get_request_filename()
	{
		if (isset($_SERVER['PATH_TRANSLATED']))
		{
		     return str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']);
		}
		else if (isset($_ENV['PATH_TRANSLATED']))
		{
		     return str_replace('\\\\', '\\', $_ENV['PATH_TRANSLATED']);
		}
		else
		{
			return $_SERVER['SCRIPT_FILENAME'];
		}
	}
	
	public static function get_calculated_url_base($whole_url = false, $add_index_php = false)
	{
		$cur_url_dir = dirname($_SERVER['SCRIPT_NAME']);
		$cur_file_dir = dirname(self::get_request_filename());
		
		$has_index_php = false;
		if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "index.php") === false)
		{
			$has_index_php = true;
		}
		
		//Get the difference in number of characters between the root
		//and the requested file
		$len = strlen($cur_file_dir) - strlen(ROOT_DIR);

		//Now substract that # from the currently requested uri
		$result = substr($cur_url_dir, 0, strlen($cur_url_dir) - $len);

		if ($whole_url)
		{
			//Ok, we want the whole url of the base -- time for some magic
			//Grab the requested uri
			$requested_uri = self::get_requested_uri();

			//Figure out where in the string our calculated base is
			$pos = strpos($requested_uri, $result, 7);
			if ($pos)
			{
				//If it exists, substr out the whole thing
				$result = substr($requested_uri, 0, $pos + strlen($result));
			}
		}
		
		if ($add_index_php && $has_index_php)
			$result = $result . '/index.php';
		
		//if (!ends_with($result, '/'))
		//	$result = $result . '/';
		
		return $result;
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
	
	public static function set_cookie($name, $value, $expire = null)
	{
		setcookie($name, $value, $expire);
	}
}

# vim:ts=4 sw=4 noet
?>