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
$CMS_TOP_MENU='admin';
$CMS_ADMIN_TITLE='preferences';

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$thisurl=basename(__FILE__).$urlext;

function siteprefs_display_permissions($permsarr)
{
  $tmparr = array(lang('owner'),lang('group'),lang('other'));
  if( count($permsarr) != 3 ) return lang('permissions_parse_error');

  $result = array();
  for( $i = 0; $i < 3; $i++ )
    {
      $str = $tmparr[$i].': ';
      $str .= implode(',',$permsarr[$i]);
      $result[] = $str;
    }
  $str = implode('<br/>&nbsp;&nbsp;',$result);
  return $str;
}

check_login();
$userid = get_userid();
$access = check_permission($userid, 'Modify Site Preferences');
if (!$access) {
	die('Permission Denied');
return;
}
global $gCms;
$db =& $gCms->GetDb();

$error = "";
$message = "";

$disablesafemodewarning = 0;
if (isset($_POST["disablesafemodewarning"])) $disablesafemodewarning = 1;

$allowparamcheckwarnings = 0;
if (isset($_POST["allowparamcheckwarnings"])) 
  {
    $allowparamcheckwarnings = 1;
  }

$enablenotifications = 1;
if (!isset($_POST["enablenotifications"])) 
  {
    $enablenotifications = 0;
  }

$enablecustom404 = "0";
if (isset($_POST["enablecustom404"])) $enablecustom404 = "1";

$xmlmodulerepository = "";
if (isset($_POST["xmlmodulerepository"])) $xmlmodulerepository = $_POST["xmlmodulerepository"];

$urlcheckversion = "";
if (isset($_POST["urlcheckversion"])) $urlcheckversion = $_POST["urlcheckversion"];

$defaultdateformat = "";
if (isset($_POST["defaultdateformat"])) $defaultdateformat = $_POST["defaultdateformat"];

$custom404 = "<p>Page not found<//p>";
if (isset($_POST["custom404"])) $custom404 = $_POST["custom404"];

$custom404template = "-1";
if (isset($_POST["custom404template"])) $custom404template = $_POST["custom404template"];

$enablesitedownmessage = "0";
if (isset($_POST["enablesitedownmessage"])) $enablesitedownmessage = "1";

$sitedownmessage = "<p>Site is currently down.  Check back later.</p>";
if (isset($_POST["sitedownmessage"])) $sitedownmessage = $_POST["sitedownmessage"];

$sitedownmessagetemplate = "-1";
if (isset($_POST["sitedownmessagetemplate"])) $sitedownmessagetemplate = $_POST["sitedownmessagetemplate"];

$metadata = '';
if (isset($_POST['metadata'])) $metadata = $_POST['metadata'];

$sitename = '';
if (isset($_POST['sitename'])) $sitename = cms_htmlentities($_POST['sitename']);

$css_max_age = 0;
if (isset($_POST['css_max_age'])) $css_max_age = (int)$_POST['css_max_age'];

#$useadvancedcss = "1";
#if (isset($_POST["useadvancedcss"])) $useadvancedcss = $_POST["useadvancedcss"];

$frontendlang = '';
if (isset($_POST['frontendlang'])) $frontendlang = $_POST['frontendlang'];

$frontendwysiwyg = '';
if (isset($_POST['frontendwysiwyg'])) $frontendwysiwyg = $_POST['frontendwysiwyg'];

$nogcbwysiwyg = '0';
if (isset($_POST['nogcbwysiwyg'])) $nogcbwysiwyg = '1';

$global_umask = '022';
if (isset($_POST['global_umask'])) 
  {
    $global_umask = $_POST['global_umask'];
  }

// ADDED
$logintheme = "default";
if (isset($_POST["logintheme"])) $logintheme = $_POST["logintheme"];
// STOP


$userid = get_userid();
$access = check_permission($userid, 'Modify Site Preferences');

if (isset($_POST["cancel"])) {
	redirect("index.php".$urlext);
	return;
}

$testresults = lang('untested');
if (isset($_POST["testumask"]))
{
  $testdir = TMP_CACHE_LOCATION;
  $testfile = $testdir.DIRECTORY_SEPARATOR.'dummy.tst';
  if( !is_writable($testdir) )
    {
      $testresults = lang('errordirectorynotwritable');
    }
  else
    {
      @umask(octdec($global_umask));
      
      $fh = @fopen($testfile,"w");      
      if( !$fh )
	{
	  $testresults = lang('errorcantcreatefile').' ('.$testfile.')';
	}
      else
	{
	  @fclose($fh);
	  $filestat = stat($testfile);
	  
	  if( $filestat == FALSE )
	    {
	      $testresults = lang('errorcantcreatefile');
	    }
	  
	  if(function_exists("posix_getpwuid")) //function posix_getpwuid not available on WAMP systems
	    {
	      $userinfo = @posix_getpwuid($filestat[4]);
	      
	      $username = isset($userinfo['name'])?$userinfo['name']:lang('unknown');
	      $permsstr = siteprefs_display_permissions(interpret_permissions($filestat[2]));
	      $testresults = sprintf("%s: %s<br/>%s:<br/>&nbsp;&nbsp;%s",
				     lang('owner'),$username,
				     lang('permissions'),$permsstr);
	      
	      
	    } else {
	    $testresults = sprintf("%s: %s<br/>%s:<br/>&nbsp;&nbsp;%s",
				   lang('owner'),"N/A",
				   lang('permissions'),"N/A");
	    
	  }
	  @unlink($testfile);
	}	
    }
  }
