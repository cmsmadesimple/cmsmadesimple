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
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$submitted= - 1;
if (isset($_POST["submitted"])) $submitted = $_POST["submitted"];
else if (isset($_GET["submitted"])) $submitted = $_GET["submitted"];

if (isset($_POST["cancel"])) {
	redirect("topusers.php".$urlext);
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Permissions');
if (!$access) {
	die('Permission Denied');
	return;
}
$userops =& $gCms->GetUserOperations();
$adminuser = ($userops->UserInGroup($userid,1) || $userid == 1);
$group_name = '';
$message = '';

include_once("header.php");
global $gCms;
$db =& $gCms->GetDb();
$smarty =& $gCms->GetSmarty();

if (!$access) {
	die('permission denied');
}

if( isset($_POST['filter']) )
  {
    $disp_group = $_POST['groupsel'];
    set_preference($userid,'changegroupassign_group',$disp_group);
  }
$disp_group = get_preference($userid,'changegroupassign_group',-1);

// always display the group pull down
global $gCms;
$groupops =& $gCms->GetGroupOperations();
$tmp = new stdClass();
$tmp->name = lang('all_groups');
$tmp->id=-1;
$allgroups = array($tmp);
$sel_groups = array($tmp);
$group_list = $groupops->LoadGroups();
$sel_group_ids = array();
foreach( $group_list as $onegroup )
{
  if( $onegroup->id == 1 && $adminuser == false )
    {
      continue;
    }
  $allgroups[] = $onegroup;
  if( $disp_group == -1 || $disp_group == $onegroup->id )
    {
      $sel_groups[] = $onegroup;
      $sel_group_ids[] = $onegroup->id;
    }
}

$smarty->assign('group_list',$sel_groups);
$smarty->assign('allgroups',$allgroups);

if ($submitted == 1)
  {
    // we have group permissions
    $now = $db->DbTimeStamp(time());
    $iquery = "INSERT INTO ".cms_db_prefix().
      "group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) 
       VALUES (?,?,?,$now,$now)";

    $groups = array();
    foreach( $_POST as $key=>$value )
      {
	if (strpos($key,"pg") == 0 && strpos($key,"pg") !== false)
	  {
	    $keyparts = explode('_',$key);
	    if ($keyparts[2] != '1' && $value == '1')
	      {
		if( !in_array($keyparts[2],$groups) )
		  {
		    $groups[] = $keyparts[2];
		  }
	      }
	  }
      }

    $selected_groups = unserialize(base64_decode($_POST['sel_groups']));
    $query = 'DELETE FROM '.cms_db_prefix().'group_perms 
               WHERE group_id IN ('.implode(',',$selected_groups).')';
    $db->Execute($query);
    
    foreach ($_POST as $key=>$value)
      {
	if (strpos($key,"pg") == 0 && strpos($key,"pg") !== false)
	  {
	    $keyparts = explode('_',$key);
	    if ($keyparts[2] != '1' && $value == '1')
	      {
		$new_id = $db->GenID(cms_db_prefix()."group_perms_seq");
		$result = $db->Execute($iquery, array($new_id,$keyparts[2],$keyparts[1]));
		if( !$result )
		  {
		    echo "FATAL: ".$db->ErrorMsg().'<br/>'.$db->sql; exit();
		  }
	      }
	  }
      }
    
    audit($userid, 'Group ID', lang('permissionschanged'));
    $message = lang('permissionschanged');
  }

$query = "SELECT p.permission_id, p.permission_text, up.group_id FROM ".
  cms_db_prefix()."permissions p LEFT JOIN ".cms_db_prefix().
  "group_perms up ON p.permission_id = up.permission_id ORDER BY p.permission_name";

$result = $db->Execute($query);

$perm_struct = array();
while($result && $row = $result->FetchRow())
  {
    if (isset($perm_struct[$row['permission_id']]))
      {
	$str = &$perm_struct[$row['permission_id']];
	$str->group[$row['group_id']]=1;
      }
    else
      {
	$thisPerm = new stdClass();
	$thisPerm->group = array();
	if (!empty($row['group_id']))
	  {
	    $thisPerm->group[$row['group_id']] = 1;
	  }
	$thisPerm->id = $row['permission_id'];
	$thisPerm->name = $row['permission_text'];
	$perm_struct[$row['permission_id']] = $thisPerm;
      }
  }
$smarty->assign_by_ref('perms',$perm_struct);		
$smarty->assign('cms_secure_param_name',CMS_SECURE_PARAM_NAME);
$smarty->assign('cms_user_key',$_SESSION[CMS_USER_KEY]);
$smarty->assign('admin_group_warning',$themeObject->ShowErrors(lang('adminspecialgroup')));
$smarty->assign('form_start','<form id="groupname" method="post" action="changegroupperm.php">');
$smarty->assign('form_end','</form>');
$smarty->assign('disp_group',$disp_group);
$smarty->assign('apply',lang('apply'));
$smarty->assign('title_permission',lang('permission'));
$smarty->assign('selectgroup',lang('selectgroup'));
$smarty->assign('hidden2','<input type="hidden" name="sel_groups" value="'.base64_encode(serialize($sel_group_ids)).'"/>');
$smarty->assign('hidden','<input type="hidden" name="submitted" value="1" />');
$smarty->assign('submit','<input type="submit" name="changeperm" value="'.lang('submit').
	'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" />');
$smarty->assign('cancel','<input type="submit" name="cancel" value="'.lang('cancel').
	'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" />');


# begin output
if( !empty($message) )
  {
    echo $themeObject->ShowMessage($message);
  }
echo '<div class="pagecontainer">'.$themeObject->ShowHeader('grouppermissions',array($group_name));
echo $smarty->fetch('changegroupperm.tpl');
echo '</div>';
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
