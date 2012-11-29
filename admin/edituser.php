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
require_once("../lib/classes/class.user.inc.php");
$urlext=CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$error = "";

$dropdown = "";

$user = "";
if (isset($_POST["user"])) $user = cleanValue($_POST["user"]);

$password = "";
if (isset($_POST["password"])) $password = $_POST["password"];

$passwordagain = "";
if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];

$firstname = "";
if (isset($_POST["firstname"])) $firstname = cleanValue($_POST["firstname"]);

$lastname = "";
if (isset($_POST["lastname"])) $lastname = cleanValue($_POST["lastname"]);

$email = "";
if (isset($_POST["email"])) $email = trim(strip_tags($_POST["email"]));

$adminaccess = 1;
if (!isset($_POST["adminaccess"]) && isset($_POST["edituser"])) $adminaccess = 0;

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["edituser"])) $active = 0;

$userid = get_userid();
$user_id = $userid;
if (isset($_POST["user_id"])) $user_id = cleanValue($_POST["user_id"]);
else if (isset($_GET["user_id"])) $user_id = cleanValue($_GET["user_id"]);

$gCms = cmsms();
$userops = $gCms->GetUserOperations();
$groupops = $gCms->GetGroupOperations();
$group_list = $groupops->LoadGroups();
$db = $gCms->GetDb();


$thisuser = $userops->LoadUserByID($user_id);
if (strlen($thisuser->username) > 0)
  {
    $CMS_ADMIN_SUBTITLE = $thisuser->username;
  }

// this is now always true... but we may want to change how things work, so I'll leave it
$access_perm = check_permission($userid, 'Modify Users');
$access_user = ($userid == $user_id);
$access_group = $userops->UserInGroup($userid,1) || (!$userops->UserInGroup($user_id,1));
$access = ($access_perm || $access_user) && $access_group;

$assign_group_perm = check_permission($userid,'Modify Group Assignments');

$use_wysiwyg = "";
#if (isset($_POST["use_wysiwyg"])){$use_wysiwyg = $_POST["use_wysiwyg"];}
#else{$use_wysiwyg = get_preference($userid, 'use_wysiwyg');}

