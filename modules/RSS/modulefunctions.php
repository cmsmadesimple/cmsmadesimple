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
			echo "<div class=\"modulerssentry\"><a href=\"$url\">$title</a><br /></div>\n";
		}
	}
}

function rss_module_help($cms)
{
	//Text to show for the help box...
	?>

	<h3>What does this do?</h3>
	<p>RSS is a module for displaying news feeds from other sites in your site.  It can be inserted into a template or content page as a tag and will display the title and url of each item from the feed given.</p>
	<h3>Anything else I should know?</h3>
	<p>The RSS module will cache feeds so that they are not downloaded and parsed on every refresh.  Instead, it pulls the feed down every so many page refreshes and stores the data locally so it's easily accessible.  When the local data becomes stale, it will pull fresh data.  You should notice no performance hit by using it in a template.</p>
	<h3>How do I use it?</h3>
	<p>As this is just a tag module, it's inserted into your page or template by using the cms_module tag.  Example syntax would be: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
	<h3>What parameters are there?</h3>
	<p>
	<ul>
		<li>url="http://feed_url" - RSS feed URL</li>
		<li><em>(optional)</em>numbertoshow="5" - Maximum number of items to display -- leaving empty will show all items</li>
	</ul>
	</p>

	<?php

}

function rss_module_about() {
	?>
	<p>Author: Ted Kulp &lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
