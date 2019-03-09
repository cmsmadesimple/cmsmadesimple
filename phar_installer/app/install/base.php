<?php
global $admin_user;

status_msg(ilang('install_requireddata'));

$query = 'INSERT INTO '.CMS_DB_PREFIX.'version VALUES (203)';
$db->Execute($query);
verbose_msg(ilang('install_setschemaver'));

//
// site preferences
//
verbose_msg(ilang('install_initsiteprefs'));
cms_siteprefs::set('metadata',"<meta name=\"Generator\" content=\"CMS Made Simple - Copyright (C) 2004-" . date('Y') . ". All rights reserved.\" />\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n");
cms_siteprefs::set('global_umask','022');
cms_siteprefs::set('auto_clear_cache_age',60); // cache files for only 60 days by default
cms_siteprefs::set('adminlog_lifetime',3600*24*31); // admin log entries only live for 60 days.
cms_siteprefs::set('allow_browser_cache',1); // allow browser to cache cachable pages
cms_siteprefs::set('browser_cache_expiry',60); // browser can cache pages for 60 minutes.
cms_siteprefs::set('site_signature',sha1(bin2hex(random_bytes(256)))); // a unique signature to identify this site.  Useful for some signatures too.

//
// permissions
// note: most of these permissions should now be in CmsContentManager and DesignManager install routines.
//
verbose_msg(ilang('install_initsiteperms'));
$all_perms = array();
$perms = array('Add Pages','Manage Groups','Add Templates','Manage Users','Modify Any Page',
               'Modify Permissions','Modify Templates','Remove Pages',
               'Modify Modules','Modify Files','Modify Site Preferences',
               'Manage Stylesheets','Manage Designs',
               'Modify Events','View Tag Help','Manage All Content','Reorder Content','Manage My Settings',
               'Manage My Account', 'Manage My Bookmarks');
foreach( $perms as $one_perm ) {
  $permission = new CmsPermission();
  $permission->source = 'Core';
  $permission->name = $one_perm;
  $permission->text = $one_perm;
  $permission->save();
  $all_perms[$one_perm] = $permission;
}

//
// initial groups
//
verbose_msg(ilang('install_initsitegroups'));
$admin_group = new Group();
$admin_group->name = 'Admin';
$admin_group->description = 'Members of this group can manage the entire site.';
$admin_group->active = 1;
$admin_group->Save();

$editor_group = new Group();
$editor_group->name = 'Editor';
$editor_group->description = 'Members of this group can manage content';
$editor_group->active = 1;
$editor_group->Save();
$editor_group->GrantPermission('Manage All Content');
$editor_group->GrantPermission('Manage My Account');
$editor_group->GrantPermission('Manage My Settings');
$editor_group->GrantPermission('Manage My Bookmarks');

$designer_group = new Group();
$designer_group->name = 'Designer';
$designer_group->description = 'Members of this group can manage stylesheets, templates, and content';
$designer_group->active = 1;
$designer_group->Save();
$designer_group->GrantPermission('Add Templates');
$designer_group->GrantPermission('Manage Designs');
$designer_group->GrantPermission('Modify Templates');
$designer_group->GrantPermission('Manage Stylesheets');
$designer_group->GrantPermission('Manage All Content');
$designer_group->GrantPermission('Manage My Account');
$designer_group->GrantPermission('Manage My Settings');
$designer_group->GrantPermission('Manage My Bookmarks');
$designer_group->GrantPermission('Modify Files');

//
// initial user account
//
verbose_msg(ilang('install_initsiteusers'));
$sitemask = cms_siteprefs::get('sitemask');
$admin_user = new User;
$admin_user->username = $adminaccount['username'];
if( isset($adminaccount['emailaddr']) && $adminaccount['emailaddr'] ) $admin_user->email = $adminaccount['emailaddr'];
$admin_user->active = 1;
$admin_user->adminaccess = 1;
$admin_user->password = password_hash( $adminaccount['password'], PASSWORD_BCRYPT );
$admin_user->Save();
UserOperations::get_instance()->AddMemberGroup($admin_user->id,$admin_group->id);
cms_userprefs::set_for_user($admin_user->id,'wysiwyg','MicroTiny'); // the one, and only user preference we need.

$create_private_dir = function($relative_dir) {
    $app = \__appbase\get_app();
    $destdir = $app->get_destdir();
    $relative_dir = trim($relative_dir);
    if( !$relative_dir ) return;

    $dir = $destdir.'/'.$relative_dir;
    if( !is_dir($dir) ) {
        @mkdir($dir,0777,true);
    }
    @touch($dir.'/index.html');
};

// create the assets directory structure
verbose_msg(ilang('install_createassets'));
$create_private_dir('assets/templates');
$create_private_dir('assets/configs');
$create_private_dir('assets/admin_custom');
$create_private_dir('assets/module_custom');
$create_private_dir('assets/modules');
$create_private_dir('assets/plugins');
$create_private_dir('assets/simple_plugins');
$create_private_dir('assets/images');
$create_private_dir('assets/css');
