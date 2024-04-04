<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

if ( !defined('CMS_VERSION') ) exit;

$managePermission = $this->CheckPermission(UserGuide::MANAGE_PERM);
$usePermission = $this->CheckPermission(UserGuide::USE_PERM);
if ( !$managePermission && !$usePermission ) {
    $this->ShowErrors( $this->Lang('need_permission') );
    return;
}


$query = new UserGuideQuery;
$pages = $query->GetMatches();
$useSmarty = $this->GetPreference('useSmarty', false);
$adminTheme = cms_utils::get_theme_object();
$separate_settings = $this->GetPreference('separate_settings', false);

// Create Admin tabs
echo $this->StartTabHeaders();

    // All active User Guide tabs
    if ( is_array($pages) ) {
        foreach ($pages as $page) {
            if ($page->active && $managePermission >= $page->admin) {
                $adminOnlyStar = $page->admin ? '*' : '';
                echo $this->SetTabHeader( 'UGPage'.$page->id, htmlspecialchars($page->title).$adminOnlyStar );
            }

        }
    }

    if ( $managePermission && !$separate_settings) {
        echo $this->SetTabHeader("pages", $this->Lang("tab_pages").'*');
        echo $this->SetTabHeader("options", $this->Lang("tab_options").'*');
    }

echo $this->EndTabHeaders();


// create Tab content
echo $this->StartTabContent();

    // All active User Guide tab content
    if ( is_array($pages) ) {
        foreach ($pages as $page) {
            if ($page->active && $managePermission >= $page->admin) {
                echo $this->StartTab('UGPage'.$page->id);
                $tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_user_guide_page.tpl'), null, null, $smarty );
                $tpl->assign( 'page',$page );
                $aspect = '16x9';   // default aspect ratio - could be selectable: 16x9,4x3,1x1,21x9
                $tpl->assign( 'aspect', $aspect );
                $tpl->assign( 'managePermission', $managePermission );
                $tpl->assign( 'separate_settings', $separate_settings );
                if ($useSmarty) {
                    try {
                        $smarty->display( 'eval:'.$tpl->fetch() );
                    } catch (Exception $e) {
                        // possible Smarty eval error
                        $tpl->assign( 'error', $this->Lang('smarty_error') );

                        $tpl->display(); // without eval
                    }
                } else {
                    $tpl->display();
                }
                echo $this->EndTab();
            }
        }
    }

    if ( $managePermission && !$separate_settings) {
        include(dirname(__FILE__).'/action.admin_settings.php');
    }


echo $this->EndTabContent();