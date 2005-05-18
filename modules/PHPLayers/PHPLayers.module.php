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

class PHPLayers extends CMSModule
{
	function GetName()
	{
		return 'PHPLayers';
	}

	function GetVersion()
	{
		return '1.2';
	}

	function GetHelp($lang = 'en_US')
	{
		return $this->Lang('help');
	}

	function GetAuthor()
	{
		return 'Julien Lancien';
	}

	function GetAuthorEmail()
	{
		return 'calexico@cmsmadesimple.org';
	}

	function GetChangeLog()
	{
		return "
			1.2: Now supports multiple instances (intersol)<br />
			1.1: Module API changes<br />
			1.0: Initial release
		";
	}

	function IsPluginModule()
	{
		return true;
	}

	function PHPLayers() // constructor
	{
		require_once dirname(__FILE__).'/phplayers/lib/PHPLIB.php';
		require_once dirname(__FILE__).'/phplayers/lib/layersmenu-common.inc.php';
		require_once dirname(__FILE__).'/phplayers/lib/layersmenu.inc.php';

		$_SESSION['layersmenuobj'] = new LayersMenu();
	
	}

	function ContentPreRender(&$content)
	{
		global $gCms;

		$config = $this->cms->config;

		$text = '
		<script language="JavaScript" type="text/javascript" src="'.$config['root_url'].'/modules/PHPLayers/phplayers/libjs/layersmenu-browser_detection.js"></script>
		<script language="JavaScript" type="text/javascript" src="'.$config['root_url'].'/modules/PHPLayers/phplayers/libjs/layersmenu-library.js"></script>
		<script language="JavaScript" type="text/javascript" src="'.$config['root_url'].'/modules/PHPLayers/phplayers/libjs/layersmenu.js"></script>';

		$content = ereg_replace("<\/head>", $text."</head>", $content);
	}
	function ContentPostRender(&$content)
	{
		$content = ereg_replace('<body>', '<body>' . $_SESSION['layersmenuobj']->getHeader(), $content);
		$content = ereg_replace('</body>', $_SESSION['layersmenuobj']->getFooter() . '</body>' , $content);
		$_SESSION['layersmenuobj_next'] = null;
		$_SESSION['layersmenuobj'] = null;
	}

		function ContentStylesheet(&$stylesheet)
	{
		$config = $this->cms->config;
		@ob_start();
		@readfile(dirname(__FILE__).'/phplayers/layersmenu-cms.css');
		$stylesheet = @ob_get_contents() . $stylesheet;
		@ob_end_clean();
	}

