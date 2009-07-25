<?php
if (!isset($gCms)) exit;

debug_buffer('', 'Start of Menu Manager Display');

$hm =& $gCms->GetHierarchyManager();

$usefile = true;
$tpl_name = $this->GetPreference('default_template','simple_navigation.tpl');
if( !endswith($tpl_name,'.tpl') )
  {
    $usefile = false;
  }

if (isset($params['template']) && $params['template'] != '')
{
	$tpl_name = $params['template'];
	if (!endswith($tpl_name, '.tpl'))
	{
		$usefile = false;
	}
}

$mdid = md5($gCms->variables['content_id'].implode('|', $params));

$cached = false;
$origdepth = 0;

if (!$cached)
{
	$nodelist = array();
	$count = 0;
	$getchildren = true;

	$rootnode = null;

	$prevdepth = 1;

	if (isset($params['start_page']) || isset($params['start_element']))
	{
		if (isset($params['start_page']))
			$rootnode =& $hm->sureGetNodeByAlias($params['start_page']);
		else
			$rootnode =& $hm->getNodeByHierarchy($params['start_element']);

		if (isset($rootnode))
		{
			$content =& $rootnode->GetContent();
			if (isset($content))
			{
				if (isset($params['show_root_siblings']) && $params['show_root_siblings'] == '1')
				{
					if ($rootnode->getLevel() == 0)
					{
						$rootnode =& $hm->getRootNode();
						$prevdepth = 1;
					}
					else
					{
						#Set original depth first before getting parent node
						#This is slightly hackish, but it works nicely
					        #+1 and +2 fix HM changes of root node level
					        #even more hackish ;)
						$origdepth = $rootnode->getLevel()+1;
						$rootnode =& $rootnode->getParentNode();
						$prevdepth = $rootnode->getLevel()+2;
					}
				}
				else
				{
					//Show a page if it's active and set to show in menu...
					//Or if show_all is set and the page isn't a "system" page
					if ($content->Active() && ($content->ShowInMenu() || ($params['show_all'] == 1 && !$content->IsSystemPage())))
					{
						$prevdepth = count(explode('.', $content->Hierarchy()));
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
		$curnode =& $hm->sureGetNodeById($gCms->variables['content_id']);
		if (isset($curnode))
		{
			$curcontent =& $curnode->GetContent();
			$properparentpos = $this->nthPos($curcontent->Hierarchy() . '.', '.', intval($params['start_level']) - 1);
			if ($properparentpos > -1)
			{
				$prevdepth = intval($params['start_level']);
				$rootnode =& $hm->getNodeByHierarchy(substr($curcontent->Hierarchy(), 0, $properparentpos));
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
				$oneitem =& $items[$key];
				$curnode =& $hm->sureGetNodeByAlias(trim($oneitem));
				if ($curnode)
				{
					$curcontent =& $curnode->GetContent();
					if (isset($curcontent))
					{
						$prevdepth = 1;
						$newnode =& $this->FillNode($curcontent, $curnode, $nodelist, $gCms, $count, $prevdepth, 1);
						//$newnode->depth = 1;
						//$newnode->prevdepth = 1;
					}
				}
			}

			#PHP 4 is stupid.  Go through and reset all depths and prevdepths to 1
			reset($nodelist);
			while (list($key) = each($nodelist))
			{
				$onenode =& $nodelist[$key];
				$onenode->depth = 1;
				$onenode->prevdepth = 1;
			}
		}
	}
	else
	{
	  // load all content
		$rootnode =& $hm->getRootNode();
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
	  $this->GetChildNodes($rootnode, $nodelist, $gCms, $prevdepth, $count, $params, $origdepth, $showparents, $deep);

	if (count($nodelist) > 0)
	{
		$smarty =& $this->smarty;
		$smarty->assign('menuparams',$params);
		$smarty->assign('count', count($nodelist));
		$smarty->assign_by_ref('nodelist', $nodelist);
		if ($usefile)
			echo $this->ProcessTemplate($tpl_name, $mdid, false, $gCms->variables['content_id']);
		else
		  echo $this->ProcessTemplateFromDatabase($tpl_name, $mdid, false);
	}
}
/*
else
{
	if ($usefile)
		echo $this->ProcessTemplate($tpl_name, $mdid, true, $gCms->variables['content_id']);
	else
		echo $this->ProcessTemplateFromDatabase($tpl_name, $mdid, true, $gCms->variables['content_id']);
}
*/
debug_buffer('', 'End of Menu Manager Display');
?>
