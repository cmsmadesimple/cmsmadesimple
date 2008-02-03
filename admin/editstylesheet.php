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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

if (isset($_POST["cancel"]))
{
	redirect("liststylesheets.php");
}

$gCms = cmsms();
$smarty = cms_smarty();
$smarty->assign('action', 'editstylesheet.php');

$contentops = $gCms->GetContentOperations();
$stylesheetops = $gCms->GetStylesheetOperations();

// Make sure we have permission to access this page
$userid = get_userid();
$access = check_permission($userid, 'Modify Stylesheets');

require_once("header.php");

$submit = array_key_exists('submitbutton', $_POST);
$apply = array_key_exists('applybutton', $_POST);

function &get_stylesheet_object($stylesheet_id)
{
	$stylesheet_object = cmsms()->cms_stylesheet->find_by_id($stylesheet_id);
	if (isset($_REQUEST['stylesheet']))
		$stylesheet_object->update_parameters($_REQUEST['stylesheet']);	
	return $stylesheet_object;
}

//Get a working page object
$stylesheet_id = coalesce_key($_REQUEST, 'css_id', '-1');
$stylesheet_object = get_stylesheet_object($stylesheet_id);
$media_types = $stylesheet_object->media_types;

if ($access)
{
	if ($submit || $apply)
	{
		// Handle the media types
		$submitted_types = $_POST['media_types'];
		foreach ($submitted_types as $key => $onetype) 
		{
			if($onetype != -1)
			{	
				$types .= $media_types[$onetype]['name'] . ', ';			
				$media_types[$onetype]['selected'] = true;
			}
		}
		if ($types!='') 
		{
			$types = substr($types, 0, -2); #strip last space and comma
		} 
		else 
		{
			$types='';
		}
			
		$stylesheet_object->media_type = $types;

		if ($stylesheet_object->save())
		{
			audit($stylesheet_object->id, $stylesheet_object->name, 'Edited Stylesheet');
			if ($submit)
			{
				redirect("liststylesheets.php");
			}
		}
	}
}

if (!is_array($stylesheet_object->media_type)) {
  $selected_media_types = split (", " , $stylesheet_object->media_type);
}

foreach($media_types AS $key => $value)
{
	if(in_array($value['name'], $selected_media_types))
	{
		$media_types[$key]['selected'] = true;	
	}
}

//Add the header
$smarty->assign('header_name', $themeObject->ShowHeader('editstylesheet'));

//Assign the stylesheet media types
$smarty->assign('media_types', $media_types);

//Setup the stylesheet object
$smarty->assign_by_ref('stylesheet_object', $stylesheet_object);

//extra buttons
$ExtraButtons = array(
		      array(
			    'name'    => 'applybutton',
			    'caption' => lang('apply'),
			    ),
		      );

$smarty->assign('DisplayButtons', $ExtraButtons);

$smarty->display('addstylesheet.tpl');

include_once("footer.php");
?>