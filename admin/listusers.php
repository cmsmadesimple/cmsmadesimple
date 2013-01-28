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
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$userid = get_userid();
if( !check_permission($userid, 'Manage Users') ) {
  die('Permission Denied');
  return;
}

include_once("header.php");
$gCms = cmsms();
$db = $gCms->GetDb();
$templateuser = cms_siteprefs::get('template_userid');
$page = 1;
$limit = 20;
$message = '';
$error = '';
$userops = UserOperations::get_instance();

if (isset($_GET["toggleactive"])) {
  if($_GET["toggleactive"]==1) {
    $error .= "<li>".lang('errorupdatinguser')."</li>";
  } else {
    $userops = $gCms->GetUserOperations();
    $thisuser = $userops->LoadUserByID($_GET["toggleactive"]);

    if( $thisuser ) {

      //modify users, is this enough?
      $userid = get_userid();

      $result = false;
      $thisuser->active == 1 ? $thisuser->active = 0 : $thisuser->active=1;
      Events::SendEvent('Core','EditUserPre',array('user'=>$thisuser));
      $result = $thisuser->save();

      if ($result) {
	// put mention into the admin log
	audit($userid, 'Admin Username: '.$thisuser->username, 'Edited');
	Events::SendEvent('Core','EditUserPost',array('user'=>$thisuser));
      } else {
	$error .= "<li>".lang('errorupdatinguser')."</li>";
      }
    }
  }
}
else if( isset($_POST['bulk']) && isset($_POST['bulkaction']) && 
	 isset($_POST['multiselect']) && is_array($_POST['multiselect']) && count($_POST['multiselect']) ) {
  switch( $_POST['bulkaction'] ) {
  case 'delete':
    $userops = UserOperations::get_instance();
    $ndeleted = 0;
    foreach( $_POST['multiselect'] as $uid ) {
      $uid = (int)$uid;
      if( $uid <= 1 ) continue; // can't delete the magic user... 
      if( $uid == get_userid() ) continue; // can't delete self.
      $oneuser = $userops->LoadUserById($uid);
      if( !is_object($oneuser) ) continue; // invalid user
      $ownercount = $userops->CountPageOwnershipById($uid);
      if( $ownercount > 0 ) continue; // can't delete user who owns pages.
      
      // ready to delete.
      Events::SendEvent('Core', 'DeleteUserPre', array('user' => &$oneuser));
      $oneuser->Delete();
      Events::SendEvent('Core', 'DeleteUserPost', array('user' => &$oneuser));
      audit($uid, 'Admin Username: '.$user_name, 'Deleted');
      $ndeleted++;
    }
    if( $ndeleted > 0 ) {
      $message = lang('msg_userdeleted',$ndeleted);
    }
    break;

  case 'clearoptions':
    $nusers = 0;
    foreach( $_POST['multiselect'] as $uid ) {
      $uid = (int)$uid;
      if( $uid <= 1 ) continue; // can't edit the magic user... 
      $oneuser = $userops->LoadUserById($uid);
      if( !is_object($oneuser) ) continue; // invalid user

      Events::SendEvent('Core','EditUserPre',array('user'=>$oneuser));
      cms_userprefs::remove_for_user($uid);
      Events::SendEvent('Core','EditUserPost',array('user'=>$oneuser));
      audit($uid,'Admin Username: '.$oneuser->username,'Settings cleared');
      $nusers++;
    }
    if( $nusers > 0 ) {
      $message = lang('msg_usersedited',$nusers);
    }
    break;

  case 'disable':
    $nusers = 0;
    foreach( $_POST['multiselect'] as $uid ) {
      $uid = (int)$uid;
      if( $uid <= 1 ) continue; // can't disable the magic user... 
      if( $uid == get_userid() ) continue; // can't disable self.
      $oneuser = $userops->LoadUserById($uid);
      if( !is_object($oneuser) ) continue; // invalid user

      if( $oneuser->active ) {
	Events::SendEvent('Core','EditUserPre',array('user'=>$oneuser));
	$oneuser->active = 0;
	$oneuser->save();
	Events::SendEvent('Core','EditUserPost',array('user'=>$oneuser));
	audit($uid,'Admin Username: '.$oneuser->username,'Disabled');
	$nusers++;
      }
    }
    if( $nusers > 0 ) {
      $message = lang('msg_usersedited',$nusers);
    }
    break;

  case 'enable':
    $nusers = 0;
    foreach( $_POST['multiselect'] as $uid ) {
      $uid = (int)$uid;
      if( $uid <= 1 ) continue; // can't disable the magic user... 
      if( $uid == get_userid() ) continue; // can't disable self.
      $oneuser = $userops->LoadUserById($uid);
      if( !is_object($oneuser) ) continue; // invalid user

      if( !$oneuser->active ) {
	Events::SendEvent('Core','EditUserPre',array('user'=>$oneuser));
	$oneuser->active = 1;
	$oneuser->save();
	Events::SendEvent('Core','EditUserPost',array('user'=>$oneuser));
	audit($uid,'Admin Username: '.$oneuser->username,'Enabled');
	$nusers++;
      }
    }
    if( $nusers > 0 ) {
      $message = lang('msg_usersedited',$nusers);
    }
    break;
  }
}

