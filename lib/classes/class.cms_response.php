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
 * Static methods for handling web responses.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsResponse extends CmsObject
{
	function __construct()
	{
		parent::__construct();
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

	    if (headers_sent() && !(isset($config) && $config['debug'] == true))
	    {
	        // use javascript instead
	        echo '<script type="text/javascript">
	            <!--
	                location.replace("'.$to.'");
	            // -->
	            </script>
	            <noscript>
	                <meta http-equiv="Refresh" content="0;URL='.$to.'">
	            </noscript>';
	        exit;
	    }
	    else
	    {
	        if (isset($config) && $config['debug'] == true)
	        {
	            echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
	            echo "<a href=\"".$to."\">".$to."</a><br />";
	            exit();
	        }
	        else
	        {
	            header("Location: $to");
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
						redirect($content->get_url());
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
		while (@ob_end_clean());
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");
		echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>';
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