<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

/**
 * Init variables / objects
 */

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU='admin';
$CMS_ADMIN_TITLE='preferences';

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$thisurl=basename(__FILE__).$urlext;
$userid = get_userid(); // <- Checks also login

/** 
 * A convenience function to interpret octal permissions, and return 
 * a human readable string.  Uses the lang() function for translation.
 *
 * @internal
 * @param int The permissions to test.
 * @return string
 */
function siteprefs_interpret_permissions($perms)
{
  $owner = array();
  $group = array();
  $other = array();

  if( $perms & 0400 ) $owner[] = lang('read');
  if( $perms & 0200 ) $owner[] = lang('write');
  if( $perms & 0100 ) $owner[] = lang('execute');
  if( $perms & 0040 ) $group[] = lang('read');
  if( $perms & 0020 ) $group[] = lang('write');
  if( $perms & 0010 ) $group[] = lang('execute');
  if( $perms & 0004 ) $other[] = lang('read');
  if( $perms & 0002 ) $other[] = lang('write');
  if( $perms & 0001 ) $other[] = lang('execute');

  return array($owner,$group,$other);
}


function siteprefs_display_permissions($permsarr)
{
  $tmparr = array(lang('owner'),lang('group'),lang('other'));
  if( count($permsarr) != 3 ) return lang('permissions_parse_error');

  $result = array();
  for( $i = 0; $i < 3; $i++ ) {
    $str = $tmparr[$i].': ';
    $str .= implode(',',$permsarr[$i]);
    $result[] = $str;
  }
  $str = implode('<br/>&nbsp;&nbsp;',$result);
  return $str;
}

$access = check_permission($userid, 'Modify Site Preferences');
if (!$access) {
  die('Permission Denied'); // <- Pretty cruel huh? maybe redirection and message, or something. -Stikki-
  return;
}

$gCms = cmsms();
$db = $gCms->GetDb();
$config = $gCms->GetConfig();

$pretty_urls = $config['url_rewriting'] == 'none' ? 0 : 1;
$error = "";
$message = "";
$mail_is_set = cms_siteprefs::get('mail_is_set',0);
$testresults = lang('untested');
$thumbnail_width = 96;
$thumbnail_height = 96;
$disablesafemodewarning = 0;
$enablenotifications = 1;
$sitedownexcludes = '';
$sitedownexcludeadmins = '';
$disallowed_contenttypes = '';
$basic_attributes = null;
$xmlmodulerepository = "";
$checkversion = 1;
$defaultdateformat = "";
$enablesitedownmessage = "0";
$use_wysiwyg = "1";
$sitedownmessage = "<p>Site is currently down.  Check back later.</p>";
$sitedownmessagetemplate = "-1";
$metadata = '';
$sitename = 'CMSMS Website';
$frontendlang = '';
$frontendwysiwyg = '';
$global_umask = '022';
$logintheme = "default";
$backendwysiwyg = '';
$auto_clear_cache_age = 0;
$allow_browser_cache = 0;
$browser_cache_expiry = 60;
$pseudocron_granularity = 60;
$content_autocreate_urls = 0;
$content_autocreate_flaturls = 0;
$content_mandatory_urls = 0;
$contentimage_useimagepath = 0;
$content_imagefield_path = '';
$content_thumbnailfield_path = '';
$content_cssnameisblockname = 1;
$contentimage_path = '';
$adminlog_lifetime = (60*60*24*31);
$search_module = 'Search';
$use_smartycache = 0;
$use_smartycompilecheck = 1;
$mailprefs = array('mailer'=>'mail',
		   'host'=>'localhost',
		   'port'=>25,
		   'from'=>'root@localhost.localdomain',
		   'fromuser'=>'CMS Administrator',
		   'sendmail'=>'/usr/sbin/sendmail',
		   'smtpauth'=>0,
		   'username'=>'',
		   'password'=>'',
		   'secure'=>'',
		   'timeout'=>60,
		   'charset'=>'utf-8');

if (isset($_POST["cancel"])) {
  redirect("index.php".$urlext);
  return;
}

/**
 * Get preferences
 */
