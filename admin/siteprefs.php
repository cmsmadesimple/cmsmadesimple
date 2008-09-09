<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t;  -*-
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
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

$CMS_ADMIN_PAGE = 1;
$CMS_TOP_MENU = 'admin';
$CMS_ADMIN_TITLE = 'preferences';

require_once("../include.php");

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
global $gCms;
$db =& $gCms->GetDb();

$smarty = cms_smarty();

$error = "";
$message = "";

$disablesafemodewarning = 0;
if (isset($_POST["disablesafemodewarning"]))
{
 	$disablesafemodewarning = $_POST["disablesafemodewarning"];
}

$allowparamcheckwarnings = 0;
if (isset($_POST["allowparamcheckwarnings"])) 
{
	$allowparamcheckwarnings = $_POST["allowparamcheckwarnings"];
}

$enablenotifications = 1;
if (isset($_POST["enablenotifications"])) 
  {
    $enablenotifications = $_POST["enablenotifications"];
  }
  
$enablecustom404 = "0";
if (isset($_POST["enablecustom404"])) 
{
	$enablecustom404 = $_POST["enablecustom404"];
}

$xmlmodulerepository = "";
if (isset($_POST["xmlmodulerepository"])) 
{
	$xmlmodulerepository = $_POST["xmlmodulerepository"];
}

$custom404 = "<p>Page not found</p>";
if (isset($_POST["custom404"])) 
{
	$custom404 = $_POST["custom404"];
}

$custom404template = "-1";
if (isset($_POST["custom404template"])) 
{
	$custom404template = $_POST["custom404template"];
}

$enablesitedownmessage = "0";
if (isset($_POST["enablesitedownmessage"])) 
{
	$enablesitedownmessage = $_POST["enablesitedownmessage"];
}

$sitedownmessage = "<p>Site is current down.  Check back later.</p>";
if (isset($_POST["sitedownmessage"])) 
{
	$sitedownmessage = $_POST["sitedownmessage"];
}

$defaultpagecontent = '';
if (isset($_POST['defaultpagecontent'])) 
{
	$defaultpagecontent = $_POST['defaultpagecontent'];
}

$sitedownmessagetemplate = "-1";
if (isset($_POST["sitedownmessagetemplate"])) 
{
	$sitedownmessagetemplate = $_POST["sitedownmessagetemplate"];
}

$metadata = '';
if (isset($_POST['metadata'])) 
{
	$metadata = $_POST['metadata'];
}

if (isset($_POST['parent_id'])) 
{
	$default_parent_page = $_POST['parent_id'];
}

$sitename = '';
if (isset($_POST['sitename'])) 
{
	$sitename = $_POST['sitename'];
}

#$useadvancedcss = "1";
#if (isset($_POST["useadvancedcss"])) $useadvancedcss = $_POST["useadvancedcss"];

$frontendlang = '';
if (isset($_POST['frontendlang'])) 
{
	$frontendlang = $_POST['frontendlang'];
}

$global_umask = '022';
if (isset($_POST['global_umask'])) 
{
	$global_umask = $_POST['global_umask'];
}

$logintheme = "default";
if (isset($_POST["logintheme"])) 
{
	$logintheme = $_POST["logintheme"];
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Site Preferences');

$use_javasyntax = false;
if (get_preference($userid, 'use_javasyntax') == "1") $use_javasyntax = true;

if (isset($_POST["cancel"]))
{
	redirect("index.php");
	return;
}

$testresults = lang('untested');
if (isset($_POST["testumask"]))
{
	$testdir = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'tmp';
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
				$testresults = sprintf("%s: %s<br/>%s:<br/>&nbsp;&nbsp;%s", lang('owner'), $username, lang('permissions'), $permsstr);
			}
			else
			{
				$testresults = sprintf("%s: %s<br/>%s:<br/>&nbsp;&nbsp;%s", lang('owner'),"N/A", lang('permissions'),"N/A");

			}
			@unlink($testfile);
		}
	}
}
else if( isset($_POST['test_mail']) )
{
  $mailer = new CmsMail;
  $mailer->add_address( $_POST['test_address'] );
  $mailer->set_body( lang('test_email_body') );
  $mailer->set_subject( lang('test_email_subject') );
  $mailer->send();

  // todo, display a nice message here
  $message .= lang('test_message_sent');
}

