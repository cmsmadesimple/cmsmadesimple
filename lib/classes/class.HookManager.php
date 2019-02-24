<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
#(c)2013-2016 by The CMSMS Dev Team
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * Contains classes and utilities for working with CMSMS hooks.
 *
 * This class provides a static interface for dealing with CMSMS hooks.
 *
 * @deprecated.
 * @see CmsApp::get_hook_manager()
 * @package CMS
 * @license GPL
 * @since 2.2
 */

namespace CMSMS;

/**
 * A class to manage hooks, and to call hook handlers.
 *
 * This class is capable of managing a flexible list of hooks, registering handlers for those hooks, and calling the handlers
 *
 * @package CMS
 * @license GPL
 * @since 2.2
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class HookManager
{
    /**
     * High priority handler
     */
    const PRIORITY_HIGH = 1;

    /**
     * Indicates a normal priority handler
     */
    const PRIORITY_NORMAL = 2;

    /**
     * Indicates a low priority handler
     */
    const PRIORITY_LOW = 3;

    /**
     * @ignore
     */
    private function __construct()
    {
    }

    /**
     * Add a handler to a hook
     *
     * @param string $name The hook name.  If the hook does not already exist, it is added.
     * @param callable $callable A callable function, or a string representing a callable function.  Closures are also supported.
     * @param int $priority The priority of the handler.
     */
    public static function add_hook($name,$callable,$priority = self::PRIORITY_NORMAL)
    {
        $args = [ $name, $callable, $priority ];
        $hook_manager = \CmsApp::get_instance()->get_hook_manager();
        return call_user_func_array( [ $hook_manager, 'do_hook' ], $args );
    }

    /**
     * Trigger a hook, progressively altering the value of the input.  i.e: a filter.
     *
     * This method accepts variable arguments.  The first argument (required) is the name of the hook to execute.
     * Further arguments will be passed to the various handlers.
     *
     * @return mixed The output of this method depends on the hook.
     */
    public static function do_hook()
    {
        $args = func_get_args();
        $hook_manager = \CmsApp::get_instance()->get_hook_manager();
        return call_user_func_array( [ $hook_manager, 'do_hook' ], $args );
    }

    /**
     * Trigger a hook, returning the first non empty value.
     *
     * This method accepts variable arguments.  The first argument (required) is the name of the hook to execute.
     * Further arguments will be passed to the various handlers.
     *
     * This method will always pass the same input arguments to each hook handler.
     *
     * @return mixed The output of this method depends on the hook.
     */
    public static function do_hook_first_result()
    {
        $args = func_get_args();
        $hook_manager = \CmsApp::get_instance()->get_hook_manager();
        return call_user_func_array( [ $hook_manager, 'do_hook_first_result' ], $args );
    }

    /**
     * Trigger a hook, accumulating the results of each hook handler into an array.
     *
     * This method accepts variable arguments.  The first argument (required) is the name of the hook to execute.
     * Further arguments will be passed to the various handlers.
     *
     * The data returned in the $params array will be appended to the output array.
     *
     * @return array Mixed data, as it cannot be ascertained what data is passed back from handlers.
     */
    public static function do_hook_accumulate()
    {
        $args = func_get_args();
        $hook_manager = \CmsApp::get_instance()->get_hook_manager();
        return call_user_func_array( [ $hook_manager, 'do_hook_accumulate' ], $args );
    }
} // end of class