$pseudocron_granularity = cms_siteprefs::get('pseudocron_granularity',$pseudocron_granularity);
$allow_browser_cache = cms_siteprefs::get('allow_browser_cache',$allow_browser_cache);
$browser_cache_expiry = cms_siteprefs::get('browser_cache_expiry',$browser_cache_expiry);
$auto_clear_cache_age = cms_siteprefs::get('auto_clear_cache_age',$auto_clear_cache_age);
$thumbnail_width = cms_siteprefs::get('thumbnail_width',$thumbnail_width);
$thumbnail_height = cms_siteprefs::get('thumbnail_height',$thumbnail_height);
$global_umask = cms_siteprefs::get('global_umask',$global_umask);
$frontendlang = cms_siteprefs::get('frontendlang',$frontendlang);
$frontendwysiwyg = cms_siteprefs::get('frontendwysiwyg',$frontendwysiwyg);
$enablesitedownmessage = cms_siteprefs::get('enablesitedownmessage',$enablesitedownmessage);
$use_wysiwyg = cms_siteprefs::get('sitedown_use_wysiwyg',$use_wysiwyg);
$sitedownmessage = cms_siteprefs::get('sitedownmessage',$sitedownmessage);
$xmlmodulerepository = cms_siteprefs::get('xmlmodulerepository',$xmlmodulerepository);
$checkversion = cms_siteprefs::get('checkversion',$checkversion);
$defaultdateformat = cms_siteprefs::get('defaultdateformat',$defaultdateformat);
$logintheme = cms_siteprefs::get('logintheme',$logintheme);
$backendwysiwyg = cms_siteprefs::get('backendwysiwyg',$backendwysiwyg);
$metadata = cms_siteprefs::get('metadata',$metadata);
$sitename = cms_siteprefs::get('sitename',$sitename);
$disablesafemodewarning = cms_siteprefs::get('disablesafemodewarning',$disablesafemodewarning);
$enablenotifications = cms_siteprefs::get('enablenotifications',$enablenotifications);
$sitedownexcludes = cms_siteprefs::get('sitedownexcludes',$sitedownexcludes);
$sitedownexcludeadmins = cms_siteprefs::get('sitedownexcludeadmins',$sitedownexcludeadmins);
$disallowed_contenttypes = cms_siteprefs::get('disallowed_contenttypes',$disallowed_contenttypes);
$basic_attributes = cms_siteprefs::get('basic_attributes',$basic_attributes);
$content_autocreate_urls = cms_siteprefs::get('content_autocreate_urls',$content_autocreate_urls);
$content_autocreate_flaturls = cms_siteprefs::get('content_autocreate_flaturls',$content_autocreate_flaturls);
$content_mandatory_urls = cms_siteprefs::get('content_mandatory_urls',$content_mandatory_urls);
$content_imagefield_path = cms_siteprefs::get('content_imagefield_path',$content_imagefield_path);
$content_thumbnailfield_path = cms_siteprefs::get('content_thumbnailfield_path',$content_thumbnailfield_path);
$content_cssnameisblockname = cms_siteprefs::get('content_cssnameisblockname',$content_cssnameisblockname);
$contentimage_path = cms_siteprefs::get('contentimage_path',$contentimage_path);
$adminlog_lifetime = cms_siteprefs::get('adminlog_lifetime',$adminlog_lifetime);
$search_module = cms_siteprefs::get('searchmodule',$search_module);
$use_smartycache = cms_siteprefs::get('use_smartycache',$use_smartycache);
$use_smartycompilecheck = cms_siteprefs::get('use_smartycompilecheck',$use_smartycompilecheck);
$tmp = cms_siteprefs::get('mailprefs');
if( $tmp ) $mailprefs = unserialize($tmp);

/**
 * Check tab
 */
$tab='';
if( isset($_POST['active_tab']) ) $tab = trim($_POST['active_tab']);

/**
 * Submit 
 */
