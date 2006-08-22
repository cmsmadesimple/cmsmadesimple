<?php

# CMS - CMS Made Simple
#
# (c)2004 by Ted Kulp (wishy@users.sf.net)
#
# This project's homepage is: http://cmsmadesimple.sf.net
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_sitemap($params, &$smarty) {

        global $gCms;

        $contentops =& $gCms->GetContentOperations();
        $allcontent = $contentops->GetAllContent();

        // defining variables
        $menu = '';
        $last_level = 0;
        $count = 0;
        $in_hr = 0;

        $add_elements = isset($params["add_elements"]) ? $params["add_elements"] : 0 ;
        $add_element = explode(",", $add_elements);

        foreach ($allcontent as $onecontent)
        {
                // Handy little trick to figure out how deep in the tree we are
                // Remember, content comes to use in order of how it should be displayed in the tree already
                $depth = count(split('\.', $onecontent->Hierarchy()));

                // If hierarchy starts with the start_element (if it's set), then continue on
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
                // Now check to make sure we're not too many levels deep if number_of_levels is set
                if (isset($params['number_of_levels']))
                {
                        $number_of_levels = $params['number_of_levels'] - 1;
                        $base_level = 1;

                        // Is start_element set?  If so, reset the base_level to it's level
                        if (isset($params['start_element']))
                        {
                                $base_level = count(split('\.', $params['start_element'])) + 1;
                        }
                        // If this element's level is more than base_level + number_of_levels, then scratch it
                        if ($base_level + $number_of_levels < $depth)
                        {
                                continue;
                        }
                }

                // Not active or separator?  Toss it.
                if (!$onecontent->Active() || !$onecontent->MenuText())
                {
                        continue;
                }

                // Not shown in menu?  Toss it.
                if (!$onecontent->ShowInMenu())
                {
                        // If param showall, display also content not shown in menu.
                        if (((isset($params['showall']) && $params['showall'] == 1)) or
                        ($add_elements && in_array($onecontent->Alias(),$add_element)))
                        {
                        }

                        else continue;
                }

                if ($depth < $last_level)
                {
                        for ($i = $depth; $i < $last_level; $i++) $menu .= "</li>\n</ul>\n";
                        if ($depth > 0)
                        {
                                $menu .= "</li>\n";
                        }
                }
                if ($depth == $last_level)
                {
                        $menu .= "</li>\n";
                }
                if ($depth > $last_level)
                {
                        if ((isset($params['class']) && $params['class'] != '') && ($count == 0))
                        {
                                $menu .= '<ul class="'.$params['class'].'">'."\n";
                        }
                        else
                        {
                                $menu .= "\n<ul>\n";
                        }
                }

				if (! ((isset($params['relative']) && $params['relative']==1) &&
					(isset($gCms->variables['content_id']) && $onecontent->Id() == $gCms->variables['content_id']) ))
				// we are not going to show current page if relative it's enabled - we'll show only his childs
				{
						$menu .= "<li>";

						if ((isset($params['delimiter']) && $params['delimiter'] != '') && ($depth > 1))
						{
								$ddepth = (split('\.', $onecontent->Hierarchy()));
								if (($ddepth[sizeof($ddepth)-1] > 1) || (isset($params['initial']) && $params['initial'] == '1'))
								{
										$menu .= $params['delimiter'];
								}
						}

                		// No link if section header.
                		if ($onecontent->HasUsableLink())
                		{
								$menu .= "<a href=\"".$onecontent->GetURL()."\"";
								if (isset($gCms->variables['content_id']) && $onecontent->Id() == $gCms->variables['content_id'])
								{
										$menu .= " class=\"currentpage\"";
								}
								if ($onecontent->GetPropertyValue('target') != '')
								{
										$menu .= ' target="'.$onecontent->GetPropertyValue('target').'"';
								}
								$menu .= ">".$onecontent->MenuText()."</a>";
						}
						else
						{
								$menu .= $onecontent->MenuText();
						}
				}
                $last_level = $depth;
                $count++;
        }

        for ($i = 0; $i < $last_level; $i++) $menu .= "</li>\n</ul>";

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
                <li><em>(optional)</em> <tt>class</tt> - A css_class for the ul-tag which includes the complete sitemap.</li>
                <li><em>(optional)</em> <tt>start_element</tt> - The hierarchy of your element (ie : 1.2 or 3.5.1 for example). This parameter sets the root of the menu. You can use the page alias instead of hierarchy.</li>
                <li><em>(optional)</em> <tt>number_of_levels</tt> - An integer, the number of levels you want to show in your menu. Should be set to 2 using a delimiter.</li>
                <li><em>(optional)</em> <tt>delimiter</tt> - Text to separate entries not on depth 1 of the sitemap (i.e. 1.1, 1.2). This is helpful for showing entries on depth 2 beside each other (using css display:inline).</li>
                <li><em>(optional)</em> <tt>initial 1/0</tt> - If set to 1, begin also the first entries not on depth 1 with a delimiter (i.e. 1.1, 2.1).</li>
                <li><em>(optional)</em> <tt>relative 1/0</tt> - We are not going to show current page (with the sitemap) - we'll show only his childs.</li>
                <li><em>(optional)</em> <tt>showall 1/0</tt> - We are going to show all pages if showall is enabled, else we'll only show pages with active menu entries.</li>
                <li><em>(optional)</em> <tt>add_elements</tt> - A comma separated list of alias names which will be added to the shown pages with active menu entries (showall not enabled).</li>
        </ul>
        </p>
        <?php
}

function smarty_cms_about_function_sitemap()
{
        ?>
        <p>Author: Marcus Deglos &lt;<a href="mailto:md@zioncore.com">md@zioncore.com</a>&gt;</p>
        <p>Version: 1.23</p>
        <p>
        Change History:<br/>
        1.23 - Section headers and separators are shown, but without link (Simon van der Linden)<br />
        1.22 - Modified to use the new parameters class, delimiter, initial und add_elements (LeisureLarry)<br />
        1.21 - Changed help to show the existing parameters relative and showall (LeisureLarry)<br />
        1.2 -  Modified to support alias instead of hierarchy and minor output improvement (intersol).<br />
        1.1 -  Modified to use new content rewrite (wishy)
        </p>
        <?php
}

# vim:ts=4 sw=4 noet
?>
