<?php
global $admin_user;

status_msg(ilang('install_requireddata'));

$query = 'INSERT INTO '.CMS_DB_PREFIX.'version VALUES (202)';
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

//
// permissions
//
verbose_msg(ilang('install_initsiteperms'));
$all_perms = array();
$perms = array('Add Pages','Manage Groups','Add Templates','Manage Users','Modify Any Page',
	       'Modify Permissions','Modify Templates','Remove Pages',
	       'Modify Modules','Modify Files','Modify Site Preferences',
	       'Manage Stylesheets','Manage Designs','Modify User-defined Tags','Clear Admin Log',
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
$designer_group->GrantPermission('Modify User-defined Tags');

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
$admin_user->password = md5($sitemask.$adminaccount['password']);
$admin_user->Save();
UserOperations::get_instance()->AddMemberGroup($admin_user->id,$admin_group->id);
cms_userprefs::set_for_user($admin_user->id,'wysiwyg','MicroTiny'); // the one, and only user preference we need.

//
// Events
//
verbose_msg(ilang('install_initevents'));
Events::CreateEvent('Core','LoginPost');
Events::CreateEvent('Core','LogoutPost');
Events::CreateEvent('Core','LoginFailed');
Events::CreateEvent('Core','LostPassword');
Events::CreateEvent('Core','LostPasswordReset');

Events::CreateEvent('Core','AddUserPre');
Events::CreateEvent('Core','AddUserPost');
Events::CreateEvent('Core','EditUserPre');
Events::CreateEvent('Core','EditUserPost');
Events::CreateEvent('Core','DeleteUserPre');
Events::CreateEvent('Core','DeleteUserPost');
Events::CreateEvent('Core','AddGroupPre');
Events::CreateEvent('Core','AddGroupPost');
Events::CreateEvent('Core','EditGroupPre');
Events::CreateEvent('Core','EditGroupPost');
Events::CreateEvent('Core','DeleteGroupPre');
Events::CreateEvent('Core','DeleteGroupPost');

Events::CreateEvent('Core','AddStylesheetPre');
Events::CreateEvent('Core','AddStylesheetPost');
Events::CreateEvent('Core','EditStylesheetPre');
Events::CreateEvent('Core','EditStylesheetPost');
Events::CreateEvent('Core','DeleteStylesheetPre');
Events::CreateEvent('Core','DeleteStylesheetPost');
Events::CreateEvent('Core','AddTemplatePre');
Events::CreateEvent('Core','AddTemplatePost');
Events::CreateEvent('Core','EditTemplatePre');

Events::CreateEvent('Core','EditTemplatePost');
Events::CreateEvent('Core','DeleteTemplatePre');
Events::CreateEvent('Core','DeleteTemplatePost');
Events::CreateEvent('Core','AddTemplateTypePre');
Events::CreateEvent('Core','AddTemplateTypePost');
Events::CreateEvent('Core','EditTemplateTypePre');
Events::CreateEvent('Core','EditTemplateTypePost');
Events::CreateEvent('Core','DeleteTemplateTypePre');
Events::CreateEvent('Core','DeleteTemplateTypePost');
Events::CreateEvent('Core','AddDesignPre');
Events::CreateEvent('Core','AddDesignPost');
Events::CreateEvent('Core','EditDesignPre');
Events::CreateEvent('Core','EditDesignPost');
Events::CreateEvent('Core','DeleteDesignPre');
Events::CreateEvent('Core','DeleteDesignPost');

Events::CreateEvent('Core','TemplatePreCompile');
Events::CreateEvent('Core','TemplatePreFetch');
Events::CreateEvent('Core','TemplatePostCompile');

Events::CreateEvent('Core','ContentEditPre');
Events::CreateEvent('Core','ContentEditPost');
Events::CreateEvent('Core','ContentDeletePre');
Events::CreateEvent('Core','ContentDeletePost');

Events::CreateEvent('Core','AddUserDefinedTagPre');
Events::CreateEvent('Core','AddUserDefinedTagPost');
Events::CreateEvent('Core','EditUserDefinedTagPre');
Events::CreateEvent('Core','EditUserDefinedTagPost');
Events::CreateEvent('Core','DeleteUserDefinedTagPre');
Events::CreateEvent('Core','DeleteUserDefinedTagPost');

Events::CreateEvent('Core','ModuleInstalled');
Events::CreateEvent('Core','ModuleUninstalled');
Events::CreateEvent('Core','ModuleUpgraded');
Events::CreateEvent('Core','ContentPreCompile');
Events::CreateEvent('Core','ContentPostCompile');
Events::CreateEvent('Core','ContentPreRender'); // 2.2
Events::CreateEvent('Core','ContentPostRender');
Events::CreateEvent('Core','SmartyPreCompile');
Events::CreateEvent('Core','SmartyPostCompile');
Events::CreateEvent('Core','ChangeGroupAssignPre');
Events::CreateEvent('Core','ChangeGroupAssignPost');
Events::CreateEvent('Core','StylesheetPreCompile');
Events::CreateEvent('Core','StylesheetPostCompile');
Events::CreateEvent('Core','StylesheetPostRender');

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

/*
$move_directory_files = function($srcdir,$destdir) {
    $srcdir = trim($srcdir);
    $destdir = trim($destdir);
    if( !is_dir($srcdir) ) return;

    $files = glob($srcdir.'/*');
    if( !count($files) ) return;

    foreach( $files as $src ) {
        $bn = basename($src);
        $dest = $destdir.'/'.$bn;
        rename($src,$dest);
    }
    @touch($dir.'/index.html');
};
*/

// create the assets directory structure
verbose_msg(ilang('install_createassets'));
$create_private_dir('assets/templates');
$create_private_dir('assets/configs');
$create_private_dir('assets/admin_custom');
$create_private_dir('assets/module_custom');
$create_private_dir('assets/plugins');
$create_private_dir('assets/images');
$create_private_dir('assets/css');
