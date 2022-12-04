<?php

/**
 * Define an interface for File Picker related modules.
 *
 * @package CMS
 * @license GPL
 */
namespace CMSMS;

/**
 * Define an interface for modules that provide filepicker functionality.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since  2.2
 */
interface FilePickerInterface
{
    /**
     * Given a profile name, and other data return a suitable profile by name, or return a default profile
     *
     * @param string $profile_name the desired profile name to load
     * @param string $dir A suitable top location
     * @param int $uid An optional admin user id.
     * @return FilePickerProfile
     */
    public function get_profile_or_default( $profile_name, $dir = null, $uid = null );

    /**
     * Get the default profile for the specified data.
     * @param string $dir A suitable top location
     * @param int $uid An optional admin user id.
     * @return FilePickerProfile
     */
    public function get_default_profile( $dir = null, $uid = null );

    /**
     * Get the URL required to render the filepicker
     *
     * @return string
     */
    public function get_browser_url();

    /**
     * Generate HTML to display an input field that is initialized with the filepicker plugin.
     *
     * @param string $name The name for the input field.
     * @param string $value the current value for the input filed
     * @param FilePickerProfile $profile The profile to use when building the filepicker interface.
     */
    public function get_html( $name, $value, \CMSMS\FilePickerProfile $profile );
} // end of class