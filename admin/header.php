<?php

cms_admin_sendheaders();
$starttime = microtime();
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false)) @ob_start();

$userid = get_userid();
$smarty = cmsms()->GetSmarty();

if (isset($USE_THEME) && $USE_THEME == false) {
    //echo '<!-- admin theme disabled -->';
}
else {
    debug_buffer('before theme load');
    $themeObject = cms_utils::get_theme_object();
    $smarty->assign('secureparam', CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY]);
    debug_buffer('after theme load');

    if( isset($headtext) && $headtext != '' ) $themeObject->set_value('headertext',$headtext);

    // Display notification stuff from modules
    // should be controlled by preferences or something
    $ignoredmodules = explode(',',cms_userprefs::get_for_user($userid,'ignoredmodules'));
    if( cms_siteprefs::get('enablenotifications',1) && cms_userprefs::get_for_user($userid,'enablenotifications',1) ) {
        debug_buffer('before notifications');
        if( ($data = cms_siteprefs::get('__NOTIFICATIONS__')) ) {
            $data = unserialize($data);
            if( is_array($data) && count($data) ) {
                foreach( $data as $item ) {
                    $old = $item->html;
                    $regex = '/'.CMS_SECURE_PARAM_NAME.'\=[0-9a-z]{16}/';
                    $to = CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
                    $new = preg_replace($regex,$to,$old);

                    $themeObject->AddNotification($item->priority,$item->name,$item->html);
                }
            }
        }

        if( is_writable(CONFIG_FILE_LOCATION) ) $themeObject->AddNotification(1,'Core',lang('config_writable'));

        $pattern = cms_join_path(CMS_ROOT_PATH,'cmsms-*-install.php');
        $files = glob($pattern);
        if( is_array($files) && count($files) > 0 ) {
            $fn = basename($files[0]);
            $themeObject->AddNotification(1,'Core',lang('installfileexists',basename($fn)));
        }

        if(  !cms_siteprefs::get('mail_is_set',0) ) $themeObject->AddNotification(1,'Core',lang('info_mail_notset'));

        // Display a warning sitedownwarning
        $sitedown_message = lang('sitedownwarning', TMP_CACHE_LOCATION . '/SITEDOWN');
        $sitedown_file = TMP_CACHE_LOCATION . '/SITEDOWN';
        if (file_exists($sitedown_file)) $themeObject->AddNotification(1,'Core',$sitedown_message);

        // Display an upgrade notification
        // but only do a check once per day
        if( cms_siteprefs::get('checkversion') ) {
            if( CmsAdminUtils::site_needs_updating() ) {
                $remote_ver = CmsAdminUtils::fetch_latest_cmsms_ver();
                $themeObject->AddNotification(1,'Core',lang('new_version_available'));
                // only audit once per day
                if( cms_siteprefs::get('last_versioncheck') < (time() - 3600 * 24) ) {
                    cms_siteprefs::set('last_versioncheck',time());
                    audit('','Core','CMSMS version '.$remote_ver.' is available');
                }
            }
            else {
                // only audit once per day
                if( cms_siteprefs::get('last_versioncheck') < (time() - 3600 * 24) ) {
                    cms_siteprefs::set('last_versioncheck',time());
                    audit('','Core','Tested for newer CMSMS Version. None Available.');
                }
            }

        }
    }

    $themeObject->do_header();
}
?>