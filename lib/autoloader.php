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

function __cms_load($filename)
{
  $gCms = cmsms();
  static $_cumulative = 0;
  $mem = memory_get_usage();
  require_once($filename);
  $mem = memory_get_usage() - $mem;
  $_cumulative += $mem;
  debug_buffer("Loading $filename = $mem bytes for an approximate total of $_cumulative");
}

function cms_autoloader($classname)
{
  //if( $classname != 'Smarty_CMS' && $classname != 'Smarty_Parser' && startswith($classname,'Smarty') ) return;

  $config = cmsms()->GetConfig();

  // standard classes
  $fn = cms_join_path($config['root_path'],'lib','classes',"class.{$classname}.php");
  if( file_exists($fn) )
    {
      __cms_load($fn);
      return;
    }

  $lowercase = strtolower($classname);
  $fn = cms_join_path($config['root_path'],'lib','classes',"class.{$lowercase}.inc.php");
  if( file_exists($fn) && $classname != 'Content' )
    {
      __cms_load($fn);
      return;
    }

  // standard interfaces
  $fn = cms_join_path($config['root_path'],'lib','classes',"interface.{$classname}.php");
  if( file_exists($fn) )
    {
      __cms_load($fn);
      return;
    }

  global $CMS_LAZYLOAD_MODULES;
  global $CMS_INSTALL_PAGE;
  if( !isset($CMS_LAZYLOAD_MODULES) || isset($CMS_INSTALL_PAGE) ) return;

  // standard content types
  $fn = cms_join_path($config['root_path'],'lib','classes','contenttypes',"{$classname}.inc.php");
  if( file_exists($fn) )
    {
      __cms_load($fn);
      return;
    }

  // module loaded content types
  $contentops = ContentOperations::get_instance();
  if( $contentops )
    {
      // why would this ever NOT be true.. dunno, but hey.
      $types = $contentops->ListContentTypes();
      if( in_array(strtolower($classname),array_keys($types)) )
	{
	  $contentops->LoadContentType(strtolower($classname));
	  return;
	}
    }

  $fn = $config['root_path']."/modules/{$classname}/{$classname}.module.php";
  if( file_exists($fn) )
    {
      __cms_load($fn);
      return;
    }

  $list = ModuleOperations::get_instance()->GetLoadedModules();
  if( is_array($list) && count($list) )
    {
      foreach( array_keys($list) as $modname )
	{
	  $fn = $config['root_path']."/modules/$modname/lib/class.$classname.php";
	  if( file_exists( $fn ) )
	    {
	      __cms_load($fn);
	      return;
	    }
	}
    }
  // module classes
}

spl_autoload_register('cms_autoloader');

#
# EOF
#
?>
