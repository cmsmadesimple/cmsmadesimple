<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

require_once('../include.php');

function smarty_cms_function_adminicon($params, &$smarty)
{
  $themeObject = CmsAdminTheme::get_instance();

  if( !isset( $params['icon'] ) ) return;
  $file = $params['icon'];
  $alt = $file;
  $path = 'icons/system';
  $width = '';
  $height = '';
  $class = 'systemicon';
  
  if( isset($params['alt_lang']) )
    {
      $alt = lang($params['alt_lang']);
    }
  if( isset($params['path']) )
    {
      $path = trim($params['path']);
    }
  if( isset($params['width']) )
    {
      $width = $params['width'];
    }
  if( isset($params['height']) )
    {
      $height = $params['height'];
    }
  if( isset($params['class']) )
    {
      $class = $params['class'];
    }
  
  $file = cms_join_path( $path, $file );
  return $themeObject->display_image( $file, $alt, $width, $height, $class );
}

// EOF
?>