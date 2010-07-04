<?php
#Modern Theme - A theme for CMS Made Simple
#(c)2005 by Alexander Endresen - alexander@endresen.org
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

class defaultTheme extends AdminTheme
{
  function renderMenuSection($section, $depth, $maxdepth)
  {
    if ($maxdepth > 0 && $depth > $maxdepth)
      {
	return;
      }
    if (! $this->menuItems[$section]['show_in_menu'])
      {
	return;
      }
    if (strlen($this->menuItems[$section]['url']) < 1)
      {
	echo "<li>".$this->menuItems[$section]['title']."</li>";
	return;
      }
    echo "<li><a href=\"";
    echo $this->menuItems[$section]['url'];
    echo "\"";
    if (array_key_exists('target', $this->menuItems[$section]))
      {
	echo ' rel="external"';
      }
    $class = array();
    if ($this->menuItems[$section]['selected'])
      {
	$class[] = 'selected';
      }
    if (isset($this->menuItems[$section]['firstmodule']))
      {
	$class[] = 'first_module';
      }
    else if (isset($this->menuItems[$section]['module']))
      {
	$class[] = 'module';
      }
    if (count($class) > 0)
      {
	echo ' class="';
	for($i=0;$i<count($class);$i++)
	  {
	    if ($i > 0)
	      {
		echo " ";
	      }
	    echo $class[$i];
	  }
	echo '"';
      }
    echo ">";
    echo $this->menuItems[$section]['title'];
    echo "</a>";
    if ($this->HasDisplayableChildren($section))
      {
	echo "<ul>";
	foreach ($this->menuItems[$section]['children'] as $child)
	  {
	    $this->renderMenuSection($child, $depth+1, $maxdepth);
	  }
	echo "</ul>";
      }
    echo "</li>";
    return;
  }

    /**
     * DisplayHTMLHeader
     * This method outputs the HEAD section of the html page in the admin section.
     */
    function DisplayHTMLHeader($showielink = false, $addt = '')
    {
		global $gCms;
		$config =& $gCms->GetConfig();
?><head>
<meta name="Generator" content="CMS Made Simple - Copyright (C) 2004-10 Ted Kulp. All rights reserved." />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title><?php echo $this->cms->siteprefs['sitename'] ." - ". $this->title ?></title>
<link rel="stylesheet" type="text/css" href="style.php" />
<?php
	if ($showielink) {
?>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="style.php?ie=1" />
<![endif]-->
<?php
	}
?>
<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->
<?php $this->OutputHeaderJavascript(); ?>
<?php echo $addt ?>
<base href="<?php echo $config['root_url'] . '/' . $config['admin_dir'] . '/'; ?>" />
</head>
<?php
    }


    function DisplayTopMenu()
    {
	echo '<div><p class="logocontainer"><img src="themes/default/images/logo.gif" alt="" /><span class="logotext">'.lang('adminpaneltitle').' - '. $this->cms->siteprefs['sitename'] .' &nbsp;&nbsp; '.lang('welcome_user').': '.$this->cms->variables['username'].'</span></p></div>';
        echo "<div class=\"topmenucontainer\">\n\t<ul id=\"nav\">";
        foreach ($this->menuItems as $key=>$menuItem) {
        	if ($menuItem['parent'] == -1) {
        	    echo "\n\t\t";
        		$this->renderMenuSection($key, 0, -1);
        	}
        }
        echo "\n\t</ul>\n";
		//ICON VIEW SITE
		echo "\n\t<div id=\"nav-icons_all\"><ul id=\"nav-icons\">\n";
		echo "\n\t<li class=\"viewsite-icon\"><a  rel=\"external\" title=\"".lang('viewsite')."\"  href=\"../\">".lang('viewsite')."</a></li>\n";
		//ICON LAGOUT
		echo "\n\t<li class=\"logout-icon\"><a  title=\"".lang('logout')."\"  href=\"logout.php\">".lang('logout')."</a></li>\n";
		echo "\n\t</ul></div>\n";
		//END ICONS
		echo "\t<div class=\"clearb\"></div>\n";
		echo "</div>\n";
		echo '<div class="breadcrumbs"><p class="breadcrumbs">';
		$counter = 0;
		foreach ($this->breadcrumbs as $crumb) {
			if ($counter > 0) {
				echo " &#187; ";
			}
			if (isset($crumb['url']) && 
			    str_replace('&amp;', '&', $crumb['url']) != basename($_SERVER['REQUEST_URI']))
			  {
			    echo '<a class="breadcrumbs" href="'.$crumb['url'];
			    echo '">'.$crumb['title'];
			    echo '</a>';
			  }
			else
			  {
			    echo $crumb['title'];
			  }
			$counter++;
		}

		echo '</p></div>';
		echo '<div class="hstippled">&nbsp;</div>';
    }

