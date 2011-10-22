<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
require_once("../lib/classes/class.group.inc.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$group_id = -1;
if (isset($_GET["group_id"]))
{
	$group_id = $_GET["group_id"];

	if( $group_id == 1 )
	  {
	    // can't delete this group
	    redirect("listgroups.php".$urlext);
	  }

	$group_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Groups');

	# you can't delete admin group (also admin group it's the first group)
	if (!$access)
	{
	    // no access
	    redirect("listgroups.php".$urlext);
	}

	$result = false;
	
	$gCms = cmsms();
	$groupops = $gCms->GetGroupOperations();
	$userops = $gCms->GetUserOperations();
	$groupobj = $groupops->LoadGroupByID($group_id);
	$group_name = $groupobj->name;
	
        # check to make sure we're not a member of this group
	if( $userops->UserInGroup($userid,$group_id) )
	  {
	    # can't delete a group we're a member of.
	    redirect("listgroups.php".$urlext);
	  }

	// now do the work.
	Events::SendEvent('Core', 'DeleteGroupPre', array('group' => &$groupobj));
	
	if ($groupobj)
	  {
	    $result = $groupobj->Delete();
	  }
	
	Events::SendEvent('Core', 'DeleteGroupPost', array('group' => &$groupobj));
	
	if ($result == true)
	  {
	    // put mention into the admin log
		audit($group_id, 'Admin User Group: '.$group_name, 'Deleted');
	  }
}

redirect("listgroups.php".$urlext);

# vim:ts=4 sw=4 noet
?>
