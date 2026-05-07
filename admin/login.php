<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net) and (c) 2016 by Robert Campbell (calguy1000@cmsmadesimple.org)
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
#$Id: login.php 13234 2025-07-25 17:00:19Z JoMorg $

namespace CMSMS;

$CMS_ADMIN_PAGE=1;
$CMS_LOGIN_PAGE=1;

require_once("../lib/include.php");
$gCms = \CmsApp::get_instance();
$db = $gCms->GetDb();

// if we allow modules to do the login operations
// module registers itself as 'admin login module' in the constructor
// getloginModule
// call the module's getLoginForm() action
//
\CMSMS\HookManager::do_hook('Core::LoginAttempted', []);

$login_ops = \CMSMS\LoginOperations::get_instance();

$error = "";
$forgotmessage = "";
$changepwhash = "";
$force_password_change = false;

/**
 * A function to send lost password recovery email to a specified admin user (by name)
 *
 * @internal
 * @access private
 * @param string the username
 * @return results from the attempt to send a message.
 */
function send_recovery_email(\User $user)
{
    $gCms = \CmsApp::get_instance();
    $config = $gCms->GetConfig();
    $userops = $gCms->GetUserOperations();

    $obj = new \cms_mailer;
    $obj->IsHTML(TRUE);
    $obj->AddAddress($user->email, html_entity_decode($user->firstname . ' ' . $user->lastname));
    $obj->SetSubject(lang('lostpwemailsubject',html_entity_decode(get_site_preference('sitename','CMSMS Site'))));

    $code = md5(md5(__FILE__ . '--' . $user->username . md5($user->password.time())));
    \cms_userprefs::set_for_user( $user->id, 'pwreset', $code );
    $url = $config['admin_url'] . '/login.php?recoverme=' . $code;
    $body = lang('lostpwemail',html_entity_decode(get_site_preference('sitename','CMSMS Site')), $user->username, $url, $url);

    $obj->SetBody($body);

    audit('','Core','Sent Lost Password Email for '.$user->username);
    return $obj->Send();
}

/**
 * A function find a matching user id given an identity hash
 *
 * @internal
 * @access private
 * @param string the hash
 * @return object The matching user object if found, or null otherwise.
 */
function find_recovery_user($hash)
{
    $gCms = \CmsApp::get_instance();
    $config = $gCms->GetConfig();
    $userops = $gCms->GetUserOperations();

    foreach ($userops->LoadUsers() as $user) {
        $code = \cms_userprefs::get_for_user( $user->id, 'pwreset' );
        if( $code && $hash && $hash === $code ) return $user;
    }

    return null;
}

