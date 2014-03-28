<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
check_login();

$gCms = cmsms();
$db = $gCms->GetDb();

$dateformat = trim(cms_userprefs::get_for_user(get_userid(),'date_format_string','%x %X'));
if( empty($dateformat) ) $dateformat = '%x %X';

$result = $db->Execute("SELECT * FROM ".cms_db_prefix()."adminlog ORDER BY timestamp DESC");
$totalrows = $result->RecordCount();

if (isset($_GET['download']))
{
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="adminlog.txt"');
	if ($result && $result->RecordCount() > 0)
	{
		while ($row = $result->FetchRow())
		{
		  echo strftime($dateformat,$row['timestamp'])."|";
		  echo $row['username'] . "|";
		  echo (((int)$row['item_id']==-1)?'':$row['item_id']) . "|";
		  echo $row['item_name'] . "|";
		  echo $row['action'];
		  echo "\n";
		}
	}
	return;
}

include_once("header.php");

$smarty->assign("urlext",$urlext);

$userid = get_userid();
$access = check_permission($userid, 'Clear Admin Log');

if (check_permission($userid, 'Modify Site Preferences'))
{
  if (isset($_GET['clear']) && $access) {
    $query = "DELETE FROM ".cms_db_prefix()."adminlog";
    $db->Execute($query);
    echo $themeObject->ShowMessage(lang('adminlogcleared'));
	// put mention into the admin log
    audit('', 'Admin Log', 'Cleared');
  }

  $page = 1;
  if (isset($_GET['page']))$page = $_GET['page'];

  $limit = 20;
  $page_string = "";
  $from = ($page * $limit) - $limit;

  if (isset($_POST["filterreset"])) {
    set_site_preference('adminlog_filteruser','');
    //set_site_preference('adminlog_filtername','');
    set_site_preference('adminlog_filteraction','');
  }
  if (isset($_POST["filterapply"])) {
    if (isset($_POST['filteruser'])) set_site_preference('adminlog_filteruser',trim($_POST["filteruser"]));
    if (isset($_POST['filteraction'])) set_site_preference('adminlog_filteraction',trim($_POST["filteraction"]));
  }

  $params=array();
  $criteria ="";
  $filterdisplay="none";
  if (get_site_preference('adminlog_filteruser')!='') {
    $criteria.="WHERE username=?";
    $params=array_merge($params,array(get_site_preference('adminlog_filteruser')));
    $filterdisplay="block";
  }
  if (get_site_preference('adminlog_filteraction')!='') {
    if ($criteria!="") $criteria.=" AND ";
    $criteria.="WHERE action LIKE ?";
    $params=array_merge($params,array("%".get_site_preference('adminlog_filteraction')."%"));
    $filterdisplay="block";
  }

  $result = $db->SelectLimit('SELECT * from '.cms_db_prefix().'adminlog '.$criteria.' ORDER BY timestamp DESC', $limit, $from, $params);
  $smarty->assign("header",$themeObject->ShowHeader('adminlog'));

  if ($result && $result->RecordCount() > 0)
    {

      $page_string = pagination($page, $totalrows, $limit);
      $smarty->assign("pagestring",$page_string);

      $smarty->assign("downloadlink",$themeObject->DisplayImage('icons/system/attachment.gif', lang('download'),'','','systemicon'));
      $smarty->assign("langdownload",lang("download"));

      $smarty->assign("languser",lang("user"));
      $smarty->assign("langitemid",lang("itemid"));
      $smarty->assign("langitemname",lang("itemname"));
      $smarty->assign("langaction",lang("action"));
      $smarty->assign("langdate",lang("date"));

      $loglines=array();
      while ($row = $result->FetchRow()) {
	$one=array();
	$one['ip_addr'] = $row['ip_addr'];
	$one["username"]=$row["username"];
	$one["itemid"]=($row["item_id"]!=-1?$row["item_id"]:"&nbsp;");
	$one["itemname"]=$row["item_name"];
	$one["action"]=$row["action"];
	$one["date"]=strftime($dateformat,$row['timestamp']);

	$loglines[]=$one;
      }
      $smarty->assign("loglines",$loglines);
      $smarty->assign("logempty",false);
    }	else {
    $smarty->assign("langlogempty",lang('adminlogempty'));
    $smarty->assign("logempty",true);
  }

  $smarty->assign("clearicon","");
  if ($access && $result && $result->RecordCount() > 0) {
    $smarty->assign("clearicon",$themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon'));
    $smarty->assign("langclear",lang('clearadminlog'));
	$smarty->assign("sysmain_confirmclearlog",lang('sysmain_confirmclearlog'));
  }

  $smarty->assign('filteruser',get_site_preference('adminlog_filteruser',''));
  $smarty->assign('filteraction',get_site_preference('adminlog_filteraction',''));
  $smarty->assign("langfilteruser",lang("filteruser"));
  $smarty->assign("langfilteraction",lang("filteraction"));
  $smarty->assign("langfilterapply",lang("filterapply"));
  $smarty->assign("langfilterreset",lang("filterreset"));
  $smarty->assign("langfilters",lang("filters"));
  $smarty->assign("langshowfilters",lang("showfilters"));
  $smarty->assign("filteruservalue",get_site_preference("adminlog_filteruser"));
  $smarty->assign("filteractionvalue",get_site_preference("adminlog_filteraction"));
  $smarty->assign("filterdisplay",$filterdisplay);
  $smarty->assign('SECURE_PARAM_NAME',CMS_SECURE_PARAM_NAME);
  $smarty->assign('CMS_USER_KEY',$_SESSION[CMS_USER_KEY]);
}

echo $smarty->fetch('adminlog.tpl');

include_once("footer.php");


?>