if (FALSE == empty($error)) {
  echo $themeObject->ShowErrors('<ul class="error">'.$error.'</ul>');
}
if (isset($_GET["message"])) {
  $message = preg_replace('/\</','',$_GET['message']);
}
if( FALSE == empty($message)) {
  echo '<div class="pagemcontainer"><p class="pagemessage">'.$message.'</p></div>';
}


$offset = ((int)$page - 1)*$limit;
$userlist = $userops->LoadUsers($limit,$offset);
foreach( $userlist as &$oneuser ) {
  $oneuser->access_to_user = 1;
  if( $userops->UserInGroup($oneuser->id,1) && !$userops->UserInGroup($userid,1) ) {
    $oneuser->access_to_user = 0;
  }
  $oneuser->pagecount = $userops->CountPageOwnershipById($uid);
}
$smarty->assign('users',$userlist);
$smarty->assign('my_userid',get_userid());
$smarty->assign('urlext',$urlext);

$smarty->display('listusers.tpl');
?>
<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('back')?></a></p>
<?php
include_once("footer.php");

 /*
<div class="pagecontainer">
  <div class="pageoverflow">
  <?php
    $userid = get_userid();

    $query = "SELECT user_id, username, active FROM ".cms_db_prefix()."users ORDER BY user_id";
    $result = $db->Execute($query);

    $userops = $gCms->GetUserOperations();
    $userlist = $userops->LoadUsers();

    $page = 1;
    if (isset($_GET['page'])) $page = $_GET['page'];
    $limit = 20;
    if (count($userlist) > $limit) {
      echo "<p class=\"pageshowrows\">".pagination($page, count($userlist), $limit)."</p>";
    }
    echo $themeObject->ShowHeader('currentusers').'</div>';
    if ($userlist && count($userlist) > 0) {
      echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
      echo '<thead>';
      echo "<tr>\n";
      echo "<th>".lang('username')."</th>\n";
      echo "<th style=\"text-align: center;\">".lang('active')."</th>\n";
      echo "<th class=\"pageicon\">".lang('templateuser')."</th>\n";
      echo "<th class=\"pageicon\">&nbsp;</th>\n";
      echo "<th class=\"pageicon\">&nbsp;</th>\n";
      echo "<th class=\"pageicon\"><input type=\"checkbox\" id=\"sel_all\" value=\"1\"></th>\n";
      echo "</tr>\n";
      echo '</thead>';
      echo '<tbody>';

      $currow = "row1";
      // construct true/false button images
      $image_true = $themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon');
      $image_false = $themeObject->DisplayImage('icons/system/false.gif', lang('false'),'','','systemicon');

      $counter=0;
      foreach ($userlist as $oneuser) {
	// can access user if: i have edit permission AND user is not in group1 unless I am also in group 1
	// except, I can always edit my own account.  can't delete myself though.
	$this_user = $userid == $oneuser->id;
	$access_to_user = TRUE;
	if( $userops->UserInGroup($oneuser->id,1) && !$userops->UserInGroup($userid,1) ) {
	  $access_to_user = FALSE;
	}
	$access_user = $this_user || $access_to_user;

	if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit) {
	  echo "<tr class=\"$currow\">\n";
	  if( $access_user ) {
	    echo "<td><a href=\"edituser.php".$urlext."&amp;user_id=".$oneuser->id."\">".$oneuser->username."</a></td>\n";
	  }
	  else {
	    echo "<td>{$oneuser->username}</td>\n";
	  }

	  if( $oneuser->id != 1 && $oneuser->id != $userid ) {
	    echo "<td class=\"pagepos\"><a href=\"listusers.php".$urlext."&amp;toggleactive=".$oneuser->id."\">".($oneuser->active == 1?$image_true:$image_false)."</a></td>\n";
	  }
	  else {
	    echo "<td class=\"pagepos\">&nbsp;</td>\n";
	  }

	  if ($access_user) {
	    if( $templateuser > 0 && $templateuser == $oneuser->id ) {
              echo '<td style="text-align: center;">'.$themeObject->DisplayImage('icons/system/true.gif', lang('info_templateuser'),'','','systemicon').'</td>';
	    }
	    else {
              echo '<td></td>';
	    }

	    echo "<td><a href=\"edituser.php".$urlext."&amp;user_id=".$oneuser->id."\">";
	    echo $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
	    echo "</a></td>\n";
	  }
	  else {
	    echo "<td>&nbsp;</td>\n";
	    echo "<td>&nbsp;</td>\n";
	  }

	  if ($oneuser->id != 1 && $oneuser->id != $userid) {
	    echo "<td><a href=\"deleteuser.php".$urlext."&amp;user_id=".$oneuser->id."\" onclick=\"return confirm('".lang('deleteconfirm', $oneuser->username)."');\">";
	    echo $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
	    echo "</a></td>\n";
	  }
	  else {
	    echo '<td></td>';
	  }
	  echo "</tr>\n";

	  ($currow=="row1"?$currow="row2":$currow="row1");
	}
	$counter++;
      }

      echo '</tbody>';
      echo "</table>\n";
    }
  ?>
  <div class="pageoptions">
    <p class="pageoptions">

      <a href="adduser.php<?php echo $urlext ?>">
	<?php 
	  echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('adduser'),'','','systemicon').'</a>';
          echo ' <a class="pageoptions" href="adduser.php'.$urlext.'">'.lang("adduser");
	?>
      </a>
    </p>
  </div>
</div>

<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('back')?></a></p>

<?php

include_once("footer.php");
 */
# vim:ts=4 sw=4 noet
?>
