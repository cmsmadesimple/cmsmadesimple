<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

global $gCms;
$smarty =& $gCms->smarty;
$contentops =& $gCms->GetContentOperations();

check_login();
$userid = get_userid();

if (isset($_POST["cancel"]))
{
	redirect("listcontent.php");
}

//See if some variables are returned
$page_type = coalesce_key($_REQUEST, 'page_type', 'link');
$orig_page_type = coalesce_key($_POST, 'orig_page_type', 'link');
$preview = array_key_exists('previewbutton', $_POST);
$submit = array_key_exists('submitbutton', $_POST);
$apply = array_key_exists('applybutton', $_POST);

#Get current userid and make sure they have permission to add something
$access = (check_permission($userid, 'Add Pages') || check_permission($userid, 'Modify Page Structure'));

require_once("header.php");

function get_type($n)
{
	return $n->type;
}

function get_friendlyname($n)
{
	return $n->friendlyname;
}

function &get_page_object($page_type, &$orig_page_type, $userid)
{
	global $gCms;

	$page_object = new StdClass();

	if (isset($_POST["serialized_content"]))
	{
		$contentops =& $gCms->GetContentOperations();
		$contentops->LoadContentType($orig_page_type);
		$page_object = unserialize(base64_decode($_POST["serialized_content"]));
		$page_object->update_parameters($_REQUEST['content']);
		/*
		if (strtolower(get_class($contentobj)) != $content_type)
		{
			#Fill up the existing object with values in form
			#Create new object
			#Copy important fields to new object
			#Put new object on top of old on
			copycontentobj($contentobj, $content_type);
		}
		*/
	}
	else
	{
		$contentops =& $gCms->GetContentOperations();
		$page_object = $contentops->CreateNewContent($page_type);
		$page_object->owner = $userid;
		$page_object->active = TRUE;
		$page_object->show_in_menu = TRUE;
		$page_object->last_modified_by = $userid;
		//if ($parent_id!=-1) $contentobj->SetParentId($parent_id);
	}
	
	return $page_object;
}

//Get a working page object
$page_object =& get_page_object($page_type, $orig_page_type, $userid);
if ($access)
{
	if ($submit || $apply)
	{
		if ($page_object->save())
		{
			$contentops->SetAllHierarchyPositions();
			if ($submit)
			{
				audit($page_object->Id(), $page_object->Name(), 'Added Content');
				//redirect('listcontent.php?message=contentadded');
			}
		}
	}
}

//Set some page variables
$smarty->assign('header_name', $themeObject->ShowHeader('addcontent'));

//Setup the page object
$smarty->assign_by_ref('page_object', $page_object);
$smarty->assign('serialized_object', SerializeObject($page_object));
$smarty->assign('orig_page_type', $orig_page_type);

//Set the pagetypes
$smarty->assign('page_types', array_combine(array_map('get_type', $gCms->contenttypes), array_map('get_friendlyname', $gCms->contenttypes)));
$smarty->assign('selected_page_type', $page_type);

//Set the parent dropdown
$smarty->assign('show_parent_dropdown', $access);
$smarty->assign('parent_dropdown', $contentops->CreateHierarchyDropdown('', $page_object->parent_id, 'content[parent_id]'));

//Set the users
$userops =& $gCms->GetUserOperations();
$smarty->assign('show_owner_dropdown', true);
$smarty->assign('owner_dropdown', $userops->GenerateDropdown($page_object->owner_id, 'content[owner_id]'));

//Other fields that aren't easily done with smarty
$smarty->assign('metadata_box', create_textarea(false, $page_object->metadata, 'content[metadata]', 'pagesmalltextarea', 'content_metadata', '', '', '80', '6'));

$smarty->display('addcontent.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>