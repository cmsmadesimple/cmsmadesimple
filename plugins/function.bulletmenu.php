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

	$log =& LoggerManager::getLogger('index.php');
	$log->debug('Starting smarty_cms_function_bulletmenu');

	global $gCms;
	global $db;
	global $config;

	# getting menu parameters
	$showadmin = isset($params['showadmin']) ? $params['showadmin'] : 0 ;
	$collapse = isset($params['collapse']) ? $params['collapse'] : 0 ;

	$allcontent = ContentManager::GetAllContent(false);

	# defining variables
	$menu = "";
	$last_level = 0;
	$count = 0;
	$in_hr = 0;

	# array to hold hierarchy postitions of disabled pages
	$disabled = array();

	if (count($allcontent))
	{
		$basedepth = 0;
		$menu .= "<div class=\"bulletmenu\">\n";

		#Reset the base depth if necessary...
		if (isset($params['start_page']))
		{
			foreach( $allcontent as $onecontent )
			{
				if( $onecontent->Alias() == $params['start_page'] || $onecontent->Id() == $params['start_page'])
				{
					$params['start_element'] = $onecontent->Hierarchy();
					break;
				}
			}
		}

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
					if (isset($params['show_root_siblings']) && $params['show_root_siblings'] == '1')
					{
						# Find direct parent of current item
						$curparent = substr($onecontent->Hierarchy(), 0, strrpos($onecontent->Hierarchy(), "."));
						if ($curparent != '')
						{
							$curparent = $curparent . ".";
						}

						# Find direct parent of start_element
						$otherparent = substr((string)$params['start_element'], 0, strpos((string)$params['start_element'], "."));
						if ($otherparent != '')
						{
							$otherparent = $otherparent . ".";
						}

						# Make sure the parents match
						if ($curparent != $otherparent)
						{
							# Show the submenus of siblings, that is everything whose beginning matches parent
							if (substr($curparent,0,strlen($otherparent))!=$otherparent) continue;
						}
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

				#If this element's level is more than base_level + number_of_levels, then scratch it
				if ($basedepth + $number_of_levels < $depth)
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

			# Set depth to be the relative position
			$depth = $depth - $basedepth;

			# Now try to remove items that shouldn't be shown, based on current location
			if ($collapse == 1)
			{
				if ($depth > 1) # All root level items should show 
				{
					$curpos = $gCms->variables['position'];
					$curdepth = count(split('\.', $curpos)) - $basedepth;
					$curparent = substr($gCms->variables['position'], 0, strrpos($gCms->variables['position'], "."));
					if ($curparent != '')
					{
						$curparent = $curparent . ".";
					}

					$skipme = true;

					# Are we the currently selected page?
					if ($onecontent->Hierarchy() == $curpos)
					{
						$skipme = false;
					}

					# First, are we a direct decendant of the current position?
					if (strstr($onecontent->Hierarchy(), $curpos) == $onecontent->Hierarchy() && $curdepth == ($depth - 1))
					{
						$skipme = false;
					}

					# Now for the nasty part...  loop through all parents and show them and direct siblings
					if ($skipme)
					{
						$log->debug('skipme is true');
						$blah = '';
						$count = 1;
						foreach (split('\.', $curpos) as $level)
						{
							$blah .= $level . '.';
							#$log->debug('blah is ' . $blah . ' and hierarchy is ' . $onecontent->Hierarchy());
							if (strstr($onecontent->Hierarchy(), ContentManager::CreateFriendlyHierarchyPosition($blah)) == $onecontent->Hierarchy())
							{
								$log->debug('It\'s a match -- ' . $depth . ' -- ' . ($count + 1));
								if ($depth == ($count + 1))
								{
									$log->debug('Setting skipme to false');
									$skipme = false;
									continue;
								}
							}
							$count++;
						}
					}

					# Ok, so should we skip this thing already?
					if ($skipme)
					{
						continue;
					}
				}
			}

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
				$menu .= "<li style=\"list-style-type: none;\"><hr class=\"separator\" />";
			}
			else if ($onecontent->Type() == 'sectionheader')
			{
				$menu .= '<li><span class="bullet_sectionheader">'.$onecontent->MenuText()."</span>\n";
			}
			else
			{
				$menu .= "<li><a href=\"".$onecontent->GetURL()."\"";
				if (isset($gCms->variables['content_id']) && $onecontent->Id() == $gCms->variables['content_id'])
				{
					$menu .= " class=\"currentpage\"";
				}
				if (method_exists($onecontent, 'HasProperty'))
				{
					if ($onecontent->HasProperty('target') && $onecontent->GetPropertyValue('target') != '')
					{
						$menu .= ' target="'.$onecontent->GetPropertyValue('target').'"';
					}
				}
				$menu .= ">".$onecontent->MenuText()."</a>";
			}

			$in_hr = 1;
			$last_level = $depth;
			$count++;
		}

		for ($i = 0; $i < $last_level; $i++) $menu .= "</li></ul>";

		if ($showadmin == 1)
		{
			$menu .= "<ul><li><a href='".$config['admin_dir']."/'>Admin</a></li></ul>\n";
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
		<li><em>(optional)</em> <tt>collapse</tt> - 1/0, whether you want to collapse sub items that shouldn't be shown.  Defaults to 0.</li>
		<li><em>(optional)</em> <tt>start_page</tt> - the page id or alias (ie : 1.2 or 3.5.1 for example). This parameter sets the root node of the menu and only shows it and it's children. (a replacement for start_element)</li>
		<li><em>(optional)</em> <tt>start_element</tt> - the hierarchy of your element (ie : 1.2 or 3.5.1 for example). This parameter sets the root node of the menu and only shows it and it's children.</li>
		<li><em>(optional)</em> <tt>show_root_siblings</tt> - 1/0, if start_element (above) is given, then show direct siblings of the give start_element as well.</li>
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
