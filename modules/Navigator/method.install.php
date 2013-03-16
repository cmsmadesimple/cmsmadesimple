<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Navigator (c) 2013 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  An module for CMS Made Simple to allow building hierarchical navigations.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
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
  $menu_template_type->reset_content_to_factory();
  $menu_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  return FALSE;
}

try {
  $fn = cms_join_path(dirname(__FILE__),'templates','simple_navigation.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name('Simple Navigation');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($menu_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  return FALSE;
}

// register plugins
$this->RegisterModulePlugin(true);
$this->RegisterSmartyPlugin('nav_breadcrumbs','function','nav_breadcrumbs');

#
# EOF
#
?>