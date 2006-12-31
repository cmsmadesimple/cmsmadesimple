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

class CmsAdmin extends CmsObject
{
	static private $instance = NULL;

	function __construct()
	{
		parent::__construct();
	}
	
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsAdmin();
		}
		return self::$instance;
	}
	
	/**
	 * Checks to see if the user is logged in.   If not, redirects the browser
	 * to the admin login.
	 *
	 * @since 0.1
	 * @param string no_redirect - If true, then don't redirect if not logged in
	 * @return bool If they're logged in, true.  If not logged in, false. 
	 */
	static public function check_login($no_redirect = false)
	{
		$config = cms_config();

		//Handle a current login if one is in queue in the SESSION
		if (isset($_SESSION['login_user_id']))
		{
			debug_buffer("Found login_user_id.  Going to generate the user object.");
			self::generate_user_object($_SESSION['login_user_id']);
			unset($_SESSION['login_user_id']);
		}

		if (isset($_SESSION['login_cms_language']))
		{
			debug_buffer('Setting language to: ' . $_SESSION['login_cms_language']);
			setcookie('cms_language', $_SESSION['login_cms_language']);
			unset($_SESSION['login_cms_language']);
		}

		if (!isset($_SESSION["cms_admin_user_id"]))
		{
			debug_buffer('No session found.  Now check for cookies');
			if (isset($_COOKIE["cms_admin_user_id"]) && isset($_COOKIE["cms_passhash"]))
			{
				debug_buffer('Cookies found, do a passhash check');
				if (check_passhash(isset($_COOKIE["cms_admin_user_id"]), isset($_COOKIE["cms_passhash"])))
				{
					debug_buffer('passhash check succeeded...  creating session object');
					self::generate_user_object($_COOKIE["cms_admin_user_id"]);
				}
				else
				{
					debug_buffer('passhash check failed...  redirect to login');
					$_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
					if (false == $no_redirect)
					{
						CmsResponse::redirect($config["root_url"]."/".$config['admin_dir']."/login.php");
					}
					return false;
				}
			}
			else
			{
				debug_buffer('No cookies found.  Redirect to login.');
				$_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
				if (false == $no_redirect)
				{
					CmsResponse::redirect($config["root_url"]."/".$config['admin_dir']."/login.php");
				}
				return false;
			}
		}
		else
		{
			debug_buffer('Session found.  Moving on...');
			return true;
		}
	}
	
	/**
	 * Gets the userid of the currently logged in user.
	 *
	 * @returns If they're logged in, the user id.  If not logged in, false.
	 * @since 0.1
	 */
	static public function get_user($check = true)
	{
		if ($check)
		{
			self::check_login(); //It'll redirect out to login if it fails
		}

		if (isset($_SESSION["cms_admin_user"]))
		{
			return $_SESSION["cms_admin_user"];
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Gets the userid of the currently logged in user.
	 *
	 * @returns If they're logged in, the user id.  If not logged in, false.
	 * @since 0.1
	 */
	static public function get_userid($check = true)
	{
		if ($check)
		{
			self::check_login(); //It'll redirect out to login if it fails
		}

		if (isset($_SESSION["cms_admin_user_id"]))
		{
			return $_SESSION["cms_admin_user_id"];
		}
		else
		{
			return false;
		}
	}
	
	static public function check_passhash($userid, $checksum)
	{
		$oneuser = cmsms()->user->find_by_id($userid);
		if ($oneuser && $checksum == md5(md5($config['root_path'] . '--' . $oneuser->password)))
		{
			return true;
		}

		return false;
	}
	
	static public function generate_user_object($userid)
	{
		$oneuser = cmsms()->user->find_by_id($userid);
		if ($oneuser)
		{
			$_SESSION['cms_admin_user_id'] = $userid;
			$_SESSION['cms_admin_username'] = $oneuser->username;
			$_SESSION['cms_admin_user'] = $oneuser;
			setcookie('cms_admin_user_id', $oneuser->id);
			setcookie('cms_passhash', md5(md5($config['root_path'] . '--' . $oneuser->password)));
		}
	}
}

# vim:ts=4 sw=4 noet
?>