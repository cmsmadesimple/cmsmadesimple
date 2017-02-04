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
$orig_memory = (function_exists('memory_get_usage')?memory_get_usage():0);
require_once("../lib/include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
check_login();

$gCms = \CmsApp::get_instance();
$db = $gCms->GetDb();

$dateformat = trim(cms_userprefs::get_for_user(get_userid(),'date_format_string','%x %X'));
if( empty($dateformat) ) $dateformat = '%x %X';

// get the total number of records.
$totalrows = $db->GetOne("SELECT count(timestamp) FROM ".cms_db_prefix()."adminlog");

if (isset($_GET['download'])) {
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="adminlog.txt"');
	if ($result && $result->RecordCount() > 0)	{
		while ($row = $result->FetchRow()) {
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

if (check_permission($userid, 'Modify Site Preferences')) {
    if (isset($_GET['clear']) && $access) {
        $query = "DELETE FROM ".cms_db_prefix()."adminlog";
        $db->Execute($query);
        echo $themeObject->ShowMessage(lang('adminlogcleared'));
        // put mention into the admin log
        audit('', 'Admin Log', 'Cleared');
    }

    $page = ( isset($_SESSION['adminlog_page']) ) ? (int) $_SESSION['adminlog_page'] : 1;
    if (isset($_REQUEST['page'])) {
        $page = (int) $_REQUEST['page'];
        $_SESSION['adminlog_page'] = $page;
    }

    $limit = 25;
    $npages = ceil($totalrows / $limit);
    $page = max(1,min($npages,$page));
    $from = ($page-1) * $limit;
    $orig_filter = new StdClass;
    $orig_filter->user = $orig_filter->action = $orig_filter->item_name = null;
    $filter = clone $orig_filter;
    if( !empty($_SESSION['adminlog_filter']) ) $_filter = $_SESSION['adminlog_filter'];

    // handle filtering dialog.
    if( isset($_POST['filterapply']) ) {
        $filter->user = trim(cleanValue($_POST['filteruser']));
        $filter->action = trim(cleanValue($_POST['filteraction']));
        $filter->item_name = trim(cleanValue($_POST['filteritem']));
        $_SESSION['adminlog_filter'] = $filter;
        $page = 1;
        unset($_SESSION['adminlog_page']);
    } else if( isset($_POST['filterreset']) ) {
        $filter = $orig_filter;
        unset($_SESSION['adminlog_filter']);
        $page = 1;
        unset($_SESSION['adminlog_page']);
    }
    $filter_applied = ($filter == $orig_filter) ? FALSE : TRUE;

    // now do the query
    $sql = 'SELECT * FROM '.cms_db_prefix().'adminlog ';
    $where = $parms = array();
    if( $filter->user ) {
        $where[] = 'username = ?';
        $parms[] = $filter->user;
    }
    if( $filter->action ) {
        $where[] = 'action LIKE ?';
        $parms[] = '%'.$filter->action.'%';
    }
    if( $filter->item_name ) {
        $where[] = 'item_name LIKE ?';
        $parms[] = '%'.$filter->item_name.'%';
    }
    if( count($where) ) {
        $sql .= ' WHERE '.implode(' AND ',$where);
    }
    $sql .= ' ORDER BY timestamp DESC';
    $result = $db->SelectLimit($sql,$limit,$from,$parms);

    $smarty->assign("header",$themeObject->ShowHeader('adminlog'));
    if ($result && $result->RecordCount() > 0) {

        $pagelist = array();
        if( $npages < 20 ) {
            for( $i = 1; $i <= $npages; $i++ ) {
                $pagelist[$i] = $i;
            }
        }
        else {
            // first 5
            for( $i = 0; $i <= 5; $i++ ) {
                $pagelist[$i] = $i;
            }
            $tpage = $page;
            if( $tpage <= 5 || $tpage >= ($npages - 5) ) $tpage = $npages / 2;
            $x1 = max(1,(int)($tpage - 5 / 2));
            $x2 = min($npages,(int)($tpage + 5 / 2));
            for( $i = $x1; $i <= $x2; $i++ ) {
                $pagelist[] = $i;
            }
            for( $i = max(1,$npages - 5); $i <= $npages; $i++ ) {
                $pagelist[] = $i;
            }
            $pagelist = array_unique($pagelist);
            sort($pagelist);
            $pagelist = array_combine($pagelist,$pagelist);
        }
        $smarty->assign('page',$page);
        $smarty->assign('pagelist',$pagelist);
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
            $one["username"] = $row["username"];
            $one["itemid"] = ($row["item_id"]!=-1?$row["item_id"]:"&nbsp;");
            $one["itemname"] = cleanValue($row["item_name"]);
            $one["action"] = cleanValue($row["action"]);
            $one["date"] = $row['timestamp'];

            $loglines[]=$one;
        }
        $smarty->assign("loglines",$loglines);
        $smarty->assign("logempty",false);
    }
    else {
        $smarty->assign("langlogempty",lang('adminlogempty'));
        $smarty->assign("logempty",true);
    }

    $smarty->assign("clearicon","");
    if ($access && $result && $result->RecordCount() > 0) {
        $smarty->assign("clearicon",$themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon'));
        $smarty->assign("langclear",lang('clearadminlog'));
    }

    $smarty->assign("sysmain_confirmclearlog",lang('sysmain_confirmclearlog'));
    $smarty->assign("langfilteruser",lang("filteruser"));
    $smarty->assign("langfilteraction",lang("filteraction"));
    $smarty->assign("langfilterapply",lang("filterapply"));
    $smarty->assign("langfilterreset",lang("filterreset"));
    $smarty->assign('filter',$filter);
    $smarty->assign('filter_applied',$filter_applied);
    $smarty->assign('SECURE_PARAM_NAME',CMS_SECURE_PARAM_NAME);
    $smarty->assign('CMS_USER_KEY',$_SESSION[CMS_USER_KEY]);
}

echo $smarty->fetch('adminlog.tpl');

include_once("footer.php");