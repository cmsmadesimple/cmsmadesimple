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

require_once(dirname(__FILE__)."/include.php");
#require_once(dirname(__FILE__)."/lib/preview.functions.php");

#$smarty = new Smarty_Preview($config);
#$gCms->smarty = &$smarty;

#$smarty = new Smarty_CMS($config);
#$gCms->smarty = &$smarty;

$page = "";

if (isset($_GET["tmpfile"]) && $_GET["tmpfile"] != "")
{
	$page = $_GET["tmpfile"];
	$page = str_replace("..", "", $page);
	$page = str_replace("\\", "", $page);
	$page = str_replace("/", "", $page);
	
	$page = htmlentities($page);

	#header("Content-Language: " . $current_language);
	#header("Content-Type: text/html; charset=" . get_encoding());

	$html = $smarty->fetch('preview:'.$page);

	#Perform the content postrender callback
	reset($gCms->modules);
	while (list($key) = each($gCms->modules))
	{
		$value =& $gCms->modules[$key];
		if ($gCms->modules[$key]['installed'] == true &&
			$gCms->modules[$key]['active'] == true)
		{
			$gCms->modules[$key]['object']->ContentPostRender($html);
		}
	}
	
	Events::SendEvent('Core', 'ContentPostRender', array('content' => &$html));

	echo $html;
}

# vim:ts=4 sw=4 noet
?>