if( isset($_POST['testmail']) ) {
  if( !$mail_is_set ) {
    $error .= '<li>'.lang('error_mailnotset_notest').'</li>';
  }
  else if( $_POST['mailtest_testaddress'] == '' ) {
    $error .= '<li>'.lang('error_mailtest_noaddress').'</li>';
  }
  else {
    $addr = strip_tags($_POST['mailtest_testaddress']);
    if( !is_email($addr) ) {
      $error .= '<li>'.lang('error_mailtest_notemail').'</li>';
    }
    else {
      // we got an email, and we have settings.
      $mailer = new cms_mailer();
      $mailer->AddAddress($addr);
      $mailer->IsHTML(TRUE);
      $mailer->SetBody(lang('mail_testbody','siteprefs'));
      $mailer->SetSubject(lang('mail_testsubject','siteprefs'));
      $mailer->Send();
      if( $mailer->IsError() ) {
	$error .= '<li>'.$mailer->GetErrorInfo().'</li>';
      }
      else {
	$message .= lang('testmsg_success');
      }
    }
  }
}

if (isset($_POST["testumask"])) {
  $testdir = TMP_CACHE_LOCATION;
  $testfile = $testdir.DIRECTORY_SEPARATOR.'dummy.tst';
  if( !is_writable($testdir) ) {
    $testresults = lang('errordirectorynotwritable');
  }
  else {
    @umask(octdec($global_umask));
      
    $fh = @fopen($testfile,"w");      
    if( !$fh ) {
      $testresults = lang('errorcantcreatefile').' ('.$testfile.')';
    }
    else {
      @fclose($fh);
      $filestat = stat($testfile);
      if( $filestat == FALSE ) $testresults = lang('errorcantcreatefile');

      if(function_exists("posix_getpwuid")) {
	//function posix_getpwuid not available on WAMP systems
	$userinfo = @posix_getpwuid($filestat[4]);
	$username = isset($userinfo['name'])?$userinfo['name']:lang('unknown');
	$permsstr = siteprefs_display_permissions(siteprefs_interpret_permissions($filestat[2]));
	$testresults = sprintf("%s: %s<br/>%s:<br/>&nbsp;&nbsp;%s",lang('owner'),$username,lang('permissions'),$permsstr);
      } else {
	$testresults = sprintf("%s: %s<br/>%s:<br/>&nbsp;&nbsp;%s",lang('owner'),"N/A",lang('permissions'),"N/A");
      }
      @unlink($testfile);
    }	
  }
}

