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
require_once("../include.php");
check_login();

$admintheme = 'default';
if (isset($_POST['admintheme'])) $admintheme = $_POST['admintheme'];

$bookmarks = 0;
if (isset($_POST['bookmarks'])) $bookmarks = $_POST['bookmarks'];

$hide_help_links = 0;
if (isset($_POST['hide_help_links'])) $hide_help_links = $_POST['hide_help_links'];

$indent = 0;
if (isset($_POST['indent'])) $indent = $_POST['indent'];

$enablenotifications = 1;
if (!isset($_POST['enablenotifications'])) $enablenotifications = 0;

$paging = 0;
if (isset($_POST['paging'])) $paging = $_POST['paging'];

$homepage = '';
if (isset($_POST['homepage'])) $homepage = $_POST['homepage'];

$wysiwyg = '';
if (isset($_POST["wysiwyg"])) $wysiwyg = $_POST["wysiwyg"];

$syntaxhighlighter = '';
if (isset($_POST["syntaxhighlighter"])) $syntaxhighlighter = $_POST["syntaxhighlighter"];

$gcb_wysiwyg = 0;
if (isset($_POST['gcb_wysiwyg'])) $gcb_wysiwyg = 1;

$date_format_string = '%x %X';
if (isset($_POST['date_format_string'])) $date_format_string = $_POST['date_format_string'];

$ignoredmodules = array();
if (isset($_POST['ignoredmodules']) )
  {
    $ignoredmodules = $_POST['ignoredmodules'];
    if( in_array('**none**',$ignoredmodules) )
      {
	$ignoredmodules = array();
      }
  }

$userid = get_userid();

if (isset($_POST["cancel"])) {
	redirect("index.php");
	return;
}

$modules = array();
$modules[ucwords(lang('none'))] = '**none**';
$modules['---'] = '**none**';
foreach($gCms->modules as $key=>$value)
{
  if ($gCms->modules[$key]['installed'] == true &&
      $gCms->modules[$key]['active'] == true)
    {
      $obj =& $gCms->modules[$key]['object'];
      $modules[$obj->GetFriendlyName()] = $obj->GetName();
    }
}


if (isset($_POST["submit_form"])) {
	set_preference($userid, 'gcb_wysiwyg', $gcb_wysiwyg);
	set_preference($userid, 'wysiwyg', $wysiwyg);
	set_preference($userid, 'syntaxhighlighter', $syntaxhighlighter);
	set_preference($userid, 'default_cms_language', $default_cms_lang);
	set_preference($userid, 'admintheme', $admintheme);
	set_preference($userid, 'bookmarks', $bookmarks);
	set_preference($userid, 'hide_help_links', $hide_help_links);
	set_preference($userid, 'indent', $indent);
	set_preference($userid, 'enablenotifications',$enablenotifications);
	set_preference($userid, 'paging', $paging);
	set_preference($userid, 'date_format_string', $date_format_string);
	set_preference($userid, 'homepage', $homepage );
	set_preference($userid, 'ignoredmodules', implode(',',$ignoredmodules));
	audit(-1, '', 'Edited User Preferences');
	$page_message = lang('prefsupdated');
	#redirect("index.php");
	#return;
} else if (!isset($_POST["edituserprefs"])) {
	$gcb_wysiwyg = get_preference($userid, 'gcb_wysiwyg', 1);
	$wysiwyg = get_preference($userid, 'wysiwyg');
	$syntaxhighlighter = get_preference($userid, 'syntaxhighlighter');
	$default_cms_lang = get_preference($userid, 'default_cms_language');
	$old_default_cms_lang = $default_cms_lang;
	$admintheme = get_preference($userid, 'admintheme');
	$bookmarks = get_preference($userid, 'bookmarks');
	$indent = get_preference($userid, 'indent', true);
	$enablenotifications = get_preference($userid, 'enablenotifications', 1);
	$paging = get_preference($userid, 'paging', 0);
	$date_format_string = get_preference($userid, 'date_format_string','%x %X');
	$homepage = get_preference($userid,'homepage');
	$hide_help_links = get_preference($userid, 'hide_help_links');
	$ignoredmodules = explode(',',get_preference($userid,'ignoredmodules'));
}

include_once("header.php");

if (FALSE == empty($page_message)) {
	echo $themeObject->ShowMessage($page_message);
}

?>

