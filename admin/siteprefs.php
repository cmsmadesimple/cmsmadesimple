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


if (!$access) 
  {
    die('Permission Denied');
    return;
  }

$gCms = cmsms();
$db = $gCms->GetDb();
$config = $gCms->GetConfig();

$error = "";
$message = "";

$thumbnail_width = 96;
$thumbnail_height = 96;
$clear_vc_cache = 0;
$disablesafemodewarning = 0;
$allowparamcheckwarnings = 0;
$enablenotifications = 1;
$sitedownexcludes = '';
$sitedownexcludeadmins = '';
$basic_attributes = '';
$xmlmodulerepository = "";
$urlcheckversion = "";
$defaultdateformat = "";
$enablesitedownmessage = "0";
$use_wysiwyg = "1";
$sitedownmessage = "<p>Site is currently down.  Check back later.</p>";
$sitedownmessagetemplate = "-1";
$metadata = '';
$sitename = 'CMSMS Website';
$frontendlang = '';
$frontendwysiwyg = '';
$nogcbwysiwyg = '0';
$global_umask = '022';
$logintheme = "default";
$auto_clear_cache_age = 0;
$allow_browser_cache = 0;
$browser_cache_expiry = 60;
$pseudocron_granularity = 60;
$listcontent_showalias = 1;
$listcontent_showurl = 1;
$listcontent_showtitle = 1;
$content_autocreate_urls = 0;
$content_autocreate_flaturls = 0;
$content_mandatory_urls = 0;
$contentimage_useimagepath = 0;
$content_imagefield_path = '';
$content_thumbnailfield_path = '';
$contentimage_path = '';
$adminlog_lifetime = (60*60*24*31);

if (isset($_POST["cancel"])) {
	redirect("index.php".$urlext);
	return;
}


$pseudocron_granularity = get_site_preference('pseudocron_granularity',$pseudocron_granularity);
$allow_browser_cache = get_site_preference('allow_browser_cache',$allow_browser_cache);
$browser_cache_expiry = get_site_preference('browser_cache_expiry',$browser_cache_expiry);
$auto_clear_cache_age = get_site_preference('auto_clear_cache_age',$auto_clear_cache_age);
$thumbnail_width = get_site_preference('thumbnail_width',$thumbnail_width);
$thumbnail_height = get_site_preference('thumbnail_height',$thumbnail_height);
$global_umask = get_site_preference('global_umask',$global_umask);
$frontendlang = get_site_preference('frontendlang',$frontendlang);
$frontendwysiwyg = get_site_preference('frontendwysiwyg',$frontendwysiwyg);
$nogcbwysiwyg = get_site_preference('nogcbwysiwyg',$nogcbwysiwyg);
$enablesitedownmessage = get_site_preference('enablesitedownmessage',$enablesitedownmessage);
$use_wysiwyg = get_site_preference('sitedown_use_wysiwyg',$use_wysiwyg);
$sitedownmessage = get_site_preference('sitedownmessage',$sitedownmessage);
$xmlmodulerepository = get_site_preference('xmlmodulerepository',$xmlmodulerepository);
$urlcheckversion = get_site_preference('urlcheckversion',$urlcheckversion);
$defaultdateformat = get_site_preference('defaultdateformat',$defaultdateformat);
$logintheme = get_site_preference('logintheme',$logintheme);
$metadata = get_site_preference('metadata',$metadata);
$sitename = get_site_preference('sitename',$sitename);
$clear_vc_cache = get_site_preference('clear_vc_cache',$clear_vc_cache);
$disablesafemodewarning = get_site_preference('disablesafemodewarning',$disablesafemodewarning);
$allowparamcheckwarnings = get_site_preference('allowparamcheckwarnings',$allowparamcheckwarnings);
$enablenotifications = get_site_preference('enablenotifications',$enablenotifications);
$sitedownexcludes = get_site_preference('sitedownexcludes',$sitedownexcludes);
$sitedownexcludeadmins = get_site_preference('sitedownexcludeadmins',$sitedownexcludeadmins);
$basic_attributes = get_site_preference('basic_attributes',$basic_attributes);
$listcontent_showalias = get_site_preference('listcontent_showalias',$listcontent_showalias);
$listcontent_showurl = get_site_preference('listcontent_showurl',$listcontent_showurl);
$listcontent_showtitle = get_site_preference('listcontent_showtitle',$listcontent_showtitle);
$content_autocreate_urls = get_site_preference('content_autocreate_urls',$content_autocreate_urls);
$content_autocreate_flaturls = get_site_preference('content_autocreate_flaturls',$content_autocreate_flaturls);
$content_mandatory_urls = get_site_preference('content_mandatory_urls',$content_mandatory_urls);
$content_imagefield_path = get_site_preference('content_imagefield_path',$content_imagefield_path);
$content_thumbnailfield_path = get_site_preference('content_thumbnailfield_path',$content_thumbnailfield_path);
$contentimage_path = get_site_preference('contentimage_path',$contentimage_path);
$adminlog_lifetime = get_site_preference('adminlog_lifetime',$adminlog_lifetime);

