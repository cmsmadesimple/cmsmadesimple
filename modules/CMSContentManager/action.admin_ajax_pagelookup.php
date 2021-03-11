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

if( !isset($gCms) ) exit;
if( !$this->CanEditContent() ) exit;

$out = array();

if( isset($_REQUEST['term']) ) {
  // find all pages with this text...
  // that this user can edit.
  $term = trim(strip_tags($_REQUEST['term']));

  $pref = $this->GetPreference('list_namecolumn','title');
  $field = 'content_name';
  if( $pref != 'title' ) $field = 'menu_text';

  $query = 'SELECT content_id,hierarchy,'.$field.' FROM '.CMS_DB_PREFIX.'content WHERE '.$field.' LIKE ?';
  $parms = array('%'.$term.'%');

  if( !$this->CheckPermission('Manage All Content') && !$this->CheckPermission('Modify Any Page') ) {
    $pages = author_pages(get_userid(FALSE));
    if( count($pages) == 0 ) return;

    // query only these pages.
    $query .= ' AND content_id IN ('.implode(',',$pages).')';
  }

  $list = $db->GetArray($query,$parms);
  if( is_array($list) && count($list) ) {
    $builder = new ContentListBuilder($this);
    $builder->expand_all(); // it'd be cool to open all parents to each item.
    $contentops = ContentOperations::get_instance();
    foreach( $list as $row ) {
      $label = $contentops->CreateFriendlyHierarchyPosition($row['hierarchy']);
      $label = $row[$field]." ({$label})";
      $out[] = array('label'=>$label,'value'=>$row['content_id']);
    }
  }
}

echo json_encode($out);
exit;

#
# EOF
#