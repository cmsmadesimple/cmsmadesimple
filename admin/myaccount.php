<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

$orig_memory = (function_exists('memory_get_usage')?memory_get_usage():0);

$CMS_ADMIN_PAGE = 1;
$CMS_TOP_MENU = 'admin';
$CMS_ADMIN_TITLE = 'myaccount';
require_once ("../lib/include.php");
check_login();
$urlext = '?' . CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
$thisurl = basename(__FILE__) . $urlext;
$userid = get_userid(); // Checks also login
if( !check_permission($userid,'Manage My Settings') && !check_permission($userid,'Manage My Account') ) return;

$userobj = UserOperations::get_instance()->LoadUserByID($userid); // <- Safe to do, cause if $userid fails, it redirects automatically to login.
$db = cmsms()->GetDb();
$error = '';
$message = '';


/**
 * Get preferences
 */
$wysiwyg = cms_userprefs::get_for_user($userid, 'wysiwyg');
$ce_navdisplay = cms_userprefs::get_for_user($userid,'ce_navdisplay');
$syntaxhighlighter = cms_userprefs::get_for_user($userid, 'syntaxhighlighter');
$default_cms_language = cms_userprefs::get_for_user($userid, 'default_cms_language');
$old_default_cms_lang = $default_cms_language;
$admintheme = cms_userprefs::get_for_user($userid, 'admintheme', CmsAdminThemeBase::GetDefaultTheme());
$bookmarks = cms_userprefs::get_for_user($userid, 'bookmarks', 0);
$indent = cms_userprefs::get_for_user($userid, 'indent', true);
$paging = cms_userprefs::get_for_user($userid, 'paging', 0);
$date_format_string = cms_userprefs::get_for_user($userid, 'date_format_string', '%x %X');
$default_parent = cms_userprefs::get_for_user($userid, 'default_parent', -2);
$homepage = cms_userprefs::get_for_user($userid, 'homepage');
$hide_help_links = cms_userprefs::get_for_user($userid, 'hide_help_links', 0);

/**
 * Cancel
 */
if (isset($_POST["cancel"])) redirect("index.php" . $urlext);

/**
 * Check tab
 */
$tab='';
if( isset($_POST['active_tab']) ) $tab = trim(cleanValue($_POST['active_tab']));

/**
 * Submit account
 *
 * NOTE: Assumes that we succesfully acquired user object.
 */
if (isset($_POST['submit_account']) && check_permission($userid,'Manage My Account')) {

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
  if (isset($_POST["email"])) $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

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
    \CMSMS\HookManager::do_hook('Core::EditUserPre', [ 'user'=>&$userobj ] );

    if ($password != '') $userobj->SetPassword($password);
    $result = $userobj->Save();

    if($result) {
      // put mention into the admin log
        audit($userid, 'Admin Username: '.$userobj->username, 'Edited');
        \CMSMS\HookManager::do_hook('Core::EditUserPost', [ 'user'=>&$userobj ] );
        $message = lang('accountupdated');
    } else {
        // throw exception? update just failed.
    }
  }
} // end of account submit

/**
 * Submit prefs
 */
