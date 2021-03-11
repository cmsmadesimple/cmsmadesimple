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
$db = $this->GetDb();

$uid = 1;

if( version_compare($oldversion,'1.50') < 1 ) {
  $this->RegisterModulePlugin(true);
  $this->RegisterSmartyPlugin('search','function','function_plugin');

  try {
      try {
          $searchform_type = new CmsLayoutTemplateType();
          $searchform_type->set_originator($this->GetName());
          $searchform_type->set_name('searchform');
          $searchform_type->set_dflt_flag(TRUE);
          $searchform_type->set_lang_callback('Search::page_type_lang_callback');
          $searchform_type->set_content_callback('Search::reset_page_type_defaults');
          $searchform_type->reset_content_to_factory();
          $searchform_type->save();
      }
      catch( \CmsInvalidDataException $e ) {
          // ignore this error.
      }

      $template = $this->GetTemplate('displaysearch');
      if( $template ) {
          $tpl = new CmsLayoutTemplate();
          $tpl->set_name('Search Form Sample');
          $tpl->set_owner($uid);
          $tpl->set_content($template);
          $tpl->set_type($searchform_type);
          $tpl->set_type_dflt(TRUE);
          $tpl->save();
          $this->DeleteTemplate('displaysearch');
      }

      try {
          $searchresults_type = new CmsLayoutTemplateType();
          $searchresults_type->set_originator($this->GetName());
          $searchresults_type->set_name('searchresults');
          $searchresults_type->set_dflt_flag(TRUE);
          $searchresults_type->set_lang_callback('Search::page_type_lang_callback');
          $searchresults_type->set_content_callback('Search::reset_page_type_defaults');
          $searchresults_type->reset_content_to_factory();
          $searchresults_type->save();
      }
      catch( \CmsInvalidDataException $e ) {
          // ignore this error.
      }

      $template = $this->GetTemplate('displayresult');
      if( $template ) {
          $tpl = new CmsLayoutTemplate();
          $tpl->set_name('Search Results Sample');
          $tpl->set_owner($uid);
          $tpl->set_content($template);
          $tpl->set_type($searchresults_type);
          $tpl->set_type_dflt(TRUE);
          $tpl->save();
          $this->DeleteTemplate('displayresult');
      }
  }
  catch( CmsException $e ) {
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  }
}

if( version_compare($oldversion,'1.51') < 0 ) {
    $tables = array(CMS_DB_PREFIX.'module_search_items',CMS_DB_PREFIX.'module_search_index',CMS_DB_PREFIX.'module_search_words');
    $sql_i = "ALTER TABLE %s ENGINE=InnoDB";
    foreach( $tables as $table ) {
        $db->Execute(sprintf($sql_i,$table));
    }
}

#
# EOF
#