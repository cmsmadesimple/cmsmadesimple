<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_tree (c) 2010 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A simple php tree class.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
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
 * Static classes providing convenience functions for admin console requests.
 * @package CMS
 * @license GPL
 */

if( !CmsApp::get_instance()->test_state(CmsApp::STATE_ADMIN_PAGE) )
    throw new CmsLogicException('Attempt to use cms_admin_utils class from an invalid request');

/**
 * A Simple static class providing various convenience utilities for admin requests.
 *
 * @package CMS
 * @license GPL
 * @author  Robert Campbell
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.0
 */
final class cms_admin_utils
{
	/**
	 * @ignore
	 */
	private function __construct() {}

	/**
	 * Get the complete URL to an admin icon
	 *
	 * @param string $icon the basename of the desired icon
	 * @return string
	 */
	public static function get_icon($icon)
	{
		$theme = cms_utils::get_theme_object();
		if( !is_object($theme) ) return;

		$smarty = \Smarty_CMS::get_instance();
		$module = $smarty->get_template_vars('actionmodule');

		$dirs = array();
		if( $module ) {
			$obj = cms_utils::get_module($module);
			if( is_object($obj) ) {
				$img = basename($icon);
				$dirs[] = array(cms_join_path($obj->GetModulePath(),'icons',"{$img}"),$obj->GetModuleURLPath()."/icons/{$img}");
				$dirs[] = array(cms_join_path($obj->GetModulePath(),'images',"{$img}"),$obj->GetModuleURLPath()."/images/{$img}");
			}
		}
		if( basename($icon) == $icon ) $icon = "icons/system/{$icon}";
		$config = \cms_config::get_instance();
		$dirs[] = array(cms_join_path($config['root_path'],$config['admin_dir'],"themes/{$theme->themeName}/images/{$icon}"),
						$config['admin_url']."/themes/{$theme->themeName}/images/{$icon}");

		$fnd = null;
		foreach( $dirs as $one ) {
			if( file_exists($one[0]) ) {
				$fnd = $one[1];
				break;
			}
		}
		return $fnd;
	}

	/**
	 * Get a help tag for displaying inlne, popup help.
	 *
	 * This method accepts variable arguments.  If only one argument is passed it is assumed to be
	 * the second key for the help tag and the first key is assumed to be the current module name.
	 * If two arguments are passed the first argument is assumed to be key1 and the second to be key2.
	 *
	 * @param string $keys,... [$key2|$key1,$key2]
	 * @return string HTML content of the help tag
	 */
	public static function get_help_tag()
	{
		if( !CmsApp::get_instance()->test_state(CmsApp::STATE_ADMIN_PAGE) ) return;

		$params = array();
		$args = func_get_args();
		if( count($args) >= 2 && is_string($args[0]) && is_string($args[1]) ) {
			$params['key1'] = $args[0];
			$params['key2'] = $args[1];
            if( isset($args[2]) ) $params['title'] = $args[2];
		}
		else if( count($args) == 1 && is_string($args[0]) ) {
			$params['key2'] = $args[0];
		}
		else {
			$params = $args[0];
		}

		$theme = cms_utils::get_theme_object();
		if( !is_object($theme) ) return;

		$key1 = '';
		$key2 = '';
		$title = '';
        $titlekey = '';
		foreach( $params as $key => $value ) {
			switch( $key ) {
			case 'key1':
			case 'realm':
				$key1 = trim($value);
				break;
			case 'key':
			case 'key2':
			case 'key':
				$key2 = trim($value);
				break;
            case 'titlekey':
                $titlekey = $value;
                break;
			case 'title':
				$title = $value;
			}
		}

		if( !$key1 ) {
			$smarty = \Smarty_CMS::get_instance();
			$module = $smarty->get_template_vars('actionmodule');
			if( $module ) {
				$key1 = $module;
			}
			else {
				$key1 = 'help';
			}
		}

		if( !$key1 ) 	return;

		$key = $key1;
		if( $key2 !== '' ) $key .= '__'.$key2;
		if( $title === '' ) $title = $key2;

		$icon = self::get_icon('info.gif');
		if( !$icon ) return;

		return '<span class="cms_help" data-cmshelp-key="'.$key.'" data-cmshelp-title="'.$title.'"><img class="cms_helpicon" src="'.$icon.'" alt="'.$title.'" /></span>';
	}
} // end of class

#
# EOF
#