	function DoAction($name, $id, $params)
	{
		$config = $this->cms->config;
		if ($name == 'default')
		{
			$basedepth = 1;
			$allcontent = ContentManager::GetAllContent(false);

			# getting menu parameters
			$showadmin = isset($params['showadmin']) ? $params['showadmin'] : 0 ;
			$horizontal = isset($params['horizontal']) ? $params['horizontal'] : 0 ;
			
			if(isset($_SESSION['layersmenuobj_cnt']))
			{
				$menuid = "menu" . $_SESSION['layersmenuobj_next'];
				$_SESSION['layersmenuobj_next'] += 1;
			}
			else
			{
				$menuid = isset($params['id']) ? $params['id'] : 'menu' ;
				$_SESSION['layersmenuobj_next'] = 2;
			}

			#$menuid = "menu1";

			$menu = '';

			#Reset the base depth if necessary...
			if (isset($params['start_element']))
			{
				$basedepth = count(split('\.', (string)$params['start_element']));
			}

			$disabled = array();

			foreach ($allcontent as $onecontent)
			{
				#Handy little trick to figure out how deep in the tree we are
				#Remember, content comes to use in order of how it should be displayed in the tree already
				$depth = count(split('\.', $onecontent->Hierarchy()));

				#If hierarchy starts with the start_element (if it's set), then continue on
				if (isset($params['start_element']))
				{
					if ((strpos($onecontent->Hierarchy(), (string)$params['start_element']) === FALSE) || (strpos($onecontent->Hierarchy(), (string)$params['start_element']) != 0))
					{
						continue;
					}
				}

				#Now check to make sure we're not too many levels deep if number_of_levels is set
				if (isset($params['number_of_levels']))
				{
					$number_of_levels = $params['number_of_levels'] - 1;
					$base_level = 1;
					
					#Is start_element set?  If so, reset the base_level to it's level
					if (isset($params['start_element']))
					{
						$base_level = count(split('\.', (string)$params['start_element']));
					}

					#If this element's level is more than base_level + number_of_levels, then scratch it
					if ($base_level + $number_of_levels < $depth)
					{
						continue;
					}
				}

				# Check for inactive items or items set not to show in the menu
				if (!$onecontent->Active() || !$onecontent->ShowInMenu())
				{
					# Stuff the hierarchy position into that array, so we can test for
					# children that shouldn't be showing.  Put the dot on the end
					# since it will only affect children anyway...  saves from a
					# .1 matching .11
					array_push($disabled, $onecontent->Hierarchy() . ".");
					continue;
				}

				$disableme = false;

				# Loop through disabled array to see if this is a child that
				# shouldn't be showing -- we check this by seeing if the current
				# hierarhcy postition starts with one of the disabled positions
				foreach ($disabled as $onepos)
				{
					# Why php doesn't have a starts_with function is beyond me...
					if (strstr($onecontent->Hierarchy(), $onepos) == $onecontent->Hierarchy())
					{
						$disableme = true;
						continue; # Break from THIS foreach
					}
				}

				if ($disableme)
				{
					continue; # Break from main foreach
				}

				for ($i = $basedepth; $i <= $depth; $i++) { $menu .= "."; }

				if ($onecontent->Type() == 'sectionheader')
				{
					$menu .= "|".$onecontent->MenuText()."\n";
				}
				else if ($onecontent->Type() == 'separator')
				{
					$menu .= "|---\n";
				}
				else
				{
					$img = $config['root_url'] . "/images/icons/". $onecontent->mAlias . ".png";
					if (is_file($config['root_path'] . "/images/icons/". $onecontent->mAlias . ".png"))
                    	$menu .= "|".$onecontent->MenuText()."|".$onecontent->GetURL()."|" . 
					$onecontent->mAlias . $img
					."|". $img ."\n";
					else
                    	$menu .= "|".$onecontent->MenuText()."|".$onecontent->GetURL()."|$img|\n";


				}

				$count++;
			}

			if ($showadmin == 1)
			{
				$menu .= ".|---\n";
				$menu .= ".|Admin|".$config['admin_dir']."/\n";
			}

			global $gCms;
			$config = $gCms->config;

			$text = '';
			$mid = $_SESSION['layersmenuobj'];
			
			/* TO USE RELATIVE PATHS: */
			$mid->setDirroot(dirname(__FILE__).'/phplayers/');
			$mid->setLibjsdir(dirname(__FILE__).'/phplayers/libjs/');
			$mid->setImgdir(dirname(__FILE__).'/phplayers/menuimages/');
			$mid->setImgwww($config['root_url'].'/modules/PHPLayers/phplayers/menuimages/');
			//$mid->setIcondir(dirname(__FILE__).'/phplayers/menuicons/');
			//$mid->setIconwww($config['root_url'].'/modules/PHPLayers/phplayers/menuicons/'); 
			$mid->setTpldir(dirname(__FILE__).'/phplayers/templates/');
			if ($horizontal == 1)
			{
			  $mid->setHorizontalMenuTpl('layersmenu-horizontal_menu.ihtml');
			}
			else
			{
			  $mid->setVerticalMenuTpl('layersmenu-vertical_menu.ihtml');
			}
			$mid->setSubMenuTpl('layersmenu-sub_menu.ihtml');

			
			$mid->setMenuStructureString($menu);

			$mid->setIconsize(16, 16);

			$mid->parseStructureForMenu($menuid);
			if ($horizontal == 1)
			{
			  $mid->newHorizontalMenu($menuid);
			}
			else
			{
			  $mid->newVerticalMenu($menuid);
			}

			return $mid->getMenu($menuid);;
		}

		//Catch-all
		return '';
	}


}

# vim:ts=4 sw=4 noet
?>