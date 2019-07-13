<?php
class CoreAdminLogin extends CMSModule
{
    public function GetName()
    {
	return 'CoreAdminLogin';
    }

    public function GetVersion() 
    {
	return '0.0.1';
    }

    public function MinimumCMSVersion()
    {
        return '2.2.903';
    }

    protected function getLoginUtils()
    {
        static $_obj;
        if( !$_obj ) $_obj = new \CoreAdminLogin\LoginUtils( $this );
        return $_obj;
    }
} // class
