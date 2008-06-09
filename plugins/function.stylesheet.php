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

function smarty_cms_function_stylesheet($params, &$smarty)
{
  global $gCms;
  $config = &$gCms->config;
  $pageinfo = &$gCms->variables['pageinfo'];
  $db =& $gCms->GetDb();
  
  $stylesheet = '';
  
  if (isset($params['name']) && $params['name'] != '')
    {
      $query = 'SELECT css_id FROM '.cms_db_prefix().'css 
                     WHERE name = ?';
      $cssid = $db->GetOne( $query, array($params['name']));
      if( $cssid )
	{
	  $stylesheet .= '<link rel="stylesheet" type="text/css" ';
	  if (isset($params['media']) && $params['media'] != '')
	    {
	      $stylesheet .= 'media="' . $params['media'] . '" ';
	    }
	  $stylesheet .= 'href="'.$config['root_url'].'/stylesheet.php?cssid='.$cssid;
	  $stylesheet .= "\" />\n"; 
	}
    }
  else
    {
      $query = 'SELECT DISTINCT A.css_id,A.media_type 
                      FROM '.cms_db_prefix().'css A, '.cms_db_prefix().'css_assoc B
                     WHERE A.css_id = B.assoc_css_id
                       AND B.assoc_type = ?
                       AND B.assoc_to_id = ?';
      $res = $db->GetArray($query,array('template',$pageinfo->template_id));
      $fmt1 = '<link rel="stylesheet" type="text/css" media="%s" href="%s" />';
      $fmt2 = '<link rel="stylesheet" type="text/css" href="%s" />';
      foreach( $res as $one )
	{
	  $url = $config['root_url'].'/stylesheet.php?cssid='.$one['css_id'];
	  if( isset($one['media_type']) && !empty($one['media_type']) )
	    {
	      $url .= '&amp;mediatype='.urlencode($one['media_type']);
	      $stylesheet .= sprintf($fmt1,$one['media_type'],$url);
	    }
	  else
	    {
	      $stylesheet .= sprintf($fmt2,$url);
	    }
	  $stylesheet .= "\n";
	}
    }
  
  if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
    {
      $stylesheet = ereg_replace("\{\/?php\}", "", $stylesheet);
    }
  
  return $stylesheet;
}

function smarty_cms_help_function_stylesheet() {
  echo lang('help_function_stylesheet');
}

function smarty_cms_about_function_stylesheet() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