	function DisplayFooter() {
		global $CMS_VERSION;
		global $CMS_VERSION_NAME;
		echo '<p class="footer"><a class="footer" href="http://www.cmsmadesimple.org">CMS Made Simple</a> '.$CMS_VERSION.' "' . $CMS_VERSION_NAME . '"<br /><a class="footer" href="http://www.cmsmadesimple.org">CMS Made Simple</a> is free software released under the General Public Licence.</p>';
	}
	
	function OutputHeaderJavascript() {
		echo '<script type="text/javascript" src="themes/default/includes/standard.js"></script>';
		echo '<script type="text/javascript" src="../lib/scriptaculous/prototype.js"></script>';
		echo '<script type="text/javascript" src="../lib/scriptaculous/scriptaculous.js"></script>';
		echo '<script type="text/javascript" src="../lib/scriptaculous/cmsext.js"></script>';
	}

	function StartRighthandColumn() {
		echo '<div class="navt_menu">'."\n";
		echo '<div id="navt_display" class="navt_show" onclick="change(\'navt_display\', \'navt_hide\', \'navt_show\'); change(\'navt_container\', \'invisible\', \'visible\');"></div>'."\n";
		echo '<div id="navt_container" class="invisible">'."\n";
		echo '<div id="navt_tabs">'."\n";
		if (get_preference($this->userid, 'bookmarks')) {
				echo '<div id="navt_bookmarks">'.lang('bookmarks').'</div>'."\n";
		}
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '<div id="navt_content">'."\n";
	}

	function DisplayRecentPages()	 {
		if (get_preference($this->userid, 'recent')) {	
			echo '<div id="navt_recent_pages_c">'."\n";
			$counter = 0;
			foreach($this->recent as $pg) {
				echo "<a href=\"". $pg->url."\">".++$counter.'. '.$pg->title."</a><br />"."\n";
				}
			echo '</div>'."\n";
		}
	}

	function DisplayBookmarks($marks) {
		if (get_preference($this->userid, 'bookmarks')) {	
			echo '<div id="navt_bookmarks_c">'."\n";
			$counter = 0;
			foreach($marks as $mark) {
				echo "<a href=\"". $mark->url."\">".++$counter.'. '.$mark->title."</a><br />"."\n";
				}
			echo '</div>'."\n";
		}
	}	 

	function EndRighthandColumn() {
		echo '</div>'."\n";
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '</div>'."\n";
	}

	function DisplayDocType() {
	
#      		echo '<QUESTION_MARK xml version="1.0" encoding="'.get_encoding().'"QUESTION_MARK>'."\n";
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'."\n";
		echo '	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
	}

	function DisplayHTMLStartTag() {
		$tag = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"';
		if (isset($this->cms->nls['direction']) &&  $this->cms->nls['direction'] == 'rtl')
		{
			$tag .= ' dir="rtl"';
		}
		$tag .= ">\n\n";
		echo $tag;
	}