$active_tab='unknown';
if( isset($_POST['active_tab']) )
  {
    $active_tab = trim($_POST['active_tab']);
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
	$gCms->clear_cached_files();
	audit(-1,'Core',lang_en('cachecleared'));
	$message .= lang('cachecleared');
}
else if (isset($_POST["editsiteprefs"]))
{
  if ($access)
    {
      switch( $active_tab )
	{
	case 'general':
	  // tab 1
	  if (isset($_POST['sitename'])) $sitename = cms_htmlentities($_POST['sitename']);
	  set_site_preference('sitename', $sitename);
	  if (isset($_POST['frontendlang'])) $frontendlang = $_POST['frontendlang'];
	  set_site_preference('frontendlang', $frontendlang);      
	  if (isset($_POST['frontendwysiwyg'])) $frontendwysiwyg = $_POST['frontendwysiwyg'];
	  set_site_preference('frontendwysiwyg', $frontendwysiwyg);
	  if (isset($_POST['metadata'])) $metadata = $_POST['metadata'];
	  set_site_preference('metadata', $metadata);
	  if (isset($_POST["logintheme"])) $logintheme = $_POST["logintheme"];
	  set_site_preference('logintheme', $logintheme);
	  if (isset($_POST["defaultdateformat"])) $defaultdateformat = $_POST["defaultdateformat"];
	  set_site_preference('defaultdateformat', $defaultdateformat);
	  if (isset($_POST['nogcbwysiwyg'])) $nogcbwysiwyg = $_POST['nogcbwysiwyg'];
	  set_site_preference('nogcbwysiwyg', $nogcbwysiwyg);
	  if( isset($_POST['thumbnail_width']) ) $thumbnail_width = (int)$_POST['thumbnail_width'];
	  if( isset($_POST['thumbnail_height']) ) $thumbnail_height = (int)$_POST['thumbnail_height'];
	  set_site_preference('thumbnail_width',$thumbnail_width);
	  set_site_preference('thumbnail_height',$thumbnail_height);
	  break;

	case 'editcontent':
	  $content_autocreate_urls = (int)$_POST['content_autocreate_urls'];
	  set_site_preference('content_autocreate_urls',$content_autocreate_urls);
	  $content_autocreate_flaturls = (int)$_POST['content_autocreate_flaturls'];
	  set_site_preference('content_autocreate_flaturls',$content_autocreate_flaturls);
	  $content_mandatory_urls = (int)$_POST['content_mandatory_urls'];
	  set_site_preference('content_mandatory_urls',$content_mandatory_urls);
	  $content_imagefield_path = trim($_POST['content_imagefield_path']);
	  set_site_preference('content_imagefield_path',$content_imagefield_path);
	  $content_thumbnailfield_path = trim($_POST['content_thumbnailfield_path']);
	  set_site_preference('content_thumbnailfield_path',$content_thumbnailfield_path);
	  $contentimage_path = trim($_POST['contentimage_path']);
	  set_site_preference('contentimage_path',$contentimage_path);
	  if( isset($_POST['basic_attributes']) )
	    {
	      $basic_attributes = implode(',',($_POST['basic_attributes']));
	    }
	  else
	    {
	      $basic_attributes = '';
	    }
	  set_site_preference('basic_attributes',$basic_attributes);
	  break;

	case 'listcontent':
	  $listcontent_showalias = (int)$_POST['listcontent_showalias'];
	  set_site_preference('listcontent_showalias',$listcontent_showalias);
	  $listcontent_showurl = (int)$_POST['listcontent_showurl'];
	  set_site_preference('listcontent_showurl',$listcontent_showurl);
	  $listcontent_showtitle = (int)$_POST['listcontent_showtitle'];
	  set_site_preference('listcontent_showtitle',$listcontent_showtitle);
	  break;

	case 'sitedown':
	  if( isset($_POST['sitedownexcludes']) )
	    {
	      $sitedownexcludes = trim($_POST['sitedownexcludes']);
	    }
	  $sitedownexcludeadmins = (int)$_POST['sitedownexcludeadmins'];
	  if (isset($_POST["enablesitedownmessage"])) $enablesitedownmessage=$_POST['enablesitedownmessage'];
	  if (isset($_POST["sitedownmessage"])) $sitedownmessage = $_POST["sitedownmessage"];
	  if (isset($_POST["use_wysiwyg"])) $use_wysiwyg = $_POST["use_wysiwyg"];
	  set_site_preference('enablesitedownmessage', $enablesitedownmessage);
	  set_site_preference('sitedown_use_wysiwyg', $use_wysiwyg);
	  set_site_preference('sitedownmessage', $sitedownmessage);
	  set_site_preference('sitedownexcludes',$sitedownexcludes);
	  set_site_preference('sitedownexcludeadmins',$sitedownexcludeadmins);
	  break;

	case 'setup':
	  if (isset($_POST["clear_vc_cache"])) $clear_vc_cache = $_POST['clear_vc_cache'];
	  if (isset($_POST["disablesafemodewarning"])) $disablesafemodewarning = $_POST['disablesafemodewarning'];
	  if (isset($_POST["allowparamcheckwarnings"])) $allowparamcheckwarnings = $_POST['allowparamcheckwarnings'];
	  if (isset($_POST["enablenotifications"])) $enablenotifications = $_POST['enablenotifications'];
	  if (isset($_POST["xmlmodulerepository"])) $xmlmodulerepository = $_POST["xmlmodulerepository"];
	  if (isset($_POST["urlcheckversion"])) $urlcheckversion = $_POST["urlcheckversion"];
	  if (isset($_POST['global_umask'])) 
	    {
	      $global_umask = $_POST['global_umask'];
	    }
	  set_site_preference('global_umask', $global_umask);
	  set_site_preference('xmlmodulerepository', $xmlmodulerepository);
	  set_site_preference('urlcheckversion', $urlcheckversion);
	  if( isset($_POST["clear_vc_cache"])) 
	    {
	      set_site_preference('lastcmsversioncheck',0);
	    }
	  set_site_preference('clear_vc_cache', $clear_vc_cache);
	  set_site_preference('disablesafemodewarning',$disablesafemodewarning);
	  set_site_preference('allowparamcheckwarnings',$allowparamcheckwarnings);
	  set_site_preference('enablenotifications',$enablenotifications);
	  if( isset($_POST['allow_browser_cache']) )
	    {
	      $allow_browser_cache = (int)$_POST['allow_browser_cache'];
	      set_site_preference('allow_browser_cache',$allow_browser_cache);
	    }
	  if( isset($_POST['browser_cache_expiry']) )
	    {
	      $browser_cache_expiry = (int)$_POST['browser_cache_expiry'];
	      set_site_preference('browser_cache_expiry',$browser_cache_expiry);
	    }
	  if( isset($_POST['auto_clear_cache_age']) )
	    {
	      $auto_clear_cache_age = (int)$_POST['auto_clear_cache_age'];
	      set_site_preference('auto_clear_cache_age',$auto_clear_cache_age);
	    }
	  if( isset($_POST['pseudocron_granularity']) )
	    {
	      $pseudocron_granularity = (int)$_POST['pseudocron_granularity'];
	      set_site_preference('pseudocron_granularity',$pseudocron_granularity);
	    }
      if (isset($_POST["adminlog_lifetime"])) {
          $adminlog_lifetime = $_POST["adminlog_lifetime"];
          set_site_preference('adminlog_lifetime',$adminlog_lifetime);
      }
	  break;
	}

      audit(-1, '', lang_en('prefsupdated'));
      $message .= lang('prefsupdated');
    }
  else
    {
      $error .= "<li>".lang('noaccessto', array('Modify Site Permissions'))."</li>";
    }
} 

