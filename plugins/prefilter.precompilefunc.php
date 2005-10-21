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
	$log =& LoggerManager::getLogger('prefilter.precompilefunc.php');
	$log->debug('Starting smarty_cms_prefilter_precompilefunc');

	$result = explode(':', $smarty->_current_file);
	if (count($result) > 1)
	{
		global $gCms;

		switch ($result[0])
		{
			case "content":
				#Perform the content prerender callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$log->debug('Calling ContentPreCompile');
						$gCms->modules[$key]['object']->ContentPreCompile($tpl_output);
						
						#For backwards compatibility, though I think PreCompile makes more sense
						$log->debug('Calling ContentPreRender');
						$gCms->modules[$key]['object']->ContentPreRender($tpl_output);
					}
				}
				break;
			case "template":
				#Perform the content prerender callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$log->debug('Calling TemplatePreCompile');
						$gCms->modules[$key]['object']->TemplatePreCompile($tpl_output);
					}
				}
				break;
			default:
				break;
		}
	}
	return $tpl_output;
}
?>
