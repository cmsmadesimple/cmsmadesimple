<?php
if (!isset($gCms)) exit;

debug_buffer('', 'Start of Menu Manager Display');

$hm = $gCms->GetHierarchyManager();

$usefile = true;
$tpl_name = coalesce_key($params, 'template', $this->GetPreference('default_template','simple_navigation.tpl'));
$recursive = '0';
if (endswith($tpl_name, '.tpl'))
{
	$usefile = true;
}
else
{
	$recursive = $this->GetPreference('tpl_' . $tpl_name . '_recursive', '1');
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
		$this->display_menu(CmsPageTree::get_instance()->get_root_node()->get_children(), $params, true);
	}
}
else
{
	//Pull the old logic out of a separate file.  No need to confuse matters.
	include(cms_join_path(dirname(__FILE__), 'old_default_action.php'));
}
debug_buffer('', 'End of Menu Manager Display');
?>
