<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
require_once("../lib/classes/class.user.inc.php");
global $gCms;
$db =& $gCms->GetDb();

$error = "";

if (isset($_SESSION['logout_user_now']))
{
	debug_buffer("Logging out.  Clearning cookies and session variables.");
	unset($_SESSION['logout_user_now']);
	unset($_SESSION['cms_admin_user_id']);
	setcookie('cms_admin_user_id', '', time() - 3600);
	setcookie('cms_passhash', '', time() - 3600);
}
else
{
	$_SESSION["t_redirect_url"] = $_SESSION["redirect_url"];
	$no_redirect = true;
	$is_logged_in = check_login($no_redirect);
	$_SESSION["redirect_url"] = $_SESSION["t_redirect_url"];
	unset($_SESSION["t_redirect_url"]);

	if (true == $is_logged_in)
	{
		$userid = get_userid();
		$dest = $config['root_url'].'/'.$config['admin_dir'];
		$homepage = get_preference($userid,'homepage');
		if( $homepage == '' )
		{
			$homepage = 'index.php';
		}
		{
			$tmp = explode('?',$homepage);
			if( !file_exists($tmp[0]) ) $homepage = 'index.php';
		}
		$dest = $dest.'/'.$homepage;
		redirect($dest);
	}
}
if (isset($_POST["logincancel"]))
{
	debug_buffer("Login cancelled.  Returning to content.");
	redirect($config["root_url"].'/index.php', true);
}

if (isset($_POST["username"]) && isset($_POST["password"])) {

	$username = "";
	if (isset($_POST["username"])) $username = cleanValue($_POST["username"]);

	$password = "";
	if (isset($_POST["password"])) $password = $_POST["password"];

	global $gCms;
	$userops =& $gCms->GetUserOperations();
	$oneuser =& $userops->LoadUserByUsername($username, $password, true, true);
	
	debug_buffer("Got user by username");
	debug_buffer($oneuser);

	if ($username != "" && $password != "" && isset($oneuser) && $oneuser == true && isset($_POST["loginsubmit"]))
	{
		debug_buffer("Starting login procedure.  Setting userid so that other pages will pick it up and set a cookie.");
		#generate_user_object($oneuser->id);
		$_SESSION['login_user_id'] = $oneuser->id;
		$_SESSION['login_user_username'] = $oneuser->username;
		$default_cms_lang = get_preference($oneuser->id, 'default_cms_language');
		if ($default_cms_lang != '')
		{
			#setcookie('cms_language', $default_cms_lang);
			$_SESSION['login_cms_language'] = $default_cms_lang;
		}
		audit($oneuser->id, $oneuser->username, 'User Login');

		#Perform the login_post callback  TODO: Remove me
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->LoginPost($oneuser);
			}
		}
		
		#Now call the event
		Events::SendEvent('Core', 'LoginPost', array('user' => &$oneuser));

		// redirect to upgrade if db_schema it's old
		$current_version = $CMS_SCHEMA_VERSION;
	
		$query = "SELECT version from ".cms_db_prefix()."version";
		$row = $db->GetRow($query);
		if ($row) $current_version = $row["version"];

		if ($current_version < $CMS_SCHEMA_VERSION)
		{
			redirect($gCms->config['root_url'] . "/install/upgrade.php");
		}
		
		if (isset($_POST['redirect_url']))
		{
			$_SESSION['redirect_url'] = $_POST['redirect_url'];
		}
		if (isset($_SESSION["redirect_url"]))
		{
			if (isset($gCms->config) and $gCms->config['debug'] == true)
			{
				echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
				echo "<a href=\"".$_SESSION["redirect_url"]."\">".$_SESSION["redirect_url"]."</a><br />";
				foreach ($gCms->errors as $globalerror)
				{
					echo $globalerror;
				}
			}
			else
			{
				#echo ('<html><head><title>Logging in... please wait</title><meta http-equiv="refresh" content="1; url='.$_SESSION["redirect_url"].'"></head><body>Logging in and redirecting to <a href="'.$_SESSION["redirect_url"].'">'.$_SESSION["redirect_url"].'</a>, one moment please...</body></html>');
				$tmp = $_SESSION["redirect_url"];
				unset($_SESSION["redirect_url"]);
				redirect($tmp);
			}
			unset($_SESSION["redirect_url"]);
		}
		else
		{
			if (isset($config) and $config['debug'] == true)
			{
				echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
				echo "<a href=\"index.php\">index.php</a><br />";
				foreach ($gCms->errors as $globalerror)
				{
					echo $globalerror;
				}
			}
			else
			{
				#echo ('<html><head><title>Logging in... please wait</title><meta http-equiv="refresh" content="1; url=./index.php"></head><body>Logging in and redirecting to <a href="./index.php">index.php</a>, one moment please...</body></html>');
			  $homepage = get_preference($oneuser->id,'homepage');
			  if( $homepage == '' )
			    {
			      $homepage = 'index.php';
			    }
			  {
			    $tmp = explode('?',$homepage);
			    if( !file_exists($tmp[0]) ) $homepage = 'index.php';
			  }
			  redirect($homepage);
			}
		}
		return;
		#redirect("index.php");
	}
	else if (isset($_POST['loginsubmit'])) { //No error if changing languages
		$error .= lang('usernameincorrect');
		debug_buffer("Login failed.  Error is: " . $error);
	}
	else
	{
		debug_buffer($_POST["loginsubmit"]);
	}

}

// Language shizzle
//header("Content-Encoding: " . get_encoding());
header("Content-Language: " . $current_language);
header("Content-Type: text/html; charset=" . get_encoding());

//CHANGED
$theme=get_site_preference('logintheme', 'default');
//echo "theme:$theme";
debug_buffer('debug is:' . $error);
if (file_exists(dirname(__FILE__)."/themes/$theme/login.php")) {
	include(dirname(__FILE__)."/themes/$theme/login.php");
} else {
	include(dirname(__FILE__)."/themes/default/login.php");
}
//STOP
?>

<?php
	if (isset($gCms->config) and $gCms->config['debug'] == true)
	{
		foreach ($gCms->errors as $globalerror)
		{
			echo $globalerror;
		}
	}
# vim:ts=4 sw=4 noet
?>
