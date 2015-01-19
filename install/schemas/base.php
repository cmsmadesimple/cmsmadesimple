<?php
global $admin_user;

$db = CmsInstaller::get_db();
$dir = dirname(dirname(__FILE__)).time();

$query = 'INSERT INTO '.$db_prefix.'version VALUES (200)';
$db->Execute($query);

//
// site preferences
//
cms_siteprefs::set('sitedownmessage','<p>Site is currently down for maintenance</p>');
cms_siteprefs::set('sitedownmessagetemplate',-1);
cms_siteprefs::set('metadata',"<meta name=\"Generator\" content=\"CMS Made Simple - Copyright (C) 2004-" . date('Y') . " - All rights reserved\" />\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n");
cms_siteprefs::set('xmlmodulerepository','');
cms_siteprefs::set('logintheme','OneEleven');
cms_siteprefs::set('global_umask','022');
cms_siteprefs::set('backendwysiwyg','MicroTiny');
cms_siteprefs::set('page_active',1);
cms_siteprefs::set('page_showinmenu',1);
cms_siteprefs::set('page_cachable',1);
cms_siteprefs::set('page_metadata','');
cms_siteprefs::set('defaultpagecontent','');
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
  $permission->Save();
  $all_perms[$one_perm] = $permission;
}

//
// initial groups
//
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
// User Tags
//
UserTagOperations::get_instance()->SetUserTag('user_agent',
  'echo $_SERVER["HTTP_USER_AGENT"];',
  'Code to show the user\'s user agent information');

$txt = <<<EOT
// set start date your site was published\n\$startCopyRight = '2004';\n\n// check if start year is this year\nif (date('Y') == \$startCopyRight)\n{\n    // it was, just print this year\n    echo \$startCopyRight;\n} else {\n    // it was not, print start year and this year delimited with a dash\n    echo \$startCopyRight . '-' . date('Y');\n}
EOT;
UserTagOperations::get_instance()->SetUserTag('custom_copyright',$txt,'Code to output copyright information');

//
// Template Types
//
$page_template_type = new CmsLayoutTemplateType();
$page_template_type->set_originator(CmsLayoutTemplateType::CORE);
$page_template_type->set_name('page');
$page_template_type->set_dflt_flag(TRUE);
$page_template_type->set_lang_callback('CmsTemplateResource::page_type_lang_callback');
$page_template_type->set_content_callback('CmsTemplateResource::reset_page_type_defaults');
$page_template_type->reset_content_to_factory();
$page_template_type->set_content_block_flag(TRUE);
$dflt_content = <<<EOT
{process_pagedata}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
<title>{sitename} - {title}</title>
{metadata}
{cms_stylesheet}
</head>
<body>

{* start header *}
<div id="header">
  <h1>{sitename}</h1>
</div>
{* end header *}

{* start menu *}
<div id="menu">
  {menu}
</div>
{*} end menu *}

{* start content *}
<div id="content">
  <h1>{title}</h1>
  {content}
</div>
{* end content *}

</body>
</html>
EOT;
$page_template_type->set_dflt_contents($dflt_content);
$page_template_type->save();

$gcb_template_type = new CmsLayoutTemplateType();
$gcb_template_type->set_originator(CmsLayoutTemplateType::CORE);
$gcb_template_type->set_name('generic');
$gcb_template_type->set_lang_callback('CmsTemplateResource::generic_type_lang_callback');
$dflt_content = <<<EOT
{* New generic template *}
EOT;
$gcb_template_type->set_dflt_contents($dflt_content);
$gcb_template_type->save();

//
// Events
//
Events::CreateEvent('Core','LoginPost');
Events::CreateEvent('Core','LogoutPost');
Events::CreateEvent('Core','LoginFailed');

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
Events::CreateEvent('Core','ContentPostRender');
Events::CreateEvent('Core','SmartyPreCompile');
Events::CreateEvent('Core','SmartyPostCompile');
Events::CreateEvent('Core','ChangeGroupAssignPre');
Events::CreateEvent('Core','ChangeGroupAssignPost');
Events::CreateEvent('Core','StylesheetPreCompile');
Events::CreateEvent('Core','StylesheetPostCompile');

?>