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

function rss_module_install($cms)
{
	//Doesn't do anything
}

function rss_module_uninstall($cms)
{
	//Doesn't do anything
}

function rss_module_execute($cms, $id, $params)
{
	//This is the entryway into the module.  All requests from CMS will come through here.

	$config = $cms->config;

	define('MAGPIE_CACHE_DIR', $config["root_path"].'/smarty/cms/cache');

	require_once dirname(__FILE__).('/rss_fetch.inc');

	if (isset($params['url']))
	{
		$rss = fetch_rss($params['url']);
		$items = array();
		if (isset($params['numbertoshow']))
		{
			$items = array_slice($rss->items, 0, $params['numbertoshow']);
		}
		else
		{
			$items = $rss->items;
		}

		foreach ($items as $item ) {
			$title = $item[title];
			$url   = $item[link];
			echo "<a href=\"$url\">$title</a><br />\n";
		}
	}
}

# vim:ts=4 sw=4 noet
?>
