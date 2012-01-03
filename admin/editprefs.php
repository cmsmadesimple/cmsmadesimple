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

$CMS_ADMIN_PAGE = 1;
$CMS_TOP_MENU = 'admin';
$CMS_ADMIN_TITLE = 'editprefs';
require_once ("../include.php");
$urlext = '?' . CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
$thisurl = basename(__FILE__) . $urlext;

check_login();
$userid = get_userid();
/* let editor edit suser ettings
$access = check_permission($userid, 'Modify Site Preferences');
if (!$access) {
	die('Permission Denied');
	return;
}
*/

$gCms = cmsms();
$db = $gCms -> GetDb();

$error = '';
$message = '';

$allmodules = ModuleOperations::get_instance() -> GetInstalledModules();
$modules = array();
foreach ($allmodules as $key) {
	$obj = ModuleOperations::get_instance() -> get_module_instance($key);
	if (!$obj)
		continue;
	$modules[$obj -> GetFriendlyName()] = $obj -> GetName();
}
$ignoredmodules = explode(',', get_preference($userid, 'ignoredmodules'));

/**
 * get preferences
 */
$gcb_wysiwyg = get_preference($userid, 'gcb_wysiwyg', 1);
$wysiwyg = get_preference($userid, 'wysiwyg');
$syntaxhighlighter = get_preference($userid, 'syntaxhighlighter');
$default_cms_language = get_preference($userid, 'default_cms_language');
$old_default_cms_lang = $default_cms_language;
$admintheme = get_preference($userid, 'admintheme', CmsAdminThemeBase::GetDefaultTheme());
$bookmarks = get_preference($userid, 'bookmarks', 0);
$indent = get_preference($userid, 'indent', true);
$enablenotifications = get_preference($userid, 'enablenotifications', 1);
$paging = get_preference($userid, 'paging', 0);
$date_format_string = get_preference($userid, 'date_format_string', '%x %X');
$default_parent = get_preference($userid, 'default_parent', -2);
$listtemplates_pagelimit = get_preference($userid, 'listtemplates_pagelimit', 20);
$liststylesheets_pagelimit = get_preference($userid, 'liststylesheets_pagelimit', 20);
$listgcbs_pagelimit = get_preference($userid, 'listgcbs_pagelimit', 20);
$homepage = get_preference($userid, 'homepage');
$to = '?' . CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
$pos = strpos($homepage, '?' . CMS_SECURE_PARAM_NAME);
$from = substr($homepage, $pos, strlen($to));
$homepage = str_replace($from, $to, $homepage);
$homepage = str_replace('&', '&amp;', $homepage);
$hide_help_links = get_preference($userid, 'hide_help_links', 0);

$message = '';
$gCms -> clear_cached_files();

if (isset($_POST["cancel"])) {
	redirect("index.php" . $urlext);
	return;
}

/**
 * submit form
 */
if (isset($_POST['submit_form'])) {
	//debug_display($_POST);
	/**
	 * proces user prefs
	 */
	$gcb_wysiwyg = (isset($_POST['gcb_wysiwyg']) ? 1 : 0);
	$wysiwyg = $_POST['wysiwyg'];
	$syntaxhighlighter = $_POST['syntaxhighlighter'];
	$default_cms_language = '';
	if (isset($_POST['default_cms_language'])) {
		$default_cms_language = $_POST['default_cms_language'];
	}
	$old_default_cms_lang = '';
	if (isset($_POST['old_default_cms_lang'])) {
		$old_default_cms_lang = $_POST['old_default_cms_lang'];
	}
	$admintheme = $_POST['admintheme'];
	$bookmarks = (isset($_POST['bookmarks']) ? 1 : 0);
	$indent = (isset($_POST['indent']) ? true : false);
	$enablenotifications = (isset($_POST['enablenotifications']) ? 1 : 0);
	$paging = (isset($_POST['paging']) ? 1 : 0);
	$date_format_string = $_POST['date_format_string'];
	$default_parent = '';
	if (isset($_POST['parent_id'])) {
		$default_parent = $_POST['parent_id'];
	}
	$listtemplates_pagelimit = '20';
	if (isset($_POST['listtemplates_pagelimit'])) {
		$listtemplates_pagelimit = $_POST['listtemplates_pagelimit'];
	}
	$liststylesheets_pagelimit = '20';
	if (isset($_POST['liststylesheets_pagelimit'])) {
		$liststylesheets_pagelimit = $_POST['liststylesheets_pagelimit'];
	}
	$listgcbs_pagelimit = '20';
	if (isset($_POST['listgcbs_pagelimit'])) {
		$listgcbs_pagelimit = $_POST['listgcbs_pagelimit'];
	}
	$homepage = $_POST['homepage'];
	$hide_help_links = (isset($_POST['hide_help_links']) ? 1 : 0);
	$ignoredmodules = array();
	if (isset($_POST['ignoredmodules'])) {
		$ignoredmodules = $_POST['ignoredmodules'];
		if (in_array('**none**', $ignoredmodules)) {
			$ignoredmodules = array();
		}
	}

	/**
	 * set user prefs
	 */
	set_preference($userid, 'gcb_wysiwyg', $gcb_wysiwyg);
	set_preference($userid, 'wysiwyg', $wysiwyg);
	set_preference($userid, 'syntaxhighlighter', $syntaxhighlighter);
	set_preference($userid, 'default_cms_language', $default_cms_language);
	set_preference($userid, 'admintheme', $admintheme);
	set_preference($userid, 'bookmarks', $bookmarks);
	set_preference($userid, 'hide_help_links', $hide_help_links);
	set_preference($userid, 'indent', $indent);
	set_preference($userid, 'enablenotifications', $enablenotifications);
	set_preference($userid, 'paging', $paging);
	set_preference($userid, 'date_format_string', $date_format_string);
	set_preference($userid, 'default_parent', $default_parent);
	set_preference($userid, 'homepage', $homepage);
	set_preference($userid, 'listtemplates_pagelimit', $listtemplates_pagelimit);
	set_preference($userid, 'liststylesheets_pagelimit', $liststylesheets_pagelimit);
	set_preference($userid, 'listgcbs_pagelimit', $listgcbs_pagelimit);
	set_preference($userid, 'ignoredmodules', implode(',', $ignoredmodules));
	// put mention into the admin log
	audit($userid, 'Admin User Preferences', 'Edited');
	$message = lang('prefsupdated');
	$gCms -> clear_cached_files();
}

