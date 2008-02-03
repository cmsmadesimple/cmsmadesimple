<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

//
// PROCESS Any GET or POST results here
//
$page = 1;
if (isset($_GET['page'])) $page = $_GET['page'];

//Hadle changing an active flag
//TODO: Check permission
if (isset($_REQUEST['makeactive']) && isset($_REQUEST['stylesheet_id']))
{
	$ss = cms_orm()->cms_stylesheet->find_by_id($_REQUEST['stylesheet_id']);
	$ss->active = $_REQUEST['makeactive'];
	$ss->save();
}

// 
// Begin Output
//
include_once("header.php");

$smarty = cms_smarty();
$smarty->assign('header_name',$themeObject->ShowHeader('currentstylesheets'));
local_setup_smarty($page);
$smarty->display('liststylesheets.tpl');

include_once("footer.php");

function local_setup_smarty($page)
{
	$smarty = cms_smarty();
	$userid = get_userid();
	
	$stylesheetlist = CmsStylesheetOperations::load_stylesheets();
	$smarty->assign_by_ref('stylesheets', $stylesheetlist);
	
	$smarty->assign('modify_layout', true);

	$limit = get_preference($userid, 'pagelimit', 20);
	$smarty->assign('pagination', $page,count($stylesheetlist),$limit);
	$smarty->assign('pagelimit', $limit);
	$smarty->assign('page', $page);
	$smarty->assign('stylesheets', $stylesheetlist);
}

# vim:ts=4 sw=4 noet
?>