if ($access) {

  if (isset($_POST["cancel"])) {
    redirect("index.php?section=usersgroups&".$urlext);
    return;
  }

  if (isset($_POST["edituser"])) {
	
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

    if (!empty($email) && !is_email($email))
      {
	$validinfo = false;
	$error .= '<li>'.lang('invalidemail').': '.$email.'</li>';
      }

    if ($validinfo) {
#set_preference($userid, 'use_wysiwyg', $use_wysiwyg);
#audit(-1, '', 'Edited User');

      $result = false;
      if ($thisuser)
	{
	  $thisuser->username = $user;
	  $thisuser->firstname = $firstname;
	  $thisuser->lastname = $lastname;
	  $thisuser->email = $email;
	  $thisuser->adminaccess = $adminaccess;
	  $thisuser->active = $active;
	  if ($password != "")
	    {
	      $thisuser->SetPassword($password);
	    }
				
	  Events::SendEvent('Core', 'EditUserPre', array('user' => &$thisuser));


	  $result = $thisuser->save();
				
	  if ($assign_group_perm && isset($_POST['groups']))
	    {
	      $dquery = "delete from ".cms_db_prefix()."user_groups where user_id=?";
	      $iquery = "insert into ".cms_db_prefix().
		"user_groups (user_id,group_id) VALUES (?,?)";
	      $result = $db->Execute($dquery,array($thisuser->id));
	      foreach($group_list as $thisGroup)
		{
                  if (isset($_POST['g'.$thisGroup->id]) && $_POST['g'.$thisGroup->id] == 1)
		    {
		      $result = $db->Execute($iquery,array($thisuser->id,$thisGroup->id));
		    }
		}
	    }
	}

      if ($result)
	{
	  // put mention into the admin log
	  audit($user_id, 'Admin Username: '.$thisuser->username, 'Edited');
	  Events::SendEvent('Core', 'EditUserPost', array('user' => &$thisuser));
	  $gCms->clear_cached_files();
				
	  if ($access_perm)
	    {
	      redirect("listusers.php?".$urlext);
	    }
	  else
	    {
	      redirect("index.php?section=usersgroups&".$urlext);
	    }

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
}

include_once("header.php");

if (!$access) {
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('edituser')))."</p></div>";	
}
else {
	if (FALSE == empty($error)) {
	    echo $themeObject->ShowErrors('<ul class="error">'.$error.'</ul>');
	}
?>

<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('edituser'); ?>
	<form method="post" action="edituser.php">
        <div>
          <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
        </div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" name="user" maxlength="25" value="<?php echo $user?>" class="standard" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('password')?>:</p>
	   <p class="pageinput"><input type="password" name="password" maxlength="25" value="" />&nbsp;<?php echo lang('info_edituser_password') ?></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('passwordagain')?>:</p>
													   <p class="pageinput"><input type="password" name="passwordagain" maxlength="25" value="" class="standard" />&nbsp;<?php echo lang('info_edituser_passwordagain') ?></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('firstname')?>:</p>
			<p class="pageinput"><input type="text" name="firstname" maxlength="50" value="<?php echo $firstname?>" class="standard" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('lastname')?>:</p>
			<p class="pageinput"><input type="text" name="lastname" maxlength="50" value="<?php echo $lastname?>" class="standard" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('email')?>:</p>
			<p class="pageinput"><input type="text" name="email" maxlength="255" value="<?php echo $email?>" class="standard" /></p>
		</div>
	   <?php
  	   if( $access_perm && !$access_user && ($user_id != 1) ) {
           ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('active')?>:</p>
			<p class="pageinput"><input class="pagecheckbox" type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> /></p>
		</div>
	   <?php
	   } else {
			echo '<input type="hidden" name="active" value="'.$active.'" />';
	   }

      if ($assign_group_perm && !$access_user && ($user_id != 1))
      {
      ?>
		<div class="pageoverflow">
			<div class="pagetext"><?php  echo $themeObject->DisplayImage('icons/system/permissions.gif', lang('permissions'),'','','icon-extra');?><b><?php echo lang('groups')?>: </b>
			
            </div>
			<div class="pageinput">
      <?php
	     $query = "SELECT group_id FROM ".cms_db_prefix()."user_groups where user_id=?";

	     $result = $db->Execute($query,array($user_id));
        $groups=array();
	     while($result && $row = $result->FetchRow())
            {
            $groups[$row['group_id']] = 1;
            }

	     echo '<div class="group_memberships clear"><input type="hidden" name="groups" value="1" />';
		$adminuser = ($userops->UserInGroup($userid,1) || $userid == 1);
        foreach($group_list as $thisGroup)
            {
			if( $thisGroup->id == 1 && $adminuser == false )
			  {
				continue;
			  }
            echo '<div class="group"><input type="checkbox" name="g'.$thisGroup->id.'" id="g'.$thisGroup->id.
               '" value="1" ';
            if (isset($groups[$thisGroup->id]) && $groups[$thisGroup->id] == 1)
               {
               echo 'checked="checked"';
               }
            echo '/><label for="g'.$thisGroup->id.'">'.$thisGroup->name.'</label></div>';
            }
        echo '</div>';
      ?>
         </div>
		</div>

      <?php
      }
           ?>
		<div class="pageoverflow">
			<div class="pagetext">&nbsp;</div>
			<div class="pageinput">
				<input type="hidden" name="user_id" value="<?php echo $user_id?>" />
				<input type="hidden" name="edituser" value="true" />
				<input class="pagebutton" type="submit" value="<?php echo lang('submit')?>" />
				<input class="pagebutton" type="submit" name="cancel" value="<?php echo lang('cancel')?>" />
			</div>
		</div>
	</form>
</div>
<?php

}

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