include_once ("header.php");
if ($error != "") {
	echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">" . $error . "</ul></div>";
}
if ($message != "") {
	echo $themeObject -> ShowMessage($message);
}

/**
 *  give everything to smarty.
 */
$smarty = cmsms() -> GetSmarty();
$contentops = cmsms() -> GetContentOperations();
$smarty -> assign('SECURE_PARAM_NAME', CMS_SECURE_PARAM_NAME);
$smarty -> assign('CMS_USER_KEY', $_SESSION[CMS_USER_KEY]);

/**
 * wysiwyg editor
 */
{
	$tmp = module_meta::get_instance() -> module_list_by_method('IsWYSIWYG');
	$tmp2 = array(-1 => lang('none'));
	for ($i = 0; $i < count($tmp); $i++) {
		$tmp2[$tmp[$i]] = $tmp[$i];
	}
	$smarty -> assign('wysiwyg_opts', $tmp2);
}
/**
 * syntaxhighlighter editor
 */
{
	$tmp = module_meta::get_instance() -> module_list_by_method('IsSyntaxHighlighter');
	$tmp2 = array(-1 => lang('none'));
	for ($i = 0; $i < count($tmp); $i++) {
		$tmp2[$tmp[$i]] = $tmp[$i];
	}
	$smarty -> assign('syntax_opts', $tmp2);
}
/**
 * admin themes
 */
$smarty->assign('themes_opts',CmsAdminThemeBase::GetAvailableThemes());

/**
 * get modules
 */
{
	$modules = ModuleOperations::get_instance() -> GetInstalledModules();
	if (is_array($modules) && count($modules)) {
		$tmp = array();
		$tmp['-1'] = lang('none');
		for ($i = 0; $i < count($modules); $i++) {
			$tmp[$modules[$i]] = $modules[$i];
		}
		$smarty -> assign('module_opts', $tmp);
	}
}

$smarty -> assign('gcb_wysiwyg', $gcb_wysiwyg);
$smarty -> assign('wysiwyg', $wysiwyg);
$smarty -> assign('syntaxhighlighter', $syntaxhighlighter);
$smarty -> assign('language_opts', get_language_list());
$smarty -> assign('default_cms_language', $default_cms_language);
$smarty -> assign('old_default_cms_lang', $old_default_cms_lang);
$smarty -> assign('bookmarks', $bookmarks);
$smarty -> assign('admintheme', $admintheme);
$smarty -> assign('hide_help_links', $hide_help_links);
$smarty -> assign('indent', $indent);
$smarty -> assign('enablenotifications', $enablenotifications);
$smarty -> assign('paging', $paging);
$smarty -> assign('date_format_string', $date_format_string);
$smarty -> assign('default_parent', $contentops -> CreateHierarchyDropdown(0, $default_parent, 'parent_id', 0, 1));
$smarty -> assign('homepage', $themeObject -> GetAdminPageDropdown('homepage', $homepage));
$tmp = array(10 => 10, 20 => 20, 50 => 50, 100 => 100);
$smarty -> assign('pagelimit_opts', $tmp);
$smarty -> assign('listtemplates_pagelimit', $listtemplates_pagelimit);
$smarty -> assign('liststylesheets_pagelimit', $liststylesheets_pagelimit);
$smarty -> assign('listgcbs_pagelimit', $listgcbs_pagelimit);
$smarty -> assign('ignoredmodules', $ignoredmodules);
$smarty -> assign('header', $themeObject -> showHeader('userprefs'));
$smarty -> assign('backurl', $themeObject -> backUrl());

/**
 * output
 */
echo $smarty -> display('editprefs.tpl');
include_once ("footer.php");
# vim:ts=4 sw=4 noet
?>