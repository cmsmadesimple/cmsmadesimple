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
#$Id: editprefs.php 7685 2012-01-22 21:52:55Z calguy1000 $

/**
 * Init variables / objects
 */

$CMS_ADMIN_PAGE = 1;
$CMS_TOP_MENU = 'admin';
$CMS_ADMIN_TITLE = 'myaccount';
require_once ("../include.php");
$urlext = '?' . CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
$thisurl = basename(__FILE__) . $urlext;
$userid = get_userid(); // Checks also login
$userobj = UserOperations::get_instance()->LoadUserByID($userid); // <- Safe to do, cause if $userid fails, it redirects automatically to login.
$db = cmsms()->GetDb();
$error = '';
$message = '';

/**
 * Get preferences
 */
$ignoredmodules = explode(',', get_preference($userid, 'ignoredmodules'));
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
if( strpos($homepage,'&') !== FALSE && strpos($homepage,'&amp;') === FALSE ) {
  $homepage = str_replace('&','&amp;',$homepage);
}
$to = CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
$pos = strpos($homepage, CMS_SECURE_PARAM_NAME.'=');
if( $pos !== FALSE )  {
  $from = substr($homepage, $pos, strlen($to));
  $homepage = str_replace($from, $to, $homepage);
}
$hide_help_links = get_preference($userid, 'hide_help_links', 0);


/**
 * Cancel
 */

if (isset($_POST["cancel"])) {

	redirect("index.php" . $urlext);
	return;
}

/**
 * Check tab
 */
$tab='';
if( isset($_POST['active_tab']) ) {

    $tab = trim($_POST['active_tab']);
}

/**
 * Submit account
 *
 * NOTE: Assumes that we succesfully acquired user object.
 */
 if (isset($_POST['submit_account'])) {

	// Collect params
 
	$username = '';
	if (isset($_POST["user"])) $username = cleanValue($_POST["user"]);

	$password = '';
	if (isset($_POST["password"])) $password = $_POST["password"];

	$passwordagain = '';
	if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];

	$firstname = '';
	if (isset($_POST["firstname"])) $firstname = cleanValue($_POST["firstname"]);

	$lastname = '';
	if (isset($_POST["lastname"])) $lastname = cleanValue($_POST["lastname"]);

	$email = '';
	if (isset($_POST["email"])) $email = trim($_POST["email"]);	
	
	// Do validations
	
	$validinfo = true;
	
	if ($username == "") {
	
		$validinfo = false;
		$error = lang('nofieldgiven', array(lang('username')));
	}

	else if ( !preg_match("/^[a-zA-Z0-9\._ ]+$/", $username) ) {
	
		$validinfo = false;
		$error = lang('illegalcharacters', array(lang('username')));
	} 

	else if ($password != $passwordagain) {
	
		$validinfo = false;
		$error = lang('nopasswordmatch');
	}

	else if (!empty($email) && !is_email($email)) {
	
		$validinfo = false;
		$error = lang('invalidemail').': '.$email;
	}
 
	// If success do action
 
	if($validinfo) {
 
		$userobj->username = $username;
		$userobj->firstname = $firstname;
		$userobj->lastname = $lastname;
		$userobj->email = $email;
		
		if ($password != '') {
		
			$userobj->SetPassword($password);
		}
		
		Events::SendEvent('Core', 'EditUserPre', array('user' => &$userobj));

		$result = $userobj->Save();
		
		if($result) {
		
			// put mention into the admin log
			audit($userid, 'Admin Username: '.$userobj->username, 'Edited');
			Events::SendEvent('Core', 'EditUserPost', array('user' => &$userobj));	
			$message = lang('accountupdated');			
			
		} else {
		
			// throw exception? update just failed.
		}
	}
	
} // end of account submit
 
/**
 * Submit prefs
 */ 
if (isset($_POST['submit_prefs'])) {
	
	# Get values from request and drive em to variables
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

	# Set prefs
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

	# Audit, message, cleanup
	audit($userid, 'Admin Username: '.$userobj->username, 'Edited');
	$message = lang('prefsupdated');
	cmsms()->clear_cached_files();

} // end of prefs submit

/**
 * Build page
 */

include_once ("header.php");

