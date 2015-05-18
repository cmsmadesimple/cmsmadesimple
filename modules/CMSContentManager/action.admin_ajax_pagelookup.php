<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Content (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A module for managing content in CMSMS.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
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
?>