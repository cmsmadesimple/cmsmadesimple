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

function smarty_cms_function_cms_selflink($params, &$smarty) {

        global $gCms;
        $db = $gCms->db;
        $config = $gCms->config;

        $rellink = (isset($params['rellink']) && $params['rellink'] == '1' ? true : false);

        /* LeisureLarry - Changed if statement */
        if (isset($params['page']) or isset($params['href']))
        {
                $page = $params['page'];
                $name = $params['page']; //mbv - 21-06-2005

                /* LeisureLarry - Begin */
                if (isset($params['href']))
                {
                        $page = $params['href'];
                }
                /* LeisureLarry - End */

                # check if the page exists in the db
                $manager =& $gCms->GetHierarchyManager();
                $node =& $manager->sureGetNodeByAlias($page);
                if (!isset($node)) return;
                $content =& $node->GetContent();
                if ($content !== FALSE)
                {
                        $pageid = $content->Id();
                        $alias = $content->Alias();
                        $name = $content->Name(); //mbv - 21-06-2005
                        $url = $content->GetUrl();
                        $menu_text = $content->MenuText();
                        $titleattr = $content->TitleAttribute();
                }
                        $Prev_label = "";
                        $Next_label = "";
        }
        elseif (isset($params['dir'])) // this part is by mbv built on a proposal by 100rk
        {
                if (isset($params['lang'])){
                        switch (strtolower($params['lang']))
                        {
                                case 'dk':
                                case 'da':
                                        $Prev_label = "Forrige side: ";
                                        $Next_label = "N&aelig;ste side: ";
                                        break;
                                case 'en':
                                        $Prev_label = "Previous page: ";
                                        $Next_label = "Next page: ";
                                        break;
                                case '0':
                                        $Prev_label = "";
                                        $Next_label = "";
                                        break;
                                default:
                                        $Prev_label = "Previous page: ";
                                        $Next_label = "Next page: ";
                        }
                }
                else
                {
                        $Prev_label = "Previous page: ";
                        $Next_label = "Next page: ";
                }
                $condition = $order_by = false;
                switch (strtolower($params['dir']))
                {
                        case 'next':
                                $condition = '>';
                                $order_by = 'hierarchy';
                                $label=$Next_label;
                                break;
                        case 'prev':
                        case 'previous':
                                $condition = '<';
                                $order_by = 'hierarchy DESC';
                                $label=$Prev_label;
                                break;
                        case 'start':
                                $condition = '-';
                                $order_by = 'something';
                                $label = '';
                                break;
                }
                if ($condition && $order_by)
                {
                        global $gCms;
                        $hm =& $gCms->GetHierarchyManager();
                        $flatcontent =& $hm->getFlattenedContent();
                        $number = 0;
                        for ($i = 0; $i < count($flatcontent); $i++)
                        {
                                if ($condition == '-')
                                {
                                        if ($flatcontent[$i]->DefaultContent() == true)
                                        {
                                                $number = $i;
                                                break;
                                        }
                                }
                                else if ($flatcontent[$i]->Id() == $gCms->variables['content_id'])
                                {
                                        $number = $i;
                                        break;
                                }
                        }
                        if ($condition == '<')
                        {
                                if ($number > 0)
                                {
                                        $content =& $flatcontent[$number-1];
                                        if (isset($content))
                                        {
                                                $pageid = $content->Id();
                                                $alias = $content->Alias();
                                                $name = $content->Name();
                                                $menu_text = $content->MenuText();
                                                $url = $content->GetURL();
                                                $titleattr = $content->TitleAttribute();
                                        }
                                }
                        }
                        else if ($condition == '>')
                        {
                                if ($number < count($flatcontent))
                                {
                                        $content =& $flatcontent[$number+1];
                                        if (isset($content))
                                        {
                                                $pageid = $content->Id();
                                                $alias = $content->Alias();
                                                $name = $content->Name();
                                                $menu_text = $content->MenuText();
                                                $url = $content->GetURL();
                                                $titleattr = $content->TitleAttribute();
                                        }
                                }
                        }
                        else if ($condition == '-')
                        {
                                $content =& $flatcontent[$number];
                                if (isset($content))
                                {
                                        $pageid = $content->Id();
                                        $alias = $content->Alias();
                                        $name = $content->Name();
                                        $menu_text = $content->MenuText();
                                        $url = $content->GetURL();
                                        $titleattr = $content->TitleAttribute();
                                }
                        }
                }
                unset($condition);
                unset($order_by);
        } // end of next-prev code

        /*
        if (isset($alias) && $alias != "") {
                if ($config["assume_mod_rewrite"])
                        $url = $config["root_url"]."/".$alias.$config['page_extension'];
                else
                        $url = $config["root_url"]."/index.php?".$config["query_var"]."=".$alias;
        } else if (isset ($pageid)) {
                if ($config["assume_mod_rewrite"])
                        $url = $config["root_url"]."/".$pageid.$config['page_extension'];
                else
                        $url = $config["root_url"]."/index.php?".$config["query_var"]."=".$pageid;
        } else {
                $url="";
        }
        */

        if (isset($params['label']))
        {
                $label = $params['label'];
        }

        $result = "";

        /* LeisureLarry - Changes if statement */
        if (($url != "") and !isset($params['href']))
        {
                if ($rellink && isset($params['dir']))
                {
                        $result .= '<link rel="';
                        if ($params['dir'] == 'prev' || $params['dir'] == 'previous')
                        {
                                $result .= 'prev';
                        }
                        else if ($params['dir'] == 'start')
                        {
                                $result .= 'start';
                        }
                        else if ($params['dir'] == 'next')
                        {
                                $result .= 'next';
                        }
                        $result .= '" title="' . ($titleattr != '' ? $titleattr : $name);
                        $result .= '" href="' . $url . '" />';
                }
                else
                {
                        $result .= $label.'<a href="'.$url.'"';

                        $result .= ' title="'.($titleattr != '' ? $titleattr : $name).'"';

                        if (isset($params['target']))
                        {
                                $result .= ' target="'.$params['target'].'"';
                        }

                        if (isset($params['id']))
                        {
                                $result .= ' id="'.$params['id'].'"';
                        }

                        if (isset($params['class']))
                        {
                                $result .= ' class="'.$params['class'].'"';
                        }

                        if (isset($params['more']))
                        {
                                $result .= ' '.$params['more'];
                        }
                        $result .= '>';

                        if (isset($params['text'])){
                                $result .= $params['text'];
                        } elseif (isset($params['menu']) && $params['menu'] == "1")        { // mbv
                                $result .= $menu_text;
                        } else {
                                $result .= $name; // mbv - 21-06-2005
                        }

                        $result .= '</a>';
                }

        /* LeisureLarry - Begin */
        }
        elseif (isset($params['href'])) {

                $result .= $url;

        /* LeisureLarry - End */

        }
        else {
                /*
                $result .= "<!-- Not a valid cms_selflink -->";
                if (isset($params['text']))
                {
                        $result .= $params['text'];
                }
                */
        }

        return $result;
}

