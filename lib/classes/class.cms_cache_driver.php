<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Class: cms_cache_driver (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  An abstract class for caching data for CMSMS.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
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
 * An abstract cache driver.
 *
 * @package CMS
 * @license GPL
 */

/**
 * An abstract class for various cache drivers
 *
 * @since 2.0
 * @author Robert Campbell
 * @package CMS
 * @license GPL
 */
abstract class cms_cache_driver
{
  /**
   * Clear all cached values in a group.
   * If the $group parameter is not specified, use the current group
   *
   * @param string $group
   */
  abstract public function clear($group = '');

  /**
   * Retrieve a cached value
   * If the $group parameter is not specified, use the current group
   *
   * @param string $key
   * @param string $group
   * @return mixed
   */
  abstract public function get($key,$group = '');

  /**
   * Test if a cached value exists
   * If the $group parameter is not specified, use the current group
   *
   * @param string $key
   * @param string $group
   * @return bool
   */
  abstract public function exists($key,$group = '');

  /**
   * Erase a cached value.
   * If the $group parameter is not specified, use the current group
   *
   * @param string $key
   * @param string $group
   */
  abstract public function erase($key,$group = '');

  /**
   * Add a cached value
   * If the $group parameter is not specified, use the current group
   *
   * @param string $key
   * @param mixed  $value
   * @param string $group
   */
  abstract public function set($key,$value,$group = '');

  /**
   * Set A current group
   *
   * @param string $group
   */
  abstract public function set_group($group);
}

?>