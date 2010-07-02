<?php

/**
 * @package CMS
 */

/**
 * An interface to define how tasks should work.
 *
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