else if (isset($_POST['clearcache']))
{
	global $gCms;
	$contentops =& $gCms->GetContentOperations();
	$contentops->ClearCache();
	$message .= lang('cachecleared');
}
else if (isset($_POST["editsiteprefs"]))
{
  if ($access)
    {
      set_site_preference('global_umask', $global_umask);
      set_site_preference('frontendlang', $frontendlang);      
      set_site_preference('frontendwysiwyg', $frontendwysiwyg);
      set_site_preference('nogcbwysiwyg', $nogcbwysiwyg);
      set_site_preference('enablecustom404', $enablecustom404);
      set_site_preference('xmlmodulerepository', $xmlmodulerepository);
      set_site_preference('urlcheckversion', $urlcheckversion);
      if( isset($_POST["clear_vc_cache"])) 
	{
	  set_site_preference('lastchsversioncheck',0);
	}

      set_site_preference('defaultdateformat', $defaultdateformat);
      set_site_preference('custom404', $custom404);
      set_site_preference('custom404template', $custom404template);
      set_site_preference('enablesitedownmessage', $enablesitedownmessage);
      set_site_preference('sitedownmessage', $sitedownmessage);
#set_site_preference('sitedownmessagetemplate', $sitedownmessagetemplate);
#set_site_preference('useadvancedcss', $useadvancedcss);
      set_site_preference('logintheme', $logintheme);
      set_site_preference('metadata', $metadata);
      set_site_preference('css_max_age',$css_max_age);
      set_site_preference('sitename', $sitename);
      set_site_preference('disablesafemodewarning',$disablesafemodewarning);
      set_site_preference('allowparamcheckwarnings',$allowparamcheckwarnings);
      set_site_preference('enablenotifications',$enablenotifications);
      audit(-1, '', 'Edited Site Preferences');
      $message .= lang('prefsupdated');
    }
  else
    {
      $error .= "<li>".lang('noaccessto', array('Modify Site Permissions'))."</li>";
    }
 } else if (!isset($_POST["submit"])) {
  $global_umask = get_site_preference('global_umask',$global_umask);
  $frontendlang = get_site_preference('frontendlang');
  $frontendwysiwyg = get_site_preference('frontendwysiwyg');
  $nogcbwysiwyg = get_site_preference('nogcbwysiwyg');
  $enablecustom404 = get_site_preference('enablecustom404');
  $custom404 = get_site_preference('custom404');
  $custom404template = get_site_preference('custom404template');
  $enablesitedownmessage = get_site_preference('enablesitedownmessage');
  $sitedownmessage = get_site_preference('sitedownmessage');
  $xmlmodulerepository = get_site_preference('xmlmodulerepository');
  $urlcheckversion = get_site_preference('urlcheckversion');
  $defaultdateformat = get_site_preference('defaultdateformat');
  #$sitedownmessagetemplate = get_site_preference('sitedownmessagetemplate');
  #$useadvancedcss = get_site_preference('useadvancedcss');
  $logintheme = get_site_preference('logintheme', 'default');
  $metadata = get_site_preference('metadata', '');
  $css_max_age = (int)get_site_preference('css_max_age',0);
  $sitename = get_site_preference('sitename', 'CMSMS Site');
  $disablesafemodewarning = get_site_preference('disablesafemodewarning',0);
  $allowparamcheckwarnings = get_site_preference('allowparamcheckwarnings',0);
  $enablenotifications = get_site_preference('enablenotifications',1);
 }


$templates = array();
$templates['-1'] = lang('none');

$query = "SELECT * FROM ".cms_db_prefix()."templates ORDER BY template_name";
$result = $db->Execute($query);

while ($result && $row = $result->FetchRow())
{
	$templates[$row['template_id']] = $row['template_name'];
}

include_once("header.php");

if ($error != "") {
	echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$error."</ul></div>";	
}
if ($message != "") {
	echo $themeObject->ShowMessage($message);
}

// Make sure cache folder is writable
if (FALSE == is_writable(TMP_CACHE_LOCATION) || 
    FALSE == is_writable(TMP_TEMPLATES_C_LOCATION) )
{
  echo $themeObject->ShowErrors(lang('cachenotwritable'));
}

