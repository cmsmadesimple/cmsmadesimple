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
//require_once("../lib/classes/class.htmlblob.inc.php");
require_once("../lib/classes/class.template.inc.php");

check_login();

$group_id = -1;
if (isset($_GET["htmlblob_id"]))
{
	$htmlblob_id = $_GET["htmlblob_id"];
	$htmlblob_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Global Content Blocks');

	if ($access)
	{
		$result = false;
		
		global $gCms;
		$gcbops =& $gCms->GetGlobalContentOperations();
		$templateops =& $gCms->GetTemplateOperations();

		$blobobj = $gcbops->LoadHtmlBlobByID($htmlblob_id);
		$htmlblob_name = $blobobj->name;

		if ($blobobj)
		{
			#Perform the deletehtmlblob_pre callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->DeleteHtmlBlobPre($blobobj);
				}
			}
			
			Events::SendEvent('Core', 'DeleteGlobalContentPre', array('global_content' => &$blobobj));

			$result = $blobobj->Delete();
		}

		if ($result == true)
		{
			#Perform the deletehtmlblob_post callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->DeleteHtmlBlobPost($blobobj);
				}
			}
			
			Events::SendEvent('Core', 'DeleteGlobalContentPost', array('global_content' => &$blobobj));

			audit($htmlblob_id, $htmlblob_name, 'Deleted Html Blob');

			$templateops->TouchAllTemplates(); #So pages recompile
		}
	}
}

redirect("listhtmlblobs.php");

# vim:ts=4 sw=4 noet
?>
