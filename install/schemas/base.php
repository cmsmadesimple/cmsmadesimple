<?php
global $admin_user;

$db = CmsInstaller::get_db();
$dir = dirname(dirname(__FILE__)).time();

$query = 'INSERT INTO '.$db_prefix.'version VALUES (36)';
$db->Execute($query);

//
// site preferences
//
cms_siteprefs::set('sitedownmessage','<p>Site is currently down for maintenance</p>');
cms_siteprefs::set('sitedownmessagetemplate',-1);
cms_siteprefs::set('metadata',"<meta name=\"Generator\" content=\"CMS Made Simple - Copyright (C) 2004-11 Ted Kulp. All rights reserved.\" />\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n ");
cms_siteprefs::set('xmlmodulerepository','');
cms_siteprefs::set('logintheme','OneElevent');
cms_siteprefs::set('global_umask','022');
cms_siteprefs::set('backendwysiwyg','MicroTiny');
cms_siteprefs::set('page_active',1);
cms_siteprefs::set('page_showinmenu',1);
cms_siteprefs::set('page_cachable',1);
cms_siteprefs::set('page_metadata','{* Add code here that should appear in the metadata section of all new pages *}');
cms_siteprefs::set('defaultpagecontent','<!-- Add code here that should appear in the content block of all new pages -->');
cms_siteprefs::set('default_parent_page',-1);
cms_siteprefs::set('additional_editors','');
cms_siteprefs::set('page_searchable',1);
cms_siteprefs::set('page_extra1','');
cms_siteprefs::set('page_extra2','');
cms_siteprefs::set('page_extra3','');
cms_siteprefs::set('sitedownexcludes','');

//
// permissions
//
$all_perms = array();
$perms = array('Add Pages','Add Groups','Add Templates','Add Users','Modify Any Page',
	       'Modify Groups','Modify Group Assignments','Modify Permissions',
	       'Modify Templates','Modify Users','Remove Pages','Remove Groups',
	       'Remove Users','Modify Modules','Modify Files','Modify Site Preferences',
	       'Modify Stylesheets','Add Stylesheets','Remove Stylesheets',
	       'Manage Designs','Modify User-defined Tags','Clear Admin Log',
	       'Modify Events',
	       'View Tag Help','Manage All Contnt','Reorder Content');
foreach( $perms as $one_perm ) {
  $permission = new CmsPermission();
  $permission->source = 'Core';
  $permission->name = $one_perm;
  $permission->text = $one_perm;
  $permission->Save();
  $all_perms[$one_perm] = $permission;
}

//
// initial groups
//
$admin_group = new Group();
$admin_group->name = 'Admin';
$admin_group->active = 1;
$admin_group->Save();

$editor_group = new Group();
$editor_group->name = 'Editor';
$editor_group->active = 1;
$editor_group->Save();
$editor_group->GrantPermission('Manage All Content');

$designer_group = new Group();
$designer_group->name = 'Designer';
$designer_group->active = 1;
$designer_group->Save();
$designer_group->GrantPermission('Add Templates');
$designer_group->GrantPermission('Modify Templates');
$designer_group->GrantPermission('Modify Stylesheets');
$designer_group->GrantPermission('Add Stylesheets');
$designer_group->GrantPermission('Remove Stylesheets');
$designer_group->GrantPermission('Add Stylesheet Assoc');
$designer_group->GrantPermission('Modify Stylesheet Assoc');
$designer_group->GrantPermission('Remove Stylesheet Assoc');
$designer_group->GrantPermission('Manage All Content');

//
// initial users
//
$sitemask = cms_siteprefs::get('sitemask');
$admin_user = new User;
$admin_user->username = $_POST['adminusername'];
$admin_user->active = 1;
$admin_user->adminaccess = 1;
$admin_user->password = md5($sitemask.$_POST['adminpassword']);
$admin_user->Save();
UserOperations::get_instance()->AddMemberGroup($admin_user->id,$admin_group->id);

//
// user prefs.
//
cms_userprefs::set_for_user($admin_user->id,'use_wysiwyg','MicroTiny');

//
// User Tags
//
UserTagOperations::get_instance()->SetUserTag('user_agent',
  '//Code to show the user\'s user agent information.\r\necho $_SERVER[\"HTTP_USER_AGENT\"];',
  'Code to show the user\s user agent information');

//
// Events
//
Events::CreateEvent('Core','LoginPost');
Events::CreateEvent('Core','LogoutPost');
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
Events::CreateEvent('Core','TemplatePreCompile');
Events::CreateEvent('Core','TemplatePostCompile');
Events::CreateEvent('Core','AddGlobalContentPre');
Events::CreateEvent('Core','AddGlobalContentPost');
Events::CreateEvent('Core','EditGlobalContentPre');
Events::CreateEvent('Core','EditGlobalContentPost');
Events::CreateEvent('Core','DeleteGlobalContentPre');
Events::CreateEvent('Core','DeleteGlobalContentPost');
Events::CreateEvent('Core','GlobalContentPreCompile');
Events::CreateEvent('Core','GlobalContentPostCompile');
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
Events::CreateEvent('Core','ContentStylesheet');
Events::CreateEvent('Core','ContentPreCompile');
Events::CreateEvent('Core','ContentPostCompile');
Events::CreateEvent('Core','ContentPostRender');
Events::CreateEvent('Core','SmartyPreCompile');
Events::CreateEvent('Core','SmartyPostCompile');
Events::CreateEvent('Core','ChangeGroupAssignPre');
Events::CreateEvent('Core','ChangeGroupAssignPost');
Events::CreateEvent('Core','StylesheetPreCompile');
Events::CreateEvent('Core','StylesheetPostCompile');
Events::CreateEvent('Core','LoginFailed');

?>