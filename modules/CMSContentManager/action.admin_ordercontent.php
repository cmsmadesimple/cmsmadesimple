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
if( !$this->CheckPermission('Manage All Content') ) return;

if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab('pages');
}
if( isset($params['orderlist']) && $params['orderlist'] != '' ) {

    function ordercontent_get_node_rec($str,$prefix = 'page_')
    {
        $gCms = cmsms();
        $tree = $gCms->GetHierarchyManager();

        if( !is_numeric($str) && startswith($str,$prefix) ) $str = substr($str,strlen($prefix));

        $id = (int)$str;
        $tmp = $tree->find_by_tag('id',$id);
        $content = '';
        if( $tmp ) {
            $content = $tmp->getContent(false,true,true);
            if( $content ) {
                $rec = aray();
                $rec['id'] = (int)$str;
            }
        }
    }

    function ordercontent_create_flatlist($tree,$parent_id = -1)
    {
        $data = array();
        $cur_parent = null;
        $order = 1;
        foreach( $tree as &$node ) {
            if( is_string($node) ) {
                $pid = (int)substr($node,strlen('page_'));
                $cur_parent = $pid;
                $data[] = array('id'=>$pid,'parent_id'=>$parent_id,'order'=>$order++);
            }
            else if( is_array($node) ) {
                $data = array_merge($data,ordercontent_create_flatlist($node,$cur_parent));
            }
        }
        return $data;
    }

    $orderlist = json_decode($params['orderlist'],TRUE);

    // step 1, create a flat list of the content items, and their new orders, and new parents.
    $orderlist = ordercontent_create_flatlist($orderlist);

    // step 2, merge in old orders, and old parents
    $hm = $gCms->GetHierarchyManager();
    $changelist = array();
    foreach( $orderlist as &$rec ) {
        $node = $hm->find_by_tag('id',$rec['id']);
        $content = $node->getContent(FALSE,TRUE,TRUE);
        if( $content ) {
            $rec['old_parent'] = $content->ParentId();
            $rec['old_order'] = $content->ItemOrder();

            if( $rec['old_parent'] != $rec['parent_id'] || $rec['old_order'] != $rec['order'] ) $changelist[] = $rec;
        }
    }

    if( !is_array($changelist) || count($changelist) == 0 ) {
        echo $this->ShowErrors($this->Lang('error_ordercontent_nothingtodo'));
    }
    else {
        $query = 'UPDATE '.CMS_DB_PREFIX.'content SET item_order = ?, parent_id = ? WHERE content_id = ?';
        foreach( $changelist as $rec ) {
            $db->Execute($query,array($rec['order'],$rec['parent_id'],$rec['id']));
        }
        $contentops = $gCms->GetContentOperations();
        $contentops->SetAllHierarchyPositions();
        audit('',$this->GetName(),'Content pages dynamically reordered');
        $this->RedirectToAdminTab('pages');
    }
}


$tree = $gCms->GetHierarchyManager();
$smarty->assign('tree',$tree);
echo $this->ProcessTemplate('admin_ordercontent.tpl');

#
# EOF
#
?>