<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2016 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

if ( !defined('CMS_VERSION') ) exit;
if ( !$this->CheckPermission(UserGuide::MANAGE_PERM) ) {
    $this->Redirect($id, 'defaultadmin', $returnid);
}


// Save Parameters Options Tab
$message = '';
$error = '';
if ( isset($params['submit']) ) { // All form sumbits save settings
    $this->SetPreference('customModuleName', (isset($params['input_customModuleName']) && $params['input_customModuleName']!='') ? $params['input_customModuleName'] : 'User Guide');
    $this->SetPreference('adminSection', isset($params['input_adminSection']) ? $params['input_adminSection'] : UserGuide::ADMIN_SECTION_DEFAULT);
    $this->SetPreference('useSmarty', isset($params['input_useSmarty']) ? $params['input_useSmarty'] : false);
    $this->SetPreference('imageFolder', isset($params['input_imageFolder']) ? $params['input_imageFolder'] : '');
    $separate_settings = isset($params['separate_settings']) ? $params['separate_settings'] : false;
    $this->SetPreference('separate_settings', $separate_settings);


    // Trigger cache clear - Touch menu cache files - core will refresh (v2.0+ )
    foreach ( glob(cms_join_path(TMP_CACHE_LOCATION, 'cache*.cms')) as $filename ) {
        touch( $filename, time() - 360000 );
    }


    switch ($params['submit']) {
        case 'save_settings':
            audit('', 'UserGuide - Options tab', 'Saved');
            $message = $this->Lang('settings_saved');
            break;

        case 'xml_import':
            $xmlfield = $id.'xmlfile';
            if ( empty($_FILES[$xmlfield]['name']) || $_FILES[$xmlfield]['type']!='text/xml' ) {
                $error = $this->Lang('file_error');
                break;
            }
            $importerExporter = new UserGuideImporterExporter();
            $imported = $importerExporter->import( $_FILES[$xmlfield]['tmp_name'] );

            if ($imported) {
                $message = $this->Lang('import_completed');
            } else {
                $error = $this->Lang('import_error');
            }
            break;

        case 'xml_export':
            $importerExporter = new UserGuideImporterExporter();
            $importerExporter->export();
            $message = $this->Lang('export_completed');
            break;

        case 'import_UsersGuide_module':    // import from legacy module
            $UsersGuideMod = $this->GetModuleInstance('UsersGuide');
            if ( !is_object($UsersGuideMod) ) break;
            // import from db
            $sql = 'INSERT INTO '.CMS_DB_PREFIX.'module_userguide (title, position, content)
                SELECT man_title, man_order, man_content FROM '.CMS_DB_PREFIX.'module_UsersGuide';
            $res = $db->Execute($sql);
            if (!$res) {
                $error = $this->Lang('module_import_database_error');
                break;
            }
            $sql = 'UPDATE '.CMS_DB_PREFIX.'module_userguide SET active = IFNULL(active, 1)';
            $res = $db->Execute($sql);
            // import settings (preferences)
            $this->SetPreference('customModuleName', $UsersGuideMod->GetPreference('module_name').'*');
            $this->SetPreference('adminSection', $UsersGuideMod->GetPreference('admin_section'));
            $message = $this->Lang('import_completed');
            break;

        case 'import_UserGuide2_module':    // import from previous module
            $UserGuide2Mod = $this->GetModuleInstance('UserGuide2');
            if ( !is_object($UserGuide2Mod) ) break;
            // import from db           
            $sql = 'INSERT INTO '.CMS_DB_PREFIX.'module_userguide (title, active, content, admin)
                SELECT title, active, content, admin FROM '.CMS_DB_PREFIX.'module_userguide2';
            $res = $db->Execute($sql);
            if (!$res) {
                $error = $this->Lang('module_import_database_error');
                break;
            }
            $sql = 'UPDATE '.CMS_DB_PREFIX.'module_userguide 
                SET position=?, embed_type=?, embed_code=?, embed_first=? 
                WHERE position=NULL'; // imported only
            $res = $db->Execute($sql, [10000, $this::EMBED_TYPES[0], '', 0]);
            $query = new UserGuideQuery;
            $updated = $query->updatePositions();
            // import settings (preferences)
            $old_custom_name = $UserGuide2Mod->GetPreference('customModuleName');
            if (!empty($old_custom_name)) $this->SetPreference('customModuleName', $old_custom_name.'*');
            $this->SetPreference('adminSection', $UserGuide2Mod->GetPreference('admin_section'));
            $this->SetPreference('useSmarty', $UserGuide2Mod->GetPreference('useSmarty'));
            $this->SetPreference('imageFolder', $UserGuide2Mod->GetPreference('imageFolder'));
            $message = $this->Lang('import_completed');
            break;

    }

    // either redirect to defaultadmin or continue to output settings template
    if (!$separate_settings) {
        if ($error) $this->SetError( $error );
        if ($message) $this->SetMessage( $message );
        $this->Redirect($id, 'defaultadmin', $returnid, ['active_tab' => 'options']);
    } 

}


// output the Settings tabs
if ($error) $this->ShowErrors( $error );
if ($message) $this->ShowMessage( $message );

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings.tpl'), null, null, $smarty );
$tpl->assign( 'separate_settings', $this->GetPreference('separate_settings', false) );
if ( !isset($pages)) {  // unless already set by defaultadmin (includes this file)
    $query = new UserGuideQuery;
    $pages = $query->GetMatches();
}
$tpl->assign('pages', $pages);
$tpl->assign('loadingIcon', $this::LOADING_ICON);
$tpl->assign('input_customModuleName', $this->CreateInputText($id, 'input_customModuleName', $this->GetPreference('customModuleName',''),50,255));
$tpl->assign('input_adminSection', $this->CreateInputDropdown($id, 'input_adminSection',
    [
        lang('main') => 'main',
        lang('content') => 'content',
        lang('layout') => 'layout',
        lang('usersgroups') => 'usersgroups',
        lang('extensions') => 'extensions',
        lang('admin') => 'siteadmin',
        lang('myprefs') => 'myprefs'
    ], -1, $this->GetPreference('adminSection', UserGuide::ADMIN_SECTION_DEFAULT)
));
$tpl->assign( 'input_useSmarty', $this->CreateInputCheckbox($id, 'input_useSmarty', true, $this->GetPreference('useSmarty', false)) );

$tpl->assign( 'imageFolder', $this->GetPreference('imageFolder','') );
$tpl->assign('input_import', $this->CreateInputFile($id, 'xmlfile', '.xml'));
$hasUsersGuideMod = $this->GetModuleInstance('UsersGuide');
$tpl->assign('hasUsersGuideMod', is_object($hasUsersGuideMod) );
$hasUserGuide2Mod = $this->GetModuleInstance('UserGuide2');
$tpl->assign('hasUserGuide2Mod', is_object($hasUserGuide2Mod) );


$tpl->display();


// echo $this->EndTab();
