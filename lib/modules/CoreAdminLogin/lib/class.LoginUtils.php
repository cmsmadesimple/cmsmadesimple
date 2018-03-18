<?php
namespace CoreAdminLogin;

class LoginUtils
{
    protected $_mod;

    public function __construct( \CoreAdminLogin $mod )
    {
        $this->_mod = $mod;
    }

    public function create_reset_code( \User $user )
    {
        $code = sha1(__FILE__ . '--' . $user->username . $user->password . rand() . time() );
        \cms_userprefs::set_for_user( $user->id, 'pwreset', $code );
        return $code;
    }

    public function remove_reset_code( \User $user )
    {
        \cms_userprefs::remove_for_user( $user->id, 'pwreset' );
    }

    public function validate_reset_code( \User $user, string $code )
    {
        $dbcode = \cms_userprefs::get_for_user( $user->id, 'pwreset' );
        if( !$dbcode ) return false;
        if( $dbcode != $code ) return false;
        $this->remove_reset_code( $user );
        return true;
    }

    public function send_recovery_email( \User $user )
    {
        if( !$user->email ) throw new \RuntimeException( $this->_mod->Lang('err_nouseremail') );

        $gCms = \CmsApp::get_instance();
        $config = $gCms->GetConfig();
        $userops = $gCms->GetUserOperations();

        $obj = new \cms_mailer;
        $obj->IsHTML(TRUE);
        $obj->AddAddress($user->email, html_entity_decode($user->firstname . ' ' . $user->lastname));
        // remember email subjects cannot contain entities.
        $obj->SetSubject($this->_mod->Lang('email_subject',html_entity_decode(get_site_preference('sitename','CMSMS Site'))));

        $code = $this->create_reset_code( $user );
        $url = $config['admin_url'] . '/login.php?recoverme=' . $code;
        $body = $this->_mod->Lang('email_body',get_site_preference('sitename','CMSMS Site'), $user->username, $url, $url);
        $obj->SetBody($body);
        $res = $obj->Send();
        if( !$res ) throw new \RuntimeException( $this->_mod->Lang('err_email_send') );
        audit('',$this->_mod->GetName(),'Sent Lost Password Email for '.$user->username);
    }

    public function find_recovery_user( string $hash )
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
} // class