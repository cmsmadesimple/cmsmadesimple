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

function smarty_cms_function_content_image($params,&$smarty)
{
  global $gCms;
  $pageinfo =& $gCms->variables['pageinfo'];
  if (!isset($pageinfo) || $pageinfo === FALSE || !isset($pageinfo->content_id) )
    return _smarty_cms_function_content_return('', $params, $smarty);
    

  $result = '';
  if( isset($params['block']) )
    {
      $oldvalue = $smarty->caching;
      $smarty->caching = false;
      $result = $smarty->fetch(str_replace(' ', '_', 'content:' . $params['block']), '', $pageinfo->content_id);
      $smarty->caching = $oldvalue;
    }
  $img = _smarty_cms_function_content_return($result, $params, $smarty);
  if( $img == -1 || empty($img) )
    return;

  $name = $params['block'];
  $alt = '';
  $width = '';
  $height = '';
  $urlonly = false;
  $xid = '';
  $class = '';
  if( isset($params['name']) ) $name = $params['name'];
  if( isset($params['class']) ) $class = $params['class'];
  if( isset($params['id']) ) $xid = $params['id'];
  if( isset($params['alt']) ) $alt = $params['alt'];
  if( isset($params['width']) ) $width = $params['width'];
  if( isset($params['height']) ) $height = $params['height'];
  if( isset($params['urlonly']) ) $urlonly = true;

  if( $urlonly ) return $img;
  $out = '<img src="'.$img.'" ';
  if( !empty($name) )
    {
      $out .= 'name="'.$name.'" ';
    }
  if( !empty($class) )
    {
      $out .= 'class="'.$class.'" ';
    }
  if( !empty($xid) )
    {
      $out .= 'id="'.$xid.'" ';
    }
  if( !empty($width) )
    {
      $out .= 'width="'.$width.'" ';
    }
  if( !empty($height) )
    {
      $out .= 'height="'.$height.'" ';
    }
  if( !empty($alt) )
    {
      $out .= 'alt="'.$alt.'" ';
    }
  $out .= '/>';
  return $out;
}

function smarty_cms_help_function_content_image()
{
  echo lang('help_function_content_image');
}

function smarty_cms_about_function_content_image()
{
	?>
	<p>Author: Robert Campbell&lt;calguy1000@cmsmadesimple.org&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	1.0 - Initial version
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>