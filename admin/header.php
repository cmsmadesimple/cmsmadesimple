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
      $ignoredmodules = explode(',',get_preference($userid,'ignoredmodules'));
      if( get_site_preference('enablenotifications',1) &&
	  get_preference($userid,'enablenotifications',1) )
	{
	  foreach( $gCms->modules as $modulename => $ext )
	    {
	      if( in_array($modulename,$ignoredmodules) ) continue;
	      $mod =& $gCms->modules[$modulename]['object'];
	      if( !is_object($mod) ) continue;
	      
	      $data = $mod->GetNotificationOutput(3); // todo, priority user preference
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
	       $themeObject->AddNotification(1,'Core', lang('installdirwarning'));
	    }

          // Display a warning if safe mode is enabled
          if( ini_get_boolean('safe_mode') && get_site_preference('disablesafemodewarning',0) == 0 )
            {
               $themeObject->AddNotification(1,'Core',lang('warning_safe_mode'));
            }

          // Display an upgrade notification 
          // but only do a check once per day
          $timelastchecked = get_site_preference('lastcmsversioncheck',0);
          $tmpl = '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">%s</p></div></div>';
          $cms_is_uptodate = 1;
          $do_getpref = 0;
          $url = strtolower(trim(get_site_preference('urlcheckversion','')));
          if( $url != 'none' && !empty($url) &&
              ($timelastchecked < time() || isset($_GET['forceversioncheck'])) )
            {
                // check forced
                // get the url
                    $do_getpref = 1;
                $goodtest = false;
                if( empty($url) )
                {
                  $url = CMS_DEFAULT_VERSIONCHECK_URL;
                }
                if( $url == 'none')
                {
                  $cms_is_uptodate = 1;
                  $do_getpref = 0;
                }
                else
                {
                    // we have a 'theoretically' valid url
                        $txt = @file_get_contents($url);
                    if( $txt !== FALSE )
                    {
                        // the url worked
                        // do a version check
                            $goodtest = true;
                        $parts = explode(':',$txt);
                        if( is_array( $parts ) && 
                            strtolower($parts[0]) == 'cmsmadesimple' )
                        {
                          $ver = $parts[1];
                          $res = version_compare( CMS_VERSION, $ver );
                          if( $res < 0 )
                          {
                                // new version available
                                    $cms_is_uptodate = 0;
                                set_site_preference('cms_is_uptodate',0);
                          }
                          else
                          {
                                // the version is valid.
                                    set_site_preference('cms_is_uptodate',1);
                          }
                        } // if
                    } // if
                } // if

                // update the last check time
                // to midnight of the current day
                    if( $goodtest )
                {
                  set_site_preference('lastcmsversioncheck',
                                      strtotime("23:59:55"));
                }
            }

            if( $cms_is_uptodate == 0 || 
                  ($do_getpref == 1 && get_site_preference('cms_is_uptodate',1) == 0) )
              {
                // it wasn't up-to-date last time either
                  $themeObject->AddNotification(1,'Core',lang('new_version_available'));
              }


          // Display a warning about mail settings.
          if( isset($gCms->modules['CMSMailer']) && 
              isset($gCms->modules['CMSMailer']['object']) &&
	      isset($gCms->modules['CMSMailer']['installed']) &&
              get_site_preference('mail_is_set',0) == 0 )
            {
               $themeObject->AddNotification(1,'Core',lang('warning_mail_settings'));
            }
	}

      // and display the dashboard.
      $themeObject->DisplayNotifications(3); // todo, a preference.

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
