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

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "include.php");

check_login();

$error = "";

$dropdown = "";

$user = "";
if (isset($_POST["user"])) $user = CmsRequest::clean_value($_POST["user"]);

$password = "";
if (isset($_POST["password"])) $password = $_POST["password"];

$passwordagain = "";
if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];

$firstname = "";
if (isset($_POST["firstname"])) $firstname = CmsRequest::clean_value($_POST["firstname"]);

$lastname = "";
if (isset($_POST["lastname"])) $lastname = CmsRequest::clean_value($_POST["lastname"]);

$email = "";
if (isset($_POST["email"])) $email = CmsRequest::clean_value($_POST["email"]);

$openid = "";
if (isset($_POST["openid"])) $openid = CmsRequest::clean_value($_POST["openid"]);

$adminaccess = 1;
if (!isset($_POST["adminaccess"]) && isset($_POST["edituser"])) $adminaccess = 0;

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["edituser"])) $active = 0;

$userid = get_userid();
$user_id = $userid;
if (isset($_POST["user_id"])) $user_id = CmsRequest::clean_value($_POST["user_id"]);
else if (isset($_GET["user_id"])) $user_id = CmsRequest::clean_value($_GET["user_id"]);

global $gCms;
$userops =& $gCms->GetUserOperations();
$thisuser = $userops->LoadUserByID($user_id);
if (strlen($thisuser->username) > 0)
{
	$CMS_ADMIN_SUBTITLE = $thisuser->username;
}

// this is now always true... but we may want to change how things work, so I'll leave it
$access_perm = check_permission($userid, 'Modify Users');
$access_user = ($userid == $user_id);
$access = $access_perm | $access_user;

$use_wysiwyg = "";
#if (isset($_POST["use_wysiwyg"])){$use_wysiwyg = $_POST["use_wysiwyg"];}
#else{$use_wysiwyg = get_preference($userid, 'use_wysiwyg');}

if ($access) {

	if (isset($_POST["cancel"])) {
		redirect("listusers.php");
		return;
	}

	if (isset($_POST["edituser"])) {
	
		$validinfo = true;

		if ($user == "") {
			$validinfo = false;
			$error .= "<li>".lang('nofieldgiven', array(lang('username')))."</li>";
		}

		if ( !preg_match("/^[a-zA-Z0-9\.]+$/", $user) ) {
			$validinfo = false;
			$error .= "<li>".lang('illegalcharacters', array(lang('username')))."</li>";
		} 

		if ($password != $passwordagain) {
			$validinfo = false;
			$error .= "<li>".lang('nopasswordmatch')."</li>";
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
				$thisuser->openid = $openid;
				if ($password != "")
				{
					$thisuser->set_password($password);
				}
				
				#Perform the edituser_pre callback
				/*
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->EditUserPre($thisuser);
					}
				}
				
				Events::SendEvent('Core', 'EditUserPre', array('user' => &$thisuser));
				*/


				$result = $thisuser->save();
			}

			if ($result)
			{
				audit($user_id, $thisuser->username, 'Edited User');

				#Perform the edituser_post callback
				/*
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->EditUserPost($thisuser);
					}
				}
				
				Events::SendEvent('Core', 'EditUserPost', array('user' => &$thisuser));
				*/
				
                if ($access_perm)
				{
				    redirect("listusers.php");
				}
				else
				{
                    redirect("topmyprefs.php");
				}
			}
			else
			{
				$error .= "<li>".lang('errorupdatinguser')."</li>";
			}
		}

	}
	else if ($user_id != -1)
	{
		$user = $thisuser->username;
		$firstname = $thisuser->firstname;
		$lastname = $thisuser->lastname;
		$email = $thisuser->email;
		$adminaccess = $thisuser->adminaccess;
		$active = $thisuser->active;
		$openid = $thisuser->openid;
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
													   <p class="pageinput"><input type="password" name="passwordagain" maxlength="25" value="" class="standard" />&nbsp;<? echo lang('info_edituser_passwordagain') ?></p>
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
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('openid')?>:</p>
			<p class="pageinput"><input type="text" name="openid" id="openid" maxlength="255" value="<?php echo $openid?>" class="standard" /></p>
		</div>
	   <?php
	   if( $access_perm && !$access_user ) {
           ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('active')?>:</p>
			<p class="pageinput"><input class="pagecheckbox" type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> /></p>
		</div>
	   <?php
	   } else {
			echo '<input type="hidden" name="active" value="'.$active.'" />';
	   }
           ?>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="user_id" value="<?php echo $user_id?>" />
				<input type="hidden" name="edituser" value="true" />
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" value="<?php echo lang('submit')?>" />
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="cancel" value="<?php echo lang('cancel')?>" />
			</p>
		</div>
	</form>
</div>
<?php

}

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
