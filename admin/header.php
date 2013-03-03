<?php

cms_admin_sendheaders();
$orig_memory = (function_exists('memory_get_usage')?memory_get_usage():0);
$starttime = microtime();
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
{
	@ob_start();
}

$gCms = cmsms();
$config = $gCms->GetConfig();
$userid = get_userid();

if (isset($USE_THEME) && $USE_THEME == false)
{
  //echo '<!-- admin theme disabled -->';
}
else
{
  debug_buffer('before theme load');
  $themeObject = cms_utils::get_theme_object();
  debug_buffer('after theme load');

  if( isset($headtext) && $headtext != '' ) {
    $themeObject->set_value('headertext',$headtext);
  }

  // Display notification stuff from modules
  // should be controlled by preferences or something
  $ignoredmodules = explode(',',get_preference($userid,'ignoredmodules'));
  if( get_site_preference('enablenotifications',1) && get_preference($userid,'enablenotifications',1) )
    {
      debug_buffer('before notifications');
      if( ($data = get_site_preference('__NOTIFICATIONS__')) )
	{
	  $data = unserialize($data);
	  if( is_array($data) && count($data) )
	    {
	      foreach( $data as $item )
		{
		  $old = $item->html;
		  $regex = '/'.CMS_SECURE_PARAM_NAME.'\=[0-9a-z]{8}/';
		  $to = CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		  $new = preg_replace($regex,$to,$old);

		  $themeObject->AddNotification($item->priority,
						$item->name,
						$item->html);
		}
	    }
	}
		  
      // if the install directory still existsx
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
	
      // Display a warning sitedownwarning
      $sitedown_message = lang('sitedownwarning', TMP_CACHE_LOCATION . '/SITEDOWN');
      $sitedown_file = TMP_CACHE_LOCATION . '/SITEDOWN';
      if (file_exists($sitedown_file))
	{
	  $themeObject->AddNotification(1,'Core',$sitedown_message);
	}
	
      // Display an upgrade notification 
      // but only do a check once per day
      {
	$timelastchecked = get_site_preference('lastcmsversioncheck',0);
	if( (get_site_preference('checkversion',1) && (time() - $timelastchecked) > (24 * 60 * 60)) || isset($_GET['forceversioncheck']) )
	  {
	    $req = new cms_http_request();
	    $req->setTimeout(10);
	    $req->execute(CMS_DEFAULT_VERSIONCHECK_URL);
	    if( $req->getStatus() == 200 )
	      {
		$remote_ver = trim($req->getResult());
		if( strpos($remote_ver,':') !== FALSE )
		  {
		    list($tmp,$remote_ver) = explode(':',$remote_ver,2);
		    $remote_ver = trim($remote_ver);
		  }
		if( version_compare(CMS_VERSION,$remote_ver) < 0 )
		  {
		    set_site_preference('cms_is_uptodate',0);
		    $themeObject->AddNotification(1,'Core',lang('new_version_available'));
		    audit('','Core','CMSMS version '.$remote_ver.' is available');
		  }
		else
		  {
		    set_site_preference('cms_is_uptodate',1);
		    audit('','Core','Tested for newer CMSMS Version. None Available.');
		  }
	      }
	    set_site_preference('lastcmsversioncheck',mktime(23,59,55));
	  }
      }
    }

  $themeObject->do_header();
}
?>
