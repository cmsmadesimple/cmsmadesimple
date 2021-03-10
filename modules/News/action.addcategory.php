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

$parent = -1;
if( isset($params['parent'])) $parent = (int)$params['parent'];
if (isset($params['cancel'])) $this->RedirectToAdminTab('categories','','admin_settings');

$name = '';
if (isset($params['name'])) {
    //if( $parent == 0 ) $parent = -1;
    $name = trim($params['name']);
    if ($name != '') {
        $query = 'SELECT news_category_id FROM '.CMS_DB_PREFIX.'module_news_categories WHERE parent_id = ? AND news_category_name = ?';
        $tmp = $db->GetOne($query,array($parent,$name));
        if( $tmp ) {
            echo $this->ShowErrors($this->Lang('error_duplicatename'));
        }
        else {
            $query = 'SELECT max(item_order) FROM '.CMS_DB_PREFIX.'module_news_categories WHERE parent_id = ?';
            $item_order = (int)$db->GetOne($query,array($parent));
            $item_order++;

            $catid = $db->GenID(CMS_DB_PREFIX."module_news_categories_seq");

            $query = 'INSERT INTO '.CMS_DB_PREFIX.'module_news_categories (news_category_id, news_category_name, parent_id, item_order, create_date, modified_date) VALUES (?,?,?,?,NOW(),NOW())';
            $parms = array($catid,$name,$parent,$item_order);
            $db->Execute($query, $parms);

            news_admin_ops::UpdateHierarchyPositions();

            \CMSMS\HookManager::do_hook('News::NewsCategoryAdded', [ 'category_id'=>$catid, 'name'=>$name ] );
            // put mention into the admin log
            audit($catid, 'News category: '.$name, ' Category added');

            $this->SetMessage($this->Lang('categoryadded'));
            $this->RedirectToAdminTab('categories','','admin_settings');
        }
    }
    else {
        echo $this->ShowErrors($this->Lang('nonamegiven'));
    }
}

// Display template
$tmp = news_ops::get_category_list();
$tmp2 = array_flip($tmp);
$categories = array(-1=>$this->Lang('none'));
foreach( $tmp2 as $k => $v ) {
    $categories[$k] = $v;
}

$smarty->assign('parent',$parent);
$smarty->assign('name',$name);
$smarty->assign('categories',$categories);
$smarty->assign('startform', $this->CreateFormStart($id, 'addcategory', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('inputname', $this->CreateInputText($id, 'name', $name, 20, 255));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
$smarty->assign('mod',$this);

echo $this->ProcessTemplate('editcategory.tpl');

#
# EOF
#