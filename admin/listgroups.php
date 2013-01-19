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
require_once(cms_join_path($dirname,'lib','html_entity_decode_utf8.php'));
require_once("../lib/classes/class.group.inc.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$userid = get_userid();
$access = check_permission($userid, "Manage Groups");

if (!$access) {
  die('Permission Denied');
  return;
}


include_once("header.php");

?>

<div class="pagecontainer">
	<div class="pageoverflow">

<?php

	$userid = get_userid();
        $gCms = cmsms();
        $userops = $gCms->GetUserOperations();
	$groupops = $gCms->GetGroupOperations();
	$grouplist = $groupops->LoadGroups();

	$page = 1;
	if (isset($_GET['page'])) $page = $_GET['page'];
	$limit = 20;
        if (count($grouplist) > $limit) {
	  echo "<p class=\"pageshowrows\">".pagination($page, count($grouplist), $limit)."</p>";
	}
	echo $themeObject->ShowHeader('currentgroups').'</div>';
	if (count($grouplist) > 0) {
	  echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
	  echo '<thead>';
	  echo "<tr>\n";
	  echo "<th class=\"pagew60\">".lang('name')."</th>\n";
	  echo "<th class=\"pagepos\">".lang('active')."</th>\n";
	  echo "<th class=\"pageicon\">&nbsp;</th>\n";
	  echo "<th class=\"pageicon\">&nbsp;</th>\n";
	  echo "<th class=\"pageicon\">&nbsp;</th>\n";
	  echo "<th class=\"pageicon\">&nbsp;</th>\n";
	  echo "</tr>\n";
	  echo '</thead>';
	  echo '<tbody>';

	  $currow = "row1";

	  // construct true/false button images
	  $image_true = $themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon');
	  $image_false = $themeObject->DisplayImage('icons/system/false.gif', lang('false'),'','','systemicon');
	  $image_groupassign = $themeObject->DisplayImage('icons/system/groupassign.gif', lang('assignments'),'','','systemicon');
	  $image_permissions = $themeObject->DisplayImage('icons/system/permissions.gif', lang('permissions'),'','','systemicon');

	  $counter=0;
	  foreach ($grouplist as $onegroup){
	    if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit) {
	      echo "<tr class=\"$currow\">\n";
	      echo "<td><a title=\"".$onegroup->description."\" href=\"editgroup.php".$urlext."&amp;group_id=".$onegroup->id."\">".$onegroup->name."</a></td>\n";
	      echo "<td class=\"pagepos\">";
	      if( $onegroup->id == 1 ) {
		echo '&nbsp;';
	      }
	      else {
		if( $onegroup->active == 1 ) {
		  echo $image_true;
		}
		else {
		  echo $image_false;
		}
	      }
	      echo "</td>\n";
	      echo "<td class=\"pagepos icons_wide\"><a href=\"changegroupperm.php".$urlext."&amp;group_id=".$onegroup->id."\">".$image_permissions."</a></td>\n";
	      echo "<td class=\"pagepos icons_wide\"><a href=\"changegroupassign.php".$urlext."&amp;group_id=".$onegroup->id."\">".$image_groupassign."</a></td>\n";
	      echo "<td class=\"icons_wide\"><a href=\"editgroup.php".$urlext."&amp;group_id=".$onegroup->id."\">";
	      echo $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
	      echo "</a></td>\n";
	      if ($onegroup->id != 1 && !$userops->UserInGroup($userid,$onegroup->id)) {
		echo "<td class=\"icons_wide\"><a href=\"deletegroup.php".$urlext."&amp;group_id=".$onegroup->id."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('deleteconfirm', $onegroup->name),true)."');\">";
		echo $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
		echo "</a></td>\n";
	      }
	      else {
		echo '<td class="icons_wide">&nbsp;</td>'."\n";
	      }
	      echo "</tr>\n";

	      ($currow == "row1"?$currow="row2":$currow="row1");
	    }
	    $counter++;
	  }

	  echo '</tbody>';
	  echo "</table>\n";

	}

if (check_permission($userid, 'Add Groups')) {
?>
	<div class="pageoptions">
		<p class="pageoptions">
			<a href="addgroup.php<?php echo $urlext ?>">
				<?php 
					echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('addgroup'),'','','systemicon').'</a>';
					echo ' <a class="pageoptions" href="addgroup.php'.$urlext.'">'.lang("addgroup");
				?>
			</a>
		</p>
	</div>
</div>

<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('back')?></a></p>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
