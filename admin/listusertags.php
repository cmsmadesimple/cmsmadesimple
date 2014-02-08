<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
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
require_once(cms_join_path($dirname,'lib','html_entity_decode_utf8.php'));
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$plugin = '';
if (isset($_GET['plugin'])) $plugin = $_GET['plugin'];

$action = '';
if (isset($_GET['action'])) $action = $_GET['action'];

$userid = get_userid();
$access = check_permission($userid, 'Modify User-defined Tags');
if (!$access) {
  die('Permission Denied');
  return;
}
$smarty = cmsms()->GetSmarty();
include_once("header.php");

function listudt_summarize($str,$numwords,$ets='...')
{
  $str = strip_tags($str);
  $stringarray = explode(" ",$str);
  if( $numwords >= count($stringarray) )
    {
      return $str;
    }
  $tmp = array_slice($stringarray,0,$numwords);
  $tmp = implode(' ',$tmp).$ets;
  return $tmp;
}

if (FALSE == empty($_GET['message'])) {
    echo $themeObject->ShowMessage(lang($_GET['message']));
}

echo '<div class="pagecontainer">';
echo '<div class="pageoverflow">';
echo $themeObject->ShowHeader('userdefinedtags');
echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
echo '<thead>';
echo "<tr>\n";
echo "<th>".lang('name')."</th>\n";
echo "<th>".lang('description')."</th>\n";
echo "<th class=\"pageicon\">&nbsp;</th>\n";
echo "<th class=\"pageicon\">&nbsp;</th>\n";
echo "</tr>\n";
echo '</thead>';
echo '<tbody>';

$curclass = "row1";

$tags = UserTagOperations::get_instance()->ListUserTags();
if( count($tags) )
  {
    foreach( $tags as $oneplugin => $label  )
      {
	$tag = UserTagOperations::get_instance()->GetUserTag($oneplugin);

	echo "<tr class=\"".$curclass."\">\n";
	echo "<td><a href=\"editusertag.php".$urlext."&amp;userplugin_id=".$oneplugin."\">$label</a></td>\n";
	echo "<td>".listudt_summarize($tag['description'],20)."</td>\n";
	echo "<td class=\"icons_wide\"><a href=\"editusertag.php".$urlext."&amp;userplugin_id=".$oneplugin."\">";
	echo $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
	echo "</a></td>\n";
	echo "<td class=\"icons_wide\"><a href=\"deleteuserplugin.php".$urlext."&amp;userplugin_id=".$oneplugin."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('deleteconfirm', $label),true)."');\">";
	echo $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
	echo "</a></td>\n";
	echo "</tr>\n";

	($curclass=="row1"?$curclass="row2":$curclass="row1");
      }
  }

	?>
	</tbody>
</table>
	<div class="pageoptions">
		<p class="pageoptions">
			<a href="editusertag.php<?php echo $urlext; ?>">
				<?php
					echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('addusertag'),'','','systemicon').'</a>';
					echo ' <a class="pageoptions" href="editusertag.php'.$urlext.'">'.lang("addusertag");
				?>
			</a>
		</p>
	</div>
</div>
</div>
<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
