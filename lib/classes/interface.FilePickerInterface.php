<?php
namespace CMSMS;

interface FilePickerInterface
{
    public function get_profile_or_default( $profile_name, $dir = null, $uid = null );
    public function get_default_profile( $dir = null, $uid = null );
    public function get_browser_url();
    public function get_html( $name, $value, \CMSMS\FilePickerProfile $profile );
} // end of class