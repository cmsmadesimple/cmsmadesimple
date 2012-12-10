<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_tree (c) 2010 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A simple php tree class.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE

/**
 * @package CMS
 */

if( !cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) )
  throw new CmsLogicException('Attempt to use cms_admin_utils class from an invalid request');

/**
 * A Simple Static class providing various convenience utilities for admin requests.
 *
 * @package CMS
 * @author  Robert Campbell
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.0
 */
final class cms_admin_utils
{
  private function __construct() {}

  public static function get_icon($icon)
  {
    $theme = cms_utils::get_theme_object();
    if( !is_object($theme) ) return;

    $smarty = cmsms()->GetSmarty();
    $module = $smarty->get_template_vars('actionmodule');

    $dirs = array();
    if( $module ) {
      $obj = cms_utils::get_module($module);
      if( is_object($obj) ) {
	$img = basename($icon);
	$dirs[] = array(cms_join_path($obj->GetModulePath(),'icons',"{$img}"),
			$obj->GetModuleURLPath()."/icons/{$img}");
	$dirs[] = array(cms_join_path($obj->GetModulePath(),'images',"{$img}"),
			$obj->GetModuleURLPath()."/images/{$img}");
      }
    }
    if( basename($icon) == $icon ) {
      $icon = "icons/system/{$icon}";
    }
    $config = cmsms()->GetConfig();
    $dirs[] = array(cms_join_path($config['root_path'],$config['admin_dir'],"themes/{$theme->themeName}/images/{$icon}"),
		    $config['admin_url']."/themes/{$theme->themeName}/images/{$icon}");

    $fnd = null;
    foreach( $dirs as $one ) {
      if( file_exists($one[0]) ) {
	$fnd = $one[1];
      }
    }
    return $fnd;
  }
} // end of class

#
#
# EOF
?>