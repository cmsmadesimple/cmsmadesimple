<?php
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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
require_once("../lib/classes/class.template.inc.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$fn = dirname(__FILE__).'/templates/orig_new_template.tpl';
$dflt_content = @file_get_contents($fn);

$error = "";

$template = "";
if (isset($_POST["template"])) $template = $_POST["template"];

$content = $dflt_content;
if (isset($_POST["content"])) $content = $_POST["content"];

/*$stylesheet = "";
if (isset($_POST["stylesheet"])) $stylesheet = $_POST["stylesheet"];
*/
/*$preview = false;
if (isset($_POST["preview"])) $preview = true;*/

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addsection"])) $active = 0;

$from='listtemplates.php'.$urlext;
if( isset($_GET['from'] ) )
  {
    $from = "moduleinterface.php".$urlext."&amp;module=".$_GET['from'];
  }
else if( isset($_REQUEST['from']) ) 
  {
    $from = $_REQUEST['from'];
  }

if (isset($_POST["cancel"]))
{
	redirect($from);
	return;
}

$gCms = cmsms();
$db = $gCms->GetDb();
$templateops = $gCms->GetTemplateOperations();
$smarty = $gCms->GetSmarty();
$smarty->caching = false;
$smarty->force_compile = true;
$userid = get_userid();
$access = check_permission($userid, 'Add Templates');

/*$use_javasyntax = false;
if (get_preference($userid, 'use_javasyntax') == "1") $use_javasyntax = true;
*/
if ($access)
{
	if (isset($_POST["addtemplate"])/* && !$preview*/)
	{
		$validinfo = true;

		if ($template == "") {
		  $error .= "<li>".lang("nofieldgiven",array(lang('name')))."</li>";
		  $validinfo = false;
		}
		else {
		  $query = "SELECT template_id from ".cms_db_prefix()."templates WHERE template_name = " . $db->qstr($template);
		  $result = $db->Execute($query);

		  if ($result && $result->RecordCount() > 0) {
		    $error .= "<li>".lang('templateexists')."</li>";
		    $validinfo = false;
		  }
		}

		if ($content == "") {
		  $error .= "<li>".lang('nofieldgiven', array(lang('content')))."</li>";
		  $validinfo = false;
		}

		if ($validinfo) {
		  try {
		    $smarty = cmsms()->GetSmarty();
		    $smarty->force_compile = TRUE;
		    
		    cms_utils::set_app_data('tmp_template',$content);
		    $smarty->registerPlugin('compiler','content',array('CMS_Content_Block','smarty_compiler_contentblock'),false);
		    $smarty->registerPlugin('compiler','content_image',array('CMS_Content_Block','smarty_compiler_imageblock'),false);
		    $smarty->registerPlugin('compiler','content_module',array('CMS_Content_Block','smarty_compiler_moduleblock'),false);
		    $smarty->registerResource('template',new CMSPageTemplateResource(''));
		    $smarty->fetch('template:appdata;tmp_template'); // do the magic.

		    // if we got here, we're golden.
		  }
		  catch( CmsEditContentException $e ) {
		    $error .= "<li>".$e->getMessage();
		    $validinfo = false;
		  }
		}

		if ($validinfo)
		{
			$newtemplate = new Template();
			$newtemplate->name = $template;
			$newtemplate->content = $content;
			$newtemplate->stylesheet = $stylesheet;
			$newtemplate->active = $active;
			$newtemplate->default = 0;

			Events::SendEvent('Core', 'AddTemplatePre', array('template' => &$newtemplate));

			$result = $newtemplate->save();

			if ($result)
			{
				Events::SendEvent('Core', 'AddTemplatePost', array('template' => &$newtemplate));
				// put mention into the admin log
				audit($newtemplate->id, 'HTML-template: '.$template, 'Added');
				redirect($from);
				return;
			}
			else
			{
				$error .= "<li>".lang('errorinsertingtemplate')."$query</li>";
			}
		}
	}
}

include_once("header.php");

if (!$access)
{
	//echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('addtemplate')))."</p></div>";
  $themeObject->ShowErrors(lang('noaccessto', lang('noaccessto', array(lang('addtemplate')))));
  return;
}
else
{
	if ($error != "")
	{
		//echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";
    $themeObject->ShowErrors(lang('noaccessto',$error));
	}
}
$smarty->assign("themeobj",$themeObject);
$smarty->assign("template",$template);
$smarty->assign("content",$content);
$smarty->assign("formurl","addtemplate.php".$urlext);

echo $smarty->fetch('addtemplate.tpl');
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
