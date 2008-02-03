<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

$smarty = cms_smarty();

$admintheme = 'default';
if (isset($_POST['admintheme'])) $admintheme = $_POST['admintheme'];

$bookmarks = 0;
if (isset($_POST['bookmarks'])) $bookmarks = $_POST['bookmarks'];

$hide_help_links = 0;
if (isset($_POST['hide_help_links'])) $hide_help_links = $_POST['hide_help_links'];

$indent = 0;
if (isset($_POST['indent'])) $indent = $_POST['indent'];

$paging = 0;
if (isset($_POST['paging'])) $paging = $_POST['paging'];


$page_message = '';

$wysiwyg = '';
if (isset($_POST["wysiwyg"])) $wysiwyg = $_POST["wysiwyg"];

$syntaxhighlighter = '';
if (isset($_POST["syntaxhighlighter"])) $syntaxhighlighter = $_POST["syntaxhighlighter"];

$gcb_wysiwyg = 0;
if (isset($_POST['gcb_wysiwyg'])) $gcb_wysiwyg = $_POST['gcb_wysiwyg'];

$date_format_string = '%x %X';
if (isset($_POST['date_format_string'])) $date_format_string = $_POST['date_format_string'];

$userid = get_userid();

if (isset($_POST["cancel"])) {
	redirect("topmyprefs.php");
	return;
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
	set_preference($userid, 'paging', $paging);
	set_preference($userid, 'date_format_string', $date_format_string);
	audit(-1, '', 'Edited User Preferences');
	$page_message = lang('prefsupdated');
	redirect("topmyprefs.php");
	return;
} else if (!isset($_POST["edituserprefs"])) {
	$smarty->assign('gcb_wysiwyg', get_preference($userid, 'gcb_wysiwyg', 1));
	$smarty->assign('wysiwyg', get_preference($userid, 'wysiwyg'));
	$smarty->assign('syntaxhighlighter', get_preference($userid, 'syntaxhighlighter'));
	$smarty->assign('default_cms_lang', get_preference($userid, 'default_cms_language'));
	$smarty->assign('old_default_cms_lang', $default_cms_lang);
	$smarty->assign('admintheme', get_preference($userid, 'admintheme'));
	$smarty->assign('bookmarks', get_preference($userid, 'bookmarks'));
	$smarty->assign('indent', get_preference($userid, 'indent', true));
	$smarty->assign('paging', get_preference($userid, 'paging', 0));
	$smarty->assign('date_format_string', get_preference($userid, 'date_format_string','%x %X'));
	$smarty->assign('hide_help_links', get_preference($userid, 'hide_help_links'));
}

include_once("header.php");

if (FALSE == empty($page_message)) {
	echo $themeObject->ShowMessage($page_message);
}

// Assign the header
$smarty->assign('header_name', $themeObject->ShowHeader('userprefs'));

// Assign the WYSIWYG options
$wysiwyg_options = array();
$wysiwyg_options[] = lang('none');
foreach($gCms->modules as $key=>$value)
{
	if ($gCms->modules[$key]['installed'] == true &&
		$gCms->modules[$key]['active'] == true &&
		$gCms->modules[$key]['object']->IsWYSIWYG())
		{
			$wysiwyg_options[$key] = $key;
		}
}
$smarty->assign('wysiwyg_options', $wysiwyg_options);

// Assign the Syntax Highlighter Options
$syntaxhighlighter_options = array();
$syntaxhighlighter_options[] = lang('none');
foreach($gCms->modules as $key=>$value)
{
	if ($gCms->modules[$key]['installed'] == true &&
		$gCms->modules[$key]['active'] == true &&
		$gCms->modules[$key]['object']->IsSyntaxHighlighter())
	{
		$syntaxhighlighter_options[$key] = $key;
	}
}
$smarty->assign('syntaxhighlighter_options', $syntaxhighlighter_options);

// Assign the language options
$default_cms_lang_options = array();
$default_cms_lang_options[] = lang('nodefault');
$list = CmsLanguage::get_language_list(true);
foreach ($list as $key=>$val) {
	$default_cms_lang_options[$key] = $val;
}
$smarty->assign('default_cms_lang_options', $default_cms_lang_options);

// Assign the admin theme options
$admintheme_options = array();
if ($dir=opendir(dirname(__FILE__)."/themes/")) { //Does the themedir exist at all, it should...
	while (($file = readdir($dir)) !== false) {
		if (@is_dir("themes/".$file) && ( $file[0] != '.')) {
			$admintheme_options[$file] = $file;
		}
	}
}
$smarty->assign('admintheme_options', $admintheme_options);

// Display the template
$smarty->display('editprefs.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
