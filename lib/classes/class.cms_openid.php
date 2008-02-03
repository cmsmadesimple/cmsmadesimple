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
 * Simple implementation of the OpenID protocol for logging into 
 * the CMSMS system.  It only handles the simple "dumb mode" of 
 * OpenID.
 *
 * @since 2.0
 * @author Ted Kulp
 **/
class CmsOpenid extends CmsObject
{
	public $server = '';
	public $delegate = '';
	public $mode = 'checkid_setup';

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Check to see if we can support doing the backend call
	 * to the server after the login procedure.
	 *
	 * @return boolean
	 * @author Ted Kulp
	 **/
	public static function is_enabled()
	{
		return ini_get('allow_url_fopen');
	}
	
	/**
	 * Generate a checksum to use for matching the user and the
	 * login request.
	 *
	 * @return string The checksum string
	 * @author Ted Kulp
	 **/
	public static function generate_checksum()
	{
		return sha1(time() . CMS_VERSION . ROOT_DIR);
	}
	
	/**
	 * Take a given OpenId identifier and clean it up so that is 
	 * follows a generic format.  This would mean no
	 * protocol at the beginning and no slash at the end
	 *
	 * @param string The given OpenId identifier
	 * @return string The cleaned up OpenId identifier
	 * @author Ted Kulp
	 **/
	public static function cleanup_openid($url)
	{
		$u = parse_url(strtolower(trim($url)));

		#Handle no path given
		if (!isset($u['path']) || $u['path'] == '/')
		{
			$u['path'] = '';			
		}

		#parse_url sometimes returns a straight domain name
		#with no path or scheme as a path.  That obviously should
		#be a host.
		if (!isset($u['host']) && $u['path'] != '')
		{
			$u['host'] = $u['path'];
			$u['path'] = '';
		}

		#If the path ends with a /, remove it.
		if(substr($u['path'],-1,1) == '/')
		{
			$u['path'] = substr($u['path'], 0, strlen($u['path'])-1);
		}
		
		#Return the straightened-out openid
		if (isset($u['query']))
		{
			return $u['host'] . $u['path'] . '?' . $u['query'];
		}
		else
		{
			return $u['host'] . $u['path'];
		}
	}
	
	/**
	 * Create a URL from the given OpenId identifier
	 * for use in the initial lookup of the provider.
	 *
	 * @param string The OpenId identifier
	 * @return string The url of the given OpenId identifier
	 * @author Ted Kulp
	 **/
	public static function create_url($url)
	{
		return 'http://' . self::cleanup_openid($url);
	}
	
	/**
	 * Lookup the provider (and delegate, if set) from the given url.
	 * These are then set internally.
	 *
	 * @param string The url to lookup the provider of
	 * @return boolean Wether or not the provider lookup was successful
	 * @author Ted Kulp
	 **/
	public function find_server($url)
	{
		$file = fopen($url, 'r');
		if (!$file)
		{
			return false;
		}
		
		$this->delegate = $url;
		
		while (!feof($file))
		{
			$line = fgets($file, 1024);
			if (preg_match("/<link rel=['\"]openid\.delegate['\"] href=['\"](.*?)['\"]/", $line, $out))
			{
				$this->delegate = $out[1];
			}
			if (preg_match("/<link rel=['\"]openid\.server['\"] href=['\"](.*?)['\"]/", $line, $out))
			{
				$this->server = $out[1];
			}
		}
		
		if ($this->server != '')
			return true;
		
		return false;
	}
	
	/**
	 * Create the return_url and redirect to the set provider for authentication.
	 *
	 * @param string The url to return to after the authentication is complete
	 * @param string The checksum to use.  If none is given, one is created.
	 * @return void
	 * @author Ted Kulp
	 **/
	public function do_authentication($return_url, $checksum = '')
	{
		if ($this->server == '' || $this->delegate == '' || $return_url == '')
			return false;
		
		if ($checksum == '')
			$checksum = self::generate_checksum();
		
		$return_url .= strpos('?', $return_url) !== FALSE ? '&' : '?';
		$return_url .= "checksum={$checksum}&endpoint=" . urlencode($this->server);
		$return_url = urlencode($return_url);
		$trust_root = urlencode(CmsConfig::get('root_url'));
		$cleaned_delegate = urlencode($this->delegate);
		
		CmsResponse::redirect("{$this->server}?openid.mode={$this->mode}&openid.identity={$cleaned_delegate}&openid.return_to={$return_url}&openid.trust_root={$trust_root}");
	}
	
	/**
	 * Does the final authentication check.  This posts back to the provider the details
	 * given to check that the provider did indeed approve the authentication request.
	 *
	 * @param array The array of parameters to send back to the provider for it's
	 *        checking process
	 * @return boolean Whether or not the authentication process was successful
	 * @author Ted Kulp
	 **/
	public function check_authentication($params)
	{
		if ($params['openid_mode'] == 'id_res' || $params['openid.mode'] == 'id_res')
		{
			$params_we_need = array();

			#Gather up all the openid* parameters to send them back
			foreach ($params as $k=>$v)
			{
				if (starts_with($k, 'openid') && !ends_with($k, 'mode'))
				{
					$k = str_replace('openid_', 'openid.', $k);
					$params_we_need[$k] = $v;
				}
			}
			
			$params_we_need['openid.mode'] = 'check_authentication';

			return self::do_post_request($params['endpoint'], $params_we_need);
		}
		
		return false;
	}
	
	/**
	 * Posts behind the scenes to another page.
	 * Taken from: http://netevil.org/blog/2006/nov/http-post-from-php-without-curl
	 *
	 * @param string The url to post to
	 * @param string The data to send in the post's payload
	 * @param string The method to use (POST or GET -- POST is default)
	 * @return string Response from the posted page
	 * @author Wez Furlong, modified by Ted Kulp
	 **/
	public static function do_post_request($url, $data, $method = 'POST')
	{	
		$uri = parse_url($url);

		$port = isset($uri['port']) ? $uri['port'] : 80;
		$host = $uri['host'] . ($port != 80 ? ':'. $port : '');
		$fp = @fsockopen($uri['host'], $port, $errno, $errstr, 15);
		if (!$fp)
		{
			return 'Error connecting to the openid server.';
		}

		$data = http_build_query($data);
		
		$headers = "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n" .
			"Host: $host\r\n" .
			"User-Agent: CMS Made Simple (http://cmsmadesimple.org)\r\n" .
			'Content-Length: '. strlen($data);
		
		$path = isset($uri['path']) ? $uri['path'] : '/';
		if (isset($uri['query']))
		{
			$path .= '?'. $uri['query'];
		}
		
		$request = $method .' '. $path ." HTTP/1.0\r\n";
		$request .= $headers;
		$request .= "\r\n\r\n";
		$request .= $data ."\r\n";
		
		fwrite($fp, $request);

		$response = '';
		while (!feof($fp) && $chunk = fread($fp, 1024))
		{
			$response .= $chunk;
		}
		fclose($fp);
		
		if (starts_with($response, 'HTTP/1.1 200 OK'))
		{
			if (strpos($response, 'is_valid:true') !== FALSE)
			{
				return true;
			}
		}
		
		return false;
	}
}

# vim:ts=4 sw=4 noet
?>