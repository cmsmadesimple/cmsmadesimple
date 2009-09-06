<?php
if (!isset($gCms)) exit;

debug_buffer('', 'Start of Menu Manager Display');

$hm =& $gCms->GetHierarchyManager();

$usefile = true;
$tpl_name = coalesce_key($params, 'template', $this->GetPreference('default_template','simple_navigation.tpl'));
$recursive = '0';
if( endswith($tpl_name, '.tpl') )
  {
    $usefile = true;
  }
else
  {
    $usefile = false;
  }

$mdid = md5($gCms->variables['content_id'].implode('|', $params));

$origdepth = 0;

if ($recursive == '1')
{
	$root_node = null;

	if (isset($params['start_page']) || isset($params['start_element']))
	{
		$tree = CmsPageTree::get_instance();
		$root_node = null;

		if (isset($params['start_page']))
		{
			$root_node = $tree->get_node_by_alias($params['start_page']);
		}
		else
		{
			$root_node = $tree->get_node_by_hierarchy($params['start_element']);
		}

		if ($root_node != null)
		{
			if (isset($params['show_root_siblings']) && $params['show_root_siblings'])
			{
				$parent_node = $root_node->get_parent();
				if ($parent_node != null)
				{
					$this->display_menu($parent_node->get_children(), $params);
				}
			}
			else if (isset($params['show_only_children']) && $params['show_only_children'])
			{
				$this->display_menu($root_node->get_children(), $params);
			}
			else
			{
				//Hack alert!
				//Make an array of one and pass that
				$ary = array($root_node);
				$this->display_menu($ary, $params);
			}
		}
	}
	else if (isset($params['start_level']) && $params['start_level'] > 1)
	{
		$tree = CmsPageTree::get_instance();
		$curnode = $tree->get_node_by_id(cmsms()->variables['content_id']);
		if ($curnode != null)
		{
			$ids = explode('.', $curnode->IdHierarchy());
			if (count($ids) >= $params['start_level'] - 2)
			{
				$parent_node = $tree->get_node_by_id($ids[$params['start_level'] - 2]);
				if ($parent_node != null && $parent_node->has_children())
				{
					$this->display_menu($parent_node->get_children(), $params);
				}
			}
		}
	}
	else if (isset($params['items']))
	{
		$result = array();

		$tree = CmsPageTree::get_instance();
		$items = explode(',', $params['items']);

		if ($tree != null && count($items) > 0)
		{
			foreach ($items as $oneitem)
			{
				$curnode = $tree->get_node_by_alias(trim($oneitem));
				if ($curnode)
				{
					$result[] = $curnode;
				}
			}
		}

		if (count($result) > 0)
		{
			$params['number_of_levels'] = 1;

			$this->display_menu($result, $params);
		}
	}
	else
	{
		$this->display_menu(CmsPageTree::get_instance()->get_root_node()->get_children(), $params);
	}
}
else
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
			if( $curcontent )  {
			  $properparentpos = $this->nthPos($curcontent->Hierarchy() . '.', '.', intval($params['start_level']) - 1);
			  if ($properparentpos > -1)
			  {
			    $prevdepth = intval($params['start_level']);
			    $rootnode =& $hm->getNodeByHierarchy(substr($curcontent->Hierarchy(), 0, $properparentpos));
			  }
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
	{
		$this->GetFlatChildNodes($rootnode, $nodelist, $gCms, $prevdepth, $count, $params, $origdepth, $showparents, $deep);
	}
	
	if (count($nodelist) > 0)
	{
		$smarty->assign('menuparams',$params);
		$smarty->assign('count', count($nodelist));
		$smarty->assign_by_ref('nodelist', $nodelist);
		if ($usefile)
		  echo $this->ProcessTemplate($tpl_name, $mdid, false, $gCms->variables['content_id']);
		else
			echo $this->ProcessTemplateFromDatabase($tpl_name, $mdid, false);
	}
}
debug_buffer('', 'End of Menu Manager Display');
?>
