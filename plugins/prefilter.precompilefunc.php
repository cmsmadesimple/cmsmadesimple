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

function smarty_cms_prefilter_precompilefunc($tpl_output, &$smarty)
{
	global $gCms;

	$result = explode(':', $smarty->_current_file);
	if (count($result) > 1)
	{
	  if( startswith($result[0],'tmp_') ) $result[0] = 'template';

		switch ($result[0])
		{
			case "content":
// 				foreach($gCms->modules as $key=>$value)
// 				{
// 					if ($gCms->modules[$key]['installed'] == true &&
// 						$gCms->modules[$key]['active'] == true)
// 					{
// 						$gCms->modules[$key]['object']->ContentPreCompile($tpl_output);
// 					}
// 				}
				
				Events::SendEvent('Core', 'ContentPreCompile', array('content' => &$tpl_output));
				
				break;
			case "template":
// 				foreach($gCms->modules as $key=>$value)
// 				{
// 					if ($gCms->modules[$key]['installed'] == true &&
// 						$gCms->modules[$key]['active'] == true)
// 					{
// 						$gCms->modules[$key]['object']->TemplatePreCompile($tpl_output);
// 					}
// 				}
				
				Events::SendEvent('Core', 'TemplatePreCompile', array('template' => &$tpl_output));
				
				break;
			case "globalcontent":
// 				foreach($gCms->modules as $key=>$value)
// 				{
// 					if ($gCms->modules[$key]['installed'] == true &&
// 						$gCms->modules[$key]['active'] == true)
// 					{
// 						$gCms->modules[$key]['object']->GlobalContentPreCompile($tpl_output);
// 					}
// 				}
				
				Events::SendEvent('Core', 'GlobalContentPreCompile', array('global_content' => &$tpl_output));
				
				break;
			default:
				break;
		}

	}

	foreach($gCms->modules as $key=>$value)
	{
		if ($gCms->modules[$key]['installed'] == true &&
			$gCms->modules[$key]['active'] == true)
		{
			$gCms->modules[$key]['object']->SmartyPreCompile($tpl_output);
		}
	}
	
	Events::SendEvent('Core', 'SmartyPreCompile', array('content' => &$tpl_output));

	return $tpl_output;
}
?>
