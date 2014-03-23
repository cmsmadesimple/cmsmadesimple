<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
$CMS_LOGIN_PAGE=1;

require_once("../include.php");
require_once("../lib/classes/class.user.inc.php");
$gCms = cmsms();
$db = $gCms->GetDb();

$error = "";
$forgotmessage = "";
$changepwhash = "";

/**
 * A function to send lost password recovery email to a specified admin user (by name)
 *
 * @internal
 * @access private
 * @param string the username
 * @return results from the attempt to send a message.
 */
function send_recovery_email($username)
{
  $gCms = cmsms();
  $config = $gCms->GetConfig();
  $userops = $gCms->GetUserOperations();
  $user = $userops->LoadUserByUsername($username);

  $obj = cms_utils::get_module('CMSMailer');
  if ($obj == null) return false;

  $obj->AddAddress($user->email, html_entity_decode($user->firstname . ' ' . $user->lastname));
  $obj->SetSubject(lang('lostpwemailsubject',html_entity_decode(get_site_preference('sitename','CMSMS Site'))));

  $url = $config['admin_url'] . '/login.php?recoverme=' . md5(md5($config['root_path'] . '--' . $user->username . md5($user->password)));
  $body = lang('lostpwemail',html_entity_decode(get_site_preference('sitename','CMSMS Site')), $user->username, $url);

  $obj->SetBody($body);

  audit('','Core','Sent Lost Password Email for '.$username);
  return $obj->Send();
}

/**
 * A function find a matching user id given an identity hash
 *
 * @internal
 * @access private
 * @param string the hash
 * @return object The matching user object if found, or null otherwise.
 */
function find_recovery_user($hash)
{
  $gCms = cmsms();
  $config = $gCms->GetConfig();
  $userops = $gCms->GetUserOperations();

  foreach ($userops->LoadUsers() as $user) {
    if ($hash == md5(md5($config['root_path'] . '--' . $user->username . md5($user->password)))) return $user;
  }

  return null;
}



//Redirect to the normal login screen if we hit cancel on the forgot pw one
//Otherwise, see if we have a forgotpw hit
if ((isset($_REQUEST['forgotpwform']) || isset($_REQUEST['forgotpwchangeform'])) && isset($_REQUEST['logincancel'])) {
  redirect('login.php');
}
else if (isset($_REQUEST['forgotpwform']) && isset($_REQUEST['forgottenusername'])) {
  $userops = $gCms->GetUserOperations();
  $oneuser = $userops->LoadUserByUsername($_REQUEST['forgottenusername']);

  if ($oneuser != null) {
    if ($oneuser->email == '') {
      $error = lang('nopasswordforrecovery');
    }
    else if (send_recovery_email($_REQUEST['forgottenusername'])) {
      $warningLogin = lang('recoveryemailsent');
    }
    else {
      $error = lang('errorsendingemail');
    }
  }
  else {
    Events::SendEvent('Core','LoginFailed',array('user'=>$_REQUEST['forgottenusername']));
    $error = lang('usernotfound');
  }
}
else if (isset($_REQUEST['recoverme']) && $_REQUEST['recoverme'])
{
  $user = find_recovery_user($_REQUEST['recoverme']);
  if ($user == null) {
    $error = lang('usernotfound');
  }
  else {
    $changepwhash = $_REQUEST['recoverme'];
  }
}
else if (isset($_REQUEST['forgotpwchangeform']) && $_REQUEST['forgotpwchangeform'])
{
  $user = find_recovery_user($_REQUEST['changepwhash']);
  if ($user == null) {
    $error = lang('usernotfound');
  }
  else {
    if ($_REQUEST['password'] != '') {
      if ($_REQUEST['password'] == $_REQUEST['passwordagain']) {
	$user->SetPassword($_REQUEST['password']);
	$user->Save();
	// put mention into the admin log
	$ip_passw_recovery = cms_utils::get_real_ip();
	audit('','Core','Completed lost password recovery for: '.$user->username.' (IP: '.$ip_passw_recovery.')');
	$acceptLogin = lang('passwordchangedlogin');
	$changepwhash = '';
      }
      else {
	$error = lang('nopasswordmatch');
	$changepwhash = $_REQUEST['changepwhash'];
      }
    }
    else {
      $error = lang('nofieldgiven', array(lang('password')));
      $changepwhash = $_REQUEST['changepwhash'];
    }
  }
}

