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

# Are there any modules with an admin interface?
$cmsmodules = $gCms->modules;

$modulesBySection = array();
$sectionCount = array();
foreach ($cmsmodules as $key=>$value)
	{
	if (isset($cmsmodules[$key]['object'])
			&& $cmsmodules[$key]['installed'] == true
			&& $cmsmodules[$key]['active'] == true
			&& $cmsmodules[$key]['object']->HasAdmin()
			&& $cmsmodules[$key]['object']->VisibleToAdminUser()
		)
		{
            $section = $cmsmodules[$key]['object']->GetAdminSection();
			if (! isset($sectionCount[$section]))
			     {
			     $sectionCount[$section] = 0;
			     }
			$modulesBySection[$section][$sectionCount[$section]]['key'] = $key;
			if ($cmsmodules[$key]['object']->GetAdminDescription() != '')
			{
				$modulesBySection[$section][$sectionCount[$section]]['description'] =
                    $cmsmodules[$key]['object']->GetAdminDescription();
			}
			else
			{
                $modulesBySection[$section][$sectionCount[$section]]['description'] = "";
			}
			$sectionCount[$section]++;
		}
	}

# find aggregate permissions
# content:
$pagePerms = check_permission($userid, 'Modify Any Page') | check_permission($userid, 'Add Pages') |
          check_permission($userid, 'Remove Pages');
$htmlPerms = check_permission($userid, 'Add Html Blobs') | check_permission($userid, 'Modify Html Blobs') |
	      check_permission($userid, 'Delete Html Blobs');
$contentPerms = $pagePerms | $htmlPerms | (isset($sectionCount['content']) && $sectionCount['content'] > 0);

#layout
$templatePerms = check_permission($userid, 'Add Templates') | check_permission($userid, 'Modify Templates') |
          check_permission($userid, 'Remove Templates');
$cssPerms = check_permission($userid, 'Add Stylesheets') | check_permission($userid, 'Modify Stylesheets') |
          check_permission($userid, 'Remove Stylesheets');
$cssAssocPerms = check_permission($userid, 'Add Stylesheet Assoc') |
        check_permission($userid, 'Modify Stylesheet Assoc') |
          check_permission($userid, 'Remove Stylesheet Assoc');
$layoutPerms = $templatePerms | $cssPerms | $cssAssocPerms |
    (isset($sectionCount['layout']) && $sectionCount['layout'] > 0);

# file / image
$filePerms = check_permission($userid, 'Modify Files') |
    (isset($sectionCount['files']) && $sectionCount['files'] > 0);
    
# user/group
$userPerms = check_permission($userid, 'Add Users') | check_permission($userid, 'Modify Users') |
          check_permission($userid, 'Remove Users');
$groupPerms = check_permission($userid, 'Add Groups') | check_permission($userid, 'Modify Groups') |
          check_permission($userid, 'Remove Groups');
$groupPermPerms = check_permission($userid, 'Modify Permissions');
$groupMemberPerms =  check_permission($userid, 'Modify Group Assignments');
$usersGroupsPerms = $userPerms | $groupPerms | $groupPermPerms | $groupMemberPerms |
            (isset($sectionCount['usersgroups']) && $sectionCount['usersgroups'] > 0);

# admin
$sitePrefPerms = check_permission($userid, 'Modify Site Preferences') |
    (isset($sectionCount['preferences']) && $sectionCount['preferences'] > 0);
$adminPerms = $sitePrefPerms | (isset($sectionCount['admin']) && $sectionCount['admin'] > 0);


# extensions
$codeBlockPerms = check_permission($userid, 'Modify Code Blocks');
$modulePerms = check_permission($userid, 'Modify Modules');
$extensionsPerms = $codeBlockPerms | $modulePerms |
    (isset($sectionCount['extensions']) && $sectionCount['extensions'] > 0);


if (isset($CMS_ADMIN_TITLE))
    {
    $pagetitle = lang($CMS_ADMIN_TITLE);
    }
else
    {
    $pagetitle = lang('adminsystemtitle');
    }
if (isset($CMS_ADMIN_SUBTITLE))
    {
    $pagetitle .= " : ".$CMS_ADMIN_SUBTITLE;
    }

# add to recent pages, delete old
require_once("../lib/classes/class.recentpage.inc.php");

$rp = new RecentPage();
$rp->setValues($pagetitle, 'url', $userid);
$rp->Save();
$rp->PurgeOldPages($userid,5);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<title><?php echo $pagetitle ?></title>

<link rel="stylesheet" type="text/css" href="style.php" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="style.php?ie=1" />
<![endif]-->
<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->
<script type="text/javascript">
var state=false;
function toggleBookmarkState()
	{
	state = !state;
	if (state)
		{
		document.getElementById("BookmarkCallout").style.visibility = 'visible';
		}
	else
		{
		document.getElementById("BookmarkCallout").style.visibility = 'hidden';
		}
	}
</script>
</head>

<body##BODYSUBMITSTUFFGOESHERE##>
<div id="AdminHeader"></div>
<div id="AllAdmin">

<div id="MainContent">

<div id="BookmarkCallout">

<p class="DashboardCalloutTitle"><?php echo lang('bookmarks') ?></p>
<?php
	$marks = BookmarkOperations::LoadBookmarks($userid);
	echo "<ul>";
	if (count($marks) > 0)
		{
		foreach($marks as $mark)
			{
			echo "<li><a href=\"". $mark->url."\">".$mark->title."</a></li>\n";
			}
		echo "<li><a href=\"listbookmarks.php\">".lang('managebookmarks')."</a></li>\n";
		}
	else
		{
		echo "<li><a href=\"makebookmark.php?title=". urlencode($pagetitle). "\">".lang('addbookmark')."</a></li>\n";
		}
	echo "</ul>\n";

?>
</div>

<div class="DashboardCallout">

<p class="DashboardCalloutTitle">Recent Pages</p>
<?php
$recent = RecentPageOperations::LoadRecentPages($userid);
	echo "<ul>";
	if (count($recent) > 0)
		{
		foreach($recent as $pg)
			{
			echo "<li><a href=\"". $pg->url."\">".$pg->title."</a></li>\n";
			}
		}
	echo "</ul>\n";
?>
</div><!-- end DashboardCallout -->
