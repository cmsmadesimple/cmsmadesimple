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
#$Id$

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU='main';
$CMS_ADMIN_TITLE='adminhome';
$CMS_ADMIN_TITLE='mainmenu';
$CMS_EXCLUDE_FROM_RECENT=1;

require_once("../include.php");

check_login();

global $gCms;
$db =& $gCms->GetDb();

include_once("header.php");
$themeObject->DisplayDashboardCallout(dirname(dirname(__FILE__)).'/install');
$themeObject->DisplayDashboardCallout(TMP_CACHE_LOCATION . '/SITEDOWN', lang('sitedownwarning', TMP_CACHE_LOCATION . '/SITEDOWN'));

// Display an upgrade notification 
// but only do a check once per day
$timelastchecked = get_site_preference('lastcmsversioncheck',0);
$tmpl = '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">%s</p></div></div>';
$cms_is_uptodate = 1;
if( $timelastchecked < time() || isset($_GET['forceversioncheck']) )
  {
    // check forced
    // get the url
    $goodtest = false;
    $url = trim(get_site_preference('urlcheckversion',''));
    if( empty($url) )
      {
	$url = CMS_DEFAULT_VERSIONCHECK_URL;
      }
    if( strtolower($url) != 'none')
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


// check cached
if( $cms_is_uptodate == 0 || get_site_preference('cms_is_uptodate',1) == 0 )
  {
    // it wasn't up-to-date last time either
    printf($tmpl,lang('new_version_available'));
  }

// Display a warning if safe mode is enabled
if( ini_get_boolean('safe_mode') && get_site_preference('disablesafemodewarning',0) == 0 )
  {
    echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">'.lang('warning_safe_mode').'</p></div></div>';
  }

// Display a warning if CMSMS needs upgrading
$current_version = $CMS_SCHEMA_VERSION;
$query = "SELECT version from ".cms_db_prefix()."version";
$row = $db->GetRow($query);
if ($row)
{
	$current_version = $row["version"];
}
if ($current_version < $CMS_SCHEMA_VERSION)
{
	echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror"><em><strong>Warning:</strong></em> CMSMS is in need of an upgrade.</p><p>You are now running schema version '.$current_version." and you need to be upgraded to version ".$CMS_SCHEMA_VERSION.'.</p><p>Please click the following link: <a href="'.$config['root_url'].'/install/upgrade.php">Start upgrade process</a>.</p></div></div>';
}
$themeObject->ShowShortcuts();
$themeObject->DisplaySectionMenuDivStart();
$themeObject->DisplayAllSectionPages();
$themeObject->DisplaySectionMenuDivEnd();
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
