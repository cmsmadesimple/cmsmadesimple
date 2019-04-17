<?php
namespace CoreAdminLogin;
use cms_url;
if( !isset($gCms) ) exit;

class LoginUserError extends \RuntimeException {}

$username = $password = null;
$theme_object = \cms_utils::get_theme_object();
$csrf_key = md5(__FILE__);
$login_ops = $gCms->get_login_operations();
$hm = $gCms->get_hook_manager();
$username = $password = $error = $warning = $pwhash = $message = null;

if( isset( $_GET['recoverme'] ) ) {
    $code = filter_var( $_GET['recoverme'], FILTER_SANITIZE_STRING );
    $user = $this->getLoginUtils()->find_recovery_user( $code );
    if( !$user ) {
        $error = $this->Lang('err_usernotfound');
    }
    else {
        $pwhash = $code;
    }
}
else if( isset( $params['forgotpwchangeform']) ) {
    try {
        $expected_csrf_val = ( isset($_SESSION[$csrf_key]) ) ? $_SESSION[$csrf_key] : null;
        $provided_csrf_val = ( isset($params['csrf']) ) ? $params['csrf'] : null;
        if( !$expected_csrf_val || !$provided_csrf_val || $expected_csrf_val != $provided_csrf_val ) {
            throw new \RuntimeException( $this->Lang('err_csrfinvalid') );
        }

        $usercode = filter_var( $params['changepwhash'], FILTER_SANITIZE_STRING );
        $username = html_entity_decode( filter_var( $params['username'], FILTER_SANITIZE_STRING ) );
        $password1 = html_entity_decode( filter_var( $params['password'], FILTER_SANITIZE_STRING ) );
        $password2 = html_entity_decode( filter_var( $params['passwordagain'], FILTER_SANITIZE_STRING ) );
        if( !$usercode || !$username || !$password1 || !$password2 ) throw new LoginUserError( $this->Lang('err_missingdata') );
        if( $password1 != $password2 ) throw new LoginUserError( $this->Lang('err_passwordmismatch') );
        $hm->emit('Core::PasswordStrengthTest', $password1 );

        $user = $this->getLoginUtils()->find_recovery_user( $usercode );
        if( !$user || $user->username != $username ) throw new LoginUserError( $this->Lang('err_usernotfound') );

        $user->SetPassword( $password1 );
        $user->Save();
        $this->getLoginUtils()->remove_reset_code( $user );

        $ip_passw_recovery = \cms_utils::get_real_ip();
        audit('','Core','Completed lost password recovery for: '.$user->username.' (IP: '.$ip_passw_recovery.')');
        $hm->emit('Core::LostPasswordReset', [ 'uid'=>$user->id, 'username'=>$user->username, 'ip'=>$ip_passw_recovery ] );
        $message = $this->Lang('msg_passwordchanged');
    }
    catch( LoginUserError $e ) {
        $error = $e->GetMessage();
        $hm->emit('Core::LoginFailed', [ 'user'=>$username ] );
        $ip_login_failed = \cms_utils::get_real_ip();
        $pwhash = $usercode;
        cms_warning('', '(IP: ' . $ip_login_failed . ') ' . "Admin Username: " . $username, 'Password Reset Failed');
    }
    catch( \Exception $e ) {
        $error = $e->GetMessage();
    }
}
else if( isset( $params['forgotpwform']) ) {
    // got the forgot password form request
    try {
        $expected_csrf_val = ( isset($_SESSION[$csrf_key]) ) ? $_SESSION[$csrf_key] : null;
        $provided_csrf_val = ( isset($params['csrf']) ) ? $params['csrf'] : null;
        if( !$expected_csrf_val || !$provided_csrf_val || $expected_csrf_val != $provided_csrf_val ) {
            throw new \RuntimeException( $this->Lang('err_csrfinvalid') );
        }

        $username = html_entity_decode( filter_var( $params['username'] ?? null, FILTER_SANITIZE_STRING ) );
        unset( $params['username'] );
        if( !$username ) throw new LoginUserError( $this->Lang('err_usernotfound') );

        $hm->emit('Core::LostPassword', [ 'username'=>$username] );
        $userops = $gCms->GetUserOperations();
        $oneuser = $userops->LoadUserByUsername($username, null, true, true );
        if( !$oneuser ) {
            $hm->emit('Core::LoginFailed', [ 'user'=>$username ] );
            throw new LoginUserError( $this->Lang('err_usernotfound') );
        }

        $this->getLoginUtils()->send_recovery_email( $oneuser );
        $warning = $this->Lang('warn_recoveryemailsent');
    }
    catch( LoginUserError $e ) {
        $error = $e->GetMessage();
        $hm->emit('Core::LoginFailed', [ 'user'=>$username ] );
        $ip_login_failed = \cms_utils::get_real_ip();
        cms_warnng('(IP: ' . $ip_login_failed . ') ' . "Admin Username: " . $username, 'Password Recovery Failed');
    }
    catch( \Exception $e ) {
        $error = $e->GetMessage();
    }
}
else if( isset( $params['submit'] ) ) {
    // validatte CSRF key
    try {
        $expected_csrf_val = ( isset($_SESSION[$csrf_key]) ) ? $_SESSION[$csrf_key] : null;
        $provided_csrf_val = ( isset($params['csrf']) ) ? $params['csrf'] : null;
        if( !$expected_csrf_val || !$provided_csrf_val || $expected_csrf_val != $provided_csrf_val ) {
            throw new \RuntimeException( $this->Lang('err_csrfinvalid') );
        }

        $username = html_entity_decode( filter_var( $params['username'] ?? null, FILTER_SANITIZE_STRING ) );
        $password = html_entity_decode( filter_var( $params['password'] ?? null, FILTER_SANITIZE_STRING ) );
        if( !$username || !$password ) throw new LoginUserError( $this->Lang('err_invalidusernamepassword') );

        $userops = $gCms->GetUserOperations();
        $oneuser = $userops->LoadUserByUsername( $username, null, true, true );
        if( !$oneuser ) throw new LoginUserError( $this->Lang('err_invalidusernamepassword') );
        if( !$oneuser->Authenticate( $password ) )  throw new LoginUserError( $this->Lang('err_invalidusernamepassword') );

        // now we could redirect somewhere for a second stage of authenticateion.
        // but for core... we don't need to.

        // user is authenticated. log him hin.
        $login_ops->save_authentication( $oneuser );
        audit($oneuser->id, "Admin Username: ".$oneuser->username, 'Logged In');
        $hm->emit('Core::LoginPost', [ 'user'=>&$oneuser ] );

        // now redirect someplace
        if( ($redirect_to = $_SESSION['login_redirect_to']) ) {
            unset($_SESSION['login_redirect_to']);
            $url_ob = new cms_url($redirect_to);
            if( !$url_ob->get_scheme() ) {
                $url_ob->set_scheme('http');
                if( $gCms->is_https_request() ) $url_ob->set_scheme('https');
            }
            $url_ob->set_host($_SERVER['SERVER_NAME']);
            $url_ob->erase_queryvar('_s_');
            $url_ob->erase_queryvar('sp_');
            $url_ob->set_queryvar(CMS_SECURE_PARAM_NAME,$_SESSION[CMS_USER_KEY]);
            $url = (string) $url_ob;
            redirect($url);
        }
        $homepage = \cms_userprefs::get_for_user($oneuser->id,'homepage');
        if( !$homepage ) $homepage = $config['admin_url'];
        $homepage = html_entity_decode( $homepage );
        $homepage = \CmsAdminUtils::get_session_url( $homepage );
        redirect( $homepage );
    }
    catch( LoginUserError $e ) {
        $error = $e->GetMessage();
        $hm->emit('Core::LoginFailed', [ 'user'=>$username ] );
        $ip_login_failed = \cms_utils::get_real_ip();
        cms_warning('(IP: ' . $ip_login_failed . ') ' . "Admin Username: " . $username, 'Login Failed');
    }
    catch( \Exception $e ) {
        $error = $e->GetMessage();
    }
}
else if( isset( $params['cancel'] ) ) {
    debug_buffer("Login cancelled.  Returning to root page.");
    $login_ops->deauthenticate(); // just in case
    redirect( $config['root_url'].'/index.php', true );
}

// display the login form

$login_ops->deauthenticate(); // cannot be logged in when we get here.
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource( 'admin_login.tpl' ), null, null, $smarty );
$tpl->assign( 'error', $error );
$tpl->assign( 'warning', $warning );
$tpl->assign( 'message', $message );
$tpl->assign( 'changepwhash', $pwhash );
$tpl->assign( 'username', $username);
$tpl->assign( 'password', $password);
$tpl->assign( 'admin_url', $config['admin_url'] );
//$tpl->assign( 'theme', $theme_object);
//$tpl->assign( 'theme_root', $theme_object->root_url );
$csrf = $_SESSION[$csrf_key] = md5(__FILE__.time().rand());
$tpl->assign( 'csrf', $csrf );
$content = $tpl->fetch();
return $content;
