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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

include_once("header.php");

$userid = get_userid();
$access = check_permission($userid, 'Clear Admin Log');

if (isset($_GET['clear']) && $access) {
       $query = "DELETE FROM ".cms_db_prefix()."adminlog";
       $db->Execute($query);
}

$page = 1;
if (isset($_GET['page']))$page = $_GET['page'];

$result = $db->Execute("SELECT * FROM ".cms_db_prefix()."adminlog ORDER BY timestamp DESC");
$totalrows = $result->RowCount();

$limit = 20;
$page_string = "";
$from = ($page * $limit) - $limit;

$query = "SELECT * from ".cms_db_prefix()."adminlog ORDER BY timestamp DESC limit $from, $limit";
$result = $db->Execute($query);

if ($result && $result->RowCount() > 0) {

       $numofpages = $totalrows / $limit;
       if ($numofpages > 1) {
               if($page != 1){
                               $pageprev = $page-1;
                               $page_string .= "<a href=\"adminlog.php?page=$pageprev\">Previous</a>&nbsp;";
               }else{
                               $page_string .= "Previous ";
               }

               for($i = 1; $i <= $numofpages; $i++){
                               if($i == $page){
                                               $page_string .= $i."&nbsp;";
                               }else{
                                               $page_string .= "<a href=\"adminlog.php?page=$i\">$i</a>&nbsp;";
                               }
               }

               if(($totalrows % $limit) != 0){
                               if($i == $page){
                                               $page_string .= $i."&nbsp;";
                               }else{
                                               $page_string .= "<a href=\"adminlog.php?page=$i\">$i</a>&nbsp;";
                               }
               }

               if(($totalrows - ($limit * $page)) > 0){
                               $pagenext = $page+1;
                               $page_string .= "<a href=\"adminlog.php?page=$pagenext\">Next</a>";
               }else{
                               $page_string .= "Next";
               }
       }

?>

<div class="adminformnobkg">
<h3><?=$gettext->gettext("Admin Log")?></h3>
<?php echo "<div align=\"right\" class=\"clearbox\">".$page_string."</div>"; ?>
<table cellspacing="0" class="admintable" style="margin-bottom: 0px;">
       <tr>
               <td><?=$gettext->gettext("User")?></td>
               <td><?=$gettext->gettext("Item ID")?></td>
               <td><?=$gettext->gettext("Item Name")?></td>
               <td><?=$gettext->gettext("Action")?></td>
               <td><?=$gettext->gettext("Date")?></td>
       </tr>

<?php

       $currow = "row1";

       while ($row = $result->FetchRow()) {

               echo "<tr class=\"$currow\">\n";
               echo "<td>".$row["username"]."</td>\n";
               echo "<td>".($row["item_id"]!=-1?$row["item_id"]:"&nbsp;")."</td>\n";
               echo "<td>".$row["item_name"]."</td>\n";
               echo "<td>".$row["action"]."</td>\n";
               echo "<td>".date("D M j, Y G:i:s T", $row["timestamp"])."</td>\n";
               echo "</tr>\n";

               ($currow == "row1"?$currow="row2":$currow="row1");

       }
?>
</table>
<?php
echo "<div align=\"right\" class=\"clearbox\">".$page_string."</div>";
echo "</div>";

       if ($access) {
               echo "<div class=\"button\"><a href=\"adminlog.php?clear=true\">Clear Admin Log</a></div>";
       }
       echo "<br>";
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
