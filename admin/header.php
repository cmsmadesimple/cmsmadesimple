<?php

@ob_start();

// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
 
// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

// Language shizzle
//header("Content-Encoding: " . get_encoding());
//header("Content-Language: " . $current_language);
if (!isset($charsetsent))
{
	header("Content-Type: text/html; charset=" . get_encoding());
}

$userid = get_userid();
# find aggregate permissions
$pagePerms = check_permission($userid, 'Modify Any Page') | check_permission($userid, 'Add Pages') |
          check_permission($userid, 'Remove Pages');
$htmlPerms = check_permission($userid, 'Add Html Blobs') | check_permission($userid, 'Modify Html Blobs') |
	      check_permission($userid, 'Delete Html Blobs');
$templatePerms = check_permission($userid, 'Add Templates') | check_permission($userid, 'Modify Templates') |
          check_permission($userid, 'Remove Templates');
$cssPerms = check_permission($userid, 'Add Stylesheets') | check_permission($userid, 'Modify Stylesheets') |
          check_permission($userid, 'Remove Stylesheets');
$filePerms = check_permission($userid, 'Modify Files');
$userPerms = check_permission($userid, 'Add Users') | check_permission($userid, 'Modify Users') |
          check_permission($userid, 'Remove Users');
$groupPerms = check_permission($userid, 'Add Groups') | check_permission($userid, 'Modify Groups') |
          check_permission($userid, 'Remove Groups');
$groupPermPerms = check_permission($userid, 'Modify Permissions');
$groupMemberPerms =  check_permission($userid, 'Modify Group Assignments');
$sitePrefPerms = check_permission($userid, 'Modify Site Preferences');
$codeBlockPerms = check_permission($userid, 'Modify Code Blocks');
$modulePerms = check_permission($userid, 'Modify Modules');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<title><?php echo lang('adminsystemtitle')?></title>

<link rel="stylesheet" type="text/css" href="style.php" />
<link rel="stylesheet" type="text/css" href="tab.php" />


<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->

</head>

<body##BODYSUBMITSTUFFGOESHERE##>


<div id="MainContent">

<div class="DashboardCallout">

<p class="DashboardCalloutTitle">Bookmarks</p>

</div> <!-- end DashboardCallout -->

<div class="DashboardCallout">

<p class="DashboardCalloutTitle">Recent Pages</p>

</div> <!-- end DashboardCallout -->