if (isset($_POST["editsiteprefs"])) {
  if ($access) {
    switch( $tab ) {
    case 'general':
      // tab 1
      if (isset($_POST['sitename'])) $sitename = cms_htmlentities($_POST['sitename']);
      cms_siteprefs::set('sitename', $sitename);
      if (isset($_POST['frontendlang'])) $frontendlang = $_POST['frontendlang'];
      cms_siteprefs::set('frontendlang', $frontendlang);      
      if (isset($_POST['frontendwysiwyg'])) $frontendwysiwyg = $_POST['frontendwysiwyg'];
      cms_siteprefs::set('frontendwysiwyg', $frontendwysiwyg);
      if (isset($_POST['metadata'])) $metadata = $_POST['metadata'];
      cms_siteprefs::set('metadata', $metadata);
      if (isset($_POST["logintheme"])) $logintheme = $_POST["logintheme"];
      cms_siteprefs::set('logintheme', $logintheme);
      if (isset($_POST['backendwysiwyg'])) $backendwysiwyg = $_POST['backendwysiwyg'];
      cms_siteprefs::set('backendwysiwyg', $backendwysiwyg);	  
      if (isset($_POST["defaultdateformat"])) $defaultdateformat = $_POST["defaultdateformat"];
      cms_siteprefs::set('defaultdateformat', $defaultdateformat);
      if( isset($_POST['thumbnail_width']) ) $thumbnail_width = (int)$_POST['thumbnail_width'];
      if( isset($_POST['thumbnail_height']) ) $thumbnail_height = (int)$_POST['thumbnail_height'];
      cms_siteprefs::set('thumbnail_width',$thumbnail_width);
      cms_siteprefs::set('thumbnail_height',$thumbnail_height);
      if( isset($_POST['search_module']) ) {
	$search_module = trim($_POST['search_module']);
	cms_siteprefs::set('searchmodule',$search_module);
      }
      break;

    case 'editcontent':
  	  if( $pretty_urls ) {
		  $content_autocreate_urls = (int)$_POST['content_autocreate_urls'];
		  cms_siteprefs::set('content_autocreate_urls',$content_autocreate_urls);
		  $content_autocreate_flaturls = (int)$_POST['content_autocreate_flaturls'];
		  cms_siteprefs::set('content_autocreate_flaturls',$content_autocreate_flaturls);
		  $content_mandatory_urls = (int)$_POST['content_mandatory_urls'];
		  cms_siteprefs::set('content_mandatory_urls',$content_mandatory_urls);
	  }
      $content_imagefield_path = trim($_POST['content_imagefield_path']);
      cms_siteprefs::set('content_imagefield_path',$content_imagefield_path);
      $content_thumbnailfield_path = trim($_POST['content_thumbnailfield_path']);
      cms_siteprefs::set('content_thumbnailfield_path',$content_thumbnailfield_path);
      $contentimage_path = trim($_POST['contentimage_path']);
      cms_siteprefs::set('contentimage_path',$contentimage_path);
	  $content_cssnameisblockname = (int)$_POST['content_cssnameisblockname'];
	  cms_siteprefs::set('content_cssnameisblockname',$content_cssnameisblockname);
      if( isset($_POST['basic_attributes']) ) {
		  $basic_attributes = implode(',',($_POST['basic_attributes']));
      }
      else {
		  $basic_attributes = null;
      }
      cms_siteprefs::set('basic_attributes',$basic_attributes);
      $disallowed_contenttypes = '';
      if( isset($_POST['disallowed_contenttypes']) ) $disallowed_contenttypes = implode(',',$_POST['disallowed_contenttypes']);
      cms_siteprefs::set('disallowed_contenttypes',$disallowed_contenttypes);
      break;

    case 'sitedown':
      if( isset($_POST['sitedownexcludes']) ) $sitedownexcludes = trim($_POST['sitedownexcludes']);
      $sitedownexcludeadmins = (int)$_POST['sitedownexcludeadmins'];
	  $prevsitedown = $enablesitedownmessage;
      if (isset($_POST["enablesitedownmessage"])) $enablesitedownmessage=$_POST['enablesitedownmessage'];
      if (isset($_POST["sitedownmessage"])) $sitedownmessage = $_POST["sitedownmessage"];
      if (isset($_POST["use_wysiwyg"])) $use_wysiwyg = $_POST["use_wysiwyg"];
	  if( !$prevsitedown && $enablesitedownmessage ) {
		  audit('','Global Settings','Sitedown enabled');
	  }
	  else if( $prevsitedown && !$enablesitedownmessage ) {
		  audit('','Global Settings','Sitedown disabled');
	  }
      cms_siteprefs::set('enablesitedownmessage', $enablesitedownmessage);
      cms_siteprefs::set('sitedown_use_wysiwyg', $use_wysiwyg);
      cms_siteprefs::set('sitedownmessage', $sitedownmessage);
      cms_siteprefs::set('sitedownexcludes',$sitedownexcludes);
      cms_siteprefs::set('sitedownexcludeadmins',$sitedownexcludeadmins);
      break;

    case 'mail':
      // gather mailprefs
      $prefix = 'mailprefs_';
      foreach( $_POST as $key => $val ) {
	if( !startswith($key,$prefix) ) continue;
	$key = substr($key,strlen($prefix));
	
	$mailprefs[$key] = trim($val);
      }

      // validate
      if( $mailprefs['from'] == '' ) {
	$error  .= '<li>'.lang('error_fromrequired').'</li>';
      }
      else if( !is_email($mailprefs['from']) ) {
	$error .= '<li>'.lang('error_frominvalid').'</li>';
      }
      if( $mailprefs['mailer'] == 'smtp' ) {
	if( $mailprefs['host'] == '' ) {
	  $error .= '<li>'.lang('error_hostrequired').'</li>';
	}
	if( $mailprefs['port'] == '' ) $mailprefs['port'] = 25; // convenience.
	if( $mailprefs['port'] < 1 || $mailprefs['port'] > 10240 ) {
	  $error .= '<li>'.lang('error_portinvalid').'</li>';
	}
	if( $mailprefs['timeout'] == '' ) $mailprefs['timeout'] = 180;
	if( $mailprefs['timeout'] < 1 || $mailprefs['timeout'] > 3600 ) {
	  $error .= '<li>'.lang('error_timeoutinvalid').'</li>';
	}
	if( $mailprefs['smtpauth'] ) {
	  if( $mailprefs['username'] == '' ) $error .= '<li>'.lang('error_usernamerequired').'</li>';
	  if( $mailprefs['password'] == '' ) $error .= '<li>'.lang('error_passwordrequired').'</li>';
	}
      }

      // save.
      if( !$error ) {
	cms_siteprefs::set('mail_is_set',1);
	cms_siteprefs::set('mailprefs',serialize($mailprefs));
      }
      break;

    case 'setup':
		if (isset($_POST["disablesafemodewarning"])) $disablesafemodewarning = $_POST['disablesafemodewarning'];
		if (isset($_POST["enablenotifications"])) $enablenotifications = $_POST['enablenotifications'];
		if (isset($_POST["xmlmodulerepository"])) $xmlmodulerepository = $_POST["xmlmodulerepository"];
		if (isset($_POST["checkversion"])) $checkversion = (int) $_POST["checkversion"];
		if (isset($_POST['global_umask'])) {
			$global_umask = $_POST['global_umask'];
		}
		cms_siteprefs::set('global_umask', $global_umask);
		cms_siteprefs::set('xmlmodulerepository', $xmlmodulerepository);
		cms_siteprefs::set('checkversion', $checkversion);
		cms_siteprefs::set('disablesafemodewarning',$disablesafemodewarning);
		cms_siteprefs::set('enablenotifications',$enablenotifications);
		if( isset($_POST['allow_browser_cache']) ) {
			$allow_browser_cache = (int)$_POST['allow_browser_cache'];
			cms_siteprefs::set('allow_browser_cache',$allow_browser_cache);
		}
		if( isset($_POST['browser_cache_expiry']) ) {
			$browser_cache_expiry = (int)$_POST['browser_cache_expiry'];
			cms_siteprefs::set('browser_cache_expiry',$browser_cache_expiry);
		}
		if( isset($_POST['auto_clear_cache_age']) ) {
			$auto_clear_cache_age = (int)$_POST['auto_clear_cache_age'];
			cms_siteprefs::set('auto_clear_cache_age',$auto_clear_cache_age);
		}
		if( isset($_POST['pseudocron_granularity']) ) {
			$pseudocron_granularity = (int)$_POST['pseudocron_granularity'];
			cms_siteprefs::set('pseudocron_granularity',$pseudocron_granularity);
		}
		if (isset($_POST["adminlog_lifetime"])) {
			$adminlog_lifetime = $_POST["adminlog_lifetime"];
			cms_siteprefs::set('adminlog_lifetime',$adminlog_lifetime);
		}
		break;
	  
    case 'smarty':
		if( isset($_POST['use_smartycache']) ) {
			$use_smartycache = $_POST['use_smartycache'];
			cms_siteprefs::set('use_smartycache',$use_smartycache);
		}
		if( isset($_POST['use_smartycompilecheck']) ) {
			$use_smartycompilecheck = $_POST['use_smartycompilecheck'];
			cms_siteprefs::set('use_smartycompilecheck',$use_smartycompilecheck);
		}
		$gCms->clear_cached_files();
		break;
    }

    // put mention into the admin log
    if( !$error ) {
      audit('', 'Global Settings', 'Edited');
      $message .= lang('siteprefsupdated');
    }
  }
  else {
    $error .= "<li>".lang('noaccessto', array('Modify Site Permissions'))."</li>";
  }
} 

