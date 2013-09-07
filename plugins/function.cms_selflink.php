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
  $smarty = $template->smarty;
  $gCms = cmsms();

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
	
	$result='<a class="external" href="'.$url.'" '.$title.''.$target.'>'.$text.'<span>'.$external_text.'</span></a>';
	if( isset($params['assign']) ){
	    $smarty->assign(trim($params['assign']),$result);
	    return;
        }
        return $result;

  }

  if (isset($params['lang']))
    {
      // attempt to override the chosen frontend lang.
      $lang = trim($params['lang']);
      cms_set_frontend_language($lang);
    }

  $urlparam = '';
  if ( isset($params['urlparam']) && ( strlen($params['urlparam']) > 0 ) ) {
    $urlparam = trim($params['urlparam']);
  }

  $label = '';
  if (isset($params['page']) or isset($params['href']))
    {
      if (isset($params['href'])) {
	$page = $params['href'];
      }
      else {
	$page = $params['page'];
      }
      $name = $page;
     
      // check if the page exists in the db
      $manager = $gCms->GetHierarchyManager();
      $node = $manager->find_by_tag('alias',$page);
      if (!isset($node)) {
	$node = $manager->find_by_tag('id',$page);
	if( !$node ) {
	  if (isset($params['lang'])) cms_set_frontend_language();
	  return;
	}
      }
      $content = $node->GetContent();
      if ($content !== FALSE && is_object($content) && $content->Active() && $content->HasUsableLink() )
	{
	  $pageid = $content->Id();
	  $alias = $content->Alias();
	  $name = $content->Name(); //mbv - 21-06-2005
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
      global $CMS_ADMIN_PAGE;
      if( isset($CMS_ADMIN_PAGE) ) return;

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
	case 'anchor': // Start Russ addition
	  $condition = '^';
	  $label=''; //No label needed
	  break; // End Russ addition
	case 'start':
	  $condition = '-';
	  $label = ''; // no label needed.
	  break;
	case 'up': // Start uplink
	  $condition = '|';
	  $label='';
	  break; // End uplink
	}

      if ($condition )
	{
	  $gCms = cmsms();
	  $hm = $gCms->GetHierarchyManager();
	  $flatcontent = array();
	  if ($condition != '|') // uplink (we don't need the flatcontent for an uplink)
	    {
	      $flatcontent = array_values($hm->getFlatList());
	      $contentops = $gCms->GetContentOperations();
	      $defaultid = $contentops->GetDefaultPageID();
	      $number = 0;
	      for ($i = 0; $i < count($flatcontent); $i++)
		{
		  if( !is_object($flatcontent[$i]) ) break;
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
		  else if ($flatcontent[$i]->getTag() == $gCms->variables['content_id'])
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
	      // anchor link... why they used stupid characters I'll never know.
	      // cg: this code is not needed... get current objects url, add anchor stuff.
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
	      $node = $hm->getNodeById($gCms->variables['content_id']);
	      if( !isset($node) ) {
                return;
              }
	      $node = $node->getParentNode();
	      if (!isset($node)) {
	        if (isset($params['lang'])) cms_set_frontend_language();
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
	    } //* End uplink
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

  // Now we build the output.
  $result = "";
  if (isset($params['label']))
    {
      $label = $params['label'];
      $label = cms_htmlentities($label);
    }

  $title = (isset($name)) ? $name : '';
  if( isset($params['title']) ) 
    $title = $params['title'];
  else if( !empty($titleattr) )
    $title = $titleattr;
  $title = cms_htmlentities($title);

  if( empty($url) )
    {
      // no url to link to, therefore nothing to do.
      if (isset($params['lang'])) cms_set_frontend_language();
      return;
    }

  if( isset($params['href']) )
    {
      if (isset($params['lang'])) cms_set_frontend_language();
		if( isset($params['assign']) ){
			$smarty->assign(trim($params['assign']),$url);
			return;
		}
      return $url;
    }

  if ($rellink && isset($params['dir']))
    {
      // output a relative link.
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
      
      if (isset($params['text'])){
	$linktext = $params['text'];
      } elseif (isset($params['menu']) && $params['menu'] == "1")	   { 
	$linktext = $menu_text;
      } else {
	$linktext = $name; 
      }
      
      //$linktext = cms_htmlentities($linktext);

      if (isset($params['image']) && ! empty($params['image'])) {
	$width = (isset($params['width']) && !empty($params['width'])) ? (int)$params['width'] : '';
	$height = (isset($params['height']) && !empty($params['height'])) ? (int)$params['height'] : '';
	$alt = (isset($params['alt']) && ! empty($params['alt'])) ? $params['alt'] : '';
	$result .= "<img src=\"{$params['image']}\" alt=\"$alt\"";
	if( $width ) $width = max(1,$width);
	if( $width ) $result .= " width=\"$width\"";
	if( $height ) $height = max(1,$height);
	if( $height ) $result .= " height=\"$height\"";
	$result .= "/>";
	if (! (isset($params['imageonly']) && $params['imageonly'])) {
	  $result .= " $linktext";
	}
      } else {
	$result .= $linktext;
      }
      
      $result .= '</a>';
      if (isset($params['label_side']) && $params['label_side'] == 'right')
	{
	  $result .= ' '.$label;
	}
    }

  if (isset($params['lang'])) cms_set_frontend_language();
  if( isset($params['assign']) ){
    $smarty->assign(trim($params['assign']),$result);
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
