<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
 * Methods for dealing with NLS issues.
 *
 * @author Ted Kulp
 * @since 1.7
 * @license GPL
 **/
class CmsNls extends CmsObject
{
  private static $_nls;

  private static function _scan_nls()
  {
    $config = cms_config();
    $dir = cms_join_path($config['root_path'],$config['admin_dir'],'lang');
    
    $handle = opendir($dir);
    while (false!==($file = readdir($handle))) {
      if (is_file("$dir/$file") && strpos($file, "nls.php") != 0) {
		  include("$dir/$file");
      }
    }
    closedir($handle);
	self::$_nls = $cms_nls;
  }

  /**
   * setup
   */
  public static function setup()
  {
    if( !isset($cms_nls) || empty($cms_nls))
	{
		// scan the nls files
		$cms_nls = array();
		self::_scan_nls();
	}
  }

  /**
   * Convert an HTTP_ACCEPT_LANGUAGE string
   * to a CMS language
   */
  public static function to_lang($str)
  {
	  self::setup();

	// get the incoming accepted languages.
    $langs = explode(',',$str);

    foreach( $langs as $onelang )
	{
		// trim the quality portion out.
		if( strpos($onelang,';') !== FALSE )
		{
			$onelang = substr($onelang,0,strpos($onelang,';'));
		}

		if( isset(self::$_nls['language'][$onelang]) )
		{
			return $onelang;
		}

		if( isset(self::$_nls['alias'][$onelang]) )
		{
			$alias = self::$_nls['alias'][$onelang];
			if( isset(self::$_nls['language'][$alias]) )
			{
				return $alias;
			}
		}
	}

	return FALSE;
  }

  
  public static function get_languages()
  {
	  self::setup();
	  
	  return self::$_nls;
  }
}