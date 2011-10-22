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
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
check_login();
$userid = get_userid();
$access = check_permission($userid, 'Modify User-defined Tags');
if (!$access) die('Permission Denied');

$gCms = cmsms();
$db = $gCms->GetDb();
$usertagops = $gCms->GetUserTagOperations();

include('header.php');
if( !isset($_REQUEST['userplugin_id']) )
  {
    echo $themeObject->ShowErrors(lang('missingparams'));
  }
$udt_id = (int)$_REQUEST['userplugin_id'];
$udt_name = '';
$usertag = UserTagOperations::get_instance()->GetUserTag($udt_id);
if( !$usertag )
  {
    // todo, change me.
    echo $themeObject->ShowErrors(lang('missingparams'));
  }

if (isset($_POST["cancel"])) {
	redirect("listusertags.php".$urlext);
	return;
}

if (FALSE == empty($_GET['message'])) 
  {
    echo $themeObject->ShowMessage(lang($_GET['message']));
  }

$output = '';
if( isset($_POST['submit']) )
  {
    @ob_start();
    $params = array();
    $res = $usertagops->CallUserTag($usertag['userplugin_name'],$params);
    $tmp = @ob_get_contents();
    @ob_end_clean();

    if( $tmp ) 
      $output = $tmp;
    else
      $output = $res;
  }

$hidden =   '<input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'"/>';
$hidden .=  '<input type="hidden" name="userplugin_id" value="'.$udt_id.'"/>';
$smarty->assign('hidden',$hidden);
if( $output )
  {
    $smarty->assign('output',$output);
  }
$smarty->assign('udt_name',$usertag['userplugin_name']);
$usertagops = $gCms->GetUserTagOperations();
$smarty->assign('code',create_textarea(false,$usertag['code'],'code','pagebigtextarea','code','','','80','15','','php','readonly="readonly"'));
echo $smarty->fetch('runuserplugin.tpl');
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>'."\n";
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
