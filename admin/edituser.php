<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
$urlext=CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$userid = get_userid();
if( !check_permission($userid, 'Manage Users') ) {
  die('Permission Denied');
}

if (isset($_POST["cancel"])) {
  redirect("listusers.php?".$urlext);
  return;
}

//
// initialization
//
$error = "";
$dropdown = "";
$user = "";
$password = "";
$passwordagain = "";
$firstname = "";
$lastname = "";
$email = "";
$adminaccess = 1;
$active = 1;
$tplmaster = 0;
$copyfromtemplate = 1;
$message = '';

$user_id = $userid;
if (isset($_POST["user_id"])) $user_id = cleanValue($_POST["user_id"]);
else if (isset($_GET["user_id"])) $user_id = cleanValue($_GET["user_id"]);

if (isset($_POST["user"])) $user = cleanValue($_POST["user"]);
if (isset($_POST["password"])) $password = $_POST["password"];
if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];
if (isset($_POST["firstname"])) $firstname = cleanValue($_POST["firstname"]);
if (isset($_POST["lastname"])) $lastname = cleanValue($_POST["lastname"]);
if (isset($_POST["email"])) $email = trim(strip_tags($_POST["email"]));
if (!isset($_POST["adminaccess"]) && isset($_POST["submit"])) $adminaccess = 0;
if (!isset($_POST["active"]) && ($user_id != $userid) && $userid != 1 && isset($_POST["submit"]) ) $active = 0;

$gCms = cmsms();
$userops = $gCms->GetUserOperations();
$groupops = $gCms->GetGroupOperations();
$group_list = $groupops->LoadGroups();
$db = $gCms->GetDb();

$thisuser = $userops->LoadUserByID($user_id);

// this is now always true... but we may want to change how things work, so I'll leave it
$access_user = ($userid == $user_id);
$access_group = $userops->UserInGroup($userid,1) || (!$userops->UserInGroup($user_id,1));
$access = $access_user && $access_group;
$assign_group_perm = check_permission($userid,'Manage Groups');
$manage_users = check_permission($userid,'Manage Users');
$use_wysiwyg = "";

if (isset($_POST["submit"])) {
  $validinfo = true;

  if ($user == "") {
    $validinfo = false;
    $error .= "<li>".lang('nofieldgiven', array(lang('username')))."</li>";
  }

  if ( !preg_match("/^[a-zA-Z0-9\._ ]+$/", $user) ) {
    $validinfo = false;
    $error .= "<li>".lang('illegalcharacters', array(lang('username')))."</li>";
  } 

  if ($password != $passwordagain) {
    $validinfo = false;
    $error .= "<li>".lang('nopasswordmatch')."</li>";
  }

  if (!empty($email) && !is_email($email)) {
    $validinfo = false;
    $error .= '<li>'.lang('invalidemail').': '.$email.'</li>';
  }

  if( isset($_POST['copyusersettings']) && $_POST['copyusersettings'] > 0 ) {
    if( isset($_POST['clearusersettings']) ) {
      // error: both can't be set
      $validinfo = false;
      $error .= '</li>'.lang('error_multiusersettings').'</li>';
    }
  }

  if ($validinfo) {
    $result = false;
    if ($thisuser) {
      $thisuser->username = $user;
      $thisuser->firstname = $firstname;
      $thisuser->lastname = $lastname;
      $thisuser->email = $email;
      $thisuser->adminaccess = $adminaccess;
      $thisuser->active = $active;
      if ($password != "") {
	$thisuser->SetPassword($password);
      }

      Events::SendEvent('Core', 'EditUserPre', array('user' => &$thisuser));

      $result = $thisuser->save();

      if ($assign_group_perm && isset($_POST['groups'])) {
	$dquery = "delete from ".cms_db_prefix()."user_groups where user_id=?";
	$iquery = "insert into ".cms_db_prefix()."user_groups (user_id,group_id) VALUES (?,?)";
	$result = $db->Execute($dquery,array($thisuser->id));
	foreach($group_list as $thisGroup) {
	  if (isset($_POST['g'.$thisGroup->id]) && $_POST['g'.$thisGroup->id] == 1) {
	    $result = $db->Execute($iquery,array($thisuser->id,$thisGroup->id));
	  }
	}
      }
    }

    audit($user_id, 'Admin Username: '.$thisuser->username, ' Edited');
    $message = lang('edited_user');
    if ($result) {
      if( isset($_POST['copyusersettings']) && $_POST['copyusersettings'] > 0 ) {
	// copy user preferences from the template user to this user.
	$prefs = cms_userprefs::get_all_for_user((int)$_POST['copyusersettings']);
	if( is_array($prefs) && count($prefs) ) {
	  cms_userprefs::remove_for_user($user_id);
	  foreach( $prefs as $k => $v ) {
	    cms_userprefs::set_for_user($user_id,$k,$v);
	  }
	  audit($user_id,'Admin Username: '.$thisuser->username,'settings copied from template user');
	  $message = lang('msg_usersettingscopied');
	}
      }
      else if( isset($_POST['clearusersettings']) ) {
	// clear all preferences for this user.
	audit($user_id,'Admin Username: '.$thisuser->username,' settings cleared');
	cms_userprefs::remove_for_user($user_id);
	$message = lang('msg_usersettingscleared');
      }

      // put mention into the admin log
      Events::SendEvent('Core', 'EditUserPost', array('user' => &$thisuser));
      $gCms->clear_cached_files();
      $url = 'listusers.php?'.$urlext;
      if( $message ) {
	$message = urlencode($message);
	$url .= '&message='.$message;
      }
      redirect($url);
    }
    else {
      $error .= "<li>".lang('errorupdatinguser')."</li>";
    }
  }
}
 else if ($user_id != -1) {
   $user = $thisuser->username;
   $firstname = $thisuser->firstname;
   $lastname = $thisuser->lastname;
   $email = $thisuser->email;
   $adminaccess = $thisuser->adminaccess;
   $active = $thisuser->active;
}

include_once("header.php");

if (FALSE == empty($error)) {
  echo $themeObject->ShowErrors('<ul class="error">'.$error.'</ul>');
}

$smarty = cmsms()->GetSmarty();
$smarty->assign('user_id',$user_id);
$smarty->assign('user',$user);
$smarty->assign('firstname',$firstname);
$smarty->assign('lastname',$lastname);
$smarty->assign('email',$email);
$smarty->assign('adminaccess',$adminaccess);
$smarty->assign('active',$active);
$smarty->assign('tplmaster',$tplmaster);
$smarty->assign('copyfromtemplate',$copyfromtemplate);
if ($assign_group_perm && !$access_user && ($user_id != 1)) {
  $groups = GroupOperations::get_instance()->LoadGroups();
  $smarty->assign('groups',$groups);
  $smarty->assign('membergroups',UserOperations::get_instance()->GetMemberGroups($user_id));
}
$smarty->assign('access_user',$access_user);
$smarty->assign('manage_users',$manage_users);

$out = array(-1=>lang('none'));
$userlist = UserOperations::get_instance()->LoadUsers();
foreach( $userlist as $one ) {
  if( $one->id == $user_id ) continue;
  $out[$one->id] = $one->username;
}
$smarty->assign('users',$out);

$smarty->display('edituser.tpl');
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>