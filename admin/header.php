<?php
namespace CMSMS;
use \cms_utils;
use \cms_userprefs;
use \cms_siteprefs;

$starttime = microtime();
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false)) @ob_start();

$_app = cmsms();
$userid = get_userid(); // does authentication too.
$smarty = $_app->GetSmarty();

if (isset($USE_THEME) && $USE_THEME == false) {
    //echo '<!-- admin theme disabled -->';
}
else {
    cms_admin_sendheaders();
    debug_buffer('before theme load');
    $themeObject = $_app->get_admin_theme();
    $smarty->assign('secureparam', CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY]);
    debug_buffer('after theme load');

    // Display notification stuff from modules
    // should be controlled by preferences or something
    $ignoredmodules = explode(',',cms_userprefs::get_for_user($userid,'ignoredmodules'));
    if( cms_siteprefs::get('enablenotifications',1) && cms_userprefs::get_for_user($userid,'enablenotifications',1) ) {
        // Display a warning sitedownwarning
        $sitedown_message = lang('sitedownwarning', TMP_CACHE_LOCATION . '/SITEDOWN');
        $sitedown_file = TMP_CACHE_LOCATION . '/SITEDOWN';
        if (file_exists($sitedown_file)) $themeObject->AddNotification(1,'Core',$sitedown_message);
    }

    $themeObject->do_header();
}
