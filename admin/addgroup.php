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
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$error = "";

$group= "";
if (isset($_POST["group"])) $group = $_POST["group"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addgroup"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listgroups.php".$urlext);
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
			$error .= "<li>".lang('nofieldgiven', lang('groupname'))."</li>";
			$validinfo = false;
		}

		if ($validinfo)
		{
			$groupobj = new Group();
			$groupobj->name = $group;
			$groupobj->active = $active;
			
			Events::SendEvent('Core', 'AddGroupPre', array('group' => &$groupobj));

			$result = $groupobj->save();

			if ($result)
			{
				Events::SendEvent('Core', 'AddGroupPost', array('group' => &$groupobj));
				audit($groupobj->id, $groupobj->name, lang_en('added_group'));
				redirect("listgroups.php".$urlext);
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
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('addgroup')))."</p></div>";
}
else
{
	if ($error != "")
	{
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";
	}
?>

<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('addgroup'); ?>
	<form method="post" action="addgroup.php">
          <div>
          <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
        </div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" name="group" maxlength="255" value="<?php echo $group?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('active')?>:</p>
			<p class="pageinput"><input class="pagecheckbox" type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="addgroup" value="true" />
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
