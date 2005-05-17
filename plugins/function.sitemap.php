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

function smarty_cms_function_sitemap($params, &$smarty) {

	global $db;

	$allcontent = ContentManager::GetAllContent();

	# defining variables
	$menu = "";
	$last_level = 0;
	$count = 0;
	$in_hr = 0;

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
				if(($onecontent->Alias() == $params['start_element']))
				{

					$params['start_element'] = $onecontent->Hierarchy();
					$depth = count(split('\.', $onecontent->Hierarchy()));
					continue;
				}
				else
				{
					continue;
				}
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
				$base_level = count(split('\.', $params['start_element'])) + 1;
			}
			#If this element's level is more than base_level + number_of_levels, then scratch it
			if ($base_level + $number_of_levels < $depth)
			{
				continue;
			}
		}

		if (!$onecontent->Active())
		{
			continue;
		}

		if ($onecontent->Type() == 'separator')
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
			if ($depth < $last_level) {
				for ($i = $depth; $i < $last_level; $i++) $menu .= "\n</li>\n</ul>\n";
				if ($depth > 0)
				{
					$menu .= "</li>\n";
				}
			}
			if ($depth > $last_level)
			{
				for ($i = $depth; $i > $last_level; $i--) $menu .= "\n<ul>\n";
			}
			if ($depth == $last_level) {
				$menu .= "</li>\n";
			}
			$menu .= "<li><h2><a href=\"".$onecontent->GetURL()."\">".$onecontent->MenuText();
			if ($onecontent->Name() != '' && $onecontent->Name()!=$onecontent->MenuText())
			{
				$menu .= ' - ' . $onecontent->Name();
			}
			$menu .= "</a></h2>";
			$in_hr = 1;
			$last_level = $depth;
		}
		$count++;
	}

	for ($i = 0; $i < $last_level; $i++) $menu .= "</li></ul>";

	return $menu;

}

function smarty_cms_help_function_sitemap()
{
	?>
	<h3>What does this do?</h3>
	<p>Prints out a sitemap.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{sitemap}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> <tt>start_element</tt> - the hierarchy of your element (ie : 1.2 or 3.5.1 for example). This parameter sets the root of the menu. You can use the page alias instead of hierarchy.</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - an integer, the number of levels you want to show in your menu.</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_sitemap()
{
	?>
	<p>Author: Marcus Deglos &lt;<a href="mailto:md@zioncore.com">md@zioncore.com</a>&gt;</p>
	<p>Version: 1.2</p>
	<p>
	Change History:<br/>
	1.1 - Modified to use new content rewrite (wishy)<br />
	1.2 - Modified to support alias insted of hierarchy and minot output improvement (intersol).
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>