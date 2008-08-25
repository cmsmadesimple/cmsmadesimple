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

      // Display notification stuff from modules
      // should be controlled by preferences or something
      if( get_site_preference('enablenotifications',1) &&
	  get_preference($userid,'enablenotifications',1) )
	{
	  foreach( $gCms->modules as $modulename => $ext )
	    {
	      $mod =& $gCms->modules[$modulename]['object'];
	      if( !is_object($mod) ) continue;
	      
	      $data = $mod->GetNotificationOutput(2); // todo, priority user preference
	      if( empty($data) ) continue;
	      if( is_object($data) )
		{
		  $themeObject->AddNotification($data->priority,
						$mod->GetName(),
						$data->html);
		}
	      else
		{
		  // we have more than one item
		  // for the dashboard from this module
		  if( is_array($data) )
		    {
		      foreach( $data as $item )
			{
			  $themeObject->AddNotification($item->priority,
						       $mod->GetName(),
						       $item->html);
			}
		    }
		}
	    }

	  // if the install directory still exists
	  // add a priority 1 dashboard item
	  if( file_exists(dirname(dirname(__FILE__)).'/install') )
	    {
	      $themeObject->AddNotification(1,'Core',
					    lang('installdirwarning'));
	    }
	}

      // and display the dashboard.
      $themeObject->DisplayNotifications();

      // we've removed the Recent Pages stuff, but other things could go in this box
      // so I'll leave some of the logic there. We can remove it later if it makes sense. SjG
      $marks = get_preference($userid, 'bookmarks');
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