if (isset($_SESSION['logout_user_now'])) {
  debug_buffer("Logging out.  Cleaning cookies and session variables.");
  unset($_SESSION['logout_user_now']);
  unset($_SESSION['cms_admin_user_id']);
  unset($_SESSION[CMS_USER_KEY]);
  cms_cookies::erase('cms_admin_user_id');
  cms_cookies::erase('cms_passhash');
  cms_cookies::erase(CMS_SECURE_PARAM_NAME);
}
else if ( isset($_SESSION['redirect_url']) ) {
  $_SESSION["t_redirect_url"] = $_SESSION["redirect_url"];
  $no_redirect = true;
  $is_logged_in = check_login($no_redirect);
  $_SESSION["redirect_url"] = $_SESSION["t_redirect_url"];
  unset($_SESSION["t_redirect_url"]);

  if (true == $is_logged_in) {
    $userid = get_userid();
    $homepage = cms_userprefs::get_for_user($userid,'homepage'.'index.php');

    $homepage = str_replace('&amp;','&',$homepage);
    $tmp = explode('?',$homepage);
    if( !file_exists($tmp[0]) ) $tmp[0] = 'index.php';
    $tmp2 = array();
    if( isset($tmp[1]) ) {
      parse_str($tmp[1],$tmp2);
      if( in_array('_s_',array_keys($tmp2)) ) unset($tmp2['_s_']);
      if( in_array('sp_',array_keys($tmp2)) ) unset($tmp2['sp_']);
    }
    $tmp2[CMS_SECURE_PARAM_NAME] = $_SESSION[CMS_USER_KEY];
    $tmp3 = array();
    foreach( $tmp2 as $k => $v ) {
      $tmp3[] = $k.'='.$v;
    }
    $homepage = $tmp[0].'?'.implode('&amp;',$tmp3);
    $homepage = html_entity_decode($homepage);
    redirect($homepage);
  }
}
if (isset($_POST["logincancel"])) {
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

  if ($username != "" && $password != "" && isset($oneuser) && $oneuser == true && isset($_POST["loginsubmit"])) {
    debug_buffer("Starting login procedure.  Setting userid so that other pages will pick it up and set a cookie.");
    generate_user_object($oneuser->id);
    $_SESSION['login_user_id'] = $oneuser->id;
    $_SESSION['login_user_username'] = $oneuser->username;
    // put mention into the admin log
    audit($oneuser->id, "Admin Username: ".$oneuser->username, 'Logged In');

    // Now call the event
    Events::SendEvent('Core', 'LoginPost', array('user' => &$oneuser));

    // redirect to upgrade if db_schema it's old
    $current_version = $CMS_SCHEMA_VERSION;

    if( $gCms->get_installed_schema_version() < $CMS_SCHEMA_VERSION ) redirect($gCms->config['root_url'] . "/install/upgrade.php");

    if (isset($_POST['redirect_url'])) $_SESSION['redirect_url'] = $_POST['redirect_url'];
    if (isset($_SESSION["redirect_url"])) {
      if (isset($gCms->config) and $gCms->config['debug'] == true) {
	echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
	echo "<a href=\"".$_SESSION["redirect_url"]."\">".$_SESSION["redirect_url"]."</a><br />";
	$arr = $gCms->get_errors();
	foreach ($arr as $globalerror) {
	  echo $globalerror;
	}
      }
      else {
	// attempt to redirect to the originally requested page
	$tmp = $_SESSION["redirect_url"];
	unset($_SESSION["redirect_url"]);

	if( strstr($tmp,CMS_SECURE_PARAM_NAME.'=') !== FALSE ) {
	  $the_url = new cms_url($tmp);
	  $the_url->set_queryvar(CMS_SECURE_PARAM_NAME,$_SESSION[CMS_USER_KEY]);
	  $tmp = (string)$the_url;
	}

	if( !strstr($tmp,'.php') || endswith($tmp,'/') ) {
	  // force the url to go to index.php
	  $tmp = $config['admin_url'].'/index.php?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	  debug_to_log('change session var to '.$tmp);
	}

	redirect($tmp);
      }
      unset($_SESSION["redirect_url"]);
    }
    else {
      if (isset($config) and $config['debug'] == true) {
	$url = 'index.php?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
	echo "<a href=\"{$url}\">{$url}</a><br />";
	$arr = $gCms->get_errors();
	foreach ($arr as $globalerror) {
	  echo $globalerror;
	}
      }
      else {
	$homepage = cms_userprefs::get_for_user($oneuser->id,'homepage');
	if( $homepage ) {
	  // quick hacks to remove old secure param name from homepage url
	  // and replace with the correct one.
	  $homepage = str_replace('&amp;','&',$homepage);
	  $tmp = explode('?',$homepage);
	  @parse_str($tmp[1],$tmp2);
	  if( in_array('_s_',array_keys($tmp2)) ) unset($tmp2['_s_']);
	  if( in_array('sp_',array_keys($tmp2)) ) unset($tmp2['sp_']);
	  $tmp2[CMS_SECURE_PARAM_NAME] = $_SESSION[CMS_USER_KEY];
	  foreach( $tmp2 as $k => $v ) {
	    $tmp3[] = $k.'='.$v;
	  }
	  $homepage = $tmp[0].'?'.implode('&amp;',$tmp3);

	  // and redirect.
	  $homepage = html_entity_decode($homepage);
	}
	else {
	  $homepage = $config['admin_url'];
	}
	redirect($homepage);
      }
    }
    return;
  }
  else if (isset($_POST['loginsubmit'])) { //No error if changing languages
    $error .= lang('usernameincorrect');
    debug_buffer("Login failed.  Error is: " . $error);

    Events::SendEvent('Core','LoginFailed',array('user'=>$_POST['username']));;
    // put mention into the admin log
    $ip_login_failed = cms_utils::get_real_ip();
    audit('', '(IP: ' . $ip_login_failed . ') ' . "Admin Username: " . $username, 'Login Failed');
  }
  else {
    debug_buffer($_POST["loginsubmit"]);
  }
}

// Language shizzle
cms_admin_sendheaders();
header("Content-Language: " . CmsNlsOperations::get_current_language());

//CHANGED
debug_buffer('debug is:' . $error);

$themeObject = cms_utils::get_theme_object();

$vars = array('error'=>$error);
if( isset($warningLogin) ) $vars['warningLogin'] = $warningLogin;
if( isset($acceptLogin) ) $vars['acceptLogin'] = $acceptLogin;
if( isset($changepwhash) ) $vars['changepwhash'] = $changepwhash;
$themeObject->do_login($vars);
?>

<?php
if (isset($gCms->config) and $gCms->config['debug'] == true) {
  foreach ($gCms->errors as $globalerror) {
    echo $globalerror;
  }
}
# vim:ts=4 sw=4 noet
?>
