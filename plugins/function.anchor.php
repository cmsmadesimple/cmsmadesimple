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

function smarty_function_anchor($params, &$template)
{
  $smarty = $template->smarty;
  $gCms = cmsms();

  $content = cms_utils::get_current_content();
  if( !is_object($content) ) return;

  // Added by Russ for class, tabindex and title for anchor 2006/07/19
  $class="";
  $title="";
  $tabindex="";
  $accesskey="";
  if (isset($params['class'])) $class = ' class="'.$params['class'].'"';
  if (isset($params['title'])) $title = ' title="'.$params['title'].'"';
  if (isset($params['tabindex'])) $tabindex = ' tabindex="'.$params['tabindex'].'"';
  if (isset($params['accesskey'])) $accesskey = ' accesskey="'.$params['accesskey'].'"';
  // End of first part added by Russ 2006/07/19

  $url = $content->GetURL().'#'.trim($params['anchor']);
  $url = str_replace('&amp;','***',$url);
  $url = str_replace('&', '&amp;', $url);
  $url = str_replace('***','&amp;',$url);
  if (isset($params['onlyhref']) && ($params['onlyhref'] == '1' || $params['onlyhref'] == 'true')) {
    // Note if you set 'onlyheref' that is what you get - no class or title or tabindex or text
    $tmp =  $url;
  }
  else {
    // Line replaced by Russ
    //	echo '<a href="'.$url.'">'.$params['text'].'</a>';
    // Line replaced with -  by Russ to reflect class and title for anchor 2006/07/19
    $tmp = '<a href="'.$url.'"'.$class.$title.$tabindex.$accesskey.'>'.$params['text'].'</a>';
    // End of second part added by Russ 2006/07/19
  }

  if( isset($params['assign']) ){
    $smarty->assign(trim($params['assign']),$tmp);
    return;
  }
  echo $tmp;
	
}
	#Ammended by Russ for class, tabindex and title for anchor 2006/07/19
function smarty_cms_help_function_anchor() {
  echo lang('help_function_anchor');
}
	#Amended by Russ for class, tabindex and title for anchor 2006/07/19
function smarty_cms_about_function_anchor() {
  echo lang('about_function_anchor');
}
?>
