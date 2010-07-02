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

/**
 * @package CMS
 * @ignore
 */

/**
 * A function for auto-loading classes.
 *
 * @since 1.7
 * @param string A class name
 * @return boolean
 */
function cms_autoloader($classname)
{
  global $gCms;
  $config = $gCms->GetConfig();

  $fn = cms_join_path($config['root_path'],'lib','classes',"class.{$classname}.php");
  if( file_exists($fn) )
    {
      require_once($fn);
      return;
    }
  $fn = cms_join_path($config['root_path'],'lib','classes',"interface.{$classname}.php");
  if( file_exists($fn) )
    {
      require_once($fn);
      return;
    }

  foreach( $gCms->modules as $module => &$data )
    {
      if( !isset($data['object']) ) continue;
      $obj =& $data['object'];
      $fn = cms_join_path($obj->GetModulePath(),'lib',"class.{$classname}.php");
      if( file_exists($fn) )
	{
	  require_once($fn);
	  return;
	}
      $fn = cms_join_path($obj->GetModulePath(),'lib',"interface.{$classname}.php");
      if( file_exists($fn) )
	{
	  require_once($fn);
	  return;
	}
    }
}

spl_autoload_register('cms_autoloader');

#
# EOF
#
?>