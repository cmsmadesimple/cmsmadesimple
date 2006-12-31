<?php
# CMS - CMS Made Simple
# (c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA
#
#$Id$

/**
 * Methods for modules to do redirection related functions
 *
 * @since		1.0
 * @package		CMS
 */

function cms_module_Redirect(&$modinstance, $id, $action, $returnid='', $params=array(), $inline=false)
{
	global $gCms;
	$config = $gCms->config;

	$name = $modinstance->GetName();

	#Suggestion by Calguy to make sure 2 actions don't get sent
	if (isset($params['action']))
	{
		unset($params['action']);
	}
	if (isset($params['id']))
	{
		unset($params['id']);
	}
	if (isset($params['module']))
	{
		unset($params['module']);
	}

	if (!$inline && $returnid != '')
		$id = 'cntnt01';

	$text = '';
	if ($returnid != '')
	{
		$text .= 'index.php';
	}
	else
	{
		$text .= 'moduleinterface.php';
	}
	#$text .= '?module='.$name.'&'.$id.'action='.$action.'&id='.$id;
	$text .= '?mact='.$name.','.$id.','.$action.','.($inline == true?1:0);
	if ($returnid != '')
	{
		$text .= '&'.$id.'returnid='.$returnid;
	}
	foreach ($params as $key=>$value)
	{
		$text .= '&'.$id.$key.'='.rawurlencode($value);
	}
	#var_dump($text);
	redirect($text);
}

function cms_module_RedirectContent(&$modinstance, $id)
{
	global $gCms;
	$manager =& $gCms->GetHierarchyManager();
	$node =& $manager->sureGetNodeByAlias($id);
	$content =& $node->GetContent();
	if (isset($content))
	{
		if ($content->GetURL() != '')
		{
			redirect($content->GetURL());
		}
	}
}

?>