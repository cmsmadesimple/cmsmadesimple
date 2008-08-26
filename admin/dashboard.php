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
<?php 
global $gCms;

$themeObject->DisplayDashboardPageItem("start");


$output="";

require_once("../lib/classes/class.user.inc.php");

$output.="Welcome ".$gCms->variables['username'].", and you have userid ".get_userid();

$db =& $gCms->GetDb();

$variables = &$gCms->variables;
$variables['ownerpages'] = array();

$query = "SELECT timestamp FROM ".cms_db_prefix()."adminlog WHERE user_id=? AND action=? ORDER BY timestamp DESC";
//echo $query;
$result = &$db->Execute($query, array(get_userid(),"User Login"));
if ($result && $result->RecordCount()>2) {
	$row=$result->FetchRow();
	$row=$result->FetchRow();
	$sincelogin=time()-$row["timestamp"];
	$output.="<br/>It's been ".$sincelogin." seconds since your last login";
}

$themeObject->DisplayDashboardPageItem("core","Core information",$output);

$themeObject->DisplayDashboardPageItem("end");

?></div></div>
<?php 

include_once("footer.php");

?>