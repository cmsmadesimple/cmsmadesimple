<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

$current_user = CmsLogin::get_current_user();
$config = cms_config();
if ($current_user->is_anonymous())
{
	$_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
	CmsResponse::redirect($config["root_url"]."/".$config['admin_dir']."/login.php");
}

CmsAdminTheme::start();

$themeObject = CmsAdminTheme::get_instance();

cmsms()->variables['admintheme'] = CmsAdminTheme::get_instance();

$gCms = cmsms();
$db = cms_db();
$userid = $current_user->id;
// echo $userid ;


			  // Display notification stuff from modules
			  // should be controlled by preferences or something
			  $ignoredmodules = explode(',',get_preference($userid,'ignoredmodules'));
			  if( CmsApplication::get_preference('enablenotifications',1) &&
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
								$mod->get_name(),
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
									   $mod->get_name(),
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
			  if( ini_get_boolean('safe_mode') && CmsApplication::get_preference('disablesafemodewarning',0) == 0 )
				{
				   $themeObject->AddNotification(1,'Core',lang('warning_safe_mode'));
				}
			
				 // Display a warning sitedownwarning
					$sitedown_message = lang('sitedownwarning', TMP_CACHE_LOCATION . '/SITEDOWN');
					$sitedown_file = TMP_CACHE_LOCATION . '/SITEDOWN';
					if (file_exists($sitedown_file))
				{
					//$smarty->assign('sitedownwarning', $sitedown_file);
					 $themeObject->AddNotification(1,'Core',$sitedown_message);
				}
			
		         	// Display a warning if CMSMS needs upgrading
				$current_version = $CMS_SCHEMA_VERSION;
				$query = "SELECT version from ".cms_db_prefix()."version";
				$row = $db->GetRow($query);
				if ($row)
				{
					$current_version = $row["version"];
				} 
				
				$upgrade_message = '<em><strong>Warning:</strong></em> CMSMS is in need of an upgrade.<br />You are now running schema version '.$current_version." and you need to be upgraded to version ".$CMS_SCHEMA_VERSION.'.<br />Please click the following link: <a href="'.$config['root_url'].'/install/upgrade.php">Start upgrade process</a>.';
				
				if ($current_version < $CMS_SCHEMA_VERSION)
				{
				   $themeObject->AddNotification(1,'Core',$upgrade_message);
				}
				
				 // Display a warning about mail settings.
				if(CmsApplication::get_preference('mail_is_set') == 0 )
				{
				   $themeObject->AddNotification(1,'Core',lang('warning_mail_settings'));
				   //echo "DEBUG:" . CmsApplication::get_preference('mail_is_set');
				}
				
				
				
			/*  ################  STANDBY - NC 1x stuff
			
          // Display an upgrade notification 
          // but only do a check once per day
          $timelastchecked = CmsApplication::get_preference('lastcmsversioncheck',0);
          $tmpl = '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">%s</p></div></div>';
          $cms_is_uptodate = 1;
          $do_getpref = 0;
          $url = strtolower(trim(CmsApplication::get_preference('urlcheckversion','')));
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
			  
               */

        

	}

      // and display the dashboard.
      $themeObject->DisplayNotifications(3); // todo, a preference.
	  
	  
?>
