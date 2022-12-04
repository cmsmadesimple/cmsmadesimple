<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
 */

/**
 * Class for operations dealing with bulk content methods.
 *
 * @package CMS
 * @author Robert Campbell
 * @since 1.7
 * @version $Revision$
 * @license GPL
 **/
final class bulkcontentoperations
{
	private function __construct() {}
	private static $_list = array();

	/**
	 * Register a function to show in the bulk content operations list
	 * in listcontent.php.
	 *
	 * @param string $label Label to show to users
	 * @param string $name Name of the action to call
	 * @param string $module Name of module, defaults to "core"
	 * @return void
	 */
	static public function register_function($label,$name,$module='core')
	{
      if( empty($name) || empty($label) ) return FALSE;

      $name = $module.'::'.$name;
	  self::$_list[$name] = $label;
    }

	/**
	 * Gets a list of the registered bulk operations.
	 *
	 * @param bool $separate_modules Split out the actions from various modules
	 *                                  with a horizontal line.
	 * @return array The list of operations
	 */
	static public function get_operation_list($separate_modules = true)
    {
		$tmpc = array();
		$tmpm = array();
		foreach( self::$_list as $name => $label ) {
			if( startswith($name,'core::') ) {
				$tmpc[$name] = $label;
			}
			else {
				$tmpm[$name] = $label;
			}
		}

		if( $separate_modules && count($tmpm) ) {
			$tmpc[-1] = '----------';
		}
		$tmpc = array_merge($tmpc,$tmpm);
		return $tmpc;
    }
} // end of class

?>