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

$group= "";
if (isset($_POST["group"])) $group = $_POST["group"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addgroup"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Add Group');

if ($access)
{
	if (isset($_POST["addgroup"]))
	{
		$validinfo = true;

		if ($group == "")
		{
			$error .= "<li>".lang('nofieldgiven', array('addgroup'))."</li>";
			$validinfo = false;
		}

		if ($validinfo)
		{
			$groupobj = new Group();
			$groupobj->name = $group;
			$groupobj->active = $active;

			#Perform the addgroup_pre callback
			foreach($gCms->modules as $key=>$value)
			{
				if (isset($gCms->modules[$key]['addgroup_pre_function']) &&
					$gCms->modules[$key]['Installed'] == true &&
					$gCms->modules[$key]['Active'] == true)
				{
					call_user_func_array($gCms->modules[$key]['addgroup_pre_function'], array(&$gCms, &$groupobj));
				}
			}

			$result = $groupobj->save();

			if ($result)
			{
				#Perform the addgroup_post callback
				foreach($gCms->modules as $key=>$value)
				{
					if (isset($gCms->modules[$key]['addgroup_post_function']) &&
						$gCms->modules[$key]['Installed'] == true &&
						$gCms->modules[$key]['Active'] == true)
					{
						call_user_func_array($gCms->modules[$key]['addgroup_post_function'], array(&$gCms, &$groupobj));
					}
				}
				audit($groupobj->id, $groupobj->name, 'Added Group');
				redirect("listgroups.php");
				return;
			}
			else
			{
				$error .= "<li>".lang('errorinsertinggroup')."</li>";
			}
		}
	}
}

include_once("header.php");

if (!$access)
{
	print "<h3>".lang('noaccessto', array(lang('addgroup')))."</h3>";
}
else
{
	if ($error != "")
	{
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="addgroup.php">

<div class="adminformSmall">

<h3><?php echo lang('addgroup')?></h3>

<table border="0">

	<tr>
		<td>*<?php echo lang('name')?>:</td>
		<td><input type="text" name="group" maxlength="255" value="<?php echo $group?>" class="standard" /></td>
	</tr>
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addgroup" value="true" />
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>

</table>

</div>

</form>

<?php
}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
