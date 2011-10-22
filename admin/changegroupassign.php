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
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$group_id= - 1;
if (isset($_POST["group_id"])) $group_id = $_POST["group_id"];
else if (isset($_GET["group_id"])) $group_id = $_GET["group_id"];

$submitted = -1;
if (isset($_POST["submitted"])) $submitted = $_POST["submitted"];
else if (isset($_GET["submitted"])) $submitted = $_GET["submitted"];

$group_name="";

if (isset($_POST["cancel"])) {
	redirect("topusers.php".$urlext);
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Group Assignments');
if (!$access) {
	die('Permission Denied');
	return;
}
$gCms = cmsms();
$userops = $gCms->GetUserOperations();
$adminuser = ($userops->UserInGroup($userid,1) || $userid == 1);
$message = '';

include_once("header.php");

$db = $gCms->GetDb();


if( isset($_POST['filter']) )
  {
    $disp_group = $_POST['groupsel'];
    set_preference($userid,'changegroupassign_group',$disp_group);
  }
$disp_group = get_preference($userid,'changegroupassign_group',-1);

// always display the group pulldown
$gCms = cmsms();
$groupops = $gCms->GetGroupOperations();
$userops = $gCms->GetUserOperations();
$tmp = new stdClass();
$tmp->name = lang('all_groups');
$tmp->id=-1;
$allgroups = array($tmp);
$groups = array($tmp);
$group_list = $groupops->LoadGroups();
foreach( $group_list as $onegroup )
{
  if( $onegroup->id == 1 && $adminuser == false )
    {
      continue;
    }
  $allgroups[] = $onegroup;
  if( $disp_group == -1 || $disp_group == $onegroup->id )
    {
      $groups[] = $onegroup;
    }
}
$smarty->assign('group_list',$groups);
$smarty->assign('allgroups',$allgroups);

// because it's easier in PHP than Javascript:
$groupidlist = array();
foreach ($group_list as $thisGroup)
{
  $groupidlist[] = $thisGroup->id;
}
$smarty->assign('groupidlist',implode(',',$groupidlist));


if ($submitted == 1)
  {
    
    foreach($groups as $thisGroup)
      {
	if( $thisGroup->id <= 0 ) continue;

	// Send the ChangeGroupAssignPre event
	Events::SendEvent('Core', 'ChangeGroupAssignPre',
			  array('group' => $thisGroup,
				'users' => $userops->LoadUsersInGroup($thisGroup->id)));
	$query = "DELETE FROM ".cms_db_prefix()."user_groups WHERE group_id = ? AND user_id != ?";
	$result = $db->Execute($query, array($thisGroup->id,$userid));
	$iquery = "INSERT INTO ".cms_db_prefix().
	  "user_groups (group_id, user_id, create_date, modified_date) VALUES (?,?,?,?)";
	
	foreach ($_POST as $key=>$value)
	  {
	    if (strpos($key,"ug") == 0 && strpos($key,"ug") !== false)
	      {
		$keyparts = explode('_',$key);
		if ($keyparts[2] == $thisGroup->id && $value == '1')
		  {
		    $result = $db->Execute($iquery, array($thisGroup->id,
							  $keyparts[1],$db->DBTimeStamp(time()),$db->DBTimeStamp(time())));
		  }
	      }
	  }
	
	Events::SendEvent('Core', 'ChangeGroupAssignPost',
			  array('group' => $thisGroup,
				'users' => $userops->LoadUsersInGroup($thisGroup->id)));
	// put mention into the admin log
	audit($group_id, 'Assignment Group ID: '.$group_id, 'Changed');
      }

    // put mention into the admin log
	audit($userid, 'Assignment User ID: '.$userid, 'Changed');
    $message = lang('assignmentchanged');
    $gCms->clear_cached_files();
  }



$query = "SELECT u.user_id, u.username, ug.group_id FROM ".
  cms_db_prefix()."users u LEFT JOIN ".cms_db_prefix().
  "user_groups ug ON u.user_id = ug.user_id ORDER BY u.username";
$result = $db->Execute($query);

$user_struct = array();
while($result && $row = $result->FetchRow())
  {
    if (isset($user_struct[$row['user_id']]))
      {
	$str = &$user_struct[$row['user_id']];
	$str->group[$row['group_id']]=1;
      }
    else
      {
	$thisUser = new stdClass();
	$thisUser->group = array();
	if (!empty($row['group_id']))
	  {
	    $thisUser->group[$row['group_id']] = 1;
	  }
	$thisUser->id = $row['user_id'];
	$thisUser->name = $row['username'];
	$user_struct[$row['user_id']] = $thisUser;
      }
  }
$smarty->assign_by_ref('users',$user_struct);


if( $adminuser ) $smarty->assign('adminuser',1);
$smarty->assign('disp_group',$disp_group);
$smarty->assign('user_id',$userid);
$smarty->assign('cms_secure_param_name',CMS_SECURE_PARAM_NAME);
$smarty->assign('cms_user_key',$_SESSION[CMS_USER_KEY]);
$smarty->assign('admin_group_warning',$themeObject->ShowErrors(lang('adminspecialgroup')));
$smarty->assign('form_start','<form id="groupname" method="post" action="changegroupassign.php">');
$smarty->assign('filter_action','changegroupassign.php');
$smarty->assign('form_end','</form>');
$smarty->assign('apply',lang('apply'));
$smarty->assign('selectgroup',lang('selectgroup'));
$smarty->assign('title_user',lang('user'));
$smarty->assign('hidden','<input type="hidden" name="submitted" value="1" />');
$smarty->assign('submit','<input type="submit" name="changegrp" value="'.lang('submit').
	'" class="pagebutton" />');
$smarty->assign('cancel','<input type="submit" name="cancel" value="'.lang('cancel').
	'" class="pagebutton" />');


# begin output
if( !empty($message) )
  {
    echo $themeObject->ShowMessage($message);
  }
echo '<div class="pagecontainer">';
echo $themeObject->ShowHeader('groupassignments',array($group_name));
echo $smarty->fetch('changeusergroup.tpl');
echo '</div>';
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';


include_once("footer.php");

# vim:ts=4 sw=4 noet
?>

