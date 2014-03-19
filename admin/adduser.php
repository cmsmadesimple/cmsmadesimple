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

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$userid = get_userid();
if( !check_permission($userid, 'Manage Users') ) {
  die('Permission Denied');
  return;
}

$gCms = cmsms();
$db = $gCms->GetDb();
$assign_group_perm = check_permission($userid,'Manage Groups');
$groupops = $gCms->GetGroupOperations();
$error = "";
$user= "";
$firstname = "";
$lastname = "";
$password = "";
$passwordagain = "";
$email = "";
$active = 1;
$copyusersettings = null;
$adminaccess = 1;
$sel_groups = array();

if (isset($_POST["user"])) $user = cleanValue($_POST["user"]);
if (isset($_POST["firstname"])) $firstname = cleanValue($_POST["firstname"]);
if (isset($_POST["lastname"])) $lastname = cleanValue($_POST["lastname"]);
if (isset($_POST["password"])) $password = trim($_POST["password"]);
if (isset($_POST["passwordagain"])) $passwordagain = trim($_POST["passwordagain"]);
if (isset($_POST["email"])) $email = trim(strip_tags($_POST["email"]));
if (!isset($_POST["active"]) && isset($_POST["adduser"])) $active = 0;
if (isset($_POST['copyusersettings'])) $copyusersettings = (int)$_POST['copyusersettings'];
if (!isset($_POST["adminaccess"]) && isset($_POST["submit"])) $adminaccess = 0;
if (isset($_POST['sel_groups']) && is_array($_POST['sel_groups']) ) $sel_groups = $_POST['sel_groups'];

if( isset($_POST["cancel"]) ) {
  redirect("listusers.php".$urlext);
  return;
}

if (isset($_POST["submit"])) {
  $validinfo = true;

  if ($user == "") {
    $validinfo = false;
    $error .= "<li>".lang('nofieldgiven', array(lang('username')))."</li>";
  }
  else if ( !preg_match("/^[a-zA-Z0-9\._ ]+$/", $user) ) {
    $validinfo = false;
    $error .= "<li>".lang('illegalcharacters', array(lang('username')))."</li>";
  }

  if ($password == "") {
    $validinfo = false;
    $error .= "<li>".lang('nofieldgiven', array(lang('password')))."</li>";
  }
  else if ($password != $passwordagain) {
    // We don't want to see this if no password was given
    $validinfo = false;
    $error .= "<li>".lang('nopasswordmatch')."</li>";
  }

  if (!empty($email) && !is_email($email)) {
    $validinfo = false;
    $error .= '<li>'.lang('invalidemail').'</li>';
  }

  if ($validinfo) {
    $newuser = new User();
    $newuser->username = $user;
    $newuser->SetPassword($password);
    $newuser->active = $active;
    $newuser->firstname = $firstname;
    $newuser->lastname = $lastname;
    $newuser->email = $email;
    $newuser->adminaccess = $adminaccess;
    $newuser->SetPassword($password);

    Events::SendEvent('Core', 'AddUserPre', array('user' => &$newuser));

    $result = $newuser->save();

    if ($result) {
      Events::SendEvent('Core', 'AddUserPost', array('user' => &$newuser));

      // set some default preferences, based on the user creating this user
      $adminid = get_userid();
      $userid = $newuser->id;
      if( $copyusersettings > 0 ) {
	$prefs = cms_userprefs::get_all_for_user($copyusersettings);
	if( is_array($prefs) && count($prefs) ) {
	  foreach( $prefs as $k => $v ) {
	    cms_userprefs::set_for_user($userid,$k,$v);
	  }
	}
      }
      else {
	set_preference($userid, 'default_cms_language', get_preference($adminid, 'default_cms_language'));
	set_preference($userid, 'wysiwyg', get_preference($adminid,'backendwysiwyg'));
	set_preference($userid, 'admintheme', get_site_preference('logintheme',CmsAdminThemeBase::GetDefaultTheme()));
	set_preference($userid, 'bookmarks', get_preference($adminid, 'bookmarks'));
	set_preference($userid, 'recent', get_preference($adminid, 'recent'));
      }

      if ($assign_group_perm && is_array($sel_groups) && count($sel_groups) ) {
	$iquery = "INSERT INTO ".cms_db_prefix()."user_groups (user_id,group_id) VALUES (?,?)";
	foreach( $sel_groups as $gid ) {
	  $gid = (int)$gid;
	  if( $gid < 1 ) continue;
	  $db->Execute($iquery,array($userid,$gid));
	}
      }

      // put mention into the admin log
      audit($newuser->id, 'Admin Username: '.$newuser->username, 'Added');
      redirect("listusers.php".$urlext);
    }
    else {
      $error .= "<li>".lang('errorinsertinguser')."</li>";
    }
  }
}

include_once("header.php");

if ($error != "") {
  echo $themeObject->ShowErrors('<ul class="error">'.$error.'</ul>');
}

$smarty = cmsms()->GetSmarty();
$smarty->assign('adminaccess',$adminaccess);
$smarty->assign('active',$active);
$smarty->assign('user',$user);
$smarty->assign('password',$password);
$smarty->assign('passwordagain',$passwordagain);
$smarty->assign('firstname',$firstname);
$smarty->assign('lastname',$lastname);
$smarty->assign('email',$email);
$smarty->assign('active',$active);
$smarty->assign('copyusersettings',$copyusersettings);
$smarty->assign('sel_groups',$sel_groups);
$smarty->assign('my_userid',get_userid());
if($assign_group_perm ) {
  $groups = GroupOperations::get_instance()->LoadGroups();
  $smarty->assign('groups',$groups);
}

$out = array(-1=>lang('none'));
$userlist = UserOperations::get_instance()->LoadUsers();
foreach( $userlist as $one ) {
  $out[$one->id] = $one->username;
}
$smarty->assign('users',$out);

$smarty->display('adduser.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>