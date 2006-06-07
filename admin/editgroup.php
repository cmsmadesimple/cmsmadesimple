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
require_once("../lib/classes/class.group.inc.php");

check_login();

$error = "";

$dropdown = "";

$group = "";
if (isset($_POST["group"])) $group = $_POST["group"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["editgroup"])) $active = 0;

$group_id = -1;
if (isset($_POST["group_id"])) $group_id = $_POST["group_id"];
else if (isset($_GET["group_id"])) $group_id = $_GET["group_id"];

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Groups');

if ($access) {

	if (isset($_POST["editgroup"]))
	{
		$validinfo = true;
		if ($group == "")
		{
			$validinfo = false;
			$error .= "<li>".lang('nofieldgiven', array(lang('name')))."</li>";
		}

		if ($validinfo)
		{
			$groupobj = new Group();
			$groupobj->id = $group_id;
			$groupobj->name = $group;
			$groupobj->active = $active;

			#Perform the editgroup_pre callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->EditGroupPre($groupobj);
				}
			}
			
			Events::SendEvent('Core', 'EditGroupPre', array(&$groupobj));

			$result = $groupobj->save();

			if ($result)
			{
				#Perform the editgroup_post callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->EditGroupPost($groupobj);
					}
				}
				
				Events::SendEvent('Core', 'EditGroupPost', array(&$groupobj));

				audit($groupobj->id, $groupobj->name, 'Edited Group');
				redirect("listgroups.php");
				return;
			}
			else {
				$error .= "<li>".lang('errorupdatinggroup')."</li>";
			}
		}

	}
	else if ($group_id != -1) {

		$query = "SELECT * from ".cms_db_prefix()."groups WHERE group_id = ?";
		$result = $db->Execute($query, array($group_id));
		
		$row = $result->FetchRow();

		$group = $row["group_name"];
		$active = $row["active"];
	}
}
if (strlen($group) > 0)
    {
    $CMS_ADMIN_SUBTITLE = $group;
    }
include_once("header.php");

if (!$access) {
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('editgroup')))."</p></div>";
}
else {
	if ($error != "") {
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";	
	}
?>

<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('editgroup'); ?>
	<form method="post" action="editgroup.php">
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" name="group" maxlength="25" value="<?php echo $group?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('active')?>:</p>
			<p class="pageinput"><input type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="group_id" value="<?php echo $group_id?>" /><input type="hidden" name="editgroup" value="true" />
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	</form>
</div>
<?php

}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
