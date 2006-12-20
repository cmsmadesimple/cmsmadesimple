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

include_once("header.php");

global $gCms;
$db =& $gCms->GetDb();

$userid = get_userid();
$access = check_permission($userid, 'Clear Admin Log');

if (isset($_GET['clear']) && $access) {
       $query = "DELETE FROM ".cms_db_prefix()."adminlog";
       $db->Execute($query);
       echo $themeObject->ShowMessage(lang('adminlogcleared'));
}

$page = 1;
if (isset($_GET['page']))$page = $_GET['page'];

$result = $db->Execute("SELECT * FROM ".cms_db_prefix()."adminlog ORDER BY timestamp DESC");
$totalrows = $result->RecordCount();

$limit = 20;
$page_string = "";
$from = ($page * $limit) - $limit;

$result = $db->SelectLimit('SELECT * from '.cms_db_prefix().'adminlog ORDER BY timestamp DESC', $limit, $from);

	echo '<div class="pagecontainer">';
	echo '<div class="pageoverflow">';

if ($result && $result->RecordCount() > 0) {
	$page_string = pagination($page, $totalrows, $limit);
	echo "<p class=\"pageshowrows\">".$page_string."</p>";
	echo $themeObject->ShowHeader('adminlog').'</div>';

	echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
	echo '<thead>';
	echo "<tr>\n";
	echo "<th>".lang('user')."</th>\n";
	echo "<th>".lang('itemid')."</th>\n";
	echo "<th>".lang('itemname')."</th>\n";
	echo "<th>".lang('action')."</th>\n";
	echo "<th>".lang('date')."</th>\n";
	echo "</tr>\n";
	echo '</thead>';
	echo '<tbody>';

       $currow = "row1";

       while ($row = $result->FetchRow()) {

               echo "<tr class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
               echo "<td>".$row["username"]."</td>\n";
               echo "<td>".($row["item_id"]!=-1?$row["item_id"]:"&nbsp;")."</td>\n";
               echo "<td>".$row["item_name"]."</td>\n";
               echo "<td>".$row["action"]."</td>\n";
               echo "<td>".date("D M j, Y G:i:s", $row["timestamp"])."</td>\n";
               echo "</tr>\n";

               ($currow == "row1"?$currow="row2":$currow="row1");

       }
	   
	echo '</tbody>';
	echo '</table>';

	}
	else {
		echo '<p class="pageheader">'.lang('adminlog').'</p></div>';
		echo '<p>'.lang('adminlogempty').'</p>';
	}

if ($access && $result && $result->RecordCount() > 0) {
	echo '<div class="pageoptions">';
	echo '<p class="pageoptions">';
	echo '<a href="adminlog.php?clear=true">';
	echo $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon').'</a>';
	echo '<a class="pageoptions" href="adminlog.php?clear=true">'.lang('clearadminlog').'</a>';
	echo '</p>';
	echo '</div>';
}

echo '</div>';

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';


include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
