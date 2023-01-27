<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
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

require_once("../lib/include.php");
check_login();
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$userid = get_userid();
$access = check_permission($userid, 'Modify User-defined Tags');
if (!$access) {
    die('Permission Denied');
    return;
}

include_once("header.php");

function listudt_summarize($str,$numwords,$ets='...')
{
    $str = strip_tags($str);
    $stringarray = explode(" ",$str);
    $numwords = min(max($numwords,1),100);
    if( $numwords >= count($stringarray) ) return $str;
    $tmp = array_slice($stringarray,0,$numwords);
    $tmp = implode(' ',$tmp).$ets;
    return $tmp;
}

if (FALSE == empty($_GET['message'])) echo $themeObject->ShowMessage(lang($_GET['message']));

$list = UserTagOperations::get_instance()->ListUserTags();
$tags = null;
if( count($list) ) {
    foreach( $list as $id => $label ) {
        $tag = UserTagOperations::get_instance()->GetUserTag($id);
        $rec = array();
        $rec['id'] = $id;
        $rec['name'] = $label;
        $rec['description'] = listudt_summarize($tag['description'],20);
        $tags[$id] = $rec;
    }
}
$smarty = \Smarty_CMS::get_instance();
$smarty->assign('tags',$tags);
$smarty->assign('addurl','editusertag.php'.$urlext);
$smarty->assign('urlext',$urlext);
echo $smarty->display('listusertags.tpl');
include_once("footer.php");
