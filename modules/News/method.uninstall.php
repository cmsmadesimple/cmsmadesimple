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
$this->DeleteTemplate('displaysummary');
$this->DeleteTemplate('displaydetail');

$dict = NewDataDictionary( $db );

$sqlarray = $dict->DropTableSQL( CMS_DB_PREFIX."module_news" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( CMS_DB_PREFIX."module_news_categories" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( CMS_DB_PREFIX."module_news_fielddefs" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( CMS_DB_PREFIX."module_news_fieldvals" );
$dict->ExecuteSQLArray($sqlarray);

$db->DropSequence( CMS_DB_PREFIX."module_news_seq" );
$db->DropSequence( CMS_DB_PREFIX."module_news_categories_seq" );

$this->RemovePermission('Modify News');
$this->RemovePermission('Approve News');
$this->RemovePermission('Delete News');

// Remove all preferences for this module
$this->RemovePreference();

// And all Templates
$this->DeleteTemplate();

#Setup events
$this->RemoveEvent('NewsArticleAdded');
$this->RemoveEvent('NewsArticleEdited');
$this->RemoveEvent('NewsArticleDeleted');
$this->RemoveEvent('NewsCategoryAdded');
$this->RemoveEvent('NewsCategoryEdited');
$this->RemoveEvent('NewsCategoryDeleted');

$this->RemoveSmartyPlugin();

cms_route_manager::del_static('',$this->GetName());

// remove templates
// and template types.
try {
  $types = CmsLayoutTemplateType::load_all_by_originator($this->GetName());
  if( is_array($types) && count($types) ) {
    foreach( $types as $type ) {
      $templates = $type->get_template_list();
      if( is_array($templates) && count($templates) ) {
	foreach( $templates as $template ) {
	  $template->delete();
	}
      }
      $type->delete();
    }
  }
}
catch( Exception $e ) {
  // log it
  audit('',$this->GetName(),'Uninstall Error: '.$e->GetMessage());
}

#
# EOF
#