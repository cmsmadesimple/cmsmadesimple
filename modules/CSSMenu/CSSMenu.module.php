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
#
#$Id$

include("strripos.php");  // Required for strripos() in PHP 4 and below

class CSSMenu extends CMSModule
{
  function GetName()
  {
    return 'CSSMenu';
  }

  function GetVersion()
  {
    return '1.1.7';
  }

  function HasAdmin()
  {
    return true;
  }

  function GetHelp($lang = 'en_US')
  {
    return $this->Lang('help');
  }

  function GetAuthor()
  {
    return 'Ted Kulp';
  }

  function GetAuthorEmail()
  {
    return 'ted@cmsmadesimple.org';
  }

	function SetParameters()
	{
		$this->CreateParameter('horizontal', '1', $this->lang('helphorizontal'));
		$this->CreateParameter('number_of_levels', '', $this->lang('helpnumber_of_levels'));
		$this->CreateParameter('start_element', '', $this->lang('helpstart_element'));
		$this->CreateParameter('showadmin', '1', $this->lang('helpshowadmin'));
		$this->CreateParameter('relative', '0', $this->lang('helprelative'));
		$this->CreateParameter('relative_show_current', '1', $this->lang('helprelative'));
		$this->CreateParameter('relative_show_parent', '1', $this->lang('helprelative'));

	}

  function GetChangeLog()
  {
    return "
      1.1.7: Added parameter 'relative' for creating context sensitive menus<br />
      1.1.6: Fixed script tag to be xhtml strict<br />
      Added horizontal option.<br />
      1.1.5: Fixed section headings when used as a submenu.<br />
      1.1.4: Fixed admin link falling outside of the menu structure.<br />
      Made section headings bold.<br />
      1.1.1 Patch 2 now 1.1.3: Fixed bug where div is replaced by li.<br />
      1.1.1 Patch 1: Fixed missing arrow.<br />
      1.1.1: Added IE6 flicker fix.<br />
      1.1: Added CSS and JS menu functionality. (Contributed by Leith Bade)<br />
      Added english help.<br />
      1.0: Initial release
    ";
  }

  function IsPluginModule()
  {
    return true;
  }

  function ContentPostRender(&$content)
  {
    if (strpos($content, '<!-- Displaying CSSMenu Module -->') !== FALSE)
    {
      $config = $this->cms->config;
      $text =  '<script type="text/javascript" src="'.$config['root_url'].'/modules/CSSMenu/CSSMenu.js"></script>' . "\n";

      $content = str_replace('</head>', $text.'</head>', $content);
    }
  }

  function ContentStylesheet(&$stylesheet)
  {
    if ($this->cms)
    {
      @ob_start();
      @readfile(dirname(__FILE__).'/CSSMenuVertical.css');
      $stylesheet = @ob_get_contents() . $stylesheet;
      @ob_end_clean();

      @ob_start();
      @readfile(dirname(__FILE__).'/CSSMenuHorizontal.css');
      $stylesheet = @ob_get_contents() . $stylesheet;
      @ob_end_clean();
    }
  }