function smarty_cms_help_function_cms_selflink() {
        ?>
        <h3>What does this do?</h3>
        <p>Creates a link to another cms content page inside your template or content.</p>
        <h3>How do I use it?</h3>
        <p>Just insert the tag into your template/page like: <code>{cms_selflink page="1"}</code> or  <code>{cms_selflink page="alias"}</code></p>
        <h3>What parameters does it take?</h3>
        <p>
        <ul>
                <li><em>(optional)</em> <tt>page</tt> - Page ID or alias to link to.</li>
                <li><em>(optional)</em> <tt>dir start/next/prev (previous)</tt> - Links to the default start page or the next or previous page. If this is used <tt>page</tt> should not be set.</li> <!-- mbv - 21-06-2005 -->
                        <B>Note!</B> Only one of the above may be used in the same cms_selflink statement!!
                <li><em>(optional)</em> <tt>text</tt> - Text to show for the link.  If not given, the Page Name is used instead.</li>
                <li><em>(optional)</em> <tt>menu 1/0</tt> - If 1 the Menu Text is used for the link text instead of the Page Name</li> <!-- mbv - 21-06-2005 -->
                <li><em>(optional)</em> <tt>target</tt> - Optional target for the a link to point to.  Useful for frame and javascript situations.</li>
                <li><em>(optional)</em> <tt>class</tt> - Class for the &lt;a&gt; link.  Useful for styling the link.</li> <!-- mbv - 21-06-2005 -->
                <li><em>(optional)</em> <tt>lang</tt> - Display link-labels  ("Next Page"/"Previous Page") in different languages (0 for no label.) Danish (dk) or English (en), for now.</li> <!-- mbv - 21-06-2005 -->
                <li><em>(optional)</em> <tt>id</tt> - Optional css_id for the &lt;a&gt; link.</li> <!-- mbv - 29-06-2005 -->
                <li><em>(optional)</em> <tt>more</tt> - place additional options inside the &lt;a&gt; link.</li> <!-- mbv - 29-06-2005 -->
                <li><em>(optional)</em> <tt>label</tt> - Label to use in front of the link if applicable.</li>
                <li><em>(optional)</em> <tt>rellink 1/0</tt> - Make a relational link for accessible navigation.  Only works if the dir parameter is set and should only go in the head section of a template.</li>
                <li><em>(optional)</em> <tt>href</tt> - If href is used only the href value is generated (no other parameters possible). <B>Example:</B> &lt;a href="{cms_selflink href="alias"}"&gt;&lt;img src=""&gt;&lt;/a&gt;</li>
        </ul>
        </p>

        <?php
}

function smarty_cms_about_function_cms_selflink() {
        ?>
        <p>Author: Ted Kulp &lt;tedkulp@users.sf.net&gt;</p>
        <p>Version: 1.1</p>
        <p>Modified: Martin B. Vestergaard &lt;mbv@nospam.dk&gt;</p>
        <p>Version: 1.41</p>
        <p>
        Change History:<br/>
        1.41 - added new parameter "href" (LeisureLarry)<br />
        1.4 - fixed bug next/prev linking to non-content pages. (Thanks Teemu Koistinen for this fix)<br />
        1.3 - added option "more"<br />
        1.2 - by Martin B. Vestergaard
        <ul>
                <li>changed default text to Page Name (was Page Alias)</li>
                <li>added option dir=next/prev to display next or previous item in the hirachy - thanks to 100rk</li>
                <li>added option class to add a class= statement to the a-tag.</li>
                <li>added option menu to display menu-text in sted of Page Name</li>
                <li>added option lang to display link-labels in different languages</li>
        </ul>
        1.1 - Changed to new content system<br />
        1.0 - Initial release
        </p>
        <?php
}

# vim:ts=4 sw=4 noet
?>