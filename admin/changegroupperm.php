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

$submitted= - 1;
if (isset($_POST["submitted"])) $submitted = $_POST["submitted"];
else if (isset($_GET["submitted"])) $submitted = $_GET["submitted"];

if (isset($_POST["cancel"])) {
	redirect("topusers.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Permissions');
$group_name = '';
$message = '';

include_once("header.php");
global $gCms;
$db =& $gCms->GetDb();
$smarty =& $gCms->GetSmarty();

if (!$access) {
	die('permission denied');
}
else {
    // always display the group pull down
	global $gCms;
	$groupops =& $gCms->GetGroupOperations();
	$allgroups = new stdClass();
	$allgroups->name = lang('all_groups');
	$allgroups->id=-1;
   $groups = array($allgroups);

	$group_list = $groupops->LoadGroups();
	$groups = array_merge($groups,$group_list);
	$smarty->assign_by_ref('group_list',$groups);

	// because it's easier in PHP than Javascript:
	$groupidlist = array();
	foreach ($groups as $thisGroup)
		{
		array_push($groupidlist,$thisGroup->id);
		}
	$smarty->assign('groupidlist',implode(',',$groupidlist));
    if ($submitted == 1)
		{
      // we have group permissions
		$query = "DELETE FROM ".cms_db_prefix()."group_perms";
		$result = $db->Execute($query);
		$iquery = "INSERT INTO ".cms_db_prefix().
			"group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (?,?,?,?,?)";


		foreach ($_POST as $key=>$value)
			{
			if (strpos($key,"pg") == 0 && strpos($key,"pg") !== false)
				{
				$keyparts = explode('_',$key);
				if ($keyparts[2] != '1' && $value == '1')
					{
					$new_id = $db->GenID(cms_db_prefix()."group_perms_seq");
               $result = $db->Execute($iquery, array($new_id,$keyparts[2],$keyparts[1],
                        $db->DBTimeStamp(time()),$db->DBTimeStamp(time())));
					}
				}
			}

		audit($userid, 'Group ID', lang('permissionschanged'));
        $smarty->assign('message',lang('permissionschanged'));
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
$smarty->assign('admin_group_warning',$themeObject->ShowErrors(lang('adminspecialgroup')));
$smarty->assign('form_start','<form id="groupname" method="post" action="changegroupperm.php">');
$smarty->assign('form_end','</form>');
$smarty->assign('title_permission',lang('permission'));
$smarty->assign('selectgroup',lang('selectgroup'));
$smarty->assign('hidden','<input type="hidden" name="submitted" value="1" />');
$smarty->assign('submit','<input type="submit" name="changeperm" value="'.lang('submit').
	'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" />');
$smarty->assign('cancel','<input type="submit" name="cancel" value="'.lang('cancel').
	'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" />');


# begin output
echo '<div class="pagecontainer">'.$themeObject->ShowHeader('grouppermissions',array($group_name));
echo $smarty->fetch('changegroupperm.tpl');
echo '</div>';
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");
}
# vim:ts=4 sw=4 noet
?>