/**
 * Build page
 */
 
include_once("header.php");

if ($error != "") $themeObject->ShowErrors($error);
if ($message != "") $themeObject->ShowMessage($message);
 
$templates = array();
$templates['-1'] = lang('none');

$query = "SELECT * FROM ".cms_db_prefix()."templates where active = 1 ORDER BY template_name";
$result = $db->Execute($query);

while ($result && $row = $result->FetchRow()) {
  $templates[$row['template_id']] = $row['template_name'];
}

// Make sure cache folder is writable
if (FALSE == is_writable(TMP_CACHE_LOCATION) || 
    FALSE == is_writable(TMP_TEMPLATES_C_LOCATION) ) {
  $themeObject->ShowErrors(lang('cachenotwritable'));
}

$modules = ModuleOperations::get_instance()->get_modules_with_capability('search');
if( is_array($modules) && count($modules) ) {
  $tmp = array();
  $tmp['-1'] = lang('none');
  for( $i = 0; $i < count($modules); $i++ ) {
    $tmp[$modules[$i]] = $modules[$i];
  }
  $smarty->assign('search_modules',$tmp);
}

$maileritems = array();
$maileritems['mail'] = 'mail';
$maileritems['sendmail'] = 'sendmail';
$maileritems['smtp'] = 'smtp';
$smarty->assign('maileritems',$maileritems);
$opts = array();
$opts[''] = lang('none');
$opts['ssl'] = 'SSL';
$opts['tls'] = 'TLS';
$smarty->assign('secure_opts',$opts);
$smarty->assign('mail_is_set',$mail_is_set);
$smarty->assign('mailprefs',$mailprefs);

