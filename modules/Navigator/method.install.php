<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

if (!isset($gCms)) exit;

$uid = null;
if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
    $uid = 1; // hardcode to first user
} else {
    $uid = get_userid();
}

try {
    $menu_template_type = new CmsLayoutTemplateType();
    $menu_template_type->set_originator($this->GetName());
    $menu_template_type->set_name('navigation');
    $menu_template_type->set_dflt_flag(TRUE);
    $menu_template_type->set_lang_callback('Navigator::page_type_lang_callback');
    $menu_template_type->set_content_callback('Navigator::reset_page_type_defaults');
    $menu_template_type->set_help_callback('Navigator::template_help_callback');
    $menu_template_type->reset_content_to_factory();
    $menu_template_type->save();
}
catch( CmsException $e ) {
    // log it
    debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
    return $e->GetMessage();
}

try {
    $bc_template_type = new CmsLayoutTemplateType();
    $bc_template_type->set_originator($this->GetName());
    $bc_template_type->set_name('breadcrumbs');
    $bc_template_type->set_dflt_flag(TRUE);
    $bc_template_type->set_lang_callback('Navigator::page_type_lang_callback');
    $bc_template_type->set_content_callback('Navigator::reset_page_type_defaults');
    $bc_template_type->set_help_callback('Navigator::template_help_callback');
    $bc_template_type->reset_content_to_factory();
    $bc_template_type->save();
}
catch( CmsException $e ) {
    // log it
    debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
    return $e->GetMessage();
}

try {
    $fn = cms_join_path(dirname(__FILE__),'templates','simple_navigation.tpl');
    if( file_exists( $fn ) ) {
        $template = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name(\CmsLayoutTemplate::generate_unique_name('Simple Navigation'));
        $tpl->set_owner($uid);
        $tpl->set_content($template);
        $tpl->set_type($menu_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }

    $fn = cms_join_path(dirname(__FILE__),'templates','dflt_breadcrumbs.tpl');
    if( file_exists( $fn ) ) {
        $template = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name(\CmsLayoutTemplate::generate_unique_name('Breadcrumbs'));
        $tpl->set_owner($uid);
        $tpl->set_content($template);
        $tpl->set_type($bc_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }

    $fn = cms_join_path(dirname(__FILE__),'templates','cssmenu.tpl');
    if( file_exists( $fn ) ) {
        $template = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name(CmsLayoutTemplate::generate_unique_name('cssmenu'));
        $tpl->set_owner($uid);
        $tpl->set_content($template);
        $tpl->set_type($menu_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }

    $fn = cms_join_path(dirname(__FILE__),'templates','cssmenu_ulshadow.tpl');
    if( file_exists( $fn ) ) {
        $template = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name(CmsLayoutTemplate::generate_unique_name('cssmenu_ulshadow'));
        $tpl->set_owner($uid);
        $tpl->set_content($template);
        $tpl->set_type($menu_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }

    $fn = cms_join_path(dirname(__FILE__),'templates','minimal_menu.tpl');
    if( file_exists( $fn ) ) {
        $template = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name(CmsLayoutTemplate::generate_unique_name('minimal_menu'));
        $tpl->set_owner($uid);
        $tpl->set_content($template);
        $tpl->set_type($menu_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }

    try {
        $simplex = CmsLayoutCollection::load('Simplex');

        $fn = cms_join_path(dirname(__FILE__),'templates','Simplex_Main_Navigation.tpl');
        if( file_exists( $fn ) ) {
            $template = @file_get_contents($fn);
            $tpl = new CmsLayoutTemplate();
            $tpl->set_name(CmsLayoutTemplate::generate_unique_name('Simplex Main Navigation'));
            $tpl->set_owner($uid);
            $tpl->set_content($template);
            $tpl->set_type($menu_template_type);
            $tpl->set_type_dflt(TRUE);
            $tpl->add_design($simplex);
            $tpl->save();
        }

        $fn = cms_join_path(dirname(__FILE__),'templates','Simplex_Footer_Navigation.tpl');
        if( file_exists( $fn ) ) {
            $template = @file_get_contents($fn);
            $tpl = new CmsLayoutTemplate();
            $tpl->set_name(CmsLayoutTemplate::generate_unique_name('Simplex Footer Navigation'));
            $tpl->set_owner($uid);
            $tpl->set_content($template);
            $tpl->set_type($menu_template_type);
            $tpl->set_type_dflt(TRUE);
            $tpl->add_design($simplex);
            $tpl->save();
        }
    }
    catch( \Exception $e ) {
        // if we got here, it's prolly because default content was not installed.
        audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
    }
}
catch( \Exception $e ) {
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  return $e->GetMessage();
}

// register plugins
$this->RegisterModulePlugin(true);
$this->RegisterSmartyPlugin('nav_breadcrumbs','function','nav_breadcrumbs');

#
# EOF
#