//
// build the form
//
$templates = array();
$templates['-1'] = lang('none');

$query = "SELECT * FROM ".cms_db_prefix()."templates where active = 1 ORDER BY template_name";
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
$allmodules = ModuleOperations::get_instance()->GetLoadedModules();
$tmp = array_keys($allmodules);
if( !is_array($tmp) || count($tmp) == 0 )
{
  echo $themeObject->ShowErrors(lang('error_nomodules'));
  return;
}
$firstmod = $tmp[0];
$smarty->assign_by_ref('mod',$allmodules[$tmp[0]]);

$smarty->assign('languages',get_language_list());
$smarty->assign('templates',$templates);

$tmp = array(''=>lang('none'));
foreach( $allmodules as $key => &$object )
{
  if( $object->IsWYSIWYG() )
    {
      if (isset($tmp[$key])) $tmp[$key].= $key; else $tmp[$key]=$key;
    }
}
$smarty->assign('wysiwyg',$tmp);

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


$smarty->assign('active_general',($active_tab == 'general')?1:0);
$smarty->assign('active_listcontent',($active_tab == 'listcontent')?1:0);
$smarty->assign('active_editcontent',($active_tab == 'editcontent')?1:0);
$smarty->assign('active_sitedown',($active_tab == 'sitedown')?1:0);
$smarty->assign('active_setup',($active_tab == 'setup')?1:0);