if ($error != "") {
	
	$themeObject->ShowErrors($error);
}
if ($message != "") {
	
	$themeObject->ShowMessage($message);
}

$smarty = cmsms()->GetSmarty();
$contentops = cmsms()->GetContentOperations();
$smarty->assign('SECURE_PARAM_NAME', CMS_SECURE_PARAM_NAME); // Assigned at include.php?
$smarty->assign('CMS_USER_KEY', $_SESSION[CMS_USER_KEY]); // Assigned at include.php?

# WYSIWYG editor
$tmp = module_meta::get_instance()->module_list_by_method('IsWYSIWYG');
$tmp2 = array(-1 => lang('none'));
for ($i = 0; $i < count($tmp); $i++) {
	$tmp2[$tmp[$i]] = $tmp[$i];
}

$smarty -> assign('wysiwyg_opts', $tmp2);

# Syntaxhighlighter editor
$tmp = module_meta::get_instance()->module_list_by_method('IsSyntaxHighlighter');
$tmp2 = array(-1 => lang('none'));
for ($i = 0; $i < count($tmp); $i++) {
	$tmp2[$tmp[$i]] = $tmp[$i];
}

$smarty->assign('syntax_opts', $tmp2);

# Admin themes
$smarty->assign('themes_opts',CmsAdminThemeBase::GetAvailableThemes());

# Modules
$allmodules = ModuleOperations::get_instance()->GetInstalledModules();
$modules = array();
foreach ((array)$allmodules as $onemodule) {

	$modules[$onemodule] = $onemodule;
}

#Tabs
$smarty->assign('tab_start',$themeObject->StartTabHeaders().
							$themeObject->SetTabHeader('maintab',lang('useraccount'), ('maintab' == $tab)?true:false).
							$themeObject->SetTabHeader('advancedtab',lang('userprefs'), ('advtab' == $tab)?true:false).
							$themeObject->EndTabHeaders() . 
							$themeObject->StartTabContent());
$smarty->assign('tabs_end',$themeObject->EndTabContent());
$smarty->assign('maintab_start',$themeObject->StartTab("maintab"));
$smarty->assign('advancedtab_start',$themeObject->StartTab("advancedtab"));
$smarty->assign('tab_end',$themeObject->EndTab());

# Prefs
$smarty->assign('module_opts', $modules);
$smarty->assign('gcb_wysiwyg', $gcb_wysiwyg);
$smarty->assign('wysiwyg', $wysiwyg);
$smarty->assign('syntaxhighlighter', $syntaxhighlighter);
$smarty->assign('language_opts', get_language_list());
$smarty->assign('default_cms_language', $default_cms_language);
$smarty->assign('old_default_cms_lang', $old_default_cms_lang);
$smarty->assign('bookmarks', $bookmarks);
$smarty->assign('admintheme', $admintheme);
$smarty->assign('hide_help_links', $hide_help_links);
$smarty->assign('indent', $indent);
$smarty->assign('enablenotifications', $enablenotifications);
$smarty->assign('paging', $paging);
$smarty->assign('date_format_string', $date_format_string);
$smarty->assign('default_parent', $contentops->CreateHierarchyDropdown(0, $default_parent, 'parent_id', 0, 1));
$smarty->assign('homepage', $themeObject->GetAdminPageDropdown('homepage', $homepage));
$tmp = array(10 => 10, 20 => 20, 50 => 50, 100 => 100);
$smarty->assign('pagelimit_opts', $tmp);
$smarty->assign('listtemplates_pagelimit', $listtemplates_pagelimit);
$smarty->assign('liststylesheets_pagelimit', $liststylesheets_pagelimit);
$smarty->assign('listgcbs_pagelimit', $listgcbs_pagelimit);
$smarty->assign('ignoredmodules', $ignoredmodules);
//$smarty->assign('header', $themeObject -> showHeader('userprefs')); // <- Totally useless as far i can see -Stikki-
$smarty->assign('backurl', $themeObject -> backUrl());
$smarty->assign('formurl', $thisurl);
$smarty->assign('userobj', $userobj);

# Output
$smarty->display('myaccount.tpl');
include_once ("footer.php");
# vim:ts=4 sw=4 noet
?>