<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
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
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$
if (!isset($gCms)) die("Can't call actions directly!");

$nodelist = array();
$count = 0;
$getchildren = true;

$rootnode = null;

$prevdepth = 1;

if (isset($params['start_page']) || isset($params['start_element']))
{
	if (isset($params['start_page']))
		$rootnode = $hm->sureGetNodeByAlias($params['start_page']);
	else
		$rootnode = $hm->getNodeByHierarchy($params['start_element']);

	if (isset($rootnode))
	{
		$content = $rootnode->get_content();
		if (isset($content))
		{
			if (isset($params['show_root_siblings']) && $params['show_root_siblings'] == '1')
			{
				if ($rootnode->getLevel() == 0)
				{
					$rootnode = $hm->getRootNode();
					$prevdepth = 1;
				}
				else
				{
					#Set original depth first before getting parent node
					#This is slightly hackish, but it works nicely
					#+1 and +2 fix HM changes of root node level
					#even more hackish ;)
					$origdepth = $rootnode->getLevel()+1;
					$rootnode = $rootnode->getParentNode();
					$prevdepth = $rootnode->getLevel()+2;
				}
			}
			else
			{
				//Show a page if it's active and set to show in menu...
				//Or if show_all is set and the page isn't a "system" page
				if ($content->active() && ($content->show_in_menu() || ($params['show_all'] == 1 && !$content->is_system_page())))
				{
					$prevdepth = count(explode('.', $content->hierarchy()));
					$this->FillNode($content, $rootnode, $nodelist, $gCms, $count, $prevdepth, $prevdepth);
					if (isset($params['number_of_levels']) && $params['number_of_levels'] == '1')
						$getchildren = false;
				}
			}
		}
	}
}
else if (isset($params['start_level']) && intval($params['start_level']) > 1)
{
	$curnode = $hm->sureGetNodeById($gCms->variables['content_id']);
	if (isset($curnode))
	{
		$curcontent = $curnode->get_content();
		$properparentpos = $this->nthPos($curcontent->hierarchy() . '.', '.', intval($params['start_level']) - 1);
		if ($properparentpos > -1)
		{
			$prevdepth = intval($params['start_level']);
			$rootnode = $hm->getNodeByHierarchy(substr($curcontent->hierarchy(), 0, $properparentpos));
		}
	}
}
else if (isset($params['items']))
{
	$items = explode(',', $params['items']);
	if (count($items) > 0)
	{
		reset($items);
		while (list($key) = each($items))
		{
			$oneitem = $items[$key];
			$curnode = $hm->sureGetNodeByAlias(trim($oneitem));
			if ($curnode)
			{
				$curcontent = $curnode->get_content();
				if (isset($curcontent))
				{
					$prevdepth = 1;
					$newnode = $this->FillNode($curcontent, $curnode, $nodelist, $gCms, $count, $prevdepth, 1);
					//$newnode->depth = 1;
					//$newnode->prevdepth = 1;
				}
			}
		}

		#PHP 4 is stupid.  Go through and reset all depths and prevdepths to 1
		reset($nodelist);
		while (list($key) = each($nodelist))
		{
			$onenode = $nodelist[$key];
			$onenode->depth = 1;
			$onenode->prevdepth = 1;
		}
	}
}
else
{
  // load all content
	$rootnode = $hm->getRootNode();
	$prevdepth = 1;
}

$showparents = array();

if (isset($params['collapse']) && $params['collapse'] == '1')
{
	$newpos = '';
	if (isset($gCms->variables['friendly_position']))
	{
		foreach (explode('.', $gCms->variables['friendly_position']) as $level)
		{
			$newpos .= $level . '.';
			$showparents[] = $newpos;
		}
	}
}

#See if origdepth was ever set...  if not, then get it from the prevdepth set earlier
if ($origdepth == 0)
	$origdepth = $prevdepth;

$deep = 1;
if( isset($params['loadprops']) && $params['loadprops'] == 0 )
	$deep = 0;

if (isset($rootnode) && $getchildren)
{
	$this->GetFlatChildNodes($rootnode, $nodelist, $gCms, $prevdepth, $count, $params, $origdepth, $showparents, $deep);
}

if (count($nodelist) > 0)
{
	$smarty->assign('menuparams',$params);
	$smarty->assign('count', count($nodelist));
	$smarty->assign_by_ref('nodelist', $nodelist);
	$usefile = true;
	$tpl_name = coalesce_key($params, 'template', $this->GetPreference('default_template','simple_navigation.tpl'));
	if( endswith($tpl_name, '.tpl') )
		{
			$usefile = true;
		}
	else
		{
			$usefile = false;
		}
			
	if ($usefile)
		{
			echo $this->ProcessTemplate($tpl_name, $mdid, false, $gCms->variables['content_id']);
		}
	else
		echo $this->ProcessTemplateFromDatabase($tpl_name, $mdid, false);
}

# vim:ts=4 sw=4 noet
?>
