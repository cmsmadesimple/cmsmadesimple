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

$plugin = '';
if (isset($_GET['plugin'])) $plugin = $_GET['plugin'];

$action = '';
if (isset($_GET['action'])) $action = $_GET['action'];

$userid = get_userid();
$access = check_permission($userid, 'Modify Modules');

include_once("header.php");

if (FALSE == empty($_GET['message'])) {
    echo $themeObject->ShowMessage(lang($_GET['message']));
}

$udt_object = new CmsUserTag();
$user_defined_tags = $udt_object->find_all();

echo '<div class="pagecontainer">';
echo '<div class="pageoverflow">';
echo $themeObject->ShowHeader('userdefinedtags');
echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
echo '<thead>';
echo "<tr>\n";
echo "<th>".lang('name')."</th>\n";
echo "<th class=\"pageicon\">&nbsp;</th>\n";
echo "<th class=\"pageicon\">&nbsp;</th>\n";
echo "</tr>\n";
echo '</thead>';
echo '<tbody>';

$curclass = "row1";

foreach($user_defined_tags as $tag)
{
		echo "<tr class=\"".$curclass."\" onmouseover=\"this.className='".$curclass.'hover'."';\" onmouseout=\"this.className='".$curclass."';\">\n";
		echo "<td><a href=\"edituserplugin.php?userplugin_id=".$tag->params['id']."\">".$tag->params['name']."</a></td>\n";
		echo "<td class=\"icons_wide\"><a href=\"edituserplugin.php?userplugin_id=".$tag->params['id']."\">";
		echo $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
		echo "</a></td>\n";
		echo "<td class=\"icons_wide\"><a href=\"deleteuserplugin.php?userplugin_id=".$tag->params['id']."\" onclick=\"return confirm('".lang('deleteconfirm', $tag->params['name'])."');\">";
		echo $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
		echo "</a></td>\n";

		echo "</tr>\n";

		($curclass=="row1"?$curclass="row2":$curclass="row1");
}

	?>
	</tbody>
</table>
	<div class="pageoptions">
		<p class="pageoptions">
			<a href="adduserplugin.php">
				<?php
					echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('addusertag'),'','','systemicon').'</a>';
					echo ' <a class="pageoptions" href="adduserplugin.php">'.lang("addusertag");
				?>
			</a>
		</p>
	</div>
</div>
</div>
<?php
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
