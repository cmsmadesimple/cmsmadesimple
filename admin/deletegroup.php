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

$group_id = -1;
if (isset($_GET["group_id"]))
{
	$group_id = $_GET["group_id"];
	$group_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Groups');
	# you can't delete admin group (also admin group it's the first group)
	if ($access && $group_id != 1)
	{
		$result = false;

		global $gCms;
		$groupobj = cms_orm()->cms_group->find_by_id($group_id);
		$group_name = $groupobj->name;

		if ($groupobj)
		{
			$result = $groupobj->delete();
		}
	}
}

redirect("listgroups.php");

# vim:ts=4 sw=4 noet
?>
