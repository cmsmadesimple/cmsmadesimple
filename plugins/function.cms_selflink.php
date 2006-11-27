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

	$rellink = (isset($params['rellink']) && $params['rellink'] == '1' ? true : false);

	$url = '';

/* ugly hack by tsw for external links with wiki styling */
if ( isset($params['ext']) ) {
   /* thanks elijah */
   $url = $params['ext'];
   $text = $params['ext'];

   if ( isset($params['text'] )) {
      $text = $params['text'];
   }

   $title= '';
   if ( isset($params['title']) ) {
      $title=' title="'.$params['title'].'" ';
   }

   $target = '';
   if ( isset($params['target']) && ( strlen($params['target']) > 0 ) )  {
      $target=' target="'.$params['target'].'" ';
   }

   $external_text = '(external link)';
   if ( isset($params['ext_info']) ) {
      $external_text ='( '.$ext_info.' )';
   }


return '<a class="external" href="'.$url.'" '.$title.''.$target.'>'.$text.'<span>'.$external_text.'</span></a>';
}

$urlparams = '';
if ( isset($params['urlparams']) && ( strlen($params['urlparams'] > 0 ) ) ) {
  $urlparams =$params['urlparams'];
}



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

		/* Mod by Nemesis */
		if (isset($params['anchorlink']))
		{
		    $url .= '#' . ltrim($params['anchorlink'], '#');
		}

		if (isset($params['urlparam']))
		{
		    $url .= trim($params['urlparam']);
		}

			/* End mod Nemesis */
		
		$Prev_label = "";
		$Next_label = "";
		$Anchor_label = ""; //*Russ
		$Parent_label = "Parent page: "; //uplink
	}
	elseif (isset($params['dir'])) // this part is by mbv built on a proposal by 100rk
	{
		/* Russ - Begin */
		if (isset($params['anchorlink']))
		{
			$anchorlink = ltrim($params['anchorlink'], '#');
			//Param to set anchor link
		}
		/* Russ - End */

		if (isset($params['lang']))
		{
			switch (strtolower($params['lang']))
			{
				case 'dk':
				case 'da':
					$Prev_label = "Forrige side: ";
					$Next_label = "N&aelig;ste side: ";
					break;
				case 'nl':
					$Prev_label = "Vorige pagina: ";
					$Next_label = "Volgende pagina: ";
					$Parent_label = "Bovenliggende pagina: "; // uplink
					break;
				case 'en':
					$Prev_label = "Previous page: ";
					$Next_label = "Next page: ";
					$Parent_label = "Parent page: "; //uplink
					break;
				case 'fr':
					$Prev_label = "Page pr&eacute;c&eacute;dente&nbsp;: ";
					$Next_label = "Page suivante&nbsp;: ";
					$Parent_label = "Page ascendante&nbsp;: "; //uplink
					break;
				case 'no':
					$Prev_label = "Forrige side : ";
					$Next_label = "Neste side: ";
					$Parent_label = "Side opp: "; //uplink
					break;
				case '0':
					$Prev_label = "";
					$Next_label = "";
					break;
				default:
					$Prev_label = "Previous page: ";
					$Next_label = "Next page: ";
					$Parent_label = "Parent page: "; //uplink
					break;
			}
		}
		else
		{
			$Prev_label = "Previous page: ";
			$Next_label = "Next page: ";
			$Parent_label = "Parent page: "; //uplink
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
			case 'anchor': //* Start Russ addition
				$condition = '^';
				$order_by = 'something';
				$label=''; //No label needed
				break;//* End Russ addition
			case 'start':
				$condition = '-';
				$order_by = 'something';
				$label = '';
				break;
			case 'up': //* Start uplink
				$condition = '|';
				$order_by = '-';
				$label='';
				break; //* End uplink
		}
		if ($condition && $order_by)
		{
			global $gCms;
			$hm =& $gCms->GetHierarchyManager();
			$flatcontent = array();
			if ($condition != '|') // uplink (we don't need the flatcontent for an uplink)
			{
				#return '';
				$flatcontent =& $hm->getFlatList();
				$number = 0;
				for ($i = 0; $i < count($flatcontent); $i++)
				{
					if ($condition == '-')
					{
						global $gCms;
						$contentops =& $gCms->GetContentOperations();
						$defaultid = $contentops->GetDefaultPageID();
						if ($flatcontent[$i]->getTag() == $defaultid)
						{
							$number = $i;
							break;
						}
					}
					else if ($flatcontent[$i]->getTag() == $gCms->variables['content_id'])
					{
						$number = $i;
						break;
					}
				}
			} //* uplink addition
			if ($condition == '<')
			{
				if ($number > 0)
				{
					for ($i = $number - 1; $i >= 0; $i--)
					{
						$content =& $flatcontent[$i]->getContent();
						if (isset($content) && $content != NULL)
						{
							if ($content->Active() && $content->ShowInMenu() && $content->HasUsableLink())
							{
								$pageid = $content->Id();
								$alias = $content->Alias();
								$name = $content->Name();
								$menu_text = $content->MenuText();
								$url = $content->GetURL();
								$titleattr = $content->TitleAttribute();
								break;
							}
						}
						else
						{
							break;
						}
					}
				}
			}
			else if ($condition == '>')
			{
				if ($number < count($flatcontent))
				{
					for ($i = $number + 1; $i < count($flatcontent); $i++)
					{
						$content =& $flatcontent[$i]->getContent();
						if(isset($content) && $content != NULL)
						{
							if ($content->Active() && $content->ShowInMenu() && $content->HasUsableLink())
							{
								$pageid = $content->Id();
								$alias = $content->Alias();
								$name = $content->Name();
								$menu_text = $content->MenuText();
								$url = $content->GetURL();
								$titleattr = $content->TitleAttribute();
								break;
							}
						}
						else
						{
							break;
						}
					}
				}
			}
			else if ($condition == '^') //* Start Russ addition
			{
				if ($number < count($flatcontent))
				{
					for ($i = $number; $i < count($flatcontent); $i++)
					{
						$content =& $flatcontent[$i]->getContent();
						if (isset($content))
						{
							if ($content->Active() && $content->ShowInMenu() && $content->HasUsableLink())
							{
								$pageid = $content->Id();
								$alias = $content->Alias();
								$name = $content->Name();
								$menu_text = $content->MenuText();
								$url = $content->GetURL().'#'.$anchorlink; //set as Param
								$titleattr = $content->TitleAttribute();
								break;
							}
						}
						else
						{
							break;
						}
					}
				}
			} //* End Russ addition
			else if ($condition == '|') //* Start uplink
			{
				$node =& $hm->getNodeById($gCms->variables['content_id']);
				$node =& $node->getParentNode();
				//				print_r($node);
				if (!isset($node)) return;
				$content =& $node->GetContent();
				if ($content != FALSE)
				{
					if ($content->Active() && $content->HasUsableLink())
					{
						$pageid = $content->Id();
						$alias = $content->Alias();
						$name = $content->Name();
						$menu_text = $content->MenuText();
						$url = $content->GetURL();
						$titleattr = $content->TitleAttribute();
					}
				}
			} //* End uplink
			else if ($condition == '-')
			{
				$content =& $flatcontent[$number]->getContent();
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

	if (isset($params['label']))
	{
		$label = $params['label'];
	}
	else
	{
	  if (!isset($label))
	    {
	      $label = '';
	    }
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
			else if ($params['dir'] == 'anchor')//* Start Russ addition
			{
				$result .= 'anchor';
			}//* End Russ addition
			else if ($params['dir'] == 'next')
			{
				$result .= 'next';
			}
			else if ($params['dir'] == 'up')//* Start uplink
			{
				$result .= 'up';
			}//* End uplink
			$result .= '" title="' . (isset($params['title']) ? $params['title'] : ($titleattr != '' ? $titleattr : $name));
			$result .= '" href="' . $url . '" />';
		}
		else
		{
			if (! isset($params['label_side']) || $params['label_side'] == 'left')
				{
				$result .= $label;
				}
			$result .= '<a href="'.$url.'"';

			$result .= ' title="'.(isset($params['title']) ? $params['title'] : ($titleattr != '' ? $titleattr : $name)).'"';

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
			//Start Russ for tabindex param
			if (isset($params['tabindex']))
			{
				$result .= ' tabindex="'.$params['tabindex'].'"';
			}
			//End Russ
			if (isset($params['more']))
			{
				$result .= ' '.$params['more'];
			}

			$result .= '>';

			//Marcus Bointon - add ability to use images for links
			if (isset($params['text'])){
				$linktext = $params['text'];
			} elseif (isset($params['menu']) && $params['menu'] == "1")	   { // mbv
				$linktext = $menu_text;
			} else {
				$linktext = $name; // mbv - 21-06-2005
			}
			if (isset($params['image']) && ! empty($params['image'])) {
				$alt = (isset($params['alt']) && ! empty($params['alt'])) ? $params['alt'] : '';
				$result .= "<img src=\"{$params['image']}\" alt=\"$alt\" />";
				if (! (isset($params['imageonly']) && $params['imageonly'])) {
					$result .= " $linktext";
				}
			} else {
				$result .= $linktext;
			}

			$result .= '</a>';
			if (isset($params['label_side']) && $params['label_side'] == 'right')
				{
				$result .= $label;
				}


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
		<p>Creates a link to another CMSMS content page inside your template or content. Can also be used for external links with the ext parameter.</p>
		<h3>How do I use it?</h3>
		<p>Just insert the tag into your template/page like: <code>{cms_selflink page=&quot;1&quot;}</code> or  <code>{cms_selflink page=&quot;alias&quot;}</code></p>
		<h3>What parameters does it take?</h3>
		<p>
		<ul>
		<li><em>(optional)</em> <tt>page</tt> - Page ID or alias to link to.</li>
		<li><em>(optional)</em> <tt>dir anchor (internal links)</tt> - New option for an internal page link. If this is used then <tt>anchorlink</tt> should be set to your link. </li> <!-- Russ - 25-04-2006 -->
		<li><em>(optional)</em> <tt>anchorlink</tt> - New paramater for an internal page link. If this is used then <tt>dir =&quot;anchor&quot;</tt> should also be set. No need to add the #, because it is added automatically.</li> <!-- Russ - 25-04-2006 -->
		<li><em>(optional)</em> <tt>urlparam</tt> - Specify additional parameters to the URL.  <strong>Do not use this in conjunction with the <em>anchorlink</em> parameter</em></strong>
		<li><em>(optional)</em> <tt>tabindex =&quot;a value&quot;</tt> - Set a tabindex for the link.</li> <!-- Russ - 22-06-2005 -->
		<li><em>(optional)</em> <tt>dir start/next/prev/up (previous)</tt> - Links to the default start page or the next or previous page, or the parent page (up). If this is used <tt>page</tt> should not be set.</li> <!-- mbv - 21-06-2005 -->
		<B>Note!</B> Only one of the above may be used in the same cms_selflink statement!!
		<li><em>(optional)</em> <tt>text</tt> - Text to show for the link.  If not given, the Page Name is used instead.</li>
		<li><em>(optional)</em> <tt>menu 1/0</tt> - If 1 the Menu Text is used for the link text instead of the Page Name</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>target</tt> - Optional target for the a link to point to.  Useful for frame and javascript situations.</li>
		<li><em>(optional)</em> <tt>class</tt> - Class for the &lt;a&gt; link. Useful for styling the link.</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>lang</tt> - Display link-labels  (&quot;Next Page&quot;/&quot;Previous Page&quot;) in different languages (0 for no label.) Danish (dk), English (en) or French (fr), for now.</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>id</tt> - Optional css_id for the &lt;a&gt; link.</li> <!-- mbv - 29-06-2005 -->
		<li><em>(optional)</em> <tt>more</tt> - place additional options inside the &lt;a&gt; link.</li> <!-- mbv - 29-06-2005 -->
		<li><em>(optional)</em> <tt>label</tt> - Label to use in with the link if applicable.</li>
		<li><em>(optional)</em> <tt>label_side left/right</tt> - Side of link to place the label (defaults to "left").</li>
		<li><em>(optional)</em> <tt>title</tt> - Text to use in the title attribute.  If none is given, then the title of the page will be used for the title.</li>
		<li><em>(optional)</em> <tt>rellink 1/0</tt> - Make a relational link for accessible navigation.  Only works if the dir parameter is set and should only go in the head section of a template.</li>
		<li><em>(optional)</em> <tt>href</tt> - If href is used only the href value is generated (no other parameters possible). <strong>Example:</strong> &lt;a href=&quot;{cms_selflink href=&quot;alias&quot;}&quot;&gt;&lt;img src=&quot;&quot;&gt;&lt;/a&gt;</li>
		<li><em>(optional)</em> <tt>image</tt> - A url of an image to use in the link. <strong>Example:</strong> {cms_selflink dir=&quot;next&quot; image=&quot;next.png&quot; text=&quot;Next&quot;}</li>
		<li><em>(optional)</em> <tt>alt</tt> - Alternative text to be used with image (alt="" will be used if no alt parameter is given).</li>
		<li><em>(optional)</em> <tt>imageonly</tt> - If using an image, whether to suppress display of text links. If you want no text in the link at all, also set lang=0 to suppress the label. <B>Example:</B> {cms_selflink dir=&quot;next&quot; image=&quot;next.png&quot; text=&quot;Next&quot; imageonly=1}</li>
		<li><em>(optional)</em> <tt>ext</tt> - For external links, will add class=&quot;external and info text. <strong>warning:</strong> only text, target and title parameters are compatible with this parameter</li>
		<li><em>(optional)</em> <tt>ext_info</tt> - Used together with &quot;ext&quot; defaults to (external link)</li>
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
		<p>Modified: Russ Baldwin</p>
		<p>Version: 1.42</p>
		<p>Modified: Marcus Bointon &lt;coolbru@users.sf.net&gt;</p>
		<p>Version: 1.43</p>
		<p>Modified: Tatu Wikman &lt;tsw@backspace.fi&gt;</p>
		<p>Version: 1.44</p>
		<p>Modified: Hans Mogren &lt;http://hans.bymarken.net/&gt;</p>
		<p>Version: 1.45</p>

		<p>
		Change History:<br/>
		1.45 - Added a new option for &quot;dir&quot;, &quot;up&quot;, for links to the parent page e.g. dir=&quot;up&quot; (Hans Mogren).<br />
		1.44 - Added new parameters &quot;ext&quot; and &quot;ext_info&quot; to allow external links with class=&quot;external&quot; and info text after the link, ugly hack but works thinking about rewriting this(Tatu Wikman)<br />
		1.43 - Added new parameters &quot;image&quot; and &quot;imageonly&quot; to allow attachment of images to be used for page links, either instead of or in addition to text links. (Marcus Bointon)<br />
		1.42 - Added new parameter &quot;anchorlink&quot; and a new option for &quot;dir&quot; namely, &quot;anchor&quot;, for internal page links. e.g. dir=&quot;anchor&quot; anchorlink=&quot;internal_link&quot;. (Russ)<br />
		1.41 - added new parameter &quot;href&quot; (LeisureLarry)<br />
		1.4 - fixed bug next/prev linking to non-content pages. (Thanks Teemu Koistinen for this fix)<br />
		1.3 - added option &quot;more&quot;<br />
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