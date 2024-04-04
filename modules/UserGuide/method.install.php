<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

if ( !defined('CMS_VERSION') ) exit;

$uid = null;
if ( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
    $uid = 1; // hardcode to first user
} else {
    $uid = get_userid();
}

// Setup Module Permissions
$this->CreatePermission(UserGuide::MANAGE_PERM, 'UserGuide - Manage');
$this->CreatePermission(UserGuide::USE_PERM, 'UserGuide - Use');

// Setup default permissions - Editor has USE permission
$perm_id = $db->GetOne("SELECT permission_id FROM ".CMS_DB_PREFIX."permissions WHERE permission_name = '".UserGuide::USE_PERM."'");
$group_id = $db->GetOne("SELECT group_id FROM `".CMS_DB_PREFIX."groups` WHERE group_name = 'Editor'");
$count = $db->GetOne("SELECT count(*) FROM " . CMS_DB_PREFIX . "group_perms WHERE group_id = ? AND permission_id = ?", [$group_id, $perm_id]);
if (isset($count) && intval($count)==0) {   // if not already set
    $new_id = $db->GenID(CMS_DB_PREFIX."group_perms_seq");
    $query = "INSERT INTO ".CMS_DB_PREFIX."group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (?, ?, ?, NOW(), NOW())";
    $db->Execute($query, [$new_id, $group_id, $perm_id]);
}

// Set Preferences
$this->SetPreference('customModuleName', 'User Guide');
$this->SetPreference('adminSection', UserGuide::ADMIN_SECTION_DEFAULT);
$this->SetPreference('useSmarty', true);
$this->SetPreference('imageFolder', '');

// Create Tables
$db = $this->GetDb();
$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

// create module_userguide table
$fields = "
    id I KEY AUTO,
    title C(255) NOTNULL,
    position I,
    active I1,
    admin I1,
    content X,
    embed_type C(10),
    embed_code X,
    embed_first I1
";
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX.'module_userguide', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// install default content
$importerExporter = new UserGuideImporterExporter();
$imported = $importerExporter->import( $this->GetModulePath().UserGuide::DEFAULT_CONTENT_XML );

