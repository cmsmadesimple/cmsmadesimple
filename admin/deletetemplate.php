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

require_once("../include.php");
require_once("../lib/classes/class.template.inc.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$from = 'listtemplates.php'.$urlext;
if (isset($_GET['from']) )
  {
    $from = 'moduleinterface.php'.$urlext.'&module='.$_GET['from'];
  }


$dodelete = true;
$template_id = -1;
if (isset($_GET["template_id"]))
{
	$template_id = $_GET["template_id"];
	$template_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Templates');

	if ($access)
	{
	  $gCms = cmsms();
		$templateops = $gCms->GetTemplateOperations();
		$onetemplate = $templateops->LoadTemplateByID($template_id);

		if ($templateops->CountPagesUsingTemplateByID($template_id) > 0)
		{
			$dodelete = false;
		}

		if ($dodelete)
		{
			Events::SendEvent('Core', 'DeleteTemplatePre', array('template' => &$onetemplate));

			$result = $templateops->DeleteTemplateByID($template_id);

			if ($result)
			{
				Events::SendEvent('Core', 'DeleteTemplatePost', array('template' => &$onetemplate));

				audit($template_id, $onetemplate->name, lang_en('deleted_template'));
			}
		}
	}
}

if ($dodelete)
{
  redirect($from);
}
else
{
  $url = $from."&message=".lang('errortemplateinuse');
  redirect($url);
}

# vim:ts=4 sw=4 noet
?>
