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

$user= "";
if (isset($_POST["user"])) $user = $_POST["user"];

$firstname = "";
if (isset($_POST["firstname"])) $firstname = $_POST["firstname"];

$lastname = "";
if (isset($_POST["lastname"])) $lastname = $_POST["lastname"];

$password= "";
if (isset($_POST["password"])) $password = $_POST["password"];

$passwordagain= "";
if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];

$email = "";
if (isset($_POST["email"])) $email = $_POST["email"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["adduser"])) $active = 0;

$adminaccess = 0;
if (isset($_POST["adminaccess"]) && isset($_POST["adduser"])) $adminaccess = 1;

if (isset($_POST["cancel"])) {
	redirect("listusers.php");
	return;
}

if (isset($_POST["adduser"])) {

	$validinfo = true;

	if ($user == "") {
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('username')))."</li>";
	}

	if ($password == "") {
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('password')))."</li>";
	}
	else if ($password != $passwordagain) {
		#We don't want to see this if no password was given
		$validinfo = false;
		$error .= "<li>".lang('nopasswordmatch')."</li>";
	}

	if ($validinfo) {
		
		#$new_user_id = $db->GenID(cms_db_prefix()."users_seq");
		#$query = "INSERT INTO ".cms_db_prefix()."users (user_id, username, password, active, create_date, modified_date) VALUES ($new_user_id, ".$db->qstr($user).", ".$db->qstr(md5($password)).", $active, ".$db->DBTimeStamp(time()).", ".$db->DBTimeStamp(time()).")";
		#$result = $db->Execute($query);

		$newuser = new User();
		$newuser->username = $user;
		$newuser->SetPassword($password);
		$newuser->active = $active;
		$newuser->firstname = $firstname;
		$newuser->lastname = $lastname;
		$newuser->email = $email;
		$result = $newuser->save();

		if ($result) {
			audit($newuser->id, $newuser->username, 'Added User');
			redirect("listusers.php");
		}
		else {
			$error .= "<li>".lang('errorinsertinguser')."</li>";
		}
	}
}

include_once("header.php");

if ($error != "") {
	echo "<ul class=\"error\">".$error."</ul>";
}

?>

<form method="post" action="adduser.php">

<div class="adminformSmall">

<h3><?php echo lang('adduser')?></h3>

<table border="0">

	<tr>
		<td>*<?php echo lang('name')?>:</td>
		<td><input type="text" name="user" maxlength="255" value="<?php echo $user?>" class="standard"></td>
	</tr>
	<tr>
		<td>*<?php echo lang('password')?>:</td>
		<td><input type="password" name="password" maxlength="255" value="" class="standard"></td>
	</tr>
	<tr>
		<td>*<?php echo lang('passwordagain')?>:</td>
		<td><input type="password" name="passwordagain" maxlength="255" value="" class="standard"></td>
	</tr>
	<tr>
		<td><?php echo lang('firstname')?>:</td>
		<td><input type="text" name="firstname" maxlength="50" value="" class="standard"></td>
	</tr>
	<tr>
		<td><?php echo lang('lastname')?>:</td>
		<td><input type="text" name="lastname" maxlength="50" value="" class="standard"></td>
	</tr>
	<tr>
		<td><?php echo lang('email')?>:</td>
		<td><input type="text" name="email" maxlength="50" value="" class="standard"></td>
	</tr>
	<tr>
		<td><?php echo lang('adminaccess')?>:</td>
		<td><input type="checkbox" name="adminaccess" <?php echo ($adminaccess == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="adduser" value="true">
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>

</table>

</div>

</form>

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
