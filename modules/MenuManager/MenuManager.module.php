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

class MenuManager extends CMSModule
{
	var $current_depth = 1;
	
	function GetName()
	{
		return 'MenuManager';
	}

	function GetFriendlyName()
	{
		return $this->Lang('menumanager');
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function VisibleToAdminUser()
	{
		return $this->CheckPermission('Manage Menu');
	}

	function Uninstall()
	{
		// remove the permissions
		$this->RemovePermission('Manage Menu');
	}

  function GetVersion()
  {
    return '1.6.2';
  }

	function MinimumCMSVersion()
	{
		return '1.6.1';
	}

	function GetAdminDescription()
	{
		return $this->Lang('description');
	}

	function GetAdminSection()
	{
		return 'layout';
	}

	function SetParameters()
	{
		$this->RestrictUnknownParams();
		$this->CreateParameter('collapse', '1', $this->lang('help_collapse'));
		$this->SetParameterType('collapse',CLEAN_INT);

		$this->CreateParameter('loadprops', '0', $this->lang('help_loadprops'));
		$this->SetParameterType('loadprops',CLEAN_INT);

		$this->CreateParameter('items', 'contact,home', $this->lang('help_items'));
		$this->SetParameterType('items',CLEAN_STRING);

		$this->CreateParameter('number_of_levels', '1', $this->lang('help_number_of_levels'));
		$this->SetParameterType('number_of_levels',CLEAN_INT);

		$this->CreateParameter('show_all', '0', $this->lang('help_show_all'));
		$this->SetParameterType('show_all',CLEAN_INT);

		$this->CreateParameter('show_root_siblings', '1', $this->lang('help_show_root_siblings'));
		$this->SetParameterType('show_root_siblings',CLEAN_INT);

		$this->CreateParameter('start_level', '2', $this->lang('help_start_level'));
		$this->SetParameterType('start_level',CLEAN_INT);

		$this->CreateParameter('start_element', '1.2', $this->lang('help_start_element'));
		$this->SetParameterType('start_element',CLEAN_STRING); // yeah, it's a string

		$this->CreateParameter('start_page', 'home', $this->lang('help_start_page'));
		$this->SetParameterType('start_page',CLEAN_STRING); 

		$this->CreateParameter('template', 'simple_navigation.tpl', $this->lang('help_template'));
		$this->SetParameterType('template',CLEAN_STRING); 

		$this->CreateParameter('excludeprefix','',$this->Lang('help_excludeprefix'));
		$this->SetParameterType('excludeprefix',CLEAN_STRING); 

		$this->CreateParameter('includeprefix','',$this->Lang('help_includeprefix'));
		$this->SetParameterType('includeprefix',CLEAN_STRING); 

		$this->RegisterModulePlugin('menu_children', 'menu_children_plugin_callback');
	}

	public function menu_children_plugin_callback($params, &$smarty)
	{
		$orig_params = $smarty->get_template_vars('orig_params');
		$params = array_merge($orig_params, $params);

		if (!isset($params['node']))
		{
			return;
		}

		if ($params['node']->has_children())
		{
			$this->current_depth++;

			//Handle number_of_levels param
			if (!isset($params['number_of_levels']) || $params['number_of_levels'] >= $this->current_depth)
			{
				//Handle collapse param
				if (!isset($params['collapse']) || $params['collapse'] != true || starts_with(cmsms()->variables['position'] . '.', $params['node']->hierarchy . '.'))
				{
					$this->display_menu($params['node']->get_children(), $params, false);
				}
			}

			$this->current_depth--;
		}
	}
    
	public function display_menu(&$nodes, $params, $first_call = true)
	{
		$usefile = true;
		//$lang = CmsMultiLanguage::get_client_language();
		//$mdid = md5(cmsms()->variables['content_id'].implode('|', $params).$lang);
		$mdid = md5(cmsms()->variables['content_id'].implode('|', $params));
		$tpl_name = coalesce_key($params, 'template', $this->GetPreference('default_template','simple_navigation.tpl'));

		if (!endswith($tpl_name, '.tpl'))
		{
			$usefile = false;
		}

		if (is_array($nodes))
		{
			$count = 0;

			foreach ($nodes as &$node)
			{
				//$content = $node->GetContent();
				$this->add_fields_to_node($node);
				$node->show = $this->should_show_node($node, $params);

				//Numeric Stuff
				$node->first = ($count == 0);
				$node->last = ($count + 1 == count($nodes));
				$node->index = $count;

				$count++;
			}

			$smarty = cms_smarty();
			$smarty->assign('count', count($nodes));
			$smarty->assign_by_ref('nodelist', $nodes);

			if ($first_call)
			{
				$smarty->assign('orig_params', $params);
				$this->current_depth = 1;
			}

			echo $this->ProcessTemplateFromDatabase($tpl_name, $mdid, false);
		}
	}
	
	public function add_fields_to_node(&$node)
	{
		$content = $node->get_content();
		//$node->url = $content->get_url(true, $lang);
		$node->url = $content->get_url(true);
		//$content->menutext = cms_htmlentities($content->get_property_value('menu_text', $lang));
		$node->haschildren = $node->has_children();
		$node->target = '';
		//if ($content->has_property('target'))
		//	$content->target = $content->get_property_value('target');
		$node->depth = $this->current_depth;
	}
	
	public function should_show_node(&$node, $params)
	{
		$include = true;
		$exclude = false;

		if (isset($params['includeprefix']))
		{
			$include = false;
			$prefixes = explode(',', $params['includeprefix']);
			foreach ($prefixes as $oneprefix)
			{
				if (starts_with(strtolower($node->Alias()), strtolower($oneprefix)))
				{
					$include = true;
					break;
				}
			}
		}

		if (isset($params['excludeprefix']))
		{
			$prefixes = explode(',', $params['excludeprefix']);
			foreach ($prefixes as $oneprefix)
			{
				if (starts_with(strtolower($node->Alias()), strtolower($oneprefix)))
				{
					$exclude = true;
					break;
				}
			}
		}

		$should_show = $node->Active() && $node->ShowInMenu() && ($include && !$exclude);

		//Override is show_all is true
		if (isset($params['show_all']) && $params['show_all'])
			$should_show = true;

		return $should_show;
	}



	function GetHelp($lang='en_US')
	{
		return $this->Lang('help');
	}

	function GetAuthor()
	{
		return 'Ted Kulp';
	}

	function GetAuthorEmail()
	{
		return 'ted@cmsmadesimple.org';
	}

	function GetChangeLog()
	{
		return $this->Lang('changelog');
	} 

	function GetMenuTemplate($tpl_name)
	{
		$data = false;
		if (endswith($tpl_name, '.tpl'))
		{
			global $gCms;
			// template file, we're gonna have to get it from
			// the filesystem, 
			$fn = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR;
			$fn .= $this->GetName().DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
			$fn .= $tpl_name;
			if( file_exists( $fn ) )
			{
				$data = file_get_contents($fn);
			}
		}
		else
		{
			$data = $this->GetTemplate($tpl_name);
		}

		return $data;
	}

	function SetMenuTemplate( $tpl_name, $content )
	{
		if (endswith($tpl_name, '.tpl'))
		{
			return false;
		}

		$this->SetTemplate( $tpl_name, $content );
		return true;
	}
	
	/**
	 * Below are methods used for the old flat style of menu development.  These are here
	 * only for backwards compatibility sake.
	 */
	
	/**
	 * Recursive function to go through all nodes and put them into a list
	 */
	function GetFlatChildNodes(&$parentnode, &$nodelist, &$gCms, &$prevdepth, &$count, &$params, $origdepth, &$showparents, $deep = false)
	{
		$includeprefix = '';
		$excludeprefix = '';
		if( isset($params['includeprefix']) )
		{
			$includeprefix = trim($params['includeprefix']);
		}
		else if( isset($params['excludeprefix']) )
		{
			$excludeprefix = trim($params['excludeprefix']);
		}

		if (isset($params['show_all']))
		{
			$show_all = $params['show_all'];
		}
		else
		{
			$show_all = 0;
		}
		
		if (isset($parentnode))
		{
			$children =& $parentnode->get_children($deep);
			if (isset($children) && count($children))
			{
				reset($children);
				while (list($key) = each($children))
				{
					$onechild =& $children[$key];
					$content = $onechild->get_content($deep);
					if( !is_object($content) ) 
					{
						// uhm, couldn't get the content object... this is strange
						// should I trigger an error?
						continue;
					}

					// see if we need to explicitly include this content
					$includeit = 1;
					if( $includeprefix != '' )
					{
						$includeit = 0;
						$prefixes = explode(',',$includeprefix);
						foreach( $prefixes as $oneprefix )
						{
							if( strstr($content->alias(),$oneprefix) !== FALSE )
							{
								$includeit = 1;
								break;
							}
						}
					}

					// see if we need to explicitly exclude this content
					$excludeit = 0;
					if( $excludeprefix != '' )
					{
						$prefixes = explode(',',$excludeprefix);
						foreach( $prefixes as $oneprefix )
						{
							if( strstr($content->alias(),$oneprefix) !== FALSE )
							{
								$excludeit = 1;
								break;
							}
						}
					}

					if ($content != NULL && $content->active() && 
					    ($includeit && !$excludeit) &&
					    ($content->show_in_menu() || $show_all == 1)  && !$content->is_system_page())
					{
						$newnode =& $this->FillNode($content, $onechild, $nodelist, 
							$gCms, $count, $prevdepth, $origdepth, $deep);
						//Ok, this one is nasty...
						//First part checks to see if number_of_levels is set and whether the current depth is deeper than the set number_of_levels depth (opposite logic, actually)
						//Second part checks to see if showparents is set...  if so, then it checks to see if this hierarchy position is one of them
						//If either of these things occurs, then try to show the children of this node
						if (!(isset($params['number_of_levels']) && 
							$newnode->depth > ($params['number_of_levels']) - ($origdepth)) && 
							(count($showparents) == 0 || (count($showparents) > 0 && 
							in_array($content->hierarchy() . '.', $showparents))))
						{
							$this->GetFlatChildNodes($onechild, $nodelist, $gCms, $prevdepth, $count, $params, $origdepth, $showparents, $deep);
						}
					}
				}
			}
		}
	}

	function & FillNode(&$content, &$node, &$nodelist, &$gCms, &$count, &$prevdepth, $origdepth, $deep = false)
	{
		$onenode = new stdClass();
		$onenode->id = $content->id();
		$onenode->pagetitle = $content->name();
		$onenode->url = $content->get_url();
		$onenode->accesskey = $content->access_key();
		$onenode->type = strtolower($content->type());
		$onenode->tabindex = $content->tab_index();
		$onenode->titleattribute = $content->title_attribute();
		$onenode->modified = $content->GetModifiedDate();
		$onenode->created = $content->GetCreationDate();

		$onenode->hierarchy = $content->hierarchy();
		$onenode->depth = count(explode('.', $content->hierarchy())) - ($origdepth - 1);
		$onenode->prevdepth = $prevdepth - ($origdepth - 1);
		if ($onenode->prevdepth == 0)
			$onenode->prevdepth = 1;
		$onenode->haschildren = false;
		if (isset($node))
			$onenode->haschildren = $node->has_children(true);
		$prevdepth = $onenode->depth + ($origdepth - 1);
		$onenode->menutext = my_htmlentities($content->menu_text());
		$onenode->raw_menutext = $content->menu_text();
		$onenode->target = '';
		$onenode->index = $count;
		$onenode->alias = $content->alias();
		$onenode->parent = false;
		$count++;

		if( $deep )
		{
			$onenode->extra1 = $content->get_property_value('extra1');
			$onenode->extra2 = $content->get_property_value('extra2');
			$onenode->extra3 = $content->get_property_value('extra3');
			$tmp = $content->get_property_value('image');
			if( !empty($tmp) && $tmp != -1 )
			{
				$onenode->image = $tmp;
			}
			$tmp = $content->get_property_value('thumbnail');
			if( !empty($tmp) && $tmp != -1 )
			{
				$onenode->thumbnail = $tmp;
			}
			if ($content->has_property('target'))
				$onenode->target = $content->get_property_value('target');
		}
		
		if (isset($gCms->variables['content_id']) && $onenode->id == $gCms->variables['content_id'])
			$onenode->current = true;
		else
		{
			$onenode->current = false;
			//So, it's not current.  Lets check to see if it's a direct parent
			if (isset($gCms->variables["friendly_position"]))
			{
				if (strstr($gCms->variables["friendly_position"] . '.', $content->hierarchy() . '.') == $gCms->variables["friendly_position"] . '.')
				{
					$onenode->parent = true;
				}
			}
		}

		$nodelist[] = $onenode;

		return $onenode;
	}

	function nthPos($str, $needles, $n=1)
	{
		//  Found at: http://us2.php.net/manual/en/function.strpos.php
		//  csaba at alum dot mit dot edu
		//  finds the nth occurrence of any of $needles' characters in $str
		//  returns -1 if not found; $n<0 => count backwards from end
		//  e.g. $str = "c:\\winapps\\morph\\photos\\Party\\Phoebe.jpg";
		//      substr($str, nthPos($str, "/\\:", -2)) => \Party\Phoebe.jpg
		//      substr($str, nthPos($str, "/\\:", 4)) => \photos\Party\Phoebe.jpg
		$pos = -1;
		$size = strlen($str);
		if ($reverse=($n<0)) { $n=-$n; $str = strrev($str); }
		while ($n--)
		{
			$bestNewPos = $size;
			for ($i=strlen($needles)-1;$i>=0;$i--)
			{
				$newPos = strpos($str, $needles[$i], $pos+1);
				if ($newPos===false) $needles = substr($needles,0,$i) . substr($needles,$i+1);
				else $bestNewPos = min($bestNewPos,$newPos);
			}
			if (($pos=$bestNewPos)==$size) return -1;
		}
		return $reverse ? $size-1-$pos : $pos;
	}
	
} // End of class

# vim:ts=4 sw=4 noet
?>
