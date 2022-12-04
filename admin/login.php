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
#$Id$

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
$login_ops = \CMSMS\LoginOperations::get_instance();

$error = "";
$forgotmessage = "";
$changepwhash = "";

/**
 * Send a lost password recovery email to a specified admin user (by name)
 * @internal
 *
 * @param User $user
 * @return results from the attempt to send a message.
 */
function send_recovery_email(\User $user)
{
    $gCms = \CmsApp::get_instance();
    $config = $gCms->GetConfig();

    $obj = new \cms_mailer;
    $obj->IsHTML(TRUE);
    $obj->AddAddress($user->email, html_entity_decode($user->firstname . ' ' . $user->lastname));
    $obj->SetSubject(lang('lostpwemailsubject',html_entity_decode(get_site_preference('sitename','CMSMS Site'))));

    $code = hash('tiger128,3', uniqid(__FILE__, true));
    \cms_userprefs::set_for_user( $user->id, 'pwreset', $code );
    $url = $config['admin_url'] . '/login.php?recoverme=' . $code;
    $body = lang('lostpwemail',html_entity_decode(get_site_preference('sitename','CMSMS Site')), $user->username, $url, $url);
    $obj->SetBody($body);

    if( $obj->Send() ) {
        audit('','Core','Sent Lost Password Email for '.$user->username);
        return true;
    }
    audit('','Core','Failed to send Lost Password Email for '.$user->username);
    return false;
}

/**
 * Find a user matching the given recovery hash
 * @internal
 *
 * @param string the hash
 * @return mixed The matching User object if found, or null otherwise.
 */
function find_recovery_user($hash)
{
    if( $hash ) {
        $gCms = \CmsApp::get_instance();
        $userops = $gCms->GetUserOperations();

        foreach( $userops->LoadUsers() as $user ) {
            $code = \cms_userprefs::get_for_user( $user->id, 'pwreset' );
            if( $code && $hash === $code ) { //OR hash_equals($hash, $code) PHP 5.6+
                return $user;
            }
        }
    }
    return null;
}

//redirect to the normal login screen if we hit cancel on the forgot pw one
if( isset($_REQUEST['logincancel']) && (isset($_REQUEST['forgotpwform']) || isset($_REQUEST['forgotpwchangeform'])) ) {
    redirect('login.php');
}
//otherwise, check for a forgot password request
else if( isset($_REQUEST['forgotpwform']) && isset($_REQUEST['forgottenusername']) ) {
    $userops = $gCms->GetUserOperations();
    $forgot_username = cms_html_entity_decode($_REQUEST['forgottenusername']);
    unset($_REQUEST['forgottenusername'],$_POST['forgottenusername']);
    \CMSMS\HookManager::do_hook('Core::LostPassword', [ 'username'=>$forgot_username] );
    $user = $userops->LoadUserByUsername($forgot_username);
    unset($_REQUEST['loginsubmit'],$_POST['loginsubmit']);

    $key = CMS_SECURE_PARAM_NAME . 'RECOVER' . md5($_SERVER['REMOTE_ADDR']); // no need for security here
    $num = (!empty($_SESSION[$key])) ? $_SESSION[$key]['times'] : 0;
    if( $user ) {
        //only display extra advice if this is not an attacker who has guessed a username
        // e.g. no prior recovery-request from same ip?
        if( $user->email == '' ) {
            if( $num < 1 ) {
                $error = lang('nopasswordforrecovery');
            }
        }
        else if( send_recovery_email($user) ) {
            if( $num < 1 ) {
                $warningLogin = lang('recoveryemailsent');
            }
            else {
                $warningLogin = lang('info_wait'); // obfuscation, really
            }
        }
        else if( $num < 1 ) {
            $error = lang('errorsendingemail');
        }
        $num = max($num + 1, 3);
        sleep($num); // small timeout whether or not valid
    }
    else {
        //record failure details
        $_SESSION[$key] = array('when'=>time(),'times'=>$num+1);
        unset($_POST['username'],$_POST['password'],$_REQUEST['username'],$_REQUEST['password']);
        \CMSMS\HookManager::do_hook('Core::LoginFailed', [ 'user'=>$forgot_username ] );
        $num = max($num + 1, 5);
        sleep($num);
    }
}
else if( isset($_REQUEST['recoverme']) && $_REQUEST['recoverme'] ) {
    $user = find_recovery_user($_REQUEST['recoverme']);
    if( $user ) {
        $changepwhash = $_REQUEST['recoverme'];
    }
    sleep(2); //small delay, whether or the hash was valid
}
else if( isset($_REQUEST['forgotpwchangeform']) && $_REQUEST['forgotpwchangeform'] ) {
    $user = find_recovery_user($_REQUEST['changepwhash']);
    if( $user ) {
        if( $_REQUEST['password'] ) {
            if( $_REQUEST['password'] == $_REQUEST['passwordagain'] ) {
                $user->SetPassword($_REQUEST['password']);
                $user->Save();
                \cms_userprefs::remove_for_user( $user->id, 'pwreset' );
                $ip_passw_recovery = \cms_utils::get_real_ip();
                // put mention into the admin log
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
    sleep(2);
}

if( isset($_SESSION['logout_user_now']) ) {
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
    debug_buffer("Login cancelled.  Returning to content.");
    redirect($config["root_url"].'/index.php', true);
}
else if( isset($_POST['loginsubmit']) ) {
    // login form submitted
    $login_ops->deauthenticate();
    $username = $password = null;
    if( isset($_POST["username"]) ) $username = cleanValue($_POST["username"]);
    if( isset($_POST["password"]) ) $password = $_POST["password"];
    unset($_POST['username'],$_POST['password'],$_REQUEST['username'],$_REQUEST['password']);

    $userops = $gCms->GetUserOperations();

    class CmsLoginError extends \CmsException {}

    try {
        if( !$username || !$password ) throw new \LogicException(lang('informationmissing'));

        // load user by name
        $user = $userops->LoadUserByUsername($username, $password, TRUE, TRUE);
        if( $user ) {
            // hook for authentication
            \CMSMS\HookManager::do_hook('Core::LoginPre', [ 'user'=>$user  ] );

            $login_ops->save_authentication($user);

            // put mention into the admin log
            audit($user->id, "Admin Username: ".$user->username, 'Logged In');

            // send the post login event
            \CMSMS\HookManager::do_hook('Core::LoginPost', [ 'user'=>$user ] );

            // redirect outa here somewhere
            if( isset($_SESSION['login_redirect_to']) ) {
                // we previously attempted a URL but didn't have the user key in the request.
                $url_ob = new \cms_url($_SESSION['login_redirect_to']);
                unset($_SESSION['login_redirect_to']);
                $url_ob->erase_queryvar('_s_');
                $url_ob->erase_queryvar('sp_');
                $url_ob->set_queryvar(CMS_SECURE_PARAM_NAME,$_SESSION[CMS_USER_KEY]);
                $url = (string) $url_ob;
                redirect($url);
            }
            else {
                // find the users homepage, if any, and redirect there.
                $homepage = \cms_userprefs::get_for_user($user->id,'homepage');
                if( !$homepage ) $homepage = $config['admin_url'];

                $homepage = \CmsAdminUtils::get_session_url($homepage);

                // and redirect.
                $homepage = html_entity_decode($homepage);
                redirect($homepage);
            }
        }
        else {
            sleep(2);
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
$themeObject->do_login($vars);
