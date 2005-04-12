<?php

@ob_start();

include_once("../lib/classes/class.admintheme.inc.php");

$themeName=get_preference(get_userid(), 'admintheme', 'default');
$themeObjectName = $themeName."Theme";
$userid = get_userid();

if (file_exists(dirname(__FILE__)."/themes/${themeName}/${themeObjectName}.php"))
{
	include(dirname(__FILE__)."/themes/${themeName}/${themeObjectName}.php");
    $themeObject = new $themeObjectName($gCms, $userid);
}
else
{
    $themeObject = new AdminTheme($gCms, $userid);
}

$themeObject->SendHeaders(isset($charsetsent), get_encoding());

if (isset($CMS_ADMIN_TITLE))
    {
    $themeObject->title = lang($CMS_ADMIN_TITLE);
    }
else
    {
    $themeObject->title = lang('adminsystemtitle');
    }
if (isset($CMS_ADMIN_SUBTITLE))
    {
    $themeObject->title .= " : ".$CMS_ADMIN_SUBTITLE;
    }

if (! isset($CMS_EXCLUDE_FROM_RECENT))
{
    $themeObject->AddAsRecentPage();
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $themeObject->title ?></title>
<link rel="stylesheet" type="text/css" href="style.php" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="style.php?ie=1" />
<![endif]-->
<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->
<?php $themeObject->OutputHeaderJavascript(); ?>
</head>

<body##BODYSUBMITSTUFFGOESHERE##>
<?php $themeObject->DoTopMenu(); ?>
<div id="AdminHeader"></div>

<div id="MainContent">
<?php
$themeObject->StartRighthandColumn();
$themeObject->DoRecentPages();
$themeObject->DoBookmarks();
$themeObject->EndRighthandColumn();
?>
