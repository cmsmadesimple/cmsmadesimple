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

function smarty_cms_function_bulletmenu($params, &$smarty) {

	global $gCms;
	global $db;

	# getting menu parameters
	$showadmin = isset($params["showadmin"]) ? $params["showadmin"] : 1 ;

	$allcontent = ContentManager::GetAllContent();

	# defining variables
	$menu = "";
	$last_level = 0;
	$count = 0;
	$in_hr = 0;

	if (count($allcontent))
	{
		$basedepth = 0;
		$menu .= "<div class=\"bulletmenu\">\n";

		#Reset the base depth if necessary...
		if (isset($params['start_element']))
		{
			$basedepth = count(split('\.', (string)$params['start_element'])) - 1;
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

				#If this element's level is more than base_level + number_of_levels, then scratch it
				if ($basedepth + $number_of_levels < $depth)
				{
					continue;
				}
			}

			$depth = $depth - $basedepth;

			if (!$onecontent->Active() || !$onecontent->ShowInMenu())
			{
				continue;
			}

			if ($onecontent->Type() == 'sectionheader')
			{
				if ($in_hr == 1)
				{
					$menu .= "</ul>\n";
					$in_hr = 0;
				}

				$menu .= "<div class=\"sectionheader\">".$onecontent->MenuText()."</div>\n";

				if ($count > 0 && $in_hr == 0)
				{
					$menu .= "<ul>\n";
					$in_hr = 1;
				}
			}
			else
			{
				if ($depth < $last_level)
				{
					for ($i = $depth; $i < $last_level; $i++)
					{
						$menu .= "\n</li>\n</ul>\n";
					}

					if ($depth > 0)
					{
						$menu .= "</li>\n";
					}
				}

				if ($depth > $last_level)
				{
					for ($i = $depth; $i > $last_level; $i--)
					{
						$menu .= "\n<ul>\n";
					}
				}

				if ($depth == $last_level)
				{
					$menu .= "</li>\n";
				}

				if ($onecontent->Type() == 'separator')
				{
					$menu .= "<li style=\"list-style-type: none;\"><hr class=\"separator\"/>";
				}
				else
				{
					$menu .= "<li><a href=\"".$onecontent->GetURL()."\"";
					if (isset($gCms->variables['page_id']) && $onecontent->Id() == $gCms->variables['page_id'])
					{
						$menu .= " class=\"currentpage\"";
					}
					$menu .= ">".$onecontent->MenuText()."</a>";
				}

				$in_hr = 1;
				$last_level = $depth;
			}
			$count++;
		}

		for ($i = 0; $i < $last_level; $i++) $menu .= "</li></ul>";

		if ($showadmin == 1)
		{
			$menu .= "<ul><li><a href='admin/'>Admin</a></li></ul>\n";
		}
		$menu .= "</div>\n";
	}

	return $menu;

}

function smarty_cms_help_function_bulletmenu() {
	?>
	<h3>What does this do?</h3>
	<p>Prints a bullet menu.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{bulletmenu}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, whether you want to show or not the admin link.</li>
		<li><em>(optional)</em> <tt>start_element</tt> - the hierarchy of your element (ie : 1.2 or 3.5.1 for example). This parameter sets the root of the menu.</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - an integer, the number of levels you want to show in your menu.</li>
	</ul>
	</p>

	<?php
}

function smarty_cms_about_function_bulletmenu() {
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
