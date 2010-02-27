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

function smarty_cms_function_sitemap($params, &$smarty) 
{

    global $gCms;

    $contentops =& $gCms->GetContentOperations();
    $allcontent =  $contentops->GetAllContent();

    // define variables
    $menu       = '';
    $last_level = 0;
    $first_level= 0;
    $count      = 0;
    $in_hr      = 0;
    $no_end     = false;

    $add_elements = isset($params['add_elements']) ? $params['add_elements'] : 0;
    $add_element  = explode(',', $add_elements);

    foreach ($allcontent as $onecontent)
    {
        // Handy little trick to figure out how deep in the tree we are
        // Remember, content comes to use in order of how it should be displayed in the tree already
        $depth = count(split('\.', $onecontent->hierarchy()));

        // If hierarchy starts with the start_element (if it's set), then continue on
        if (isset($params['start_element']))
        {
            if (
                ! (
                    strpos($onecontent->hierarchy(), $params['start_element']) !== FALSE 
                    && 
                    strpos($onecontent->hierarchy(), $params['start_element']) == 0
                )
            )
            {
                if(($onecontent->alias() == $params['start_element']))
                {
                     $params['start_element'] = $onecontent->hierarchy();
                     $depth = count(split('\.', $onecontent->hierarchy()));
                     $first_level = $depth;
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
        if (! $onecontent->active() || ! $onecontent->menu_text())
        {
            continue;
        }

        // Not shown in menu?  Toss it.
        if (! $onecontent->show_in_menu())
        {
            // If param showall, display also content not shown in menu.
            if (
                ((isset($params['showall']) && $params['showall'] == 1)) 
                ||
                ($add_elements && in_array($onecontent->alias(), $add_element))
            )
            {

            }
            else continue;
        }

        if ($depth < $last_level)
        {
            for ($i = $depth; $i < $last_level; $i++)
            {
                $menu .= "</li>\n</ul>\n";
            }
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
                $menu .= '<ul class="' . $params['class'] . '">' . "\n";
            }
            else
            {
                $menu .= "\n<ul>\n";
            }
        }
        if (
            ! (
                (isset($params['relative']) && $params['relative'] == 1) 
                && 
                (
                    isset($gCms->variables['content_id'])
                    && 
                    $onecontent->id() == $gCms->variables['content_id']
                )
            )
        )
        // we are not going to show current page if relative it's enabled - we'll show only his childs
        {
            $menu .= '<li>';

            if ((isset($params['delimiter']) && $params['delimiter'] != '') && ($depth > 1))
            {
                $ddepth = (split('\.', $onecontent->hierarchy()));
                if (
                    ($ddepth[sizeof($ddepth) - 1] > 1) 
                    ||
                    (isset($params['initial']) && $params['initial'] == '1')
                )
                {
                    $menu .= $params['delimiter'];
                }
            }

            // No link if section header.
            if ($onecontent->has_usable_link())
            {
                $menu .= '<a href="' . $onecontent->get_url() . '"';
                if (isset($gCms->variables['content_id']) && $onecontent->id() == $gCms->variables['content_id'])
                {
                    $menu .= ' class="currentpage"';
                }
                if ($onecontent->get_property_value('target') != '')
                {
                    $menu .= ' target="' . $onecontent->get_property_alue('target') . '"';
                }
                $menu .= '>' . my_htmlentities($onecontent->menu_text()) . '</a>';
            }
            else
            {
                $menu .= my_htmlentities($onecontent->menu_text());
            }
        }
        else
        {
            if (! $onecontent->has_children())
            {
                $no_end = true;
            }
            else
            {
                $menu .= '<li>';
            }
        }

        $last_level = $depth;
        $count++;
    }

    for ($i = $first_level; $i < $last_level; $i++)
    {
        if ($no_end != true)
        {
            $menu .= '</li>';
        }
        $menu .= "\n</ul>";
    }


    return $menu;

}

function smarty_cms_help_function_sitemap()
{
  echo lang('help_function_sitemap');
}

function smarty_cms_about_function_sitemap()
{
?>
    <p>Author: Marcus Deglos &lt;<a href="mailto:md@zioncore.com">md@zioncore.com</a>&gt;</p>
    <p>Version: 1.23</p>
    <p>
        Change History:<br />
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