if (isset($_POST['submit_prefs']) && check_permission($userid,'Manage My Settings')) {
  // Get values from request and drive em to variables
  $wysiwyg = cleanValue($_POST['wysiwyg']);
  $ce_navdisplay = cleanValue($_POST['ce_navdisplay']);
  $syntaxhighlighter = cleanValue($_POST['syntaxhighlighter']);
  $default_cms_language = '';
  if (isset($_POST['default_cms_language'])) $default_cms_language = cleanValue($_POST['default_cms_language']);
  $old_default_cms_lang = '';
  if (isset($_POST['old_default_cms_lang'])) $old_default_cms_lang = cleanValue($_POST['old_default_cms_lang']);
  $admintheme = cleanValue($_POST['admintheme']);
  $bookmarks = (isset($_POST['bookmarks']) ? 1 : 0);
  $indent = (isset($_POST['indent']) ? true : false);
  $paging = (isset($_POST['paging']) ? 1 : 0);
  $date_format_string = trim(strip_tags(cleanValue($_POST['date_format_string'])));
  $default_parent = '';
  if (isset($_POST['parent_id'])) $default_parent = (int)$_POST['parent_id'];
  $homepage = cleanValue($_POST['homepage']);
  $hide_help_links = (isset($_POST['hide_help_links']) ? 1 : 0);

  // Set prefs
  cms_userprefs::set_for_user($userid, 'wysiwyg', $wysiwyg);
  cms_userprefs::set_for_user($userid, 'ce_navdisplay', $ce_navdisplay);
  cms_userprefs::set_for_user($userid, 'syntaxhighlighter', $syntaxhighlighter);
  cms_userprefs::set_for_user($userid, 'default_cms_language', $default_cms_language);
  cms_userprefs::set_for_user($userid, 'admintheme', $admintheme);
  cms_userprefs::set_for_user($userid, 'bookmarks', $bookmarks);
  cms_userprefs::set_for_user($userid, 'hide_help_links', $hide_help_links);
  cms_userprefs::set_for_user($userid, 'indent', $indent);
  cms_userprefs::set_for_user($userid, 'paging', $paging);
  cms_userprefs::set_for_user($userid, 'date_format_string', strip_tags($date_format_string));
  cms_userprefs::set_for_user($userid, 'default_parent', $default_parent);
  cms_userprefs::set_for_user($userid, 'homepage', $homepage);

  // Audit, message, cleanup
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
$tmp = module_meta::get_instance()->module_list_by_capability(CmsCoreCapabilities::WYSIWYG_MODULE);
$tmp2 = array(-1 => lang('none'));
for ($i = 0; $i < count($tmp); $i++) {
  $tmp2[$tmp[$i]] = $tmp[$i];
}

$smarty -> assign('wysiwyg_opts', $tmp2);

# Syntaxhighlighter editor
$tmp = module_meta::get_instance()->module_list_by_capability(CmsCoreCapabilities::SYNTAX_MODULE);
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
$out = $themeObject->StartTabHeaders();
if( check_permission($userid,'Manage My Account') ) {
  $out .= $themeObject->SetTabHeader('maintab',lang('useraccount'), ('maintab' == $tab)?true:false);
}
if( check_permission($userid,'Manage My Settings') ) {
  $out .= $themeObject->SetTabHeader('advancedtab',lang('userprefs'), ('advtab' == $tab)?true:false);
}
$out .= $themeObject->EndTabHeaders() . $themeObject->StartTabContent();
$smarty->assign('tab_start',$out);

$smarty->assign('tabs_end',$themeObject->EndTabContent());
$smarty->assign('maintab_start',$themeObject->StartTab("maintab"));
$smarty->assign('advancedtab_start',$themeObject->StartTab("advancedtab"));
$smarty->assign('tab_end',$themeObject->EndTab());

# Prefs
$smarty->assign('module_opts', $modules);
$smarty->assign('wysiwyg', $wysiwyg);
$smarty->assign('ce_navdisplay', $ce_navdisplay);
$smarty->assign('syntaxhighlighter', $syntaxhighlighter);
$smarty->assign('language_opts', get_language_list());
$smarty->assign('default_cms_language', $default_cms_language);
$smarty->assign('old_default_cms_lang', $old_default_cms_lang);
$smarty->assign('bookmarks', $bookmarks);
$smarty->assign('admintheme', $admintheme);
$smarty->assign('hide_help_links', $hide_help_links);
$smarty->assign('indent', $indent);
$smarty->assign('paging', $paging);
$smarty->assign('date_format_string', $date_format_string);
$smarty->assign('default_parent', $contentops->CreateHierarchyDropdown(0, $default_parent, 'parent_id', 0, 1));
$smarty->assign('homepage', $themeObject->GetAdminPageDropdown('homepage', $homepage, 'homepage'));
$tmp = array(10 => 10, 20 => 20, 50 => 50, 100 => 100);
$smarty->assign('pagelimit_opts', $tmp);
$smarty->assign('backurl', $themeObject -> backUrl());
$smarty->assign('formurl', $thisurl);
$smarty->assign('userobj', $userobj);
$smarty->assign('manageaccount',check_permission($userid,'Manage My Account'));
$smarty->assign('managesettings',check_permission($userid,'Manage My Settings'));

# Output
$smarty->display('myaccount.tpl');
include_once ("footer.php");

?>
