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

class RSS extends CMSModule
{
	function GetName()
	{
		return 'RSS';
	}

	function GetVersion()
	{
		return '1.1';
	}

	function GetHelp($lang = 'en_US')
	{
		return $this->Lang('help');
	}

	function GetAuthor()
	{
		return 'Ted Kulp';
	}

	function GetAuthorEmail()
	{
		return 'wishy@cmsmadesimple.org';
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

	function SetParameters()
	{
		$this->CreateParameter('descriptions', '1', $this->lang('helpdescriptions'));
		$this->CreateParameter('target', '', $this->lang('helptarget'));
		$this->CreateParameter('numbertoshow', '5', $this->lang('helpnumbertoshow'));
		$this->CreateParameter('url', 'http://feed_url', $this->lang('helpurl'), false);
	}

	function DoAction($name, $id, $params)
	{
		if ($name == 'default')
		{
			$config = $this->cms->config;

			define('MAGPIE_CACHE_DIR', $config["root_path"].'/tmp/cache');

			require_once dirname(__FILE__).('/rss_fetch.inc');

			if (isset($params['url']))
			{
				$rss = @fetch_rss($params['url']);
				if ($rss && is_array($rss->items))
				{
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
						$title = $item['title'];
						$url   = $item['link'];
						$desc  = $item['description'];
						echo "<div class=\"modulerssentry\"><a href=\"$url\"";
						if (isset($params['target']))
						{
							echo ' target="'.$params['target'].'"';
						}
						echo ">$title</a><br />";
						if (isset($params['descriptions']))
						{
							echo $desc;
						}
						echo "</div>\n";
					}
				}
			}
		}
	}
}

# vim:ts=4 sw=4 noet
?>
