<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Nav (c) 2013 by Robert Campbell 
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

final class Nav extends CMSModule
{
  function GetName() { return 'Nav'; }
  function GetFriendlyName() { return $this->Lang('friendlyname'); }
  function IsPluginModule() { return true; }
  function HasAdmin() { return false; }
  function GetVersion() { return '1.0'; }
  function MinimumCMSVersion() { return '1.12-alpha0'; }
  function GetAdminDescription() { return $this->Lang('description'); }
  function GetAdminSection() { return 'layout'; }
  function LazyLoadFrontend() { return TRUE; }
  function LazyLoadAdmin() { return TRUE; }
  function GetHelp($lang='en_US') { return $this->Lang('help'); }
  function GetAuthor() { return 'Robert Campbell'; }
  function GetAuthorEmail() { return 'calguy1000@cmsmadesimple.org'; }
  function GetChangeLog() { return file_get_contents(dirname(__FILE__).'/changelog.inc'); } 

  public function InitializeFrontend()
  {
    $this->RestrictUnknownParams();
    $this->SetParameterType('items',CLEAN_STRING);
    $this->SetParameterType('number_of_levels',CLEAN_INT);
    $this->SetParameterType('show_all',CLEAN_INT);
    $this->SetParameterType('show_root_siblings',CLEAN_INT);
    $this->SetParameterType('start_element',CLEAN_STRING); // yeah, it's a string
    $this->SetParameterType('start_page',CLEAN_STRING); 
    $this->SetParameterType('template',CLEAN_STRING); 
    $this->SetParameterType('childrenof',CLEAN_STRING);
  }

  public function InitializeAdmin()
  {
    $this->CreateParameter('items', 'contact,home', $this->lang('help_items'));
    $this->CreateParameter('number_of_levels', '1', $this->lang('help_number_of_levels'));
    $this->CreateParameter('show_all', '0', $this->lang('help_show_all'));
    $this->CreateParameter('show_root_siblings', '1', $this->lang('help_show_root_siblings'));
    $this->CreateParameter('start_element', '1.2', $this->lang('help_start_element'));
    $this->CreateParameter('start_page', 'home', $this->lang('help_start_page'));
    $this->CreateParameter('template', '', $this->lang('help_template'));
    $this->CreateParameter('childrenof','',$this->Lang('help_childrenof'));
    $this->CreateParameter('action','',$this->Lang('help_action'));
  }

  final static public function smarty_cms_breadcrumbs($params,&$smarty)
  {
    $params['action'] = 'breadcrumbs';
    $params['module'] = 'Nav';
    return cms_module_plugin($params,$smarty);
  }

  final public static function page_type_lang_callback($str)
  {
    $mod = cms_utils::get_module('Nav');
    if( is_object($mod) ) return $mod->Lang('type_'.$str);
  }

  public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
  {
    if( $type->get_originator() != 'Nav' ) {
      throw new CmsLogicException('Cannot reset contents for this template type');
    }

    $fn = null;
    switch( $type->get_name() ) {
    case 'navigation':
      $fn = 'simple_navigation.tpl';
      break;
    }

    $fn = cms_join_path(dirname(__FILE__),'templates',$fn);
    if( file_exists($fn) ) {
      return @file_get_contents($fn);
    }
  }
} // End of class

# vim:ts=4 sw=4 noet
?>