$smarty->assign('languages',get_language_list());
$smarty->assign('templates',$templates);
$smarty->assign('tab',$tab);
$smarty->assign('pretty_urls',$pretty_urls);

// need a list of wysiwyg modules.
{
  $tmp = module_meta::get_instance()->module_list_by_capability('wysiwyg');
  $tmp2 = array(-1=>lang('none'));
  for( $i = 0; $i < count($tmp); $i++ ) {
    $tmp2[$tmp[$i]] = $tmp[$i];
  }
  $smarty->assign('wysiwyg',$tmp2);
}

if ($dir=opendir(dirname(__FILE__)."/themes/")) 
{ 
  $themes = array();
  while (($file = readdir($dir)) !== false ) {
    if( @is_dir("themes/".$file) && ($file[0]!='.') && @is_readable("themes/{$file}/{$file}Theme.php")) {
      $themes[$file] = $file;
    }
  }
  $smarty->assign('themes',$themes);
  $smarty->assign('logintheme',cms_siteprefs::get('logintheme','default'));
}

$smarty->assign('tabs_end',$themeObject->EndTabContent());
$smarty->assign('general_start',$themeObject->StartTab("general"));
$smarty->assign('editcontent_start',$themeObject->StartTab("editcontent"));
$smarty->assign('sitedown_start',$themeObject->StartTab("sitedown"));
$smarty->assign('setup_start',$themeObject->StartTab("setup"));
$smarty->assign('smarty_start',$themeObject->StartTab("smarty"));
$smarty->assign('tab_end',$themeObject->EndTab());

$smarty->assign('SECURE_PARAM_NAME',CMS_SECURE_PARAM_NAME);
$smarty->assign('CMS_USER_KEY',$_SESSION[CMS_USER_KEY]);
$smarty->assign('sitename',$sitename);
$smarty->assign('global_umask',$global_umask);
$smarty->assign('testresults',$testresults);
$smarty->assign('frontendlang',$frontendlang);
$smarty->assign('frontendwysiwyg',$frontendwysiwyg);
$smarty->assign('backendwysiwyg',$backendwysiwyg);
$smarty->assign('metadata',$metadata);
$smarty->assign('enablesitedownmessage',$enablesitedownmessage);
$smarty->assign('use_wysiwyg',$use_wysiwyg);
$smarty->assign('textarea_sitedownmessage',create_textarea($use_wysiwyg,$sitedownmessage,'sitedownmessage','pagesmalltextarea'));
$smarty->assign('checkversion',$checkversion);
$smarty->assign('disablesafemodewarning',$disablesafemodewarning);
$smarty->assign('defaultdateformat',$defaultdateformat);
$smarty->assign('enablenotifications',$enablenotifications);
$smarty->assign('sitedownexcludes',$sitedownexcludes);
$smarty->assign('sitedownexcludeadmins',$sitedownexcludeadmins);
$smarty->assign('basic_attributes',explode(',',$basic_attributes));
$smarty->assign('disallowed_contenttypes',explode(',',$disallowed_contenttypes));
$smarty->assign('thumbnail_width',$thumbnail_width);
$smarty->assign('thumbnail_height',$thumbnail_height);
$smarty->assign('allow_browser_cache',$allow_browser_cache);
$smarty->assign('browser_cache_expiry',$browser_cache_expiry);
$smarty->assign('auto_clear_cache_age',$auto_clear_cache_age);
$smarty->assign('pseudocron_granularity',$pseudocron_granularity);
$smarty->assign('content_autocreate_urls',$content_autocreate_urls);
$smarty->assign('content_autocreate_flaturls',$content_autocreate_flaturls);
$smarty->assign('content_mandatory_urls',$content_mandatory_urls);
$smarty->assign('content_imagefield_path',$content_imagefield_path);
$smarty->assign('content_thumbnailfield_path',$content_thumbnailfield_path);
$smarty->assign('content_cssnameisblockname',$content_cssnameisblockname);
$smarty->assign('contentimage_path',$contentimage_path);
$smarty->assign('adminlog_lifetime',$adminlog_lifetime);
$smarty->assign('search_module',$search_module);
$smarty->assign('use_smartycache',$use_smartycache);
$smarty->assign('use_smartycompilecheck',$use_smartycompilecheck);

