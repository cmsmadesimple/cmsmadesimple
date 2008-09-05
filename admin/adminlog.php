<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
$gCms = cmsms();
$db = cms_db();
$smarty = cms_smarty();

$dateformat = get_preference(get_userid(),'date_format_string','%x %X');

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
		  echo strftime($dateformat,$row['timestamp']).'\t';
//			echo date("D M j, Y G:i:s", $row["timestamp"]) . "\t";
		  echo $row['username'] . "\t";
		  echo $row['item_id'] . "\t";
		  echo $row['item_name'] . "\t";
		  echo $row['action'] . "\t";
		  echo "\n";
		}
	}
	return;
}

include_once("header.php");

$db = cms_db();

$userid = get_userid();
$access = check_permission($userid, 'Clear Admin Log');

if (isset($_GET['clear']) && $access)
{
       $query = "DELETE FROM ".cms_db_prefix()."adminlog";
       $db->Execute($query);
	   
	   $smarty->assign('message', lang('adminlogcleared'));
	 
     }

$page = 1;
if (isset($_GET['page']))$page = $_GET['page'];

$limit = 20;
$page_string = "";
$from = ($page * $limit) - $limit;

$result = $db->SelectLimit('SELECT * from '.cms_db_prefix().'adminlog ORDER BY timestamp DESC', $limit, $from);


    $smarty->assign('have_result', $result && $result->RecordCount() > 0);

	
	$page_string = pagination($page, $totalrows, $limit);
		
	$smarty->assign('page_string', $page_string);
	$smarty->assign('header_name', $themeObject->ShowHeader('adminlog'));


       while ($row = $result->FetchRow()) {
		   
		      $username[]=$row["username"]; 
			  $item_id[] =($row["item_id"]!=-1?$row["item_id"]:"&nbsp;"); 
			  $item_name[]=$row["item_name"];
			  $action[] = $row["action"]; 
		      $dateformats[] = strftime($dateformat,$row['timestamp']); 
			   $date[]= date("D M j, Y G:i:s", $row["timestamp"]); 
			  
			  
			  
              $smarty->assign('username', $username); 
			  $smarty->assign('item_id', $item_id); 
			  $smarty->assign('item_name', $item_name); 
			  $smarty->assign('action"', $action); 
			  $smarty->assign('dateformats', $dateformats); 
		      $smarty->assign('date', $date); 
 }
            $smarty->assign('access_result', $access && $result && $result->RecordCount() > 0);
			$smarty->assign('back_url', $themeObject->BackUrl());
			$smarty->display('adminlog.tpl');
			
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
