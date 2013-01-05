<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

function smarty_function_cms_selflink($params, &$template) 
{
	global $CMS_ADMIN_PAGE;
	if( isset($CMS_ADMIN_PAGE) ) return;

	$smarty = $template->smarty;

	$rellink = (isset($params['rellink']) && $params['rellink'] == '1' ? true : false);
	$url = '';

	if ( isset($params['ext']) )
	{
		$url = $params['ext'];
		$text = $params['ext'];
		
		if ( isset($params['text'] ))
		{
			$text = $params['text'];
		}
		
		$title= '';
		if ( isset($params['title']) )
		{
			$title=' title="'.$params['title'].'" ';
		}
		
		$target = '';
		if ( isset($params['target']) && ( strlen($params['target']) > 0 ) )
		{
			$target=' target="'.$params['target'].'" ';
		}
		
		$external_text = '(external link)';
		if ( isset($params['ext_info']) )
		{
			$external_text ='( '.$params['ext_info'].' )';
		}
		
		$result='<a class="external" href="'.$url.'" '.$title.''.$target.'>'.$text.'<span>'.$external_text.'</span></a>';
		if( isset($params['assign']) )
		{
			$smarty->assign(trim($params['assign']),$result);
			return;
		}
		return $result;
	}

	$urlparam = '';
	if ( isset($params['urlparam']) && ( strlen($params['urlparam']) > 0 ) )
	{
		$urlparam = trim($params['urlparam']);
	}

	$label = '';
	if (isset($params['page']) or isset($params['href']))
    {
		if (isset($params['href']))
		{
			$page = $params['href'];
		}
		else
		{
			$page = $params['page'];
		}
		$name = $page;
     
		// check if the page exists in the db
		$manager = cmsms()->GetHierarchyManager();
		$node = $manager->sureGetNodeByAlias($page);
		if (!isset($node)) 
		{
			return;
		}
		$content = $node->GetContent();
		if ($content !== FALSE && is_object($content) && $content->Active() && $content->HasUsableLink() )
		{
			$pageid = $content->Id();
			$alias = $content->Alias();
			$name = $content->Name();
			$url = $content->GetUrl();
			$menu_text = $content->MenuText();
			$titleattr = $content->TitleAttribute();
	 
			if (isset($params['anchorlink']))
			{
				$url .= '#' . ltrim($params['anchorlink'], '#');
			}
	 
			if( $urlparam != '' ) $url .= $urlparam;
		}
    }
	elseif (isset($params['dir'])) 
    {
		if (isset($params['anchorlink']))
		{
			$anchorlink = ltrim($params['anchorlink'], '#');
		}

		$condition = false;
		switch (strtolower($params['dir']))
		{
			case 'next':
				$condition = '>';
				$label=lang_by_realm('cms_selflink','next_label');
				break;
			case 'prev':
			case 'previous':
				$condition = '<';
				$label=lang_by_realm('cms_selflink','prev_label');
				break;
			case 'anchor':
				$condition = '^';
				$label='';
				break;
			case 'start':
				$condition = '-';
				$label = '';
				break;
			case 'up':
				$condition = '|';
				$label='';
				break;
		}

		if ($condition )
		{
			$hm = cmsms()->GetHierarchyManager();
			$flatcontent = array();
			if ($condition != '|') // uplink (we don't need the flatcontent for an uplink)
			{
				$flatcontent = $hm->getFlatList();
				$contentops = cmsms()->GetContentOperations();
				$defaultid = $contentops->GetDefaultPageID();
				$number = 0;
				for ($i = 0; $i < count($flatcontent); $i++)
				{
					if ($condition == '-')
					{
						// start link...
						// redundant...
						if ($flatcontent[$i]->getTag() == $defaultid)
						{
							$number = $i;
							break;
						}
					}
					else if ($flatcontent[$i]->getTag() == cmsms()->variables['content_id'])
					{
						$number = $i;
						break;
					}
				}
			} // uplink addition

			if ($condition == '<')
			{
				if ($number > 0)
				{
					for ($i = $number - 1; $i >= 0; $i--)
					{
						$content = $flatcontent[$i]->getContent();
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
					}
				}
			}
			else if ($condition == '>')
			{
				if ($number < count($flatcontent))
				{
					for ($i = $number + 1; $i < count($flatcontent); $i++)
					{
						$content = $flatcontent[$i]->getContent();
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
					}
				}
			}
			else if ($condition == '^') 
			{
				// anchor link... why they used stupid characters I'll never know
				// cg: this code is not needed... get current objects url, add anchor stuff
				if ($number < count($flatcontent))
				{
					for ($i = $number; $i < count($flatcontent); $i++)
					{
						$content = $flatcontent[$i]->getContent();
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
			} 
			else if ($condition == '|') 
			{
				// Uplink
				$node = $hm->getNodeById(cmsms()->variables['content_id']);
				if( !isset($node) ) 
				{
					return;
				}
				$node = $node->getParentNode();
				if (!isset($node)) {
					return;
				}
				$content = $node->GetContent();
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
			} // End uplink
			else if ($condition == '-')
			{
				$content = $flatcontent[$number]->getContent();
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
    } // end of next-prev code

	// Now we build the output
	$result = "";
	if (isset($params['label']))
    {
		$label = $params['label'];
		$label = cms_htmlentities($label);
    }

	$title = (isset($name)) ? $name : '';
	if( isset($params['title']) )
	{
		$title = $params['title'];
	}
	else if( !empty($titleattr) )
	{
		$title = $titleattr;
		$title = cms_htmlentities($title);
	}

	if( empty($url) )
    {
		// no url to link to, therefore nothing to do
		return;
    }

	if( isset($params['href']) )
    {
		if( isset($params['assign']) )
		{
		$smarty->assign(trim($params['assign']),$url);
		return;
		}
		return $url;
    }

	if ($rellink && isset($params['dir']))
    {
		// output a relative link
		$result .= '<link rel="';
		switch($params['dir'])
		{
			case 'prev':
			case 'previous':
				$result .= 'prev';
				break;
			case 'start':
			case 'anchor':
			case 'next':
			case 'up':
				$result .= $params['dir'];
				break;
		}
      
		$result .= '" title="'.$title.'" ';
		$result .= 'href="'.$url.'" />';
    }
	else
    {
		if (! isset($params['label_side']) || $params['label_side'] == 'left' && $label != '')
		{
			$result .= $label.' ';
		}
		$result .= '<a href="'.$url.'"';      
		$result .= ' title="'.$title.'" ';
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
      
		if (isset($params['tabindex']))
		{
			$result .= ' tabindex="'.$params['tabindex'].'"';
		}
      
		if (isset($params['more']))
		{
			$result .= ' '.$params['more'];
		}
      
		$result .= '>';
      
		if (isset($params['text']))
		{
			$linktext = $params['text'];
		} 
		elseif (isset($params['menu']) && $params['menu'] == "1")
		{ 
			$linktext = $menu_text;
		} 
		else
		{
			$linktext = $name; 
		}

		if (isset($params['image']) && ! empty($params['image'])) 
		{
			$width = (isset($params['width']) && !empty($params['width'])) ? (int)$params['width'] : '';
			$height = (isset($params['height']) && !empty($params['height'])) ? (int)$params['height'] : '';
			$alt = (isset($params['alt']) && ! empty($params['alt'])) ? $params['alt'] : '';
			$result .= "<img src=\"{$params['image']}\" alt=\"$alt\"";
			if( $width ) $width = max(1,$width);
			if( $width ) $result .= " width=\"$width\"";
			if( $height ) $height = max(1,$height);
			if( $height ) $result .= " height=\"$height\"";
			$result .= "/>";
			if (! (isset($params['imageonly']) && $params['imageonly']))
			{
				$result .= " $linktext";
			}
		} 
		else
		{
			$result .= $linktext;
		}
      
		$result .= '</a>';
		if (isset($params['label_side']) && $params['label_side'] == 'right')
		{
			$result .= ' '.$label;
		}
    }

	if( isset($params['assign']) )
	{
		$smarty->assign(trim($params['assign']),$result);
		return;
	}
	return $result;
}

function smarty_cms_help_function_cms_selflink() {
	echo lang('help_function_cms_selflink');
}

function smarty_cms_about_function_cms_selflink() {
?>
		<p>Author: Ted Kulp &lt;tedkulp@users.sf.net&gt;</p>

		<p>Change History:</p>
		<ul>
		<li>Adds width and height parameters.</li>
		<li>Fixes a problem with too many queries when using the dir=start option.</li>
		<li>Added a new option for &quot;dir&quot;, &quot;up&quot;, for links to the parent page e.g. dir=&quot;up&quot;</li>
		<li>Added new parameters &quot;ext&quot; and &quot;ext_info&quot; to allow external links with class=&quot;external&quot; and info text after the link, ugly hack but works thinking about rewriting this</li>
		<li>Added new parameters &quot;image&quot; and &quot;imageonly&quot; to allow attachment of images to be used for page links, either instead of or in addition to text links.</li>
		<li>Added new parameter &quot;anchorlink&quot; and a new option for &quot;dir&quot; namely, &quot;anchor&quot;, for internal page links. e.g. dir=&quot;anchor&quot; anchorlink=&quot;internal_link&quot;.</li>
		<li>Added new parameter &quot;href&quot;</li>
		<li>Fixed bug next/prev linking to non-content pages. (Thanks Teemu Koistinen for this fix)</li>
		<li>Added option &quot;more&quot;</li>
		<li>
			<ul>
				<li>changed default text to Page Name (was Page Alias)</li>
				<li>added option dir=next/prev to display next or previous item in the hierachy</li>
				<li>added option class to add a class= statement to the a-tag.</li>
				<li>added option menu to display menu-text in sted of Page Name</li>
				<li>added option lang to display link-labels in different languages</li>
			</ul></li>
		<li>Changed to new content system</li>
		<li>Initial release</li>
		</ul>
<?php
}
?>