$smarty->assign('SECURE_PARAM_NAME',CMS_SECURE_PARAM_NAME);
$smarty->assign('CMS_USER_KEY',$_SESSION[CMS_USER_KEY]);
$smarty->assign('sitename',$sitename);
$smarty->assign('global_umask',$global_umask);
$smarty->assign('testresults',$testresults);
$smarty->assign('frontendlang',$frontendlang);
$smarty->assign('frontendwysiwyg',$frontendwysiwyg);
$smarty->assign('nogcbwysiwyg',$nogcbwysiwyg);
$smarty->assign('metadata',$metadata);
$smarty->assign('enablesitedownmessage',$enablesitedownmessage);
$smarty->assign('use_wysiwyg',$use_wysiwyg);
$smarty->assign('textarea_sitedownmessage',create_textarea($use_wysiwyg,$sitedownmessage,'sitedownmessage','pagesmalltextarea'));
$smarty->assign('urlcheckversion',$urlcheckversion);
$smarty->assign('clear_vc_cache',$clear_vc_cache);
$smarty->assign('disablesafemodewarning',$disablesafemodewarning);
$smarty->assign('allowparamcheckwarnings',$allowparamcheckwarnings);
$smarty->assign('defaultdateformat',$defaultdateformat);
$smarty->assign('enablenotifications',$enablenotifications);
$smarty->assign('sitedownexcludes',$sitedownexcludes);
$smarty->assign('sitedownexcludeadmins',$sitedownexcludeadmins);
$smarty->assign('basic_attributes',explode(',',$basic_attributes));
$smarty->assign('thumbnail_width',$thumbnail_width);
$smarty->assign('thumbnail_height',$thumbnail_height);
$smarty->assign('allow_browser_cache',$allow_browser_cache);
$smarty->assign('browser_cache_expiry',$browser_cache_expiry);
$smarty->assign('auto_clear_cache_age',$auto_clear_cache_age);
$smarty->assign('pseudocron_granularity',$pseudocron_granularity);
$smarty->assign('listcontent_showalias',$listcontent_showalias);
$smarty->assign('listcontent_showurl',$listcontent_showurl);
$smarty->assign('listcontent_showtitle',$listcontent_showtitle);
$smarty->assign('content_autocreate_urls',$content_autocreate_urls);
$smarty->assign('content_autocreate_flaturls',$content_autocreate_flaturls);
$smarty->assign('content_mandatory_urls',$content_mandatory_urls);
$smarty->assign('content_imagefield_path',$content_imagefield_path);
$smarty->assign('content_thumbnailfield_path',$content_thumbnailfield_path);
$smarty->assign('contentimage_path',$contentimage_path);
$smarty->assign('adminlog_lifetime',$adminlog_lifetime);

$tmp = array(15=>lang('cron_15m'),30=>lang('cron_30m'),
	     60=>lang('cron_60m'),120=>lang('cron_120m'),
	     180=>lang('cron_3h'),360=>lang('cron_6h'),
	     12*60=>lang('cron_12h'),
	     24*60=>lang('cron_24h'),
	     -1=>lang('cron_request'));
$smarty->assign('pseudocron_options',$tmp);	     
$smarty->assign('lang_pseudocron_granularity',lang('pseudocron_granularity'));
$smarty->assign('lang_info_pseudocron_granularity',lang('info_pseudocron_granularity'));

