<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * @package CMS 
 */

/**
 * Class for Admin Theme
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class AdminTheme extends CmsAdminThemeBase
{
	/**
	 * Title
	 */
	protected $title;

    /**
     * Subtitle, for use in breadcrumb trails
     */
    protected $subtitle;

	/**
	 * Url
	 */
	protected $url;
	
    /**
	 * Script
	 */
	protected $script;

	/**
	 * Query String, for use in breadcrumb trails
	 */
	protected $query;

    /**
     * Aggregate Permissions
     */
    protected $perms;

    /**
     * Admin Section Image cache
     */
    protected $imageLink;


	/**
	 * Generic constructor.  Runs the SetInitialValues fuction.
	 */
	public function __construct($cms, $userid, $themeName)
	{
		parent::__construct();
		$this->_SetInitialValues($cms, $userid, $themeName);
	}


	public function __get($key)
	{
		if( $key == 'menuItems' )
		{
			return $this->get_admin_navigation();
		}
		return parent::__get($key);
	}

	/**
	 * Sets object to some sane initial values
	 */
	private function _SetInitialValues($cms, $userid, $themeName)
	{
		$this->themeName = $themeName;

		$this->title = '';
		$this->subtitle = '';
	}

    /**
     * DoBookmarks
     * Setup method for displaying admin bookmarks.
     */
    function DoBookmarks()
    {
		$gCms = cmsms();
      $bookops = $gCms->GetBookmarkOperations();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
      $marks = array_reverse($bookops->LoadBookmarks($this->userid));
      $tmpMark = new Bookmark();
      $tmpMark->title = lang('addbookmark');
      $tmpMark->url = 'makebookmark.php'.$urlext.'&amp;title='. urlencode($this->title);
      $marks[] = $tmpMark;
      $marks = array_reverse($marks);
      $tmpMark = new Bookmark();
      $tmpMark->title = lang('managebookmarks');
      $tmpMark->url = 'listbookmarks.php'.$urlext;
      $marks[] = $tmpMark;
      $this->DisplayBookmarks($marks);
    }


    /**
     * DoBookmarks
     * Method for displaying admin bookmarks (shortcuts) & help links.
     */
    function ShowShortcuts()
    {
      if (get_preference($this->userid, 'bookmarks')) {
	$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	echo "<div class=\"sections\">\n";
	echo '<div class="itemmenucontainer shortcuts" style="float:left;">';
	echo '<div class="itemoverflow">';
	echo '<h2>'.lang('bookmarks').'</h2>';
	echo '<p><a href="listbookmarks.php'.$urlext.'">'.lang('managebookmarks').'</a></p>';
	$gCms = cmsms();
	$bookops = $gCms->GetBookmarkOperations();
	$marks = array_reverse($bookops->LoadBookmarks($this->userid));
	$marks = array_reverse($marks);
	if (FALSE == empty($marks))
	  {
	    echo '<h3 style="margin:0">'.lang('user_created').'</h3>';
	    echo '<ul style="margin:0">';
	    foreach($marks as $mark)
	      {
		echo "<li><a href=\"". $mark->url."\">".$mark->title."</a></li>\n";
	      }
	    echo "</ul>\n";
	  }
	echo '<h3 style="margin:0;">'.lang('help').'</h3>';
	echo '<ul style="margin:0;">';
	echo '<li><a rel="external" href="http://forum.cmsmadesimple.org/">'.lang('forums').'</a></li>';
	echo '<li><a rel="external" href="http://wiki.cmsmadesimple.org/">'.lang('wiki').'</a></li>';
	echo '<li><a rel="external" href="http://cmsmadesimple.org/main/support/IRC">'.lang('irc').'</a></li>';
	echo '<li><a rel="external" href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel/Extensions/Modules">'.lang('module_help').'</a></li>';
	echo '</ul>';
	echo '</div>';
	echo '</div>';
      }
    }
    

    /**
     * DisplayBookmarks
     * Output bookmark data. Over-ride this to alter display of Bookmark information.
     * Bookmark objects contain two useful fields: title and url
     *
     *
     * @param marks - this is an array of Bookmark Objects
     */
    function DisplayBookmarks($marks)
    {
        //echo "<div id=\"BookmarkCallout\">";
        echo '<div class="tab-content"><h2 class="tab">'.lang('bookmarks').'</h2>';
        echo "<p class=\"DashboardCalloutTitle\">";
        echo lang('bookmarks');
        echo "</p>\n";

        echo "<ul>";
        foreach($marks as $mark)
            {
            echo "<li><a href=\"". $mark->url."\">".$mark->title."</a></li>\n";
            }
        echo "</ul>\n";
        echo "</div>\n";
    }




    /**
     * StartRighthandColumn
     * Override this for different behavior or special functionality
     * for the righthand column. Usual use would be a div open tag.
     */
    function StartRighthandColumn()
    {
    	echo '<div class="rightcol">';
    	echo "\n";
    	echo '<div id="admin-tab-container">';
    }

    /**
     * EndRighthandColumn
     * Override this for different behavior or special functionality
     * for the righthand column. Usual use would be a div close tag.
     */
    function EndRighthandColumn()
    {
    	echo "</div>\n</div>\n";
    }



    /**
     * OutputHeaderJavascript
     * This method can be used to dump out any javascript you'd like into the
     * Admin page header. In fact, it can be used to put just about anything into
     * the page header. It's recommended that you leave or copy the javascript
     * below into your own method if you override this -- it's used by the dropdown
     * menu in IE.
     */
    function OutputHeaderJavascript()
    {
		$config = cmsms()->GetConfig();
		$ssl = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off');
	    $ssl = $ssl && isset($config['ssl_url']);
		return cms_get_jquery('',$ssl);
	}

    /**
     * OutputFooterJavascript
     * This method can be used to dump out any javascript you'd like into the
     * Admin page footer.
     * It's recommended that you leave or copy the javascript below into your
     * own method if you override this -- it's used by bookmarks/recent pages tabs.
     */
    function OutputFooterJavascript()
    {
        echo "<script type=\"text/javascript\">BuildTabs('admin-tab-container','admin-tab-header','admin-tab-list');ActivateTab(0,'admin-tab-container','admin-tab-list');</script>";
    }


    
    /**
     *  BackUrl
     *  "Back" Url - link to the next-to-last item in the breadcrumbs
     *  for the back button.
     */
    public function BackUrl()
     {
     	$count = count($this->breadcrumbs) - 2;
	$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
     	if ($count > -1)
	  {
     	    $txt = $this->breadcrumbs[$count]['url'];
	    return $txt;
	  }
        else
	  {
	    // rely on base href to redirect back to the
	    // admin home page
     	    return 'index.php'.$urlext;
	  }
     }

    /**
     * DisplaySectionPages
     * Shows admin section pages in the specified section, wrapped in a
     * MainMenuItem div. This is used in the top-level section pages.
     *
     * You can override this if you want to change the
     * way it is shown.
     *
     * @param section - section to display
     */
    public function DisplaySectionPages($section)
    {
      if (count($this->menuItems) < 1)
	{
	  // menu should be initialized before this gets called.
	  // TODO: try to do initialization.
	  // Problem: current page selection, url, etc?
	  return -1;
	}

      foreach ($this->menuItems[$section]['children'] as $thisChild)
	{
	  $thisItem = $this->menuItems[$thisChild];
	  if (! $thisItem['show_in_menu'] || strlen($thisItem['url']) < 1)
	    {
	      continue;
	    }

	  echo "<div class=\"MainMenuItem\">\n";
	  echo "<a href=\"".$thisItem['url']."\"";
	  if (array_key_exists('target', $thisItem))
	    {
	      echo " target=" . $thisItem['target'];
	    }
	  if ($thisItem['selected'])
	    {
	      echo " class=\"selected\"";
	    }
	  echo ">".$thisItem['title']."</a>\n";
	  if (isset($thisItem['description']) && strlen($thisItem['description']) > 0)
	    {
	      echo "<span class=\"description\">";
	      echo $thisItem['description'];
	      echo "</span>\n";
	    }
	  echo "</div>\n";
        }
    }

    /**
     * TopParent
     * This method returns the menu node that is the top-level parent of the node you pass
     * to it.
     *
     * @param section - section (menu tag) to find top-level parent
     */
     function TopParent($section)
     {
     	$next = $section;
		$node = $this->menuItems[$next];
        while ($node['parent'] != -1)
        	{
        	$next = $node['parent'];
        	$node = $this->menuItems[$next];
        	}
        return $next;
     }


    /**
     * ListSectionPages
     * This method presents a nice, human-readable list of admin pages and 
     * modules that are in the specified admin section.
     *
     *
     * @param section - section to display
     */
    function ListSectionPages($section)
    {
        if (! isset($this->menuItems[$section]['children']) || count($this->menuItems[$section]['children']) < 1)
            {
            return;
            }

        if ($this->HasDisplayableChildren($section))
            {
            echo " ".lang('subitems').": ";
            $count = 0;
            foreach($this->menuItems[$section]['children'] as $thisChild)
                {
                $thisItem = $this->menuItems[$thisChild];
                if (! $thisItem['show_in_menu']  || strlen($thisItem['url']) < 1)
                    {
                    continue;
                    }
                if ($count++ > 0)
                    {
                    echo ", ";
                    }
                echo "<a href=\"".$thisItem['url'];
                echo "\">".$thisItem['title']."</a>";
                }
            }
    }



    /**
     * DisplayAllSectionPages
     *
     * Shows all admin section pages and modules. This is used to display the
     * admin "main" page.
     *
     */
    public function DisplayAllSectionPages()
    {
    	if (count($this->menuItems) < 1)
            {
            // menu should be initialized before this gets called.
            // TODO: try to do initialization.
            // Problem: current page selection, url, etc?
            return -1;
            }
        foreach ($this->menuItems as $thisSection=>$menuItem)
            {
            if ($menuItem['parent'] != -1)
            	{
            	continue;
            	}
            if (! $menuItem['show_in_menu'])
                {
                continue;
                }
            echo "<div class=\"MainMenuItem\">\n";
            echo "<a href=\"".$menuItem['url']."\"";
			if (array_key_exists('target', $menuItem))
				{
				echo " target=" . $menuItem['target'];
				}
			if ($menuItem['selected'])
				{
				echo " class=\"selected\"";
				}
            echo ">".$menuItem['title']."</a>\n";
            echo "<span class=\"description\">";
            if (isset($menuItem['description']) && strlen($menuItem['description']) > 0)
                {
                echo $menuItem['description'];
                }
            $this->ListSectionPages($thisSection);
            echo "</span>\n";
            echo "</div>\n";
            }
    }



	function renderMenuSection($section, $depth, $maxdepth)
	{
		$menuitems = $this->get_admin_navigation();
		if ($maxdepth > 0 && $depth> $maxdepth)
			{
			return;
			}
		if (! $menuitems[$section]['show_in_menu'])
			{
			return;
			}
		if (strlen($menuitems[$section]['url']) < 1)
		    {
            echo "<li>".$menuitems[$section]['title']."</li>";
            return;
            }
		echo "<li><a href=\"";
		echo $menuitems[$section]['url'];
		echo "\"";
		if (array_key_exists('target', $menuitems[$section]))
			{
			echo " target=" . $menuitems[$section]['target'];
			}
		if ($menuitems[$section]['selected'])
			{
			echo " class=\"selected\"";
			}
		echo ">";
		echo $menuitems[$section]['title'];
		echo "</a>";
		if ($this->HasDisplayableChildren($section))
			{
			echo "<ul>";
			foreach ($menuitems[$section]['children'] as $child)
				{
				$this->renderMenuSection($child, $depth+1, $maxdepth);
				}
			echo "</ul>";
			}
		echo "</li>";
		return;
	}


    /**
     * DisplayTopMenu
     * Output Top Menu data. Over-ride this to alter display of the top menu.
     *
     * @param menuItems an array of associated items; each element has a section, title,
     * url, and selection where title and url are strings, and selection is a boolean
     * to indicate this is the current selection. You can use the "section" to trap for
     * javascript links, etc.
     *
     * Cruftily written to only support a depth of two levels
     *
     */
    function DisplayTopMenu()
    {
        echo "<div id=\"TopMenu\"><ul id=\"nav\">\n";
		$menuitems = $this->get_admin_navigation();
        foreach ( $menuitems as $key=>$menuItem)
        	{
				if ($menuItem['parent'] == -1)
					{
						$this->renderMenuSection($key, 0, -1);
					}
        	}
        echo "</ul></div>\n";
    }

    /**
     * DisplayFooter
     * Displays an end-of-page footer.
     */
    function DisplayFooter()
    {
?>
<div id="Footer">
<a href="http://www.cmsmadesimple.org">CMS Made Simple&trade;</a> is Free Software released under the GNU/GPL License
</div>
<?php
    }
    

    /**
     * DisplayDocType
     * If you rewrite the admin section to output pure, beautiful, unadulterated XHTML, you can
     * change the body tag so that it proudly proclaims that there is none of the evil transitional
     * cruft.
     */
    function DisplayDocType()
    {
    	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    }

    /**
     * DisplayHTMLStartTag
     * Outputs the html open tag. Override at your own risk :)
     */
    function DisplayHTMLStartTag()
    {
		$gCms = cmsms();
		$nls = $gCms->nls;
    	echo (isset($nls['direction']) && $nls['direction'] == 'rtl') ? "<html dir=\"rtl\"\n>" : "<html>\n";
    }
	
	/**
	 * @since 1.9
     * ThemeHeader
     * This method outputs the HEAD section of the html page in the admin Theme section,
     * after OutputHeaderJavascript() and before $addt.
     */
	function ThemeHeader(){
		
		if(file_exists('themes/'. $this->themeName. '/includes/standard.js'))
		{
			return '<script type="text/javascript" src="themes/'. $this->themeName. '/includes/standard.js"></script>'."\n";
		}else{
			return '<script type="text/javascript" src="themes/default/includes/standard.js"></script>'."\n";
		}
	}

    /**
     * DisplayHTMLHeader
     * This method outputs the HEAD section of the html page in the admin section.
     */
    public function DisplayHTMLHeader($showielink = false, $addt = '')
    {
		$config = cmsms()->GetConfig();
		$urlext = CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		$title = get_site_preference('sitename').' - '.$this->title;
		$str = "<head>\r\n";
		$str .=
<<<EOT
	<title>{$title}</title>
	<base href="{$config['admin_url']}/" />
	<meta name="Generator" content="CMS Made Simple - Copyright (C) 2004-11 Ted Kulp. All rights reserved." />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<link rel="stylesheet" type="text/css" href="style.php?{$urlext}" />
EOT;

		if ($showielink) {
			$str .=
<<<EOT
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="style.php?ie=1&{$urlext}" />
<![endif]-->
EOT;
		}
		$str .= "<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->\n";
		$str .= $this->OutputHeaderJavascript()."\n";
		
		ob_start(); 
		$tmp1 = $this->ThemeHeader();
		$tmp2 = ob_get_contents();
		ob_end_clean();
		if( $tmp1 )
		{
			// data is returned
			$str .= $tmp1;
		}
		else
		{
			// assume data is echoed
			$str .= $tmp2;
		}
		
		$str .= $addt."\n";
		$str .= "</head>\n"; // fix to correct and add the end header tag -- JLB
		echo $str;
    }

    /**
     * DisplayBodyTag
     * Outputs the admin page body tag. Leave in the funny text if you want this
     * to work properly.
     */
    public function DisplayBodyTag()
    {
        echo "<body##BODYSUBMITSTUFFGOESHERE##>\n";
    }

    
    /**
     * DisplayMainDivStart
     *
     * Used to output the start of the main div that contains the admin page content
     */
    public function DisplayMainDivStart()
    {
    	echo "<div id=\"MainContent\">\n";
    }


    /**
     * DisplayMainDivEnd
     *
     * Used to output the end of the main div that contains the admin page content
     */
    public function DisplayMainDivEnd()
    {
      echo '<div class="clearb"></div>';
      echo "</div><!-- end MainContent -->\n";
    }


    /**
     * DisplaySectionMenuDivStart
     * Outputs the open div tag for the main section pages.
     */
    public function DisplaySectionMenuDivStart()
    {
        echo "<div class=\"MainMenu\">\n";
    }


    /**
     * DisplaySectionMenuDivEnd
     * Outputs the close div tag for the main section pages.
     */
    public function DisplaySectionMenuDivEnd()
    {
        echo "</div><!-- END .MainMenu-->\n";
		if (get_preference($this->userid, 'bookmarks')) {
			echo "</div><!-- END .sections-->\n";
		}
    }


    /**
     * AddToDashboard
     */
    public function AddNotification($priority,$module,$html)
    {
		$notification = new CmsAdminThemeNotification;
		$notification->priority = max(1,min(3,$priority));
		$notification->module = $module;
		$notification->html = $html;
		$this->add_notification($notification);
	}

	/**
	 * Display the available notifications
	 *
	 * @param string $priority Priority threshold
	 * @return void
	 */
    function DisplayNotifications($priority=2)
    {
		$tnotifications = $this->get_notifications();
		
		// count the total number of notifications
		$count=0;
		for( $i = 0; $i < count($tnotifications); $i++ )
			{
				if( $tnotifications[$i]->priority <= $priority ) $count++;
			}
	  
	  // Define that is singular or plural
	  $singular_or_plural = $count;
	  
	  if($singular_or_plural > 1)
		  {
			  $tmp = lang('notifications_to_handle',$count);
		  }
	  else
		  {
			  $tmp = lang('notification_to_handle',$count);
		  } 

	  // remove html tags like <b>2</b>
	  $no_html_tags = preg_replace('/(<\/?)(\w+)([^>]*>)/e','',$tmp);
	  
      echo '<div class="full-Notifications clear">'."\n";
      echo '<div class="Notifications-title">' . $tmp . '</div>'."\n";
	  echo '<div title="'.$no_html_tags.'" id="notifications-display" class="notifications-show" onclick="change(\'notifications-display\', \'notifications-hide\', \'notifications-show\'); change(\'notifications-container\', \'invisible\', \'visible\');"></div>'."\n";
	  echo '<div id="notifications-container" class="invisible">'."\n";
	  
      echo "<ul id=\"Notifications-area\">\n";
      for( $i = 0; $i < count($tnotifications); $i++ )
		  {
			  $data = $tnotifications[$i];
			  if( $data->priority > $priority ) continue;

              echo '<li class="NotificationsItem NotificationsPriority'.$data->priority.'">';
			  echo '<span class="NotificationsItemModuleName">'."\n";
			  echo $data->module."\n";
			  echo "</span>\n";
			  echo '<span class="NotificationsItemData">'."\n";
			  echo $data->html."\n";
			  echo "</span>\n";
			  echo '</li>';		  
		  }
      echo "</ul>";
	  echo "</div><!-- notifications-container -->\n";
      echo "</div><!-- full-Notifications -->\n";
	  echo "<div class=\"clearb\">&nbsp;</div>\n";
    }


   /**
	* ShowHeader
	* Outputs the page header title along with a help link to that section in the wiki.
	* 
	* @param title_name - page heading title
	* @param extra_lang_param - extra parameters to pass to lang() (I don't think this parm is needed)
	* @param link_text - Override the text to use for the help link.
	* @param module_help_type - FALSE if this is not a module, 'both' if link to
	*                           both the wiki and module help and 'builtin' if link to to the builtin help
	*/
    public function ShowHeader($title_name, $extra_lang_param=array(), $link_text = '', $module_help_type = FALSE)
    {
      $cms = cmsms();
      $config = $cms->GetConfig();             
      $header  = '<div class="pageheader">';
      if (FALSE != $module_help_type)
	{
          $module = '';
	  if( isset($_REQUEST['module']) )
	    {
	      $module = $_REQUEST['module'];
	    }
	  else if( isset($_REQUEST['mact']) )
	    {
	      $tmp = explode(',',$_REQUEST['mact']);
	      $module = $tmp[0];
	    }
	  $icon = "modules/{$module}/images/icon.gif";
	  $path = cms_join_path(cmsms()->config['root_path'],$icon);
	  if( file_exists($path) )
	    {
			$url = $config->smart_root_url().'/'.$icon;
			$header .= "<img src=\"{$url}\" class=\"itemicon\" alt=\"{$icon}\" />&nbsp;";
	    }
	  $header .= $title_name;
	}
      else
	{
	  $header .= lang($title_name, $extra_lang_param);
	}
      if (FALSE == empty($this->breadcrumbs))
	{
	  $wikiUrl = $config['wiki_url'];
	  foreach ($this->breadcrumbs AS $key => $value)
	    {
	      $title = $value['title'];
	      // If this is a module and the last part of the breadcrumbs
	      if (FALSE != $module_help_type && TRUE == empty($this->breadcrumbs[$key + 1]))
		{
		  $help_title = $title;
		  if (FALSE == empty($_GET['module']))
		    {
		      $module_name = $_GET['module'];
		    }
		  else
		    {
		      $module_name = substr($_REQUEST['mact'], 0, strpos($_REQUEST['mact'], ','));
		    }
		  // Turn ModuleName into _Module_Name
		  $moduleName =  preg_replace('/([A-Z])/', "_$1", $module_name);
		  $moduleName =  preg_replace('/_([A-Z])_/', "$1", $moduleName);
		  if ($moduleName{0} == '_')
		    {
		      $moduleName = substr($moduleName, 1);
		    }
		  $wikiUrl .= '/'.$moduleName;
		} else {
		// Remove colon and following (I.E. Turn "Edit Page: Title" into "Edit Page")
		$colonLocation = strrchr($title, ':');
		if ($colonLocation !== false)
		  {
		    $title = substr($title,0,strpos($title,':'));
		  }
		// Get the key of the title so we can use the en_US version for the URL
		$title_key = $this->_ArraySearchRecursive($title, $this->menuItems);
		$wikiUrl .= '/'.lang($title_key[0]);
		$help_title = $title;
	      }
	    }
	  if (FALSE == get_preference($this->userid, 'hide_help_links')) {
	    // Clean up URL
	    $wikiUrl = str_replace(' ', '_', $wikiUrl);
	    $wikiUrl = str_replace('&amp;', 'and', $wikiUrl);
	    // Make link to go the translated version of page if lang is not en_US
	    if (FALSE == empty($link_text))
	      {
			  $help_title = $link_text;
	      }
	    else
	      {
			  $help_title = lang('help_external');
	      }

	    $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	    $image_help = $this->DisplayImage('icons/system/info.gif', lang('module_help'),'','','systemicon');
	    $image_help_external = $this->DisplayImage('icons/system/info-external.gif', lang('wikihelp'),'','','systemicon');		
	    if ('both' == $module_help_type)
	      {
			  $module_help_link = $config['admin_url'].'/listmodules.php'.$urlext.'&amp;action=showmodulehelp&amp;module='.$module_name;
			  $header .= '<span class="helptext"><a href="'.$module_help_link.'" title="'.lang('module_help').'">'.$image_help.'</a> <a href="'.$module_help_link.'">'.lang('module_help').'</a></span>';
	      }
// 	    else
// 	      {
// 			  //$header .= '<span class="helptext"><a href="'.$wikiUrl.'" target="_blank">'.$image_help_external.'</a> <a href="'.$wikiUrl.'" target="_blank">'.lang('help').'</a> ('.lang('new_window').')</span>';
// 	      }
	  }
    }
	  $header .= '</div>';
      return $header;     
    }


    /**
     * _ArraySearchRecursive
     * recursively descend an arbitrarily deep multidimensional
     * array, stopping at the first occurence of scalar $needle.
     * return the path to $needle as an array (list) of keys
     * if not found, return null.
     * (will infinitely recurse on self-referential structures)
     * From: http://us3.php.net/function.array-search
     */
    private function _ArraySearchRecursive($needle, $haystack)
    {
       $path = NULL;
       $keys = array_keys($haystack);
       while (!$path && (list($toss,$k)=each($keys))) {
         $v = $haystack[$k];
         if (is_scalar($v)) {
             if ($v===$needle) {
               $path = array($k);
             }
         } elseif (is_array($v)) {
             if ($path=$this->_ArraySearchRecursive( $needle, $v )) {
               array_unshift($path,$k);
             }
         }
       }
       return $path;
    }


    /**
     * ShowError
     * Outputs supplied errors with a link to the wiki for troublshooting.
     *
     * @param errors - array or string of 1 or more errors to be shown
     * @param get_var - Name of the _GET variable that contains the 
     *                  name of the message lang string
     */
    public function ShowErrors($errors, $get_var = '')
    {
		$config = cmsms()->GetConfig();
      $wikiUrl = $config['wiki_url'];
      if ($wikiUrl !='none'){
		      if (FALSE == empty($_REQUEST['module'])  || FALSE == empty($_REQUEST['mact']))
			{
			  if (FALSE == empty($_REQUEST['module']))
			    {
			      $wikiUrl .= '/'.$_REQUEST['module'];
			    }
			  else
			    {
			      $wikiUrl .= '/'.substr($_REQUEST['mact'], 0, strpos($_REQUEST['mact'], ','));
			    }
			}
      $wikiUrl .= '/Troubleshooting';
      }//wiki check
      $image_error = $this->DisplayImage('icons/system/stop.gif', lang('error'),'','','systemicon');
      $output  = '<div class="pageerrorcontainer"';
      if (FALSE == empty($get_var))
	{
	  if (FALSE == empty($_GET[$get_var]))
	    {
	      $errors = cleanValue(lang(cleanValue($_GET[$get_var])));
	    }
	  else
	    {
	      $errors = '';
	      $output .= ' style="display:none;"';
	    }
	}
      $output .= '><div class="pageoverflow">';
      if (FALSE != is_array($errors))
	{
	  $output .= '<ul class="pageerror">';
	  foreach ($errors as $oneerror)
	    {
	      $output .= '<li>'.$oneerror.'</li>';
	    }
	  $output .= '</ul>';
	}
      else
	{
	  $output  .= $image_error.' '.$errors;
	}
	 if ($wikiUrl !='none'){
      $output .= ' <a href="'.$wikiUrl.'" target="_blank">'.lang('troubleshooting').'</a>';
      }//wiki check
      $output .= '</div></div>';
      return $output;
    }
    
    /**
     * ShowMessage
     * Outputs a page status message
     *
     * @param message - Message to be shown
     * @param get_var - Name of the _GET variable that contains the 
     *                  name of the message lang string
     */
    public function ShowMessage($message, $get_var = '')
    {
      $image_done = $this->DisplayImage('icons/system/accept.gif', lang('success'), '','','systemicon');
      $output = '<div class="pagemcontainer"';
      if (FALSE == empty($get_var))
	{
	  if (FALSE == empty($_GET[$get_var]))
	    {
	      $message = lang(cleanValue($_GET[$get_var]));
	    }
	  else
	    {
	      $message = '';
	      $output .= ' style="display:none;"';
	    }
	}
      $output .= '><p class="pagemessage">'.$image_done.' '.$message.'</p></div>';
      return $output;
    }
    

	public function do_header()
	{
		$this->SendHeaders(isset($charsetsent), get_encoding('', false));
		$this->DisplayDocType();
		$this->DisplayHTMLStartTag();
		$this->DisplayHTMLHeader(false, '');
		$this->DisplayBodyTag();
		$this->DisplayTopMenu();
		$this->DisplayMainDivStart();
		$this->DisplayNotifications(3); // todo, a preference.

		$marks = get_preference(get_userid(), 'bookmarks');
		if ($marks)
			{
				$this->StartRighthandColumn();
				if ($marks)
					{
						$this->DoBookmarks();
					}

				$this->EndRighthandColumn();
			}
	}

	public function do_footer()
	{
		$this->DisplayMainDivEnd();
		$this->OutputFooterJavascript();
		$this->DisplayFooter();
	}
}

# vim:ts=4 sw=4 noet
?>