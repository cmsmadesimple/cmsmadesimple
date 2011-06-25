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
$gCms = cmsms();
$db = $gCms->GetDb();

$error = "";
$forgotmessage = "";
$changepwhash = "";

//Redirect to the normal login screen if we hit cancel on the forgot pw one
//Otherwise, see if we have a forgotpw hit
if ((isset($_REQUEST['forgotpwform']) || isset($_REQUEST['forgotpwchangeform'])) && isset($_REQUEST['logincancel']))
{
	redirect('login.php');
}
else if (isset($_REQUEST['forgotpwform']) && isset($_REQUEST['forgottenusername']))
{
	$userops = $gCms->GetUserOperations();
	$oneuser = $userops->LoadUserByUsername($_REQUEST['forgottenusername']);
	
	if ($oneuser != null)
	{
		if ($oneuser->email == '')
		{
			$error = lang('nopasswordforrecovery');
		}
		else if (send_recovery_email($_REQUEST['forgottenusername']))
		{
			$warningLogin = lang('recoveryemailsent');
		}
		else
		{
			$error = lang('errorsendingemail');
		}
	}
	else
	{
	  Events::SendEvent('Core','LoginFailed',array('user'=>$_REQUEST['forgottenusername']));
	  $error = lang('usernotfound');
	}
}
else if (isset($_REQUEST['recoverme']) && $_REQUEST['recoverme'])
{
	$user = find_recovery_user($_REQUEST['recoverme']);
	if ($user == null)
	{
		$error = lang('usernotfound');
	}
	else
	{
		$changepwhash = $_REQUEST['recoverme'];
	}
}
else if (isset($_REQUEST['forgotpwchangeform']) && $_REQUEST['forgotpwchangeform'])
{
	$user = find_recovery_user($_REQUEST['changepwhash']);
	if ($user == null)
	{
		$error = lang('usernotfound');
	}
	else
	{
		if ($_REQUEST['password'] != '')
		{
			if ($_REQUEST['password'] == $_REQUEST['passwordagain'])
			{
				$user->SetPassword($_REQUEST['password']);
				$user->Save();
				$acceptLogin = lang('passwordchangedlogin');
				$changepwhash = '';
			}
			else
			{
				$error = lang('nopasswordmatch');
				$changepwhash = $_REQUEST['changepwhash'];
			}
		}
		else
		{
			$error = lang('nofieldgiven', array(lang('password')));
			$changepwhash = $_REQUEST['changepwhash'];
		}
	}
}

if (isset($_SESSION['logout_user_now']))
{
	debug_buffer("Logging out.  Cleaning cookies and session variables.");
	unset($_SESSION['logout_user_now']);
	unset($_SESSION['cms_admin_user_id']);
	unset($_SESSION[CMS_USER_KEY]);
	cms_cookies::erase('cms_admin_user_id');
	cms_cookies::erase('cms_passhash');
	cms_cookies::erase(CMS_SECURE_PARAM_NAME);
}
else if ( isset($_SESSION['redirect_url']) )
{	
	$_SESSION["t_redirect_url"] = $_SESSION["redirect_url"];
	$no_redirect = true;
	$is_logged_in = check_login($no_redirect);
	$_SESSION["redirect_url"] = $_SESSION["t_redirect_url"];
	unset($_SESSION["t_redirect_url"]);

	if (true == $is_logged_in)
	{
		$userid = get_userid();
		$dest = $config['admin_url'];
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
		$dest .= '?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
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

	$userops = $gCms->GetUserOperations();
	$oneuser = $userops->LoadUserByUsername($username, $password, true, true);
	
	debug_buffer("Got user by username");
	debug_buffer($oneuser);

	if ($username != "" && $password != "" && isset($oneuser) && $oneuser == true && isset($_POST["loginsubmit"]))
	{
		debug_buffer("Starting login procedure.  Setting userid so that other pages will pick it up and set a cookie.");
		generate_user_object($oneuser->id);
		$_SESSION['login_user_id'] = $oneuser->id;
		$_SESSION['login_user_username'] = $oneuser->username;
		audit($oneuser->id, $oneuser->username, lang_en('user_login'));

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
			  // attempt to redirect to the originally requested page
			  $tmp = $_SESSION["redirect_url"];
			  unset($_SESSION["redirect_url"]);
			  
			  if( !strstr($tmp,'.php') || endswith($tmp,'/') )
			    {
			      die('tool');
			      // force the url to go to index.php
			      $tmp = $config['admin_url'].'/index.php';
			    }
			  $tmp = $tmp . '?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
			  redirect($tmp);
			}
			unset($_SESSION["redirect_url"]);
		}
		else
		{
			if (isset($config) and $config['debug'] == true)
			{
			  $url = 'index.php?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
			  echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
			  echo "<a href=\"{$url}\">{$url}</a><br />";
			  foreach ($gCms->errors as $globalerror)
			    {
			      echo $globalerror;
			    }
			}
			else
			{
			  $homepage = get_preference($oneuser->id,'homepage');
			  if( $homepage == '' )
			    {
			      $homepage = 'index.php';
			    }
			  $tmp = explode('?',$homepage);
			  if( !file_exists($tmp[0]) ) $homepage = 'index.php';

			  // quick hack to remove old secure param name from homepage url
			  $pos = strpos($homepage,'?_s_');
			  if( $pos !== FALSE )
			    {
			      $homepage = substr($homepage,0,$pos);
			    }

			  $pos = strpos($homepage,CMS_SECURE_PARAM_NAME);
			  if( $pos !== FALSE )
			    {
			      $str = substr($homepage,$pos-1,strlen(CMS_SECURE_PARAM_NAME)+strlen($_SESSION[CMS_USER_KEY])+2);
			      $rep = '?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
			      $homepage = str_replace($str,$rep,$homepage);
			    }
			  else
			    {
			      $homepage .= '?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
			    }

			  $homepage = html_entity_decode($homepage);
			  redirect($homepage);
			}
		}
		return;
		#redirect("index.php");
	}
	else if (isset($_POST['loginsubmit'])) { //No error if changing languages
		$error .= lang('usernameincorrect');
		debug_buffer("Login failed.  Error is: " . $error);

		Events::SendEvent('Core','LoginFailed',array('user'=>$_POST['username']));;
		audit($username, $username, lang_en('login_failed'));

		#Now call the event
		//Events::SendEvent('Core', 'LoginPost', $username);

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
