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

function phplayers_prerender_function(&$cms, &$content)
{
	$text = '
	<link rel="stylesheet" href="phplayers/layersmenu-cms.css" type="text/css"></link>
	<script language="JavaScript" type="text/javascript" src="modules/PHPLayers/phplayers/libjs/layersmenu-browser_detection.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/PHPLayers/phplayers/libjs/layersmenu-library.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/PHPLayers/phplayers/libjs/layersmenu.js"></script>';

	$content = ereg_replace("<\/head>", $text."</head>", $content);
}

function phplayers_module_execute($cms, $id, $params)
{
	$allcontent = ContentManager::GetAllContent();

	# getting menu parameters
	$showadmin = isset($params["showadmin"]) ? $params["showadmin"] : 1 ;
	$horizontal = isset($params["horizontal"]) ? $params["horizontal"] : 0 ;

	$menu = "";

	foreach ($allcontent as $onecontent)
	{
		#Handy little trick to figure out how deep in the tree we are
		#Remember, content comes to use in order of how it should be displayed in the tree already
		$depth = count(split('\.', $onecontent->Hierarchy()));

		#If hierarchy starts with the start_element (if it's set), then continue on
		if (isset($params['start_element']))
		{
			if (!(strpos($onecontent->Hierarchy(), $params['start_element']) !== FALSE && strpos($onecontent->Hierarchy(), $params['start_element']) == 0))
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
				$base_level = count(split('\.', $params['start_element']));
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

		for ($i = 1; $i <= $depth; $i++) { $menu .= "."; }

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
		$menu .= ".|Admin|admin/\n";
	}

	#TODO: Needs to go into head somehow (with prerender hook?)
	$text = '
	<link rel="stylesheet" href="phplayers/layersmenu-cms.css" type="text/css"></link>
	<script language="JavaScript" type="text/javascript" src="modules/PHPLayers/phplayers/libjs/layersmenu-browser_detection.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/PHPLayers/phplayers/libjs/layersmenu-library.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/PHPLayers/phplayers/libjs/layersmenu.js"></script>';

	$text = '';
	
	require_once dirname(__FILE__).'/phplayers/lib/PHPLIB.php';
	require_once dirname(__FILE__).'/phplayers/lib/layersmenu-common.inc.php';
	require_once dirname(__FILE__).'/phplayers/lib/layersmenu.inc.php';
	
	$mid = new LayersMenu();
	
	/* TO USE RELATIVE PATHS: */
	$mid->setDirroot('./phplayers/');
	$mid->setLibjsdir('./phplayers/libjs/');
	$mid->setImgdir('./phplayers/menuimages/');
	$mid->setImgwww('./phplayers/menuimages/');
	//$mid->setIcondir('./phplayers/menuicons/');
	//$mid->setIconwww('./phplayers/menuicons/');
	
	$mid->setTpldir('./phplayers/templates/');
	if ($horizontal == 1) {
	  $mid->setHorizontalMenuTpl('layersmenu-horizontal_menu.ihtml');
	}
	else {
	  $mid->setVerticalMenuTpl('layersmenu-vertical_menu.ihtml');
	}
	$mid->setSubMenuTpl('layersmenu-sub_menu.ihtml');

	
	$mid->setMenuStructureString($menu);
	$mid->setIconsize(16, 16);
	$mid->parseStructureForMenu('menu1');
	if ($horizontal == 1) {
	  $mid->newHorizontalMenu('menu1');
	}
	else {
	  $mid->newVerticalMenu('menu1');
	}
	
	$text .= $mid->getHeader();
	$text .= $mid->getMenu('menu1');
	$text .= $mid->getFooter();
	
	echo $text;

}

function phplayers_module_help($cms)
{
	?>
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
	<?php
}

function phplayers_module_about($cms)
{
	?>
	<p>Author: Julien Lancien&lt;calexico@cmsmadesimple.org&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
