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
#require_once("../lib/classes/class.group.inc.php");

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
$access = check_permission($userid, 'Add Groups');

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
			$groupobj = new CmsGroup();
			$groupobj->name = $group;
			$groupobj->active = $active;

			$result = $groupobj->save();

			if ($result)
			{
				redirect("listgroups.php");
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
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('addgroup')))."</p></div>";
}
else
{
	if ($error != "")
	{
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";
	}
?>

	<?php echo $themeObject->ShowHeader('addgroup'); ?>
	<form method="post" action="addgroup.php">
		<div class="row">
			<label>*<?php echo lang('name')?>:</label>
			<input type="text" name="group" maxlength="255" value="<?php echo $group?>" />
		</div>
		<div class="row">
			<label><?php echo lang('active')?>:</label>
			<input class="checkbox" type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> />
		</div>
		<input type="hidden" name="addgroup" value="true" />
		<div class="submitrow">
			<button class="positive disabled" name="submitbutton" type="submit" disabled=""><?php echo lang('submit')?></button>
			<button class="negative" name="cancel" type="submit"><?php echo lang('cancel')?></button>
		</div>	
	</form>

<?php
}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
