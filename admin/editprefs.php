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

function pagelimit_dropdown($name,$opts,$selected)
{
  $str = '<select name="'.$name.'">';
  foreach( $opts as $key => $value )
    {
      if( $key == $selected )
	{
	  $str .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
	}
      else
	{
	  $str .= '<option value="'.$key.'">'.$value.'</option>';
	}
    }
  $str .= '</select>';
  return $str;
}

$pagelimit_opts = array(10=>10,20=>20,50=>50,100=>100);

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
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$thisurl=basename(__FILE__).$urlext;

check_login();
$userid = get_userid();
$access = check_permission($userid, 'Modify Site Preferences');


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
$date_format_string = cms_htmlentities(strip_tags($date_format_string));

$listtemplates_pagelimit = '20';
if (isset($_POST['listtemplates_pagelimit'])) $listtemplates_pagelimit = $_POST['listtemplates_pagelimit'];

$liststylesheets_pagelimit = '20';
if (isset($_POST['liststylesheets_pagelimit'])) $liststylesheets_pagelimit = $_POST['liststylesheets_pagelimit'];

$listgcbs_pagelimit = '20';
if (isset($_POST['listgcbs_pagelimit'])) $listgcbs_pagelimit = $_POST['listgcbs_pagelimit'];


$default_parent = '';
if( isset($_POST['parent_id']) )
  {
    $default_parent = $_POST['parent_id'];
  }

$ignoredmodules = array();
if (isset($_POST['ignoredmodules']) )
  {
    $ignoredmodules = $_POST['ignoredmodules'];
    if( in_array('**none**',$ignoredmodules) )
      {
	$ignoredmodules = array();
      }
  }

if (isset($_POST["cancel"])) {
  redirect("index.php?".CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY]);
  return;
}

$modules = array();
//Next 2 lines commented, to NOT show 'none' as a choice in the ignore list.
//$modules[ucwords(lang('none'))] = '**none**';
//$modules['---'] = '**none**';
$gCms = cmsms();
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
	set_preference($userid, 'default_parent', $default_parent);
	set_preference($userid, 'homepage', $homepage );
	set_preference($userid, 'ignoredmodules', implode(',',$ignoredmodules));
	set_preference($userid, 'listtemplates_pagelimit', $listtemplates_pagelimit);
	set_preference($userid, 'liststylesheets_pagelimit', $liststylesheets_pagelimit);
	set_preference($userid, 'listgcbs_pagelimit', $listgcbs_pagelimit);
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
	$default_parent = get_preference($userid,'default_parent',-2);
	$listtemplates_pagelimit = get_preference($userid,'listtemplates_pagelimit',20);
	$liststylesheets_pagelimit = get_preference($userid,'liststylesheets_pagelimit',20);
	$listgcbs_pagelimit = get_preference($userid,'listgcbs_pagelimit',20);

	$homepage = get_preference($userid,'homepage');
	$to = '?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	$pos = strpos($homepage,'?'.CMS_SECURE_PARAM_NAME);
	$from = substr($homepage,$pos,strlen($to));
	$homepage = str_replace($from,$to,$homepage);
	$homepage = str_replace('&','&amp;',$homepage);

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
            <div class="invisible">
	    <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" /></div>
            <div>
			<div class="pageoverflow">
				<div class="pagetext"><?php echo lang('wysiwygtouse'); ?>:</div>
				<div class="pageinput">
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
				</div>
			</div>
			<div class="pageoverflow">
				<div class="pagetext"><?php echo lang('syntaxhighlightertouse'); ?>:</div>
				<div class="pageinput">
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
				</div>
			</div>
			<div class="pageoverflow">
				<div class="pagetext"><?php echo lang('gcb_wysiwyg'); ?>:</div>
				<div class="pageinput">
  <input class="pagenb" type="checkbox" name="gcb_wysiwyg" <?php if ($gcb_wysiwyg) echo "checked=\"checked\""; if( get_site_preference('nogcbwysiwyg') == '1' ) echo "disabled=\"disabled\""; ?> /><?php echo lang('gcb_wysiwyg_help') ?>
				</div>
			</div>
				<div class="pageoverflow">
				<div class="pagetext"><?php echo lang('language'); ?>:</div>
				<div class="pageinput">
					<select name="default_cms_lang" style="vertical-align: middle;">
					<?php
					$opts = get_language_list();
