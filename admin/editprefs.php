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

check_login();

$error = "";

$use_wysiwyg = "1";
if (isset($_POST["use_wysiwyg"])) $use_wysiwyg = $_POST["use_wysiwyg"];

$userid = get_userid();

if (isset($_POST["cancel"])) {
	redirect("index.php");
	return;
}

if (isset($_POST["edituserprefs"])) {
	set_preference($userid, 'use_wysiwyg', $use_wysiwyg);
	audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], -1, '', 'Edited User Preferences');
	redirect("index.php");
	return;
} else if (!isset($_POST["submit"])) {
	$use_wysiwyg = get_preference($userid, 'use_wysiwyg');
}

include_once("header.php");

if ($error != "") {
	echo "<ul class=\"error\">".$error."</ul>";
}

?>

<form method="post" action="editprefs.php">

<div class="adminformSmall">

<h3><?php echo lang("userprefs")?></h3>

<table border="0" align="center">

	<tr>
		<td><?php echo lang("usewysiwyg")?>:</td>
		<td>
			<select name="use_wysiwyg">
				<option value="1" <?php echo  ($use_wysiwyg=="1"?"selected":"") ?>><?php echo lang('true')?></option>
				<option value="0" <?php echo  ($use_wysiwyg=="0"?"selected":"") ?>><?php echo lang('false')?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="hidden" name="edituserprefs" value="true">
		<input type="submit" name="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>

</table>

</div>

</form>

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