# give everything to smarty
$tmp = array_keys($gCms->modules);
$firstmod = $tmp[0];
$smarty->assign_by_ref('mod',$gCms->modules[$firstmod]['object']);
asort($nls["language"]);
$tmp = array(''=>lang('nodefault'));
foreach( $nls['language'] as $key=>$value )
{
  if( isset($nls['englishlang'][$key]) )
    {
      $value .= ' ('.$nls['englishlang'][$key].')';
    }
  $tmp[$key] = $value;
}
$smarty->assign('languages',$tmp);
$smarty->assign('templates',$templates);
if ($dir=opendir(dirname(__FILE__)."/themes/")) 
{ 
  $themes = array();
  while (($file = readdir($dir)) !== false )
    {
      if( @is_dir("themes/".$file) && ($file[0]!='.') &&
	  @is_readable("themes/{$file}/{$file}Theme.php"))
	{
	  $themes[$file] = $file;
	}
    }
  $smarty->assign('themes',$themes);
  $smarty->assign('logintheme',get_site_preference('logintheme','default'));
}


$smarty->assign('SECURE_PARAM_NAME',CMS_SECURE_PARAM_NAME);
$smarty->assign('CMS_USER_KEY',$_SESSION[CMS_USER_KEY]);
$smarty->assign('sitename',$sitename);
$smarty->assign('global_umask',$global_umask);
$smarty->assign('css_max_age',$css_max_age);
$smarty->assign('testresults',$testresults);
$smarty->assign('frontendwysiwyg',$frontendwysiwyg);
$smarty->assign('nogcbwysiwyg',$nogcbwysiwyg);
$smarty->assign('metadata',$metadata);
$smarty->assign('enablecustom404',$enablecustom404);
$smarty->assign('textarea_custom404',create_textarea(true,$custom404,'custom404','pagesmalltextarea'));
$smarty->assign('custom404template',$custom404template);
$smarty->assign('enablesitedownmessage',$enablesitedownmessage);
$smarty->assign('textarea_sitedownmessage',create_textarea(true,$sitedownmessage,'sitedownmessage','pagesmalltextarea'));
$smarty->assign('urlcheckversion',$urlcheckversion);
$smarty->assign('disablesafemodewarning',$disablesafemodewarning);
$smarty->assign('allowparamcheckwarnings',$allowparamcheckwarnings);
$smarty->assign('defaultdateformat',$defaultdateformat);
$smarty->assign('enablenotifications',$enablenotifications);

$smarty->assign('lang_general',lang('general_settings'));
$smarty->assign('lang_sitedown',lang('sitedown_settings'));
$smarty->assign('lang_handle404',lang('handle_404'));
$smarty->assign('lang_cancel',lang('cancel'));
$smarty->assign('lang_submit',lang('submit'));
$smarty->assign('lang_clearcache',lang('clearcache'));
$smarty->assign('lang_clear',lang('clear'));
$smarty->assign('lang_setup',lang('setup'));
$smarty->assign('lang_sitename',lang('sitename'));
$smarty->assign('lang_global_umask',lang('global_umask'));
$smarty->assign('lang_test',lang('test'));
$smarty->assign('lang_css_max_age',lang('css_max_age'));
$smarty->assign('lang_help_css_max_age',lang('help_css_max_age'));
$smarty->assign('lang_results',lang('results'));
$smarty->assign('lang_frontendlang',lang('frontendlang'));
$smarty->assign('lang_nogcbwysiwyg',lang('nogcbwysiwyg'));
$smarty->assign('lang_globalmetadata',lang('globalmetadata'));
$smarty->assign('lang_enablecustom404',lang('enablecustom404'));
$smarty->assign('lang_custom404',lang('custom404'));
$smarty->assign('lang_template',lang('template'));
$smarty->assign('lang_enablesitedown',lang('enablesitedown'));
$smarty->assign('lang_sitedownmessage',lang('sitedownmessage'));
$smarty->assign('lang_urlcheckversion',lang('urlcheckversion'));
$smarty->assign('lang_info_urlcheckversion',lang('info_urlcheckversion'));
$smarty->assign('lang_clear_version_check_cache',lang('clear_version_check_cache'));
$smarty->assign('lang_logintheme',lang('master_admintheme'));
$smarty->assign('lang_disablesafemodewarning',lang('disablesafemodewarning'));
$smarty->assign('lang_allowparamcheckwarnings',lang('allowparamcheckwarnings'));
$smarty->assign('lang_date_format_string',lang('date_format_string'));
$smarty->assign('lang_date_format_string_help',lang('date_format_string_help'));
$smarty->assign('lang_admin_enablenotifications',lang('admin_enablenotifications'));

# begin output
echo '<div class="pagecontainer">'.$themeObject->ShowHeader('siteprefs')."\n";
echo $smarty->fetch('siteprefs.tpl');
echo '</div>'."\n";
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>'."\n";
include_once("footer.php");


# vim:ts=4 sw=4 noet
?>