$tmp = array(15=>lang('cron_15m'),30=>lang('cron_30m'),
	     60=>lang('cron_60m'),120=>lang('cron_120m'),
	     180=>lang('cron_3h'),360=>lang('cron_6h'),
	     12*60=>lang('cron_12h'),
	     24*60=>lang('cron_24h'),
	     -1=>lang('cron_request'));
$smarty->assign('pseudocron_options',$tmp);	     
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

$smarty->assign('lang_info_adminlog_lifetime',lang('info_adminlog_lifetime'));
$smarty->assign('lang_info_autoclearcache',lang('info_autoclearcache'));
$smarty->assign('lang_autoclearcache',lang('autoclearcache'));

$smarty->assign('lang_cancel',lang('cancel'));
$smarty->assign('lang_submit',lang('submit'));
$smarty->assign('lang_clearcache',lang('clearcache'));
$smarty->assign('lang_clear',lang('clear'));
$smarty->assign('lang_frontendlang',lang('frontendlang'));
$smarty->assign('lang_frontendwysiwygtouse',lang('frontendwysiwygtouse'));
$smarty->assign('lang_template',lang('template'));
$smarty->assign('lang_clear_version_check_cache',lang('clear_version_check_cache'));
$smarty->assign('lang_date_format_string_help',lang('date_format_string_help'));
$smarty->assign('lang_info_sitedownexcludes',lang('info_sitedownexcludes'));
$smarty->assign('lang_info_basic_attributes',lang('info_basic_attributes'));

$all_attributes = null;
{
	$content_obj = new Content; // should this be the default type?
	$list = $content_obj->GetProperties();
	if( is_array($list) && count($list) ) {
		// pre-remove some items.
		$all_attributes = array();
		for( $i = 0; $i < count($list); $i++ ) {
			$obj = $list[$i];
			if( $obj->tab == $content_obj::TAB_PERMS ) continue;
			if( !isset($all_attributes[$obj->tab]) ) $all_attributes[$obj->tab] = array('label'=>lang($obj->tab),'value'=>array());
			$all_attributes[$obj->tab]['value'][] = array('value'=>$obj->name,'label'=>lang($obj->name));
		}
	}
	$txt = CmsFormUtils::create_option($all_attributes);
}
$smarty->assign('all_attributes',$all_attributes);
$smarty->assign('smarty_cacheoptions',array('always'=>lang('always'),'never'=>lang('never'),'moduledecides'=>lang('moduledecides')));
$smarty->assign('smarty_cacheoptions2',array('always'=>lang('always'),'never'=>lang('never')));

$contentops = cmsms()->GetContentOperations();
$all_contenttypes = $contentops->ListContentTypes(false,false);
$smarty->assign('all_contenttypes',$all_contenttypes);

$yesno = array(0=>lang('no'),1=>lang('yes'));
$smarty->assign('yesno',$yesno);

$titlemenu = array(0=>lang('menutext'),1=>lang('title'));
$smarty->assign('titlemenu',$titlemenu);

$smarty->assign('backurl', $themeObject->backUrl());
$smarty->assign('formurl', $thisurl);

# begin outputg
$smarty->display('siteprefs.tpl');
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>