foreach( $opts as $key => $value )
{
  $str = "<option value=\"$key\"";
  if( $default_cms_lang == $key )
    { 
      $str .= " selected=\"selected\"";
    }
  $str .= ">$value</option>\n";
  echo $str;
}
					?>
					</select>
				</div>
			</div>
	    <div class="pageoverflow">
		<div class="pagetext"><?php echo lang('date_format_string'); ?>:</div>
		<div class="pageinput">
		<input class="pagenb" type="text" name="date_format_string" value="<?php echo $date_format_string; ?>" size="20" maxlength="255" /><?php echo '<br/>'.lang('date_format_string_help') ?>
		</div>
	    </div>

	<div class="pageoverflow">
	  <p class="pagetext"><?php echo lang('defaultparentpage')?>:</p>
	  <p class="pageinput">
	  <?php
	    $contentops =& $gCms->GetContentOperations();
echo $contentops->CreateHierarchyDropdown(0, $default_parent, 'parent_id', 0, 1);
	  ?>
	  </p>
	</div>	

            <div class="pageoverflow">
				<div class="pagetext"><?php echo lang('admintheme');  ?>:</div>
				<div class="pageinput">
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
				</div>					
			</div>
			<div class="pageoverflow">
				<div class="pagetext"><?php echo lang('admincallout'); ?>:</div>
				<div class="pageinput">
					<input class="pagenb" type="checkbox" name="bookmarks" <?php if ($bookmarks) echo "checked=\"checked\""; ?> /><?php echo '<br/>'.lang('showbookmarks') ?>
				</div>
			</div>
			<div class="pageoverflow">
				<div class="pagetext"><?php echo lang('hide_help_links'); ?>:</div>
				<div class="pageinput">
					<input class="pagenb" type="checkbox" name="hide_help_links" <?php if ($hide_help_links) echo "checked=\"checked\""; ?> /><?php echo lang('hide_help_links_help') ?>
				</div>
			</div>

			<div class="pageoverflow">
				<div class="pagetext"><?php echo lang('homepage'); ?>:</div>
				<div class="pageinput">
						  <?php echo $themeObject->GetAdminPageDropdown('homepage',$homepage); ?>
				</div>
			</div>

<div class="pageoverflow">
  <div class="pagetext"><?php echo lang('listtemplates_pagelimit'); ?>:</div>
  <div class="pageinput">
    <?php echo pagelimit_dropdown('listtemplates_pagelimit',$pagelimit_opts,$listtemplates_pagelimit); ?>
  </div>
</div>

<div class="pageoverflow">
  <div class="pagetext"><?php echo lang('liststylesheets_pagelimit'); ?>:</div>
  <div class="pageinput">
    <?php echo pagelimit_dropdown('liststylesheets_pagelimit',$pagelimit_opts,$liststylesheets_pagelimit); ?>
  </div>
</div>

<div class="pageoverflow">
  <div class="pagetext"><?php echo lang('listgcbs_pagelimit'); ?>:</div>
  <div class="pageinput">
    <?php echo pagelimit_dropdown('listgcbs_pagelimit',$pagelimit_opts,$listgcbs_pagelimit); ?>
  </div>
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
				<div class="pagetext"><?php echo lang('adminindent'); ?>:</div>
				<div class="pageinput">
					<input class="pagenb" type="checkbox" name="indent" <?php if ($indent) echo "checked=\"checked\""; ?> /><?php echo lang('indent') ?>
				</div>
			</div>
               <?php  if ($access) 
					  {
					  ?>
			<div class="pageoverflow">
				<div class="pagetext"><?php echo lang('enablenotifications'); ?>:</div>
				<div class="pageinput">
					<input class="pagenb" type="checkbox" name="enablenotifications" <?php if ($enablenotifications) echo "checked=\"checked\""; ?> /></div>
			</div>

			<div class="pageoverflow">
			  <div class="pagetext"><?php echo lang('ignorenotificationsfrommodules'); ?>:</div>
			  <div class="pageinput">
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
			  </div>
                        </div> 
                <?php }?>
			<p class="pagetext">&nbsp;</p>
			<div class="pageinput">
            <div class="invisible">
				<input type="hidden" name="edituserprefs" value="true" />
                <input type="hidden" name="old_default_cms_lang" value="<?php echo $old_default_cms_lang; ?>" />
                </div>
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="submit_form" value="<?php echo lang('submit'); ?>" />
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="cancel" value="<?php echo lang('cancel'); ?>" />
			</div>
			</div>			
		</form>
	</div>
</div>	

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