if (isset($_POST['clearcache']))
{
	CmsContentOperations::clear_cache();
	CmsCache::clear();
	$message .= lang('cachecleared');
}
else if (isset($_POST['lang_enabled']))
{
	if ($access)
	{
		$enabled_languages = array_keys($_POST['lang_enabled']);
		CmsApplication::set_preference('enabled_languages', implode(',', $enabled_languages));
		if (in_array($_POST['default_language'], $enabled_languages))
			CmsApplication::set_preference('default_language', $_POST['default_language']);
		else
			CmsApplication::set_preference('default_language', $enabled_languages[0]);
	}
}
else if (isset($_POST["editsiteprefs"]))
{
	if ($access)
	{
		CmsApplication::set_preference('global_umask', $global_umask);
		CmsApplication::set_preference('frontendlang', $frontendlang);
		CmsApplication::set_preference('enablecustom404', $enablecustom404);
		CmsApplication::set_preference('xmlmodulerepository', $xmlmodulerepository);
		CmsApplication::set_preference('custom404', $custom404);
		CmsApplication::set_preference('custom404template', $custom404template);
		CmsApplication::set_preference('enablesitedownmessage', $enablesitedownmessage);
		CmsApplication::set_preference('sitedownmessage', $sitedownmessage);
		#CmsApplication::set_preference('sitedownmessagetemplate', $sitedownmessagetemplate);
		#CmsApplication::set_preference('useadvancedcss', $useadvancedcss);
		CmsApplication::set_preference('logintheme', $logintheme);
		CmsApplication::set_preference('enablenotifications', $enablenotifications);
		
		CmsApplication::set_preference('metadata', $metadata);
		CmsApplication::set_preference('sitename', $sitename);
		audit(-1, '', 'Edited Site Preferences');
		//redirect("siteprefs.php");
		//return;
		$message .= lang('prefsupdated');
	}
	else
	{
		$error .= "<li>".lang('noaccessto', array('Modify Site Permissions'))."</li>";
	}
}
else if( isset($_POST['mailsettings']) )
{
  if( $access )
	{  
	  CmsApplication::set_preference('mail_is_set',1);
	  $mail_mailer       = coalesce_key($_POST,'mail_mailer','mail');
	  $mail_host         = coalesce_key($_POST,'mail_host','');
	  $mail_port         = coalesce_key($_POST,'mail_port','');
	  $mail_smtpauth     = coalesce_key($_POST,'mail_smtpauth',false);
	  $mail_smtpauthuser = coalesce_key($_POST,'mail_smtpauthuser','');
	  $mail_smtpauthpw   = coalesce_key($_POST,'mail_smtpauthpw','');
	  $mail_sendmail     = coalesce_key($_POST,'mail_sendmail','');
	  $mail_from         = coalesce_key($_POST,'mail_from','');
	  $mail_fromuser     = coalesce_key($_POST,'mail_fromuser','');
	 
	  CmsApplication::set_preference('mail_mailer',$mail_mailer);
	  CmsApplication::set_preference('mail_host',$mail_host);
	  CmsApplication::set_preference('mail_port',$mail_port);
	  CmsApplication::set_preference('mail_smtpauth',$mail_smtpauth);
	  CmsApplication::set_preference('mail_smtpauthuser',$mail_smtpauthuser);
	  CmsApplication::set_preference('mail_smtpauthpw',$mail_smtpauthpw);
	  CmsApplication::set_preference('mail_sendmail',$mail_sendmail);
	  CmsApplication::set_preference('mail_from',$mail_from);
	  CmsApplication::set_preference('mail_fromuser',$mail_fromuser);
	  audit(-1, '', lang('edited_mail_preferences'));
	  $message .= lang('prefsupdated');
	}
  else
	{
		$error .= "<li>".lang('noaccessto', array('Modify Site Permissions'))."</li>";
	}
}
else if (!isset($_POST["submit"]))
{
	$global_umask = CmsApplication::get_preference('global_umask',$global_umask);
	
	$frontendlang = CmsApplication::get_preference('frontendlang');
	$enablecustom404 = CmsApplication::get_preference('enablecustom404');
	$custom404 = CmsApplication::get_preference('custom404');
	$custom404template = CmsApplication::get_preference('custom404template');
	$enablesitedownmessage = CmsApplication::get_preference('enablesitedownmessage');
	$sitedownmessage = CmsApplication::get_preference('sitedownmessage');
	$xmlmodulerepository = CmsApplication::get_preference('xmlmodulerepository');
	#$sitedownmessagetemplate = get_preference('sitedownmessagetemplate');
	#$useadvancedcss = get_preference('useadvancedcss');
	$logintheme = CmsApplication::get_preference('logintheme', 'default');
	$enablenotifications = CmsApplication::set_preference('enablenotifications', $enablenotifications);
	$metadata = CmsApplication::get_preference('metadata', '');
	$sitename = CmsApplication::get_preference('sitename', 'CMSMS Site');
}

