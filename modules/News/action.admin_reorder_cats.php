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
if( !$this->CheckPermission('Modify Site Preferences') ) return;

$this->SetCurrentTab('categories');

function news_reordercats_create_flatlist($tree,$parent_id = -1)
{
  $data = array();
  $order = 1;
  foreach( $tree as &$node ) {
    if( is_array($node) && count($node) == 2 ) {
      $pid = substr($node[0],strlen('cat_'));
      $data[] = array('id'=>$pid,'parent_id'=>$parent_id,'order'=>$order);
      if( isset($node[1]) && is_array($node[1]) && count($node[1]) > 0 ) {
	$tmp = news_reordercats_create_flatlist($node[1],$pid);
	if( is_array($tmp) && count($tmp) ) $data = array_merge($data,$tmp);
      }
    }
    else {
      $pid = substr($node,strlen('cat_'));
      $data[] = array('id'=>$pid,'parent_id'=>$parent_id,'order'=>$order);
    }
    $order++;
  }
  return $data;
}

if( isset($params['cancel']) ) {
    $this->RedirectToAdminTab('','','admin_settings');
}
else if( isset($params['submit']) ) {
  $data = json_decode($params['data']);
  $flat = news_reordercats_create_flatlist($data);
  if( is_array($flat) && count($flat) ) {
    $query = 'UPDATE '.CMS_DB_PREFIX.'module_news_categories SET parent_id = ?, item_order = ?
              WHERE news_category_id = ?';
    foreach( $flat as $rec ) {
      $dbr = $db->Execute($query,array($rec['parent_id'],$rec['order'],$rec['id']));
    }
    news_admin_ops::UpdateHierarchyPositions();
    $this->SetMessage($this->Lang('msg_categoriesreordered'));
    $this->RedirectToAdminTab('','','admin_settings');
  }
}


$query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news_categories ORDER BY hierarchy';
$allcats = $db->GetArray($query);


$smarty->assign('allcats',$allcats);
echo $this->ProcessTemplate('admin_reorder_cats.tpl');

#
# EOF
#