<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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
#

/**
 * Task class interface definition
 * @license GPL
 * @package CMS
 */

/**
 * An interface to define how tasks should work.
 *
 * @package CMS
 * @license GPL
 * @since 1.8
 */
interface CmsRegularTask
{
  /**
   * Get the name for this task
   *
   * @return string
   */
  public function get_name();


  /**
   * Get the description for this task.
   *
   * @return string
   */
  public function get_description();


  /**
   * Test if a function should be executed given the supplied time argument
   *
   * @param   int The time at which any comparisons for execution should be performed.  If empty the current time is assumed.
   * @returns boolean TRUE IF the task should be executed, FALSE otherwise.
   */
  public function test($time = '');


  /**
   * Execute a given task
   *
   * @param  int The time at which the task should consider the execution occurred at.  Assume the current time if empty.
   * @return boolean TRUE on success, FALSE otherwise.
   */
  public function execute($time = '');


  /**
   * Execute steps that should be taken on success of this task.
   * This method is called after the execute step if the execute step returned TRUE.
   *
   * @param  int The time at which the task should consider the execution occurred at.  Assume the current time if empty.
   * @return void
   */
  public function on_success($time = '');


  /**
   * Execute steps that should be taken on failure of this task.
   * This method is called after the execute step if the execute step returned FALSE.
   *
   * @param  int The time at which the task should consider the execution occurred at.  Assume the current time if empty.
   * @return void
   */
  public function on_failure($time = '');

} // interface

?>
