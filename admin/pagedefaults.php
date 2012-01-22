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
#$Id: siteprefs.php 4122 2007-09-08 21:45:28Z silmarillion $

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU='admin';
$CMS_ADMIN_TITLE='pagedefaults';

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$userid = get_userid();
$access = check_permission($userid, 'Modify Site Preferences');
if (!$access) {
	die('Permission Denied');
return;
}
$gCms = cmsms();
$db = $gCms->GetDb();

$error = "";
$message = "";

if (isset($_POST["cancel"])) {
	redirect("index.php".$urlext);
	return;
}

#
# Set all of the values from the preferences
# or from hardcoded defaults
#
$page_secure = get_site_preference('page_secure','0');
$page_active = get_site_preference('page_active',"1");
$page_showinmenu = get_site_preference('page_showinmenu',"1");
$page_extra1 = get_site_preference('page_extra1','');
$page_extra2 = get_site_preference('page_extra2','');
$page_extra3 = get_site_preference('page_extra3','');
$page_searchable = get_site_preference('page_searchable','1');
$page_cachable = get_site_preference('page_cachable',"1");
$page_metadata = get_site_preference('page_metadata',
	           "<!-- ".lang('msg_defaultmetadata')." -->");
$page_defaultcontent = get_site_preference("defaultpagecontent",
		   "<!-- ".lang('msg_defaultcontent')." -->");
$additional_editors = get_site_preference('additional_editors','');
$default_contenttype = get_site_preference('default_contenttype','content');

$message = '';
if( isset( $_POST['submit'] ) )
  {
    //
    // Process Submit
    //
    $page_secure = (isset($_POST['page_secure'])?"1":"0");
    $page_active = (isset($_POST['page_active'])?"1":"0");
    $page_showinmenu = (isset($_POST['page_showinmenu'])?"1":"0");
    $page_cachable = (isset($_POST['page_cachable'])?"1":"0");
    $page_metadata = $_POST['page_metadata'];
    $page_defaultcontent = $_POST['page_defaultcontent'];
    if( isset( $_POST['additional_editors'] ) && !empty($_POST['additional_editors']) )
      {
	$additional_editors = implode(',',$_POST['additional_editors']);
      }
    else
      {
	$additional_editors = '';
      }
    $page_searchable = (isset($_POST['page_searchable'])?"1":"0");
    $page_extra1 = $_POST['page_extra1'];
    $page_extra2 = $_POST['page_extra2'];
    $page_extra3 = $_POST['page_extra3'];
    $default_contenttype = trim($_POST['default_contenttype']);

    //
    // Store preferences
    //
    set_site_preference( 'page_secure', $page_secure );
    set_site_preference( 'page_active', $page_active );
    set_site_preference( 'page_showinmenu', $page_showinmenu );
    set_site_preference( 'page_cachable', $page_cachable );
    set_site_preference( 'page_metadata', $page_metadata );
    set_site_preference( 'defaultpagecontent', $page_defaultcontent );
    set_site_preference( 'additional_editors', $additional_editors );
    set_site_preference( 'page_searchable', $page_searchable );
    set_site_preference( 'page_extra1', $page_extra1 );
    set_site_preference( 'page_extra2', $page_extra2 );
    set_site_preference( 'page_extra3', $page_extra3 );
    set_site_preference( 'default_contenttype',$default_contenttype);

    $message = lang('prefsupdated');
  }

//
// Display Page Output
//
include_once("header.php");
if ($error != "") {
	echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$error."</ul></div>";	
}
if ($message != "") {
	echo $themeObject->ShowMessage($message);
	// put mention into the admin log
	audit('', 'Page Defaults', 'Edited');
}

// give everything to smarty.
$smarty = cmsms()->GetSmarty();
$contentops = cmsms()->GetContentOperations();
$all_contenttypes = $contentops->ListContentTypes(false,false);
$smarty->assign('all_contenttypes',$all_contenttypes);
$smarty->assign('page_secure',$page_secure);
$smarty->assign('page_active',$page_active);
$smarty->assign('page_showinmenu',$page_showinmenu);
$smarty->assign('page_searchable',$page_searchable);
$smarty->assign('page_cachable',$page_cachable);
$smarty->assign('page_metadata',$page_metadata);
$smarty->assign('page_defaultcontent',$page_defaultcontent);
$smarty->assign('page_extra1',$page_extra1);
$smarty->assign('page_extra2',$page_extra1);
$smarty->assign('page_extra3',$page_extra1);
$smarty->assign('page_additionaleditors',$additional_editors);
$smarty->assign('default_contenttype',$default_contenttype);
$smarty->assign('header',$themeObject->showHeader('pagedefaults'));
$smarty->assign('backurl',$themeObject->backUrl());

$my_addeditors = explode(',',$additional_editors);
$contentops = cmsms()->GetContentOperations();
$content = new ContentBase();
$tmp = $content->ShowAdditionalEditors($my_addeditors);
$smarty->assign('input_additional_editors',$tmp[1]);

$smarty->assign('CMS_SECURE_PARAM_NAME',CMS_SECURE_PARAM_NAME);
$smarty->assign('CMS_KEY',$_SESSION[CMS_USER_KEY]);
echo $smarty->fetch('pagedefaults.tpl');
include_once('footer.php');

// EOF
# vim:ts=4 sw=4 noet
?>
