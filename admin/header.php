<?php

@ob_start();

include_once("../lib/classes/class.admintheme.inc.php");

$themeName=get_preference(get_userid(), 'admintheme', 'default');
$themeObjectName = $themeName."Theme";
$userid = get_userid();

if (file_exists(dirname(__FILE__)."/themes/${themeName}/${themeObjectName}.php"))
{
	include(dirname(__FILE__)."/themes/${themeName}/${themeObjectName}.php");
    $themeObject = new $themeObjectName($gCms, $userid, $themeName);
}
else
{
    $themeObject = new AdminTheme($gCms, $userid, $themeName);
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

$themeObject->DisplayDocType();
$themeObject->DisplayHTMLStartTag();
$themeObject->DisplayHTMLHeader();
$themeObject->DisplayBodyTag();
$themeObject->DoTopMenu($CMS_TOP_MENU,$_SERVER['SCRIPT_NAME'],$_SERVER['QUERY_STRING']);
$themeObject->DisplayMainDivStart();

$marks = get_preference($userid, 'bookmarks');
$recent = get_preference($userid, 'recent');
if ($marks || $recent)
    {
    $themeObject->StartRighthandColumn();
    if ($recent)
        {
        $themeObject->DoRecentPages();
        }
    if ($marks)
        {
        $themeObject->DoBookmarks();
        }

    $themeObject->EndRighthandColumn();
    }
?>