    function DisplayDashboardCallout($file, $message = '')
    {
	if ($message == '')
		$message = lang('installdirwarning');

        if (file_exists($file))
        {
		echo "<div class=\"DashboardCallout\">\n";
		echo "<div class=\"pageerrorinstalldir\"><p class=\"pageerror\">".$message."</p></div>";
		echo "</div> <!-- end DashboardCallout -->\n";
        }
    }
	function DisplayDashboardPageItem($item="module", $title='', $content = '')
    {
    	switch ($item) {
    		case "start" : {
    			echo "\n<div class=\"full-content clear-db\">\n";
    			break;;
    		}
    		case "end" : {
    			echo "</div><!--full-content clear-db-->\n";
    			break;
    		}
    		case "core" : {
    			echo "<div class=\"coredashboardcontent\">\n";
    			echo "  <div class=\"dashboardheader-core\">\n";
    			echo $title;
    			echo "  </div>\n";
    			echo "  <div class=\"dashboardcontent-core\">\n";
    			echo $content;
    			echo "  </div>\n";
    			echo "</div>\n";
    			break;
    		}
    		case "module" : {
    			echo "<div class=\"moduledashboardcontent\">\n"; 
    			echo "  <div class=\"dashboardheader\">\n";
    			echo $title;
    			echo "  </div>\n";
    			echo "  <div class=\"dashboardcontent\">\n";
    			echo $content;
    			echo "  </div>\n";
    			echo "</div>\n";
    			break;
    		}
    	}
    	
	
    }
    function DisplayAllSectionPages()
    {
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
	  if ($menuItem['url'] == 'index.php'  || strlen($menuItem['url']) < 1)
	    {
	      continue;
	    }
	      
	  echo "<div class=\"itemmenucontainer\">";
	  echo '<div class="itemoverflow">';
	  echo '<p class="itemicon">';
	  $iconSpec = $thisSection;
	  if ($menuItem['url'] == $this->_viewsite_url)
	    {
	      $iconSpec = 'viewsite';
	    }
	  echo '<a href="'.$menuItem['url'].'">';
	  echo $this->DisplayImage('icons/topfiles/'.$iconSpec.'.gif', $iconSpec, '', '', 'itemicon');
	  echo '</a>';
	  echo '</p>';
	  echo '<p class="itemtext">';
	  echo "<a class=\"itemlink\" href=\"".$menuItem['url']."\"";
	  if (array_key_exists('target', $menuItem))
	    {
	      echo ' rel="external"';
	    }
	      
	  echo ">".$menuItem['title']."</a><br />\n";
	  if (isset($menuItem['description']) && strlen($menuItem['description']) > 0)
	    {
	      echo $menuItem['description']."<br />";
	    }
	  $this->ListSectionPages($thisSection);
	  echo '</p>';
	  echo "</div>";
	  echo '</div>';
	}
    }

    function DisplaySectionPages($section)
    {
      global $gCms;
      if (count($this->menuItems) < 1)
	{
	  // menu should be initialized before this gets called.
	  // TODO: try to do initialization.
	  // Problem: current page selection, url, etc?
	  return -1;
	}

      $firstmodule = true;
      foreach ($this->menuItems[$section]['children'] as $thisChild)
	{
	  $thisItem = $this->menuItems[$thisChild];
	  if (! $thisItem['show_in_menu'] || strlen($thisItem['url']) < 1)
	    {
	      continue;
	    }

	  // separate system modules from the rest.
	  if( preg_match( '/module=([^&]+)/', $thisItem['url'], $tmp) )
	    {
	      if( array_search( $tmp[1], $gCms->cmssystemmodules ) === FALSE && $firstmodule == true )
		{
		  echo "<hr width=\"90%\"/>";
		  $firstmodule = false;
		}
	    }

	  echo "<div class=\"itemmenucontainer\">\n";
	  echo '<div class="itemoverflow">';
	  echo '<p class="itemicon">';
	  $moduleIcon = false;
	  $iconSpec = $thisChild;
	  
	  // handle module icons
	  if (preg_match( '/module=([^&]+)/', $thisItem['url'], $tmp))
	    {
	      if ($tmp[1] == 'News')
		{
		  $iconSpec = 'newsmodule';
		}
	      else if ($tmp[1] == 'TinyMCE' || $tmp[1] == 'HTMLArea')
		{
		  $iconSpec = 'wysiwyg';
		}
	      else
		{
		  $imageSpec = dirname($this->cms->config['root_path'] .
				       '/modules/' . $tmp[1] . '/images/icon.gif') .'/icon.gif';
		  if (file_exists($imageSpec))
		    {
		      echo '<a href="'.$thisItem['url'].'"><img class="itemicon" src="'.
			$this->cms->config['root_url'] .
			'/modules/' . $tmp[1] . '/images/' .
			'/icon.gif" alt="'.$thisItem['title'].'" /></a>';
		      $moduleIcon = true;
		    }
		  else
		    {
		      $iconSpec=$this->TopParent($thisChild);
		    }
		}
	    }
	  if (! $moduleIcon)
	    {
	      if ($thisItem['url'] == $this->_viewsite_url)
		{
		  $iconSpec = 'viewsite';
		}
	      echo '<a href="'.$thisItem['url'].'">';
	      echo $this->DisplayImage('icons/topfiles/'.$iconSpec.'.gif', ''.$thisItem['title'].'', '', '', 'itemicon');
	      echo '</a>';
	    }
	  echo '</p>';
	  echo '<p class="itemtext">';
	  echo "<a class=\"itemlink\" href=\"".$thisItem['url']."\"";
	  if (array_key_exists('target', $thisItem))
	    {
	      echo ' rel="external"';
	    }
	  echo ">".$thisItem['title']."</a><br />\n";
	  if (isset($thisItem['description']) && strlen($thisItem['description']) > 0)
	    {
	      echo $thisItem['description']."<br />";
	    }
	  echo '</p>';
	  echo "</div>";
	  echo '</div>';			
        }

    }
	
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
	      if (! $thisItem['show_in_menu'] || strlen($thisItem['url']) < 1)
		{
		  continue;
		}
	      if ($count++ > 0)
		{
		  echo ", ";
		}
	      echo "<a class=\"itemsublink\" href=\"".$thisItem['url'];
	      echo "\">".$thisItem['title']."</a>";
	    }
	}
    }
   
	/* Functions that we want dont want the standard output from */
	function OutputFooterJavascript() {}	
}
?>
