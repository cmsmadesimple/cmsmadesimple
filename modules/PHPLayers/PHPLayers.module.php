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
		return '1.1';
	}

	function GetHelp($lang = 'en_US')
	{
		return "
			<h3>What does this do?</h3>
			<p>Prints a dhtml vertical menu.</p>
			<h3>How do I use it?</h3>
			<p>Just insert the module into your template/page like: <code>{cms_module module='phplayers'}</code></p>
			<h3>What parameters does it take?</h3>
			<p>
			<ul>
				<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, whether you want to show or not the admin link.</li>
				<li><em>(optional)</em> <tt>start_element</tt> - the hierarchy of your element (ie : 1.2 or 3.5.1 for example). This parameter sets the root of the menu.</li>
				<li><em>(optional)</em> <tt>number_of_levels</tt> - an integer, the number of levels you want to show in your menu.</li>
				<li><em>(optional)</em> <tt>horizontal</tt> - 1/0, whether you want to have a horizontal menu instead of vertical.</li>
			</ul>
			</p>
		";
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
			1.1: Module API changes<br />
			1.0: Initial release
		";
	}

	function IsPluginModule()
	{
		return true;
	}

	function ContentPreRender(&$content)
	{
		$config = $this->cms->config;

		$text = '
		<script language="JavaScript" type="text/javascript" src="'.$config['root_url'].'/modules/PHPLayers/phplayers/libjs/layersmenu-browser_detection.js"></script>
		<script language="JavaScript" type="text/javascript" src="'.$config['root_url'].'/modules/PHPLayers/phplayers/libjs/layersmenu-library.js"></script>
		<script language="JavaScript" type="text/javascript" src="'.$config['root_url'].'/modules/PHPLayers/phplayers/libjs/layersmenu.js"></script>';

		$content = ereg_replace("<\/head>", $text."</head>", $content);
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
			$allcontent = ContentManager::GetAllContent();

			# getting menu parameters
			$showadmin = isset($params['showadmin']) ? $params['showadmin'] : 1 ;
			$horizontal = isset($params['horizontal']) ? $params['horizontal'] : 0 ;

			$menu = '';

			#Reset the base depth if necessary...
			if (isset($params['start_element']))
			{
				$basedepth = count(split('\.', (string)$params['start_element']));
			}

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

				if (!$onecontent->Active() || !$onecontent->ShowInMenu())
				{
					continue;
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
					$menu .= "|".$onecontent->MenuText()."|".$onecontent->GetURL()."\n";
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
			
			require_once dirname(__FILE__).'/phplayers/lib/PHPLIB.php';
			require_once dirname(__FILE__).'/phplayers/lib/layersmenu-common.inc.php';
			require_once dirname(__FILE__).'/phplayers/lib/layersmenu.inc.php';
			
			$mid = new LayersMenu();
			
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
			$mid->parseStructureForMenu('menu1');
			if ($horizontal == 1)
			{
			  $mid->newHorizontalMenu('menu1');
			}
			else
			{
			  $mid->newVerticalMenu('menu1');
			}
			
			$text .= $mid->getHeader();
			$text .= $mid->getMenu('menu1');
			$text .= $mid->getFooter();
			
			return $text;
		}

		//Catch-all
		return '';
	}


}

# vim:ts=4 sw=4 noet
?>
