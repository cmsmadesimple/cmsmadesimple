<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if( !$this->CheckPermission('Modify Site Preferences' ) && !$this->CheckPermission('Modify Modules') ) return;

// Handle notice dismissal (AJAX)
if( !empty($params['dismiss_notice']) ) {
    $this->SetPreference('notice_dismissed', 1);
    exit;
}

if( !$this->CheckPermission('Modify Site Preferences' ) ) return;
$this->SetCurrentTab('prefs');

if( isset($config['developer_mode']) && !empty($params['reseturl']) ) {
    $this->SetPreference('module_repository',ModuleManager::_dflt_request_url);
    $this->SetMessage($this->Lang('msg_urlreset'));
    $this->RedirectToAdminTab();
}
if( isset($params['dl_chunksize']) ) $this->SetPreference('dl_chunksize',(int)trim($params['dl_chunksize']));
$latestdepends = (int)get_parameter_value($params,'latestdepends');
$this->SetPreference('latestdepends',$latestdepends);
$this->SetPreference('show_beta',(int)get_parameter_value($params,'show_beta'));
$this->SetPreference('show_incompatible',(int)get_parameter_value($params,'show_incompatible'));


if( isset($config['developer_mode']) ) {
    if( isset($params['url']) ) $this->SetPreference('module_repository',trim($params['url']));
    $disable_caching = (int)get_parameter_value($params,'disable_caching');
    $this->SetPreference('disable_caching',$disable_caching);
    $this->SetPreference('allowuninstall',(int)get_parameter_value($params,'allowuninstall'));
}
else {
    $this->SetPreference('allowuninstall',0);
}

$this->SetMessage($this->Lang('msg_prefssaved'));
$this->RedirectToAdminTab();
?>
