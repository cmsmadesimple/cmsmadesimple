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
if (!$this->CheckPermission('Modify Site Preferences')) return;

$catid = '';
if (isset($params['catid'])) $catid = $params['catid'];

// Get the category details
$query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news_categories
           WHERE news_category_id = ?';
$row = $db->GetRow( $query, array( $catid ) );

//Reset all categories using this parent to have no parent (-1)
$query = 'UPDATE '.CMS_DB_PREFIX.'module_news_categories SET parent_id=?, modified_date='.$db->DBTimeStamp(time()).' WHERE parent_id=?';
$db->Execute($query, array(-1, $catid));

//Now remove the category
$query = "DELETE FROM ".CMS_DB_PREFIX."module_news_categories WHERE news_category_id = ?";
$db->Execute($query, array($catid));

//And remove it from any articles
$query = "UPDATE ".CMS_DB_PREFIX."module_news SET news_category_id = -1 WHERE news_category_id = ?";
$db->Execute($query, array($catid));

\CMSMS\HookManager::do_hook('News::NewsCategoryDeleted', [ 'category_id'=>$catid, 'name'=>$row['news_category_name'] ] );
audit($catid, 'News category: '.$catid, ' Category deleted');

news_admin_ops::UpdateHierarchyPositions();
$params = array('tab_message'=> 'categorydeleted', 'active_tab' => 'categories');
$this->Setmessage($this->Lang('categorydeleted'));
$this->RedirectToAdminTab('categories','','admin_settings');

#
# EOF
#