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
  if( !function_exists('get_stylesheet_tag') ) {
  function get_stylesheet_tag($cssid,$media='')
  {
    global $gCms;
    $config = $gCms->GetConfig();
    
    $str = '';
    $url = '';

    $base = $config['root_url'];
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    {
      $base = $config['ssl_url'];
    }

    $url = $base.'/stylesheet.php?cssid='.$cssid;
    if( !empty($media) )
      {
	$url .= '&amp;mediatype='.$media;
      }

    $str = '<link rel="stylesheet" type="text/css" ';
    if( !empty($media) )
      {
	$str .= 'media="'.$media.'" ';
      }
    $str .= 'href="'.$url.'" />';

    return $str;
  }
  }

  global $gCms;
  $config = $gCms->GetConfig();
  $content_obj = &$gCms->variables['content_obj'];
  $template_id=$content_obj->TemplateId();
  if (isset($params["templateid"]) && $params["templateid"]!="") {
    $template_id=$params["templateid"];
  }

  $db =& $gCms->GetDb();
  
  $stylesheet = '';
  
  if (isset($params['name']) && $params['name'] != '')
    {
      $query = 'SELECT css_id FROM '.cms_db_prefix().'css 
                     WHERE css_name = ?';
      $cssid = $db->GetOne( $query, array($params['name']));
      if( $cssid )
	{
	  $stylesheet .= get_stylesheet_tag($cssid,isset($params['media'])?$params['media']:'');
	  $stylesheet .= "\n";
	}
    }
  else
    {
      $query = 'SELECT DISTINCT A.css_id,A.media_type,B.assoc_order 
                      FROM '.cms_db_prefix().'css A, '.cms_db_prefix().'css_assoc B
                     WHERE A.css_id = B.assoc_css_id
                       AND B.assoc_type = ?
                       AND B.assoc_to_id = ?
                     ORDER BY B.assoc_order';
      $res = $db->GetArray($query,array('template',$template_id));
      if( $res ) {
      $fmt1 = '<link rel="stylesheet" type="text/css" media="%s" href="%s" />';
      $fmt2 = '<link rel="stylesheet" type="text/css" href="%s" />';
      foreach( $res as $one )
	{
          $tmp = str_replace(' ','',$one['media_type']);
	  $stylesheet .= get_stylesheet_tag($one['css_id'],$tmp);
	  $stylesheet .= "\n";
	}
      }
    }
  
  if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
    {
      $stylesheet = preg_replace("/\{\/?php\}/", "", $stylesheet);
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
