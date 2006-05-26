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
require_once("../lib/classes/class.user.inc.php");

check_login();

include_once("header.php");

if (isset($_GET["message"])) {
	$message = preg_replace('/\</','',$_GET['message']);
	echo '<div class="pagemcontainer"><p class="pagemessage">'.$message.'</p></div>';
}


if (isset($_GET["toggleactive"]))
{
 if($_GET["toggleactive"]==1) {
   $error .= "<li>".lang('errorupdatinguser')."</li>";
 } else {
  $thisuser = UserOperations::LoadUserByID($_GET["toggleactive"]);

  if($thisuser) {

//modify users, is this enough?
    $userid = get_userid();
    $permission = check_permission($userid, 'Modify Users');

    $result = false;
    if($permission)
      {

    $thisuser->active == 1 ? $thisuser->active = 0 : $thisuser->active=1;

        #Perform the edituser_pre callback
        foreach($gCms->modules as $key=>$value)
              {
                 if ($gCms->modules[$key]['installed'] == true &&
                       $gCms->modules[$key]['active'] == true)
                        {
                         $gCms->modules[$key]['object']->EditUserPre($thisuser);
                        }
                 }

        $result = $thisuser->save();

      }

      if ($result)
         {
           audit($user_id, $thisuser->username, 'Edited User');
           #Perform the edituser_post callback
           foreach($gCms->modules as $key=>$value)
                  {
                   if ($gCms->modules[$key]['installed'] == true &&
                          $gCms->modules[$key]['active'] == true)
                       {
                          $gCms->modules[$key]['object']->EditUserPost($thisuser);
                       }
                  }
        } else {
           $error .= "<li>".lang('errorupdatinguser')."</li>";
        }
   }
}
}

 if ($error != "") {
     echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$error."</ul></div>";
 }


?>

<div class="pagecontainer">
	<div class="pageoverflow">

<?php

	$userid = get_userid();
	$edit = check_permission($userid, 'Modify Users');
        $remove = check_permission($userid, 'Remove Users');

	$query = "SELECT user_id, username, active FROM ".cms_db_prefix()."users ORDER BY user_id";
	$result = $db->Execute($query);

	$userlist = UserOperations::LoadUsers();

	$page = 1;
	if (isset($_GET['page'])) $page = $_GET['page'];
	$limit = 20;
	if (count($userlist) > $limit)
	{
		echo "<p class=\"pageshowrows\">".pagination($page, count($userlist), $limit)."</p>";
	}
	echo $themeObject->ShowHeader('currentusers').'</div>';
	if ($userlist && count($userlist) > 0){
		echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
		echo '<thead>';
		echo "<tr>\n";
		echo "<th class=\"pagew70\">".lang('username')."</th>\n";
		echo "<th class=\"pagepos\">".lang('active')."</th>\n";
		echo "<th class=\"pageicon\">&nbsp;</th>\n";
		if ($remove)
			echo "<th class=\"pageicon\">&nbsp;</th>\n";
		echo "</tr>\n";
		echo '</thead>';
		echo '<tbody>';

		$currow = "row1";
		// construct true/false button images
        $image_true = $themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon');
        $image_false = $themeObject->DisplayImage('icons/system/false.gif', lang('false'),'','','systemicon');

		$counter=0;
		foreach ($userlist as $oneuser){
			if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit) {
  			    echo "<tr class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
				echo "<td><a href=\"edituser.php?user_id=".$oneuser->id."\">".$oneuser->username."</a></td>\n";
				echo "<td class=\"pagepos\"><a href=\"listusers.php?toggleactive=".$oneuser->id."\">".($oneuser->active == 1?$image_true:$image_false)."</a></td>\n";
				if ($edit || $userid == $oneuser->id)
				    {
					echo "<td><a href=\"edituser.php?user_id=".$oneuser->id."\">";
                    echo $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
                    echo "</a></td>\n";
                    }
				else
				    {
					echo "<td>&nbsp;</td>\n";
					}
				if ($remove && $oneuser->id != 1)
				    {
					echo "<td><a href=\"deleteuser.php?user_id=".$oneuser->id."\" onclick=\"return confirm('".lang('deleteconfirm')."');\">";
                    echo $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
                    echo "</a></td>\n";
		            }
        		echo "</tr>\n";

				($currow=="row1"?$currow="row2":$currow="row1");
			}
			$counter++;
		}

		echo '</tbody>';
		echo "</table>\n";

	}

if (check_permission($userid, 'Add Users')) {
?>
	<div class="pageoptions">
		<p class="pageoptions">
			<a href="adduser.php">
				<?php 
					echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('adduser'),'','','systemicon').'</a>';
					echo ' <a class="pageoptions" href="adduser.php">'.lang("adduser");
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
