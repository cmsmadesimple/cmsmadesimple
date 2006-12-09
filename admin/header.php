<?php

if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
{
  @ob_start();
}

include_once("../lib/classes/class.admintheme.inc.php");

if (isset($USE_THEME) && $USE_THEME == false)
  {
    echo '<!-- admin theme disabled -->';
  }
else
  {
	$themeObject = AdminTheme::get_theme_for_user(get_userid());
    
    $gCms->variables['admintheme']=&$themeObject;
    if (isset($gCms->config['admin_encoding']) && $gCms->config['admin_encoding'] != '')
      {
	$themeObject->SendHeaders(isset($charsetsent), $gCms->config['admin_encoding']);
      }
    else
      {
	$themeObject->SendHeaders(isset($charsetsent), get_encoding('', false));
      }
    $themeObject->PopulateAdminNavigation(isset($CMS_ADMIN_SUBTITLE)?$CMS_ADMIN_SUBTITLE:'');
    
      $themeObject->DisplayDocType();
      $themeObject->DisplayHTMLStartTag();
      $themeObject->DisplayHTMLHeader(false, isset($headtext)?$headtext:'');
      $themeObject->DisplayBodyTag();
      $themeObject->DoTopMenu();
      $themeObject->DisplayMainDivStart();
      // we've removed the Recent Pages stuff, but other things could go in this box
      // so I'll leave some of the logic there. We can remove it later if it makes sense. SjG
      $marks = get_preference(get_userid(), 'bookmarks');
      if ($marks)
	{
	  $themeObject->StartRighthandColumn();
	  if ($marks)
	    {
	      $themeObject->DoBookmarks();
	    }
	  
	  $themeObject->EndRighthandColumn();
	}
  }
?>
