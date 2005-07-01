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

$default_cms_lang = '';
if (isset($_POST['default_cms_lang'])) $default_cms_lang = $_POST['default_cms_lang'];
$old_default_cms_lang = '';
if (isset($_POST['old_default_cms_lang'])) $old_default_cms_lang = $_POST['old_default_cms_lang'];

#if ($default_cms_lang != $old_default_cms_lang && $default_cms_lang != '')
if ($default_cms_lang != '')
{
	$_POST['change_cms_lang'] = $default_cms_lang;
}

$admintheme = 'default';
if (isset($_POST['admintheme'])) $admintheme = $_POST['admintheme'];

$bookmarks = 0;
if (isset($_POST['bookmarks'])) $bookmarks = $_POST['bookmarks'];

require_once("../include.php");

check_login();

$error = "";

$wysiwyg = '';
if (isset($_POST["wysiwyg"])) $wysiwyg = $_POST["wysiwyg"];

$userid = get_userid();

if (isset($_POST["cancel"])) {
	redirect("index.php");
	return;
}

if (isset($_POST["submit_form"])) {
	set_preference($userid, 'wysiwyg', $wysiwyg);
	set_preference($userid, 'default_cms_language', $default_cms_lang);
	set_preference($userid, 'admintheme', $admintheme);
	set_preference($userid, 'bookmarks', $bookmarks);
	audit(-1, '', 'Edited User Preferences');
	$error = lang('prefsupdated');
	#redirect("index.php");
	#return;
} else if (!isset($_POST["edituserprefs"])) {
	$wysiwyg = get_preference($userid, 'wysiwyg');
	$default_cms_lang = get_preference($userid, 'default_cms_language');
	$old_default_cms_lang = $default_cms_lang;
	$admintheme = get_preference($userid, 'admintheme');
    $bookmarks = get_preference($userid, 'bookmarks');
}

include_once("header.php");

if ($error != "") {
	echo '<div class="pagemcontainer"><p class="pagemessage">'.$error.'</p></div>';
}

?>

<div class="pagecontainer">
	<div class="pageoverflow">
		<p class="pageheader"><?php echo lang("userprefs"); ?></p>
		<form method="post" action="editprefs.php" name="prefsform">
			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('wysiwygtouse'); ?>:</p>
				<p class="pageinput">
					<select name="wysiwyg">
					<option value=""><?php echo lang('none'); ?></option>
					<?php
						foreach($gCms->modules as $key=>$value)
						{
							if ($gCms->modules[$key]['installed'] == true &&
								$gCms->modules[$key]['active'] == true &&
								$gCms->modules[$key]['object']->IsWYSIWYG())
							{
								echo '<option value="'.$key.'"';
								if ($wysiwyg == $key)
								{
									echo ' selected="selected"';
								}
								echo '>'.$key.'</option>';
							}
						}
					?>
					</select>
				</p>
			</div>
			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('language'); ?>:</p>
				<p class="pageinput">
					<select name="default_cms_lang" onchange="document.prefsform.submit();" style="vertical-align: middle;">
					<option value=""><?php echo lang('nodefault'); ?></option>
					<?php
						asort($nls["language"]);
						foreach ($nls["language"] as $key=>$val) {
							echo "<option value=\"$key\"";
							if ($default_cms_lang == $key) {
								echo " selected=\"selected\"";
							}
							echo ">$val";
							if (isset($nls["englishlang"][$key]))
							{
								echo " (".$nls["englishlang"][$key].")";
							}
							echo "</option>\n";
						}
					?>
					</select>
				</p>
			</div>
			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('admintheme');  ?>:</p>
				<p class="pageinput">
					<?php
						if ($dir=opendir(dirname(__FILE__)."/themes/")) { //Does the themedir exist at all, it should...
								echo '<select name="admintheme">';
									while (($file = readdir($dir)) !== false) {
										if (is_dir("themes/".$file) && ( $file[0] != '.')) {
											echo '<option value="'.$file.'"';
											echo (get_preference($userid,"admintheme")==$file?" selected=\"selected\"":"");
											echo '>'.$file.'</option>';
										}
									}
								echo '</select>';
						}
					?>	
				</p>					
			</div>
			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('admincallout'); ?>:</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="bookmarks" <?php if ($bookmarks) echo "checked=\"checked\""; ?> /><?php echo lang('showbookmarks') ?>
				</p>
			</div>
			<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="edituserprefs" value="true" /><input type="hidden" name="old_default_cms_lang" value="<?php echo $old_default_cms_lang; ?>" />
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="submit_form" value="<?php echo lang('submit'); ?>" />
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="cancel" value="<?php echo lang('cancel'); ?>" />
			</p>
			</div>			
		</form>
	</div>
</div>	

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
