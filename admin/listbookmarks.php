<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
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
$page = 1;
	if (isset($_GET['page'])) $page = $_GET['page'];
$limit = 2; 
	
include_once("header.php");
$smarty = cms_smarty();
$userid = get_userid();
	
	$marklist = cmsms()->bookmark->find_all_by_user_id($userid);
    $smarty->assign('marklist', $marklist) ;
	
	if (count($marklist) > $limit)
	{
		$smarty->assign('pagination', pagination($page, count($marklist), $limit));
	}
	    $smarty->assign('header_name', $themeObject->ShowHeader('bookmarks'));


		    $counter=0;
			$smarty->assign('counter', $counter);
			$smarty->assign('page', $page);
            $smarty->assign('limit', $limit);

$smarty->display('listbookmarks.tpl');

include_once("footer.php");
# vim:ts=4 sw=4 noet
?>

