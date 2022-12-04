<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Navigator (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  An module for CMS Made Simple to allow building hierarchical navigations.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
#$Id: News.module.php 2114 2005-11-04 21:51:13Z wishy $
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
?>
