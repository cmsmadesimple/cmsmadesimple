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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
require_once("../lib/classes/class.user.inc.php");

check_login();

$error = "";

$dropdown = "";

$user = "";
if (isset($_POST["user"])) $user = $_POST["user"];

$password = "";
if (isset($_POST["password"])) $password = $_POST["password"];

$passwordagain = "";
if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];

$firstname = "";
if (isset($_POST["firstname"])) $firstname = $_POST["firstname"];

$lastname = "";
if (isset($_POST["lastname"])) $lastname = $_POST["lastname"];

$email = "";
if (isset($_POST["email"])) $email = $_POST["email"];

$adminaccess = 1;
if (!isset($_POST["adminaccess"]) && isset($_POST["edituser"])) $adminaccess = 0;

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["edituser"])) $active = 0;

$user_id = -1;
if (isset($_POST["user_id"])) $user_id = $_POST["user_id"];
else if (isset($_GET["user_id"])) $user_id = $_GET["user_id"];

$userid = get_userid();
$access = check_permission($userid, 'Modify User') || ($userid == $user_id);

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

		if ($password != $passwordagain) {
			$validinfo = false;
			$error .= "<li>".lang('nopasswordmatch')."</li>";
		}

		if ($validinfo) {
			#set_preference($userid, 'use_wysiwyg', $use_wysiwyg);
			#audit(-1, '', 'Edited User');

			$result = false;
			$thisuser = UserOperations::LoadUserByID($user_id);
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
				
				#Perform the edituser_pre callback
				foreach($gCms->modules as $key=>$value)
				{
					if (isset($gCms->modules[$key]['edituser_pre_function']) &&
						$gCms->modules[$key]['Installed'] == true &&
						$gCms->modules[$key]['Active'] == true)
					{
						call_user_func_array($gCms->modules[$key]['edituser_pre_function'], array(&$gCms, &$thisuser));
					}
				}

				$result = $thisuser->save();
			}

			if ($result)
			{
				audit($user_id, $thisuser->username, 'Edited User');

				#Perform the edituser_post callback
				foreach($gCms->modules as $key=>$value)
				{
					if (isset($gCms->modules[$key]['edituser_post_function']) &&
						$gCms->modules[$key]['Installed'] == true &&
						$gCms->modules[$key]['Active'] == true)
					{
						call_user_func_array($gCms->modules[$key]['edituser_post_function'], array(&$gCms, &$thisuser));
					}
				}

				redirect("listusers.php");
			}
			else {
				$error .= "<li>".lang('errorupdatinguser')."</li>";
			}
		}

	}
	else if ($user_id != -1) {

		$thisuser = UserOperations::LoadUserByID($user_id);
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
	print "<h3>".lang('noaccessto', array(lang('edituser')))."</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

?>

<form method="post" action="edituser.php">

<div class="adminformSmall">

<h3><?php echo lang('edituser')?></h3>

<table border="0">

	<tr>
		<td>*<?php echo lang('name')?>:</td>
		<td><input type="text" name="user" maxlength="25" value="<?php echo $user?>" class="standard" /></td>
	</tr>
	<tr>
		<td><?php echo lang('password')?></td>
		<td ><input type="password" name="password" maxlength="25" value="" class="standard" /></td>
	</tr>
	<tr>
		<td><?php echo lang('passwordagain')?>:</td>
		<td><input type="password" name="passwordagain" maxlength="25" value="" class="standard" /></td>
	</tr>
	<tr>
		<td colspan="2" style="font-size: .83em;">Leave password fields blank to keep current password.</td>
	</tr>
	<tr>
		<td><?php echo lang('firstname')?>:</td>
		<td><input type="text" name="firstname" maxlength="50" value="<?php echo $firstname?>" class="standard" /></td>
	</tr>
	<tr>
		<td><?php echo lang('lastname')?>:</td>
		<td><input type="text" name="lastname" maxlength="50" value="<?php echo $lastname?>" class="standard" /></td>
	</tr>
	<tr>
		<td><?php echo lang('email')?>:</td>
		<td><input type="text" name="email" maxlength="255" value="<?php echo $email?>" class="standard" /></td>
	</tr>
	<!--
	<tr>
		<td><?php echo lang('adminaccess')?>:</td>
		<td><input type="checkbox" name="adminaccess" <?php echo ($adminaccess == 1?"checked=\"checked\"":"")?> /></td>
	</tr>
	-->
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> /></td>
	</tr>
	<!--
	<tr>
		<td><?php echo lang('usewysiwyg')?>:</td>
		<td>
			<select name="use_wysiwyg">
				<option value="1" <?php echo  ($use_wysiwyg=="1"?"selected":"") ?>><?php echo lang('true')?></option>
				<option value="0" <?php echo  ($use_wysiwyg=="0"?"selected":"") ?>><?php echo lang('false')?></option>
			</select>
		</td>
	</tr>
	-->
	<tr>
		<td colspan="2" align="center"><input type="hidden" name="user_id" value="<?php echo $user_id?>" /><input type="hidden" name="edituser" value="true" />
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>

</table>

</div>

</form>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
