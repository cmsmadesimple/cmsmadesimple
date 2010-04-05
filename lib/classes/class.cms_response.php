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
 * Static methods for handling web responses.
 *
 * @author Ted Kulp
 * @since 2.0
 **/
class CmsResponse extends CmsObject
{
	static private $instance = NULL;
	
	protected $body = array();
	protected $headers = array();
	protected $status = '200';
	protected $version = '1.1';

	protected $_statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested range not satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out'
	);
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns an instnace of the CmsResponse singleton.
	 *
	 * @return CmsResponse The singleton CmsResponse instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsResponse();
		}
		return self::$instance;
	}
	
	function get_encoding()
	{
		return 'UTF-8';
	}
	
	function set_status_code($code = '200')
	{
		$this->status = $code;
	}
	
	function add_header($name, $value)
	{
		if ($name == '')
			$this->headers[] = $value;
		else
			$this->headers[$name] = $value;
	}
	
	function body($str = '')
	{
		$this->body[] = $str;
	}
	
	function clear_body()
	{
		$this->body = array();
	}
	
	function render()
	{
		$this->headers[] = "HTTP/{$this->version} {$this->status} {$this->_statuses[(int)$this->status]}";
		$this->headers['Status'] = "{$this->status} {$this->_statuses[(int)$this->status]}";
		
		$this->send_headers();
		
		$body = join("\r\n", (array)$this->body);
		
		if (CmsConfig::get('output_compression') == true &&
			CmsConfig::get('debug') != true &&
			extension_loaded('zlib') &&
			$this->status == '200'
		)
		{
			$str = ob_gzhandler($body, 5);
			if ($str !== false)
			{
				$body = $str;
			}
		}
		
		$split_ary = str_split($body, 8192);

		foreach ($split_ary as $one_item)
		{
			echo $one_item;
		}
	}
	
	function send_headers()
	{
		foreach ($this->headers as $k => $v)
		{
			if (is_int($k))
			{
				header($v, true);
			}
			else
			{
				header("{$k}: {$v}", true);
			}
		}
	}
	
	/**
	 * Redirects a user to given url.
	 *
	 * @param $to The url to redirect to
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public static function redirect($to)
	{
		$_SERVER['PHP_SELF'] = null;

		$config = array();
		try
		{
			$config = cms_config();
		}
		catch (Exception $e)
		{
		}

	    $schema = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
	    $host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];

	    $components = parse_url($to);
	    if(count($components) > 0)
	    {
	        $to =  (isset($components['scheme']) && startswith($components['scheme'], 'http') ? $components['scheme'] : $schema) . '://';
	        $to .= isset($components['host']) ? $components['host'] : $host;
	        $to .= isset($components['port']) ? ':' . $components['port'] : '';
	        if(isset($components['path']))
	        {
	            if(in_array(substr($components['path'],0,1),array('\\','/')))//Path is absolute, just append.
	            {
	                $to .= $components['path'];
	            }
	            //Path is relative, append current directory first.
				else if (isset($_SERVER['PHP_SELF']) && !is_null($_SERVER['PHP_SELF'])) //Apache
				{
					$to .= (strlen(dirname($_SERVER['PHP_SELF'])) > 1 ?  dirname($_SERVER['PHP_SELF']).'/' : '/') . $components['path'];
				}
				else if (isset($_SERVER['REQUEST_URI']) && !is_null($_SERVER['REQUEST_URI'])) //Lighttpd
				{
					if (endswith($_SERVER['REQUEST_URI'], '/'))
						$to .= (strlen($_SERVER['REQUEST_URI']) > 1 ? $_SERVER['REQUEST_URI'] : '/') . $components['path'];
					else
						$to .= (strlen(dirname($_SERVER['REQUEST_URI'])) > 1 ? dirname($_SERVER['REQUEST_URI']).'/' : '/') . $components['path'];
				}
			}
			$to .= isset($components['query']) ? '?' . $components['query'] : '';
			$to .= isset($components['fragment']) ? '#' . $components['fragment'] : '';
		}
		else
		{
			$to = $schema."://".$host."/".$to;
		}
		
		$response = CmsResponse::get_instance();

		if (headers_sent() && !(isset($config) && $config['debug'] == true))
		{
			// use javascript instead
			$response->clear_body();
			$response->body('<script type="text/javascript">
				<!--
					location.replace("'.$to.'");
				// -->
				</script>
				<noscript>
					<meta http-equiv="Refresh" content="0;URL='.$to.'">
				</noscript>');
			$response->render();
			exit;
		}
		else
		{
			if (isset($config) && $config['debug'] == true)
			{
				$response->clear_body();
				$response->body("Debug is on.  Redirecting disabled...  Please click this link to continue.<br />");
				$response->body("<a href=\"".$to."\">".$to."</a><br />");

				$response->body('<pre>');
				$response->body(CmsProfiler::get_instance()->report());
				$response->body('</pre>');

				$response->render();
				exit;
			}
			else
			{
				$response->set_status_code('302');
				$response->add_header('Location', $to);
				$response->clear_body();
				$response->render();
				exit();
			}
		}
	}

	/**
	 * Given a page ID or an alias, redirect to it
	 */
	public static function redirect_to_alias($alias)
	{
		$node = CmsPageTree::get_node_by_alias($alias);
		$content = $node->get_content();
		if (isset($content))
		{
			if ($content->get_url() != '')
			{
				CmsResponse::redirect($content->get_url());
			}
		}
	}

	
	/**
	 * Shows a very close approximation of an Apache generated 404 error.
	 * It also sends the actual header along as well, so that generic
	 * browser error pages (like what IE does) will be displayed.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	function send_error_404()
	{
		$this->set_status_code('404');
		$this->body('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>');
		$this->render();
		exit();
	}
	
	/**
	 * Converts a string into a valid DOM id.
	 *
	 * @param string The string to be converted
	 * @return string The converted string
	 * @author Ted Kulp
	 **/
	public static function make_dom_id($text)
	{
		return trim(preg_replace("/[^a-z0-9_\-]+/", '_', strtolower($text)), ' _');
	}

}

# vim:ts=4 sw=4 noet
?>