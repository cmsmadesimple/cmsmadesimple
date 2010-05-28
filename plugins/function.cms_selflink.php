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
      $external_text ='( '.$params['ext_info'].' )';
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
		/* LeisureLarry - Begin */
		if (isset($params['href']))
		{
			$page = $params['href'];
		}
		/* LeisureLarry - End */
		else
		{
			$page = $params['page'];
		}
		$name = $page;

		# check if the page exists in the db
		$manager =& $gCms->GetHierarchyManager();
		$node =& $manager->sureGetNodeByAlias($page);
		if (!isset($node)) return;
		$content =& $node->GetContent();
		if ($content !== FALSE && is_object($content))
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

		$lang = get_site_preference('frontendlang','en');
		if (isset($params['lang']))
		{
		  $lang = trim($params['lang']);
                  if( !$lang ) $lang = '0';
		}
		switch ($lang)
		  {
		  case 'dk':
		  case 'da':
		  case 'da_DK':
		    $Prev_label = "Forrige side: ";
		    $Next_label = "N&aelig;ste side: ";
		    break;
		  case 'nl':
		  case 'nl_NL':
		    $Prev_label = "Vorige pagina: ";
		    $Next_label = "Volgende pagina: ";
		    $Parent_label = "Bovenliggende pagina: "; // uplink
		    break;
		  case 'fr':
		  case 'fr_FR':
		    $Prev_label = "Page pr&eacute;c&eacute;dente&nbsp;: ";
		    $Next_label = "Page suivante&nbsp;: ";
		    $Parent_label = "Page ascendante&nbsp;: "; //uplink
		    break;
		  case 'no':
		  case 'nb_NO':
		    $Prev_label = "Forrige side : ";
		    $Next_label = "Neste side: ";
		    $Parent_label = "Side opp: "; //uplink
		    break;
		  case '0':
		    $Prev_label = "";
		    $Next_label = "";
		    break;
		  case 'en':
		  default:
		    $Prev_label = "Previous page: ";
		    $Next_label = "Next page: ";
		    $Parent_label = "Parent page: "; //uplink
		    break;
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
				$contentops =& $gCms->GetContentOperations();
				$defaultid = $contentops->GetDefaultPageID();
				$number = 0;
				for ($i = 0; $i < count($flatcontent); $i++)
				{
					if ($condition == '-')
					{
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
		$label = cms_htmlentities($label);
	}
	else
	{
	  if (!isset($label))
	    {
	      $label = '';
	    }
	}
	$result = "";

	$title = (isset($name)) ? $name : '';
	if( isset($params['title']) ) 
	  $title = $params['title'];
	else if( !empty($titleattr) )
	  $title = $titleattr;
	$title = cms_htmlentities($title);

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

			$result .= '" title="'.$title.'" ';
			$result .= 'href="'.$url.'" />';
		}
		else
		{
			if (! isset($params['label_side']) || $params['label_side'] == 'left')
				{
				$result .= $label;
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

			//$linktext = cms_htmlentities($linktext);

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

	if( isset($params['assign']) )
	  {
	    $smarty->assign($params['assign'],$result);
	    return;
	  }
	return $result;
}

function smarty_cms_help_function_cms_selflink() {
  echo lang('help_function_cms_selflink');
}

function smarty_cms_about_function_cms_selflink() {
  echo lang('about_function_cms_selflink');
}

# vim:ts=4 sw=4 noet
?>
