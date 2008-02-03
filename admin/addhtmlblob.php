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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
$error = "";

$htmlblob = "";
if (isset($_POST['htmlblob'])) $htmlblob = $_POST['htmlblob'];

$content = "";
if (isset($_POST['content'])) $content = $_POST['content'];

if (isset($_POST["cancel"]))
{
	redirect("listhtmlblobs.php");
}

$gCms = cmsms();
$smarty = cms_smarty();
$smarty->assign('action', 'addhtmlblob.php');

$contentops = $gCms->GetContentOperations();
$gcbops = $gCms->GetGlobalContentOperations();

#Make sure we're logged in and get that user id
check_login();
$userid = get_userid();
$access = check_permission($userid, 'Add Global Content Blocks');

require_once("header.php");
// Create an array of additional editors
$db = cms_db();
$addt_users = "";
$query = "SELECT id, username FROM ".cms_db_prefix()."users WHERE id <> ? ORDER BY username";
$result = $db->Execute($query, array($userid));
if ($result && $result->RecordCount() > 0) {
	while($row = $result->FetchRow()) {
		$addt_users .= "<option value=\"".$row["id"]."\">".$row["username"]."</option>";
	}
}else{
	$addt_users = "<option>&nbsp;</option>";
}


$submit = array_key_exists('submitbutton', $_POST);

function &get_gcb_object()
{
	$gcb_object = new CmsGlobalContent();
	if (isset($_REQUEST['gcb']))
		$gcb_object->update_parameters($_REQUEST['gcb']);
	return $gcb_object;
}

//Get a working page object
$gcb_object = get_gcb_object($userid);
$gcb_wysiwyg = get_preference($userid, 'gcb_wysiwyg', 1);

if ($access)
{
	if ($submit)
	{
		if ($gcb_object->save())
		{
			if (isset($_POST["additional_editors"])) {
				foreach ($_POST["additional_editors"] as $addt_user_id) {
					$gcb_object->AddAuthor($addt_user_id);
				}
			}
			audit($gcb_object->id, $gcb_object->name, 'Added Global Content Block');
			redirect("listhtmlblobs.php");
		}
	}
}

//Add the header
$smarty->assign('header_name', $themeObject->ShowHeader('addhtmlblob'));

//Assign additional editors
$smarty->assign('addt_users', $addt_users);

//Setup the gcb object
$smarty->assign_by_ref('gcb_object', $gcb_object);

$smarty->display('addhtmlblob.tpl');

include_once("footer.php");
?>