<div class="pagecontainer">
	<div class="pageoverflow">
		<?php echo $themeObject->ShowHeader('userprefs'); ?>
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
				<p class="pagetext"><?php echo lang('syntaxhighlightertouse'); ?>:</p>
				<p class="pageinput">
					<select name="syntaxhighlighter">
					<option value=""><?php echo lang('none'); ?></option>
					<?php
						foreach($gCms->modules as $key=>$value)
						{
							if ($gCms->modules[$key]['installed'] == true &&
								$gCms->modules[$key]['active'] == true &&
								$gCms->modules[$key]['object']->IsSyntaxHighlighter())
							{
								echo '<option value="'.$key.'"';
								if ($syntaxhighlighter == $key)
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
				<p class="pagetext"><?php echo lang('gcb_wysiwyg'); ?>:</p>
				<p class="pageinput">
  <input class="pagenb" type="checkbox" name="gcb_wysiwyg" <?php if ($gcb_wysiwyg) echo "checked=\"checked\""; if( get_site_preference('nogcbwysiwyg') == '1' ) echo "disabled=\"disabled\""; ?> /><?php echo lang('gcb_wysiwyg_help') ?>
				</p>
			</div>
				<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('language'); ?>:</p>
				<p class="pageinput">
					<select name="default_cms_lang" style="vertical-align: middle;">
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
		<p class="pagetext"><?php echo lang('date_format_string'); ?>:</p>
		<p class="pageinput">
		<input class="pagenb" type="text" name="date_format_string" value="<?php echo $date_format_string; ?>" size="20" maxlength="20" /><?php echo lang('date_format_string_help') ?>
		</p>
	    </div>
            <div class="pageoverflow">
				<p class="pagetext"><?php echo lang('admintheme');  ?>:</p>
				<p class="pageinput">
					<?php
						if ($dir=opendir(dirname(__FILE__)."/themes/")) { //Does the themedir exist at all, it should...
								echo '<select name="admintheme">';
									while (($file = readdir($dir)) !== false) {
										if (@is_dir("themes/".$file) && ( $file[0] != '.') &&
										    is_readable("themes/{$file}/{$file}Theme.php")) {
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
				<p class="pagetext"><?php echo lang('hide_help_links'); ?>:</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="hide_help_links" <?php if ($hide_help_links) echo "checked=\"checked\""; ?> /><?php echo lang('hide_help_links_help') ?>
				</p>
			</div>

			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('homepage'); ?>:</p>
				<p class="pageinput">
						  <?php echo $themeObject->GetAdminPageDropdown('homepage',$homepage); ?>
				</p>
			</div>

			<!--
			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('adminpaging'); ?>:</p>
				<p class="pageinput">
					<select name="paging">
					<option value="0"<?php if ($paging == 0) echo " selected";?>><?php echo lang('nopaging');?></option>
					<option value="10"<?php if ($paging == 10) echo " selected";?>>10</option>
					<option value="20"<?php if ($paging == 20) echo " selected";?>>20</option>
					<option value="30"<?php if ($paging == 30) echo " selected";?>>30</option>
					<option value="40"<?php if ($paging == 40) echo " selected";?>>40</option>
					<option value="50"<?php if ($paging == 50) echo " selected";?>>50</option>
					<option value="100"<?php if ($paging == 100) echo " selected";?>>100</option>
					</select>
				</p>
			</div>
			-->
			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('adminindent'); ?>:</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="indent" <?php if ($indent) echo "checked=\"checked\""; ?> /><?php echo lang('indent') ?>
				</p>
			</div>

			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('enablenotifications'); ?>:</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="enablenotifications" <?php if ($enablenotifications) echo "checked=\"checked\""; ?> /></p>
			</div>

			<div class="pageoverflow">
			  <p class="pagetext"><?php echo lang('ignorenotificationsfrommodules'); ?>:</p>
			  <p class="pageinput">
			  <?php
			  $txt = '<select name="ignoredmodules[]" multiple="multiple" size="5">'."\n";
                          foreach( $modules as $key => $value )
                          {
                            $txt .= '<option value="'.$value.'"';
                            if( in_array($value,$ignoredmodules) )
			      {
				$txt .= ' selected="selected"';
			      }
                            $txt .= ">{$key}</option>\n";
                          }
                          $txt .= "</select>\n";
                          echo $txt;
                          ?>
			  </p>
                        </div>
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
