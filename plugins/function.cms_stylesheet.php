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
#along with this program; if not, write to the Free Softwarenews-fesubmit.html&articleid=3
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_cms_stylesheet($params, &$smarty)
{
  if( !function_exists('get_stylesheet_tag') ) {
  function get_stylesheet_tag($cssid,$media='')
  {
    global $gCms;
    $config = &$gCms->config;
    
    $str = '';
    $url = '';
    //if( $config['url_rewriting'] != 'none' )

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


  //
  // begin
  //
  global $gCms;

  if (isset($params["templateid"]) && $params["templateid"]!="")
    {
      $template_id=$params["templateid"];
    }
  else
    {
      $pageinfo = &$gCms->variables['pageinfo'];
      $template_id=$pageinfo->template_id;
    }
  $config =& $gCms->config;
  $db =& $gCms->GetDb();

  $cache_dir = TMP_CACHE_LOCATION;
  $stylesheet = '';

  if (isset($params['name']) && $params['name'] != '')
    {
      $query = 'SELECT css_id,css_name,modified_date FROM '.cms_db_prefix().'css 
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
      $qparms = array();
      $query = 'SELECT DISTINCT A.css_id,A.css_name,A.css_text,A.modified_date,
		A.media_type,B.assoc_order 
		FROM '.cms_db_prefix().'css A, '.cms_db_prefix().'css_assoc B
		WHERE A.css_id = B.assoc_css_id
		AND B.assoc_type = ?
		AND B.assoc_to_id = ?';
      $qparms = array('template',$template_id);
      if( isset($params['media']) && strtolower($params['media']) != 'all' )
	{
	  $query .= ' AND (media_type LIKE ? OR media_type LIKE ?)';
	  $qparms[] = '%'.trim($params['media']).'%';
          $qparms[] = '%all%';
	}
      $query .= ' ORDER BY B.assoc_order';
		
      $conv_filename = array(' '=>'',':'=>'_');
      $res = $db->GetArray($query,$qparms);
      if( $res ) {
	$fmt1 = '<link rel="stylesheet" type="text/css" media="%s" href="%s" />';
	$fmt2 = '<link rel="stylesheet" type="text/css" href="%s" />';
	foreach( $res as $one )
	  {
	    $media_type = str_replace(' ','',$one['media_type']);
	    $filename = strtr($one['css_name'], $conv_filename).'_'.strtotime($one['modified_date']).'.css';
	    if ( !file_exists(cms_join_path($cache_dir,$filename)) )
	      {
		$smarty = $gCms->GetSmarty();
		$smarty->left_delimiter = '[[';
		$smarty->right_delimiter = ']]';
		$smarty->_compile_source('temporary stylesheet', $one['css_text'], $_compiled );
		@ob_start();
		$smarty->_eval('?>' . $_compiled);
		$_contents = @ob_get_contents();
		@ob_end_clean();
		$smarty->left_delimiter = '{';
		$smarty->right_delimiter = '}';
		$fname = cms_join_path($cache_dir,$filename);
		$fp = fopen($fname, 'w');
		//we convert CRLF to LF for unix compatibility
		fwrite($fp, str_replace("\r\n", "\n", $_contents));
		fclose($fp);
		//set the modified date to the template modified date
		//touch($fname, $db->UnixTimeStamp($one['modified_date']));
	      }
	    if ( empty($media_type) || isset($params['media']) )
	      {
		$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$config['root_url'].'/tmp/cache/'.$filename.'"/>'."\n";
	      }
	    else
	      {
		$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$config['root_url'].'/tmp/cache/'.$filename.'" media="'.$media_type.'"/>'."\n";
	      }
	  }
      }
    }

  if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
    {
      $stylesheet = preg_replace("/\{\/?php\}/", "", $stylesheet);
    }

  return $stylesheet;
}

function smarty_cms_help_function_cms_stylesheet()
{
	echo lang('help_function_cms_stylesheet');
}

function smarty_cms_about_function_cms_stylesheet()
{
	?>
	<p>Author: jeff&lt;jeff@ajprogramming.com&gt;</p>
	<p>Version: 0.6</p>
	<p>
	Change History:<br/>
	Rework from {stylesheet}
	</p>
	<?php
}
?>
