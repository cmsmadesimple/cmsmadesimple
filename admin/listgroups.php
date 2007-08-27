<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

//$grouplist = cms_orm()->cms_group->find_all(array('order' => 'name'));

//
// PROCESS Any GET or POST results here
//
$page = 1;
if (isset($_GET['page'])) $page = $_GET['page'];

// 
// Begin Output
//
include_once("header.php");
$smarty = cms_smarty();
$smarty->assign('header_name',$themeObject->ShowHeader('currentgroups'));
local_setup_smarty( $themeObject, $page );
//$smarty->assign('group_list',$smarty->fetch('listgroups-list.tpl'));
$smarty->display('listgroups.tpl');
include_once("footer.php");

function local_setup_smarty( &$themeObject, $page )
{
  $grouplist = CmsGroupOperations::load_groups();
  $smarty = cms_smarty();

  $userid = get_userid();
  $perm = check_permission($userid, 'Modify Permissions');
  $assign = check_permission($userid, 'Modify Group Assignments');
  $edit = check_permission($userid, 'Modify Groups');
  $remove = check_permission($userid, 'Remove Groups');

  // Set permissions to smarty
  $smarty->assign('modify_permissions',$perm);
  $smarty->assign('modify_group_assignments',$assign);
  $smarty->assign('modify_groups',$edit);
  $smarty->assign('remove_groups',$remove);

  $limit = get_preference($userid,'pagelimit',20);
  $smarty->assign('pagination',$page,count($grouplist),$limit);
  $smarty->assign('pagelimit',$limit);
  $smarty->assign('page',$page);
  $smarty->assign('groups',$grouplist);
}

# vim:ts=4 sw=4 noet
?>
