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
	
// Put together a list of current categories...
$entryarray = array();
	
$query = "SELECT * FROM ".CMS_DB_PREFIX."module_news_categories ORDER BY hierarchy";
$dbresult = $db->Execute($query);
$rowclass = 'row1';
$admintheme = cms_utils::get_theme_object();
	
while ($dbresult && $row = $dbresult->FetchRow()) {
  $onerow = new stdClass();
  $depth = count(preg_split('/\./', $row['hierarchy']));
  $onerow->id = $row['news_category_id'];
  $onerow->depth = $depth - 1;
  $onerow->edit_url = $this->create_url($id,'editcategory',$returnid,array('catid'=>$row['news_category_id']));
  $onerow->name = $row['news_category_name'];
  $onerow->editlink = $this->CreateLink($id, 'editcategory', $returnid, $admintheme->DisplayImage('icons/system/edit.gif', $this->Lang('edit'),'','','systemicon'), array('catid'=>$row['news_category_id']));
  $onerow->delete_url = $this->create_url($id,'deletecategory',$returnid,
					  array('catid'=>$row['news_category_id']));
  $onerow->deletelink = $this->CreateLink($id, 'deletecategory', $returnid, $admintheme->DisplayImage('icons/system/delete.gif', $this->Lang('delete'),'','','systemicon'), array('catid'=>$row['news_category_id']), $this->Lang('areyousure'));
  $onerow->rowclass = $rowclass;

  $entryarray[] = $onerow;
  ($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
}
	
$smarty->assign('items', $entryarray);
$smarty->assign('itemcount', count($entryarray));
	
// Setup links
$smarty->assign('categorytext', $this->Lang('category'));
	
// Display template
echo $this->ProcessTemplate('categorylist.tpl');
	
#
# EOF
#