$tmp = array(
         60*60*24=>lang('adminlog_1day'),
         60*60*24*7=>lang('adminlog_1week'),
	     60*60*24*14=>lang('adminlog_2weeks'),
	     60*60*24*31=>lang('adminlog_1month'),
	     60*60*24*31*3=>lang('adminlog_3months'),
	     60*60*24*31*6=>lang('adminlog_6months'),
	     -1=>lang('adminlog_manual'));
$smarty->assign('adminlog_options',$tmp);
$smarty->assign('lang_adminlog_lifetime',lang('adminlog_lifetime'));
$smarty->assign('lang_info_adminlog_lifetime',lang('info_adminlog_lifetime'));


$smarty->assign('lang_info_autoclearcache',lang('info_autoclearcache'));
$smarty->assign('lang_autoclearcache',lang('autoclearcache'));
$smarty->assign('lang_thumbnail_width',lang('thumbnail_width'));
$smarty->assign('lang_thumbnail_height',lang('thumbnail_height'));
$smarty->assign('lang_general',lang('general_settings'));
$smarty->assign('lang_listcontent',lang('listcontent_settings'));
$smarty->assign('lang_sitedown',lang('sitedown_settings'));
$smarty->assign('lang_cancel',lang('cancel'));
$smarty->assign('lang_submit',lang('submit'));
$smarty->assign('lang_clearcache',lang('clearcache'));
$smarty->assign('lang_clear',lang('clear'));
$smarty->assign('lang_setup',lang('setup'));
$smarty->assign('lang_sitename',lang('sitename'));
$smarty->assign('lang_global_umask',lang('global_umask'));
$smarty->assign('lang_test',lang('test'));
$smarty->assign('lang_results',lang('results'));
$smarty->assign('lang_frontendlang',lang('frontendlang'));
$smarty->assign('lang_frontendwysiwygtouse',lang('frontendwysiwygtouse'));
$smarty->assign('lang_nogcbwysiwyg',lang('nogcbwysiwyg'));
$smarty->assign('lang_globalmetadata',lang('globalmetadata'));
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
$smarty->assign('lang_sitedownexcludes',lang('sitedownexcludes'));
$smarty->assign('lang_info_sitedownexcludes',lang('info_sitedownexcludes'));
$smarty->assign('lang_basic_attributes',lang('basic_attributes'));
$smarty->assign('lang_info_basic_attributes',lang('info_basic_attributes'));
$smarty->assign('lang_editcontent_settings',lang('editcontent_settings'));
$smarty->assign('lang_enablewysiwyg',lang('enablewysiwyg'));

$all_attributes = array();
$all_attributes['template'] = lang('template');
$all_attributes['active'] = lang('active');
$all_attributes['secure'] = lang('secure_page');
$all_attributes['showinmenu'] = lang('showinmenu');
$all_attributes['cachable'] = lang('cachable');
$all_attributes['target'] = lang('target');
$all_attributes['alias'] = lang('pagealias');
$all_attributes['image'] = lang('image');
$all_attributes['thumbnail'] = lang('thumbnail');
$all_attributes['pagemetadata'] = lang('page_metadata');
$all_attributes['titleattribute'] = lang('titleattribute');
$all_attributes['tabindex'] = lang('tabindex');
$all_attributes['accesskey'] = lang('accesskey');
$all_attributes['pagedata'] = lang('pagedata_codeblock');
$all_attributes['searchable'] = lang('searchable');
$all_attributes['extra1'] = lang('extra1');
$all_attributes['extra2'] = lang('extra2');
$all_attributes['extra3'] = lang('extra3');
$all_attributes['additionaleditors'] = lang('additionaleditors');
$all_attributes['page_url'] = lang('page_url');
$smarty->assign('all_attributes',$all_attributes);

$yesno = array(0=>lang('no'),1=>lang('yes'));
$smarty->assign('yesno',$yesno);

$titlemenu = array(0=>lang('menutext'),1=>lang('title'));
$smarty->assign('titlemenu',$titlemenu);

# begin output
echo '<div class="pagecontainer">'.$themeObject->ShowHeader('siteprefs')."\n";
echo $smarty->fetch('siteprefs.tpl');
echo '</div>'."\n";
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>'."\n";
include_once("footer.php");


# vim:ts=4 sw=4 noet
?>