$smarty->assign('global_umask', $global_umask);
$smarty->assign('frontendlang', $frontendlang);
$smarty->assign('enablecustom404', $enablecustom404);
$smarty->assign('custom404', $custom404);
$smarty->assign('custom404template', $custom404template);
$smarty->assign('enablesitedownmessage', $enablesitedownmessage);
$smarty->assign('sitedownmessage', $sitedownmessage);
$smarty->assign('xmlmodulerepository', $xmlmodulerepository);


// Assign the admin theme options
$admintheme_options = array();
if ($dir=opendir(dirname(__FILE__)."/themes/")) { //Does the themedir exist at all, it should...
	while (($file = readdir($dir)) !== false) {
		if (@is_dir("themes/".$file) && ( $file[0] != '.')) {
			$admintheme_options[$file] = $file;
		}
	}
}
$smarty->assign('admintheme_options', $admintheme_options);
$smarty->assign('enablenotifications', $enablenotifications);
$smarty->assign('metadata', $metadata);
$smarty->assign('sitename', $sitename);

$smarty->assign('mail_mailer',CmsApplication::get_preference('mail_mailer','mail'));
$smarty->assign('mail_host',CmsApplication::get_preference('mail_host',''));
$smarty->assign('mail_port',CmsApplication::get_preference('mail_port',''));
$smarty->assign('mail_smtpauth',CmsApplication::get_preference('mail_smtpauth',''));
$smarty->assign('mail_smtpauthuser',CmsApplication::get_preference('mail_smtpauthuser',''));
$smarty->assign('mail_smtpauthpw',CmsApplication::get_preference('mail_smtpauthpw',''));
$smarty->assign('mail_sendmail',CmsApplication::get_preference('mail_sendmail',''));
$smarty->assign('mail_from',CmsApplication::get_preference('mail_from',''));
$smarty->assign('mail_fromuser',CmsApplication::get_preference('mail_fromuser',''));

$templates = array();
$templates['-1'] = 'None';

$result = cmsms()->template->find_all(array('order' => 'template_name ASC'));

foreach ($result as &$onetemplate)
{
	$templates[$onetemplate['id']] = $onetemplate['name'];
}

$smarty->assign('templates', $templates);

include_once("header.php");

if ($error != "") {
	echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$error."</ul></div>";	
}
if ($message != "") {
	echo $themeObject->ShowMessage($message);
}
// Assign the header
$smarty->assign('header_name', $themeObject->ShowHeader('siteprefs'));


// Make sure cache folder is writable
if (FALSE == is_writable(cms_join_path(CmsConfig::get('root_path'),'tmp','cache')))
{
	echo $themeObject->ShowErrors(lang('cachenotwritable'));
}

$lang_list = CmsLanguage::get_language_list();
$enabled_languages = explode(',', CmsApplication::get_preference('enabled_languages', 'en_US'));
$default_language = CmsApplication::get_preference('default_language', 'en_US');
foreach ($lang_list as $k=>$v)
{
	$lang_name = $v;
	$v = array();
	$v['name'] = $lang_name;
	$v['enabled'] = in_array($k, $enabled_languages);
	$v['flag_image'] = CmsLanguage::get_flag_image($k);
	$v['checkbox_name'] = "lang_enabled[$k]";
	$enabled_html = " ";
	if( !$v['enabled'] )
	{
		$enabled_html = ' disabled="disabled" ';
	}
	$v['default'] = '<input type="radio" name="default_language" value="'.$k.'"' . ($default_language == $k ? ' checked="checked"' : '') . $enabled_html .' />';
	$lang_list[$k] = $v;
}
$smarty->assign('lang_list', $lang_list);
$smarty->assign('back_url', $themeObject->BackUrl());
$smarty->display('siteprefs.tpl');
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