//Redirect to the normal login screen if we hit cancel on the forgot pw one
//Otherwise, see if we have a forgotpw hit
if ((isset($_REQUEST['forgotpwform']) || isset($_REQUEST['forgotpwchangeform'])) && isset($_REQUEST['logincancel'])) {
    redirect('login.php');
}
else if (isset($_REQUEST['forgotpwform']) && isset($_REQUEST['forgottenusername'])) {
    $userops = $gCms->GetUserOperations();
    $forgot_username = cms_html_entity_decode($_REQUEST['forgottenusername']);
    unset($_REQUEST['forgottenusername'],$_POST['forgottenusername']);
    \CMSMS\HookManager::do_hook('Core::LostPassword', [ 'username'=>$forgot_username] );
    $oneuser = $userops->LoadUserByUsername($forgot_username);
    unset($_REQUEST['loginsubmit'],$_POST['loginsubmit']);

    if ($oneuser != null) {
        if ($oneuser->email == '') {
            $error = lang('nopasswordforrecovery');
        }
        else if (send_recovery_email($oneuser)) {
            $warningLogin = lang('recoveryemailsent');
        }
        else {
            $error = lang('errorsendingemail');
        }
    }
    else {
        unset($_POST['username'],$_POST['password'],$_REQUEST['username'],$_REQUEST['password']);
        \CMSMS\HookManager::do_hook('Core::LoginFailed', [ 'user'=>$forgot_username ] );
        $error = lang('usernotfound');
    }
}
else if (isset($_REQUEST['recoverme']) && $_REQUEST['recoverme']) {
    $user = find_recovery_user($_REQUEST['recoverme']);
    if ($user == null) {
        $error = lang('usernotfound');
    }
    else {
        $changepwhash = $_REQUEST['recoverme'];
    }
}
else if (isset($_REQUEST['forgotpwchangeform']) && $_REQUEST['forgotpwchangeform']) {
    $user = find_recovery_user($_REQUEST['changepwhash']);
    if ($user == null) {
        $error = lang('usernotfound');
    }
    else {
        if ($_REQUEST['password'] != '') {
            if ($_REQUEST['password'] == $_REQUEST['passwordagain']) {
                $user->SetPassword($_REQUEST['password']);
                $user->Save();
                // put mention into the admin log
                \cms_userprefs::remove_for_user( $user->id, 'pwreset' );
                $ip_passw_recovery = \cms_utils::get_real_ip();
                audit('','Core','Completed lost password recovery for: '.$user->username.' (IP: '.$ip_passw_recovery.')');
                \CMSMS\HookManager::do_hook('Core::LostPasswordReset', [ 'uid'=>$user->id, 'username'=>$user->username, 'ip'=>$ip_passw_recovery ] );
                $acceptLogin = lang('passwordchangedlogin');
                $changepwhash = '';
            }
            else {
                $error = lang('nopasswordmatch');
                $changepwhash = $_REQUEST['changepwhash'];
            }
        }
        else {
            $error = lang('nofieldgiven', array(lang('password')));
            $changepwhash = $_REQUEST['changepwhash'];
        }
    }
}
else if (isset($_REQUEST['forcepwchangeform']) && $_REQUEST['forcepwchangeform']) {
    $uid = isset($_SESSION['cms_pending_auth_userid']) ? (int) $_SESSION['cms_pending_auth_userid'] : 0;
    $userops = $gCms->GetUserOperations();
    if ($uid < 1) {
        $error = lang('usernameincorrect');
    } else {
        $u = $userops->LoadUserByID($uid);
        if (!$u) {
            $error = lang('usernotfound');
        } else {
            $pending_still_valid = isset($_SESSION['cms_pending_auth_time']) && $_SESSION['cms_pending_auth_time'] >= (time() - 300);
            $p1 = isset($_REQUEST['password']) ? (string) $_REQUEST['password'] : '';
            $p2 = isset($_REQUEST['passwordagain']) ? (string) $_REQUEST['passwordagain'] : '';
            unset($_REQUEST['password'], $_POST['password'], $_REQUEST['passwordagain'], $_POST['passwordagain']);
            if (!$userops->RequiresPasswordReset($uid)) {
                try {
                    $login_ops->finalize_authentication($u);
                    audit($u->id, 'Admin Username: ' . $u->username, 'Logged In');
                    if (isset($_SESSION['login_redirect_to'])) {
                        $url_ob = new \cms_url($_SESSION['login_redirect_to']);
                        unset($_SESSION['login_redirect_to']);
                        $url_ob->erase_queryvar('_s_');
                        $url_ob->erase_queryvar('sp_');
                        $url_ob->set_queryvar(CMS_SECURE_PARAM_NAME, $_SESSION[CMS_USER_KEY]);
                        redirect((string) $url_ob);
                    } else {
                        $homepage = \cms_userprefs::get_for_user($u->id, 'homepage');
                        if (!$homepage) {
                            $homepage = $config['admin_url'];
                        }
                        $homepage = \CmsAdminUtils::get_session_url($homepage);
                        redirect(html_entity_decode($homepage));
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    $login_ops->deauthenticate();
                }
            } elseif ($p1 === '') {
                $error = lang('nofieldgiven', array(lang('password')));
            } elseif ($p1 !== $p2) {
                $error = lang('nopasswordmatch');
            } else {
                try {
                    $u->SetPassword($p1);
                    $u->force_password_reset = 0;
                    $u->force_password_reset_reason = '';
                    if (!$u->Save()) {
                        throw new \RuntimeException(lang('errorupdatinguser'));
                    }
                    $login_ops->finalize_authentication($u);
                    audit($u->id, 'Admin Username: ' . $u->username, 'Logged In');
                    if (isset($_SESSION['login_redirect_to'])) {
                        $url_ob = new \cms_url($_SESSION['login_redirect_to']);
                        unset($_SESSION['login_redirect_to']);
                        $url_ob->erase_queryvar('_s_');
                        $url_ob->erase_queryvar('sp_');
                        $url_ob->set_queryvar(CMS_SECURE_PARAM_NAME, $_SESSION[CMS_USER_KEY]);
                        redirect((string) $url_ob);
                    } else {
                        $homepage = \cms_userprefs::get_for_user($u->id, 'homepage');
                        if (!$homepage) {
                            $homepage = $config['admin_url'];
                        }
                        $homepage = \CmsAdminUtils::get_session_url($homepage);
                        redirect(html_entity_decode($homepage));
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    $login_ops->deauthenticate();
                }
            }
            if (!empty($error) && !empty($pending_still_valid) && $userops->RequiresPasswordReset($uid)) {
                $force_password_change = true;
            }
        }
    }
}

if (isset($_SESSION['logout_user_now'])) {
    // this does the actual logout stuff.
    unset($_SESSION['logout_user_now']);
    debug_buffer("Logging out.  Cleaning cookies and session variables.");
    $userid = $login_ops->get_loggedin_uid();
    $username = $login_ops->get_loggedin_username();
    \CMSMS\HookManager::do_hook('Core::LogoutPre', [ 'uid'=>$userid, 'username'=>$username ] );
    $login_ops->deauthenticate(); // unset all the cruft needed to make sure we're logged in.
    \CMSMS\HookManager::do_hook('Core::LogoutPost', [ 'uid'=>$userid, 'username'=>$username ] );
    audit($userid, "Admin Username: ".$username, 'Logged Out');
}

if( isset($_POST['logincancel']) ) {
    if (isset($_POST['forcepwchangeform'])) {
        $login_ops->deauthenticate();
        redirect($config['admin_url'] . '/login.php');
    }
    debug_buffer("Login cancelled.  Returning to content.");
    redirect($config["root_url"].'/index.php', true);
}
else if( isset($_POST['loginsubmit']) ) {
    // login form submitted
    $login_ops->deauthenticate();
    $username = $password = null;
    if (isset($_POST["username"])) $username = cleanValue($_POST["username"]);
    if (isset($_POST["password"])) $password = $_POST["password"];
    unset($_POST['username'],$_POST['password'],$_REQUEST['username'],$_REQUEST['password']);

    $userops = $gCms->GetUserOperations();

    class CmsLoginError extends \CmsException {}

    try {
        if( !$username || !$password ) throw new \LogicException(lang('usernameincorrect'));

        // load user by name
        // do hooks for authentication
        $oneuser = $userops->LoadUserByUsername($username, $password, TRUE, TRUE);
        // $oneuser = $userops->LoadUserByUsername($username, $password, TRUE, TRUE);
        if( !$oneuser ) throw new CmsLoginError(lang('usernameincorrect'));
        \CMSMS\HookManager::do_hook('Core::LoginPre', [ 'user'=>$oneuser  ] );

        $login_ops->initialize_authentication($oneuser);
        \CMSMS\HookManager::do_hook('Core::LoginVerified', ['user' => $oneuser]);

        if (!isset($_SESSION['cms_pending_auth_userid'])) {
            return;
        }

        if ($userops->RequiresPasswordReset($oneuser->id)) {
            $force_password_change = true;
        } else {
            // default fallback
            $login_ops->finalize_authentication($oneuser);

            // put mention into the admin log
            audit($oneuser->id, "Admin Username: ".$oneuser->username, 'Logged In');

            // redirect outa hre somewhere
            if( isset($_SESSION['login_redirect_to']) ) {
                // we previously attempted a URL but didn't have the user key in the request.
                $url_ob = new \cms_url($_SESSION['login_redirect_to']);
                unset($_SESSION['login_redirect_to']);
                $url_ob->erase_queryvar('_s_');
                $url_ob->erase_queryvar('sp_');
                $url_ob->set_queryvar(CMS_SECURE_PARAM_NAME,$_SESSION[CMS_USER_KEY]);
                $url = (string) $url_ob;
                redirect($url);
            } else {
                // find the users homepage, if any, and redirect there.
                $homepage = \cms_userprefs::get_for_user($oneuser->id,'homepage');
                if( !$homepage ) $homepage = $config['admin_url'];

                $homepage = \CmsAdminUtils::get_session_url($homepage);

                // and redirect.
                $homepage = html_entity_decode($homepage);
                redirect($homepage);
            }
        }
    }
    catch( \Exception $e ) {
        $error = $e->GetMessage();
        debug_buffer("Login failed.  Error is: " . $error);
        \CMSMS\HookManager::do_hook('Core::LoginFailed', [ 'user'=>$username ] );
        // put mention into the admin log
        $ip_login_failed = \cms_utils::get_real_ip();
        audit('', '(IP: ' . $ip_login_failed . ') ' . "Admin Username: " . $username, 'Login Failed');
    }
}

//
// display the login form
//

// Language shizzle
cms_admin_sendheaders();
header("Content-Language: " . \CmsNlsOperations::get_current_language());

$themeObject = \cms_utils::get_theme_object();
$vars = array('error'=>$error);
if( isset($warningLogin) ) $vars['warningLogin'] = $warningLogin;
if( isset($acceptLogin) ) $vars['acceptLogin'] = $acceptLogin;
if( isset($changepwhash) ) $vars['changepwhash'] = $changepwhash;
if( !empty($force_password_change) ) $vars['force_password_change'] = true;
$themeObject->do_login($vars);