  function DoAction($name, $id, $params)
  {
    global $gCms;
    $config = $this->cms->config;

    if ($name == 'default')
    {
      # getting menu parameters
      $showadmin = isset($params['showadmin']) ? $params['showadmin'] : 0 ;
      $collapse = isset($params['collapse']) ? $params['collapse'] : 0 ;
      $horizontal = isset($params['horizontal']) ? $params['horizontal'] : 0 ;
      $relative = isset($params['relative']) ? $params['relative'] : 0 ;
      $relative_show_current = isset($params['relative_show_current']) ? $params['relative_show_current'] : 1 ;
      $relative_show_parrent = isset($params['relative_show_parent']) ? $params['relative_show_parent'] : 1 ;
      $show_root_siblings = isset($params['show_root_siblings']) ? $params['show_root_siblings'] : 0;

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
        $menu .= '<div id="listmenu">'."\n";


        #Reset the base depth if necessary...
        if (isset($params['start_element']))
        {
          $basedepth = count(split('\.', (string)$params['start_element'])) - 1;
        }

        # set starting position to current page if relative defined
		if ($relative) // this is a little hack
		{

			$params['start_element'] = $gCms->variables["pageinfo"]->content_hierarchy; //$gCms->variables['position'];
//          print "<pre>position=". $gCms->variables['position']. "</pre>";			
//			$basedepth = 	2; // TODO: de corectat
            $basedepth = count(split('\.', $params['start_element']));
			
		}

//		$last_level = -1;
		
        foreach ($allcontent as $onecontent)
        {
          #Handy little trick to figure out how deep in the tree we are
          #Remember, content comes to use in order of how it should be displayed in the tree already
          $depth = count(split('\.', $onecontent->Hierarchy()));

          #If hierarchy starts with the start_element (if it's set), then continue on
          if (isset($params['start_element']))
          {

			  if ((strpos($onecontent->mHierarchy, (string)$params['start_element']) === FALSE) ||
            	 (strpos($onecontent->mHierarchy, (string)$params['start_element']) != 0))
            {
            
              if ($show_root_siblings)
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
          else {      
			# check for children of inactive items
			$skip = 0;
			foreach( $disabled as $dis )
			{
				if( substr( $onecontent->Hierarchy(), 0, strlen($dis) ) == $dis )
				{
					$skip = 1;
					break;
				}
			}
			if( $skip )
			{
				continue;
			}
		  }

          # Set depth to be the relative position
          $depth = $depth - $basedepth;

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
            if ($depth > 1)
            {
             // Replace the last <li> with <li class="menuparent"> so that the parent li has the class 'menuparent'. This allows the stylesheet to work.
                $tmppos = strripos($menu, "<li>");
                if ($tmppos !== FALSE)
                {
                 $menu = substr_replace($menu, '<li class="menuparent">', $tmppos, 4);
                }
                /*else // look for <li class="sectionheader">
                {
                  $tmppos = strripos($menu, '<li class="sectionheader">');
                  if ($tmppos !== FALSE)
                  {
                   $menu = substr_replace($menu, '<li class="menuparent">', $tmppos, 4);
                  }
                }*/ // Doesn't work, need to find a work around
              }

              // Give the first ul id 'primary-nav' for the JS to work.
                                   if($horizontal == 1)
                                    $menu .= "\n<ul".($depth == 1?' id="primary-nav-horiz" class="cssmenu-horizontal"':'').">\n";
                                   else
                                    $menu .= "\n<ul".($depth == 1?' id="primary-nav-vert" class="cssmenu-vertical"':'').">\n";
                                    
                                    if (!((isset($gCms->variables['content_id']) && $onecontent->Id() == $gCms->variables['content_id'])
                                   ) && $depth==1 && $relative==1)
                                     $menu .= "<li><a href='#'>" . $gCms->variables["pageinfo"]->content_menutext . "</a></li>";
                              }
                          }


          if ($depth == $last_level)
          {
            $menu .= "</li>\n";
          }

          if ($onecontent->Type() == 'separator')
          {
            $menu .= '<li class="separator">';
          }
          else if ($onecontent->Type() == 'sectionheader')
          {
            //$menu .= '<li class="sectionheader"><a href="#">'.$onecontent->MenuText().'</a>';
            $menu .= '<li><a href="#">'.$onecontent->MenuText().'</a>'; // Need to find a work around line # 217
          }
          else
          { 
          	if (! ((isset($params['relative']) && $params['relative']==1) &&
          	       (isset($gCms->variables['content_id']) && $onecontent->Id() == $gCms->variables['content_id']) ))
          	       // we are not going to show current page if relative it's enabled - we'll show only his childs
          	{
            $menu .= "<li><a href=\"".$onecontent->GetURL()."\"";
            if (isset($gCms->variables['content_id']) && $onecontent->Id() == $gCms->variables['content_id'])
            {
              $menu .= " class=\"currentpage\"";
            }
			if ($onecontent->HasProperty('target') && $onecontent->GetPropertyValue('target') != '')
			{
				$menu .= ' target="'.$onecontent->GetPropertyValue('target').'"';
			}
            $menu .= ">".$onecontent->MenuText()."</a>";
          	}
          	
          }
          $last_level = $depth;
          $count++;
        }
      }

      if ($showadmin == 1)
      {
       for ($i = 0; $i < $last_level - 1; $i++) $menu .= "</li></ul>";
       $menu .= "</li><li><a href='".$config['admin_dir']."/'>Admin</a></li>\n";
       $menu .= "</ul></div>\n";
      }
      else
      {
       for ($i = 0; $i < $last_level; $i++) $menu .= "</li></ul>";
       $menu .= "</div>\n";
      }
      return "<!-- Displaying CSSMenu Module -->\n".$menu;
    }
	else if ($name == 'defaultadmin')
	{
		#The tabs
		echo $this->StartTabHeaders();

		echo $this->SetTabHeader('horizontal',$this->Lang('horizontal'));
		echo $this->SetTabHeader('vertical',$this->Lang('vertical'));

		echo $this->EndTabHeaders();

		#The content of the tabs
		echo $this->StartTabContent();

		echo $this->StartTab('horizontal');

		@ob_start();
		@readfile(dirname(__FILE__).'/CSSMenuHorizontal.css');
		$stylesheet = @ob_get_contents() . $stylesheet;
		@ob_end_clean();

		echo '<p>'.$this->CreateTextArea(false, $id, $stylesheet, 'horizontalcss', '').'</p>';

		echo $this->EndTab();

		echo $this->StartTab('vertical');

		@ob_start();
		@readfile(dirname(__FILE__).'/CSSMenuVertical.css');
		$stylesheet = @ob_get_contents() . $stylesheet;
		@ob_end_clean();

		echo '<p>'.$this->CreateTextArea(false, $id, $stylesheet, 'verticalcss', '').'</p>';

		echo $this->EndTab();

		echo $this->EndTabContent();

	}
  }
}

# vim:ts=4 sw=4 noet
?>
