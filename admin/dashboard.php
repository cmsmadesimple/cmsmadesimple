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
#$Id: dashboard.php 4631 2008-06-14 18:03:26Z nuno $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
require_once("../lib/classes/class.group.inc.php");

check_login();

include_once("header.php");

?>

<div class="pagecontainer">
<div class="pageoverflow">
<div class="pageheader">
<?php echo lang('dashboard'); ?>
</div>
<?php 
global $gCms;

$themeObject->DisplayDashboardPageItem("start");

$output="";

/******* Core Information Output ********/

require_once("../lib/classes/class.user.inc.php");

$output.= lang('welcome_user') . " <b>".$gCms->variables['username']."</b>";

$db =& $gCms->GetDb();

$query = "SELECT timestamp FROM ".cms_db_prefix()."adminlog WHERE user_id=? AND action=? ORDER BY timestamp DESC";
//echo $query;
$result = &$db->Execute($query, array(get_userid(),"User Login"));
if ($result && $result->RecordCount()>2) {
	$row=$result->FetchRow();
	$row=$result->FetchRow(); //Pick the previous, not the current
	$sincelogin=time()-$row["timestamp"];
	if ($sincelogin>(60*60*24)) {
		$sincelogin=ceil($sincelogin/(60*60*24));
		if ($sincelogin==1) {
			$sincelogin.=" ".lang("day");
		} else {
			$sincelogin.=" ".lang("days");
		}
	} elseif ($sincelogin>(60*60)) {
	  $sincelogin=ceil($sincelogin/(60*60));
		if ($sincelogin==1) {
			$sincelogin.=" ".lang("hour");
		} else {
			$sincelogin.=" ".lang("hours");
		}
	} else {
	  $sincelogin=ceil($sincelogin/(60));
		if ($sincelogin==1) {
			$sincelogin.=" ".lang("minute");
		} else {
			$sincelogin.=" ".lang("minutes");
		}
	}
	$output.="<br/>";
	$output.=lang('itsbeensincelogin',$sincelogin);
	//$output.="<br/>It's been ".$sincelogin." seconds since your last login";
}

$themeObject->DisplayDashboardPageItem("core","Core information",$output);

/******* ModuleInformation Output ********/

foreach ($gCms->modules as $module) {
	if (!$module["installed"]) continue;
	if (!$module["active"]) continue;
	//if ($this->GetPreference(get_userid()."_show_".$module["object"]->GetName(),"1")!="1") continue;
	
	$output=$module["object"]->GetDashboardOutput();
	if ($output!="") {
    $themeObject->DisplayDashboardPageItem("module",$module["object"]->GetFriendlyName(),$output);		
	}
}






$themeObject->DisplayDashboardPageItem("end");

?></div></div>
<?php 

include_once("footer.php");

?>