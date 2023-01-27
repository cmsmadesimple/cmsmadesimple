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
 * @package CMS
 * @license GPL
 * @since 2.2
 */

/**
 * @ignore
 */
namespace CMSMS\Hooks {

    use \CMSMS\HookManager;

    /**
     * An internal class to represent a hook handler.
     *
     * @internal
     * @ignore
     */
    class HookHandler
    {
        /**
         * @ignore
         */
        public $callable;

        /**
         * @ignore
         */
        public $priority;

        /**
         * @ignore
         */
        public function __construct($callable,$priority)
        {
            // todo: test if is callable.
            $this->priority = max(HookManager::PRIORITY_HIGH,min(HookManager::PRIORITY_LOW,(int)$priority));
            $this->callable = $callable;
        }
    }

    /**
     * An internal class to represent a hook.
     *
     * @internal
     * @ignore
     */
    class HookDefn
    {
        /**
         * @ignore
         */
        public $name;
        /**
         * @ignore
         */
        public $handlers = [];
        /**
         * @ignore
         */
        public $sorted;

        /**
         * @ignore
         */
        public function __construct($name)
        {
            $this->name = $name;
        }
    }
} // namespace

namespace CMSMS {

    /**
     * A class to manage hooks, and to call hook handlers.
     *
     * This class is capable of managing a flexible list of hooks, registering handlers for those hooks, and calling the handlers
     * and/or related events.
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
        private static $_hooks;

        /**
         * @ignore
         */
        private static $_in_process = [];

        /**
         * @ignore
         */
        private function __construct() {}

        /**
         * @ignore
         */
        private static function calc_hash($in)
        {
            if( is_object($in) ) {
                return spl_object_hash($in);
            } else if( is_callable($in) ) {
                return spl_object_hash((object)$in);
            }
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
            $name = trim($name);
            if( !isset(self::$_hooks[$name]) ) self::$_hooks[$name] = new Hooks\HookDefn($name);
            self::$_hooks[$name]->sorted = false;
            $hash = self::calc_hash($callable);
            self::$_hooks[$name]->handlers[$hash] = new Hooks\HookHandler($callable,$priority);
        }

        /**
         * Test if we are currently handling a hook or not.
         *
         * @param null|string $name The hook name to test for.  If null is provided, the system will return true if any hook is being processed.
         * @return bool
         */
        public static function in_hook($name = null)
        {
            if( !$name ) return (count(self::$_in_process) > 0);
            return in_array($name,self::$_in_process);
        }

        /**
         * Trigger a hook, progressively altering the value of the input.
         *
         * This method accepts variable arguments.  The first argument (required) is the name of the hook to execute.
         * Further arguments will be passed to the various handlers.
         *
         * If an event with the same name exists, it will be called first.  Arguments will be passed as the $params array.
         *
         * @return mixed The output of this method depends on the hook.
         */
        public static function do_hook()
        {
            $is_assoc = function($in) {
                if( !is_array($in) ) return FALSE;
                return array_keys($in) !== range(0, count($in) - 1);
            };
            $args = func_get_args();
            $name = array_shift($args);
            $name = trim($name);

            $is_event = false;
            $module = $eventname = null;
            if( strpos($name,':') !== FALSE ) list($module,$eventname) = explode('::',$name);
            if( $module && $eventname ) $is_event = true;

            if( !$is_event && ( !isset(self::$_hooks[$name]) || !count(self::$_hooks[$name]->handlers) )  ) return; // nothing to do.

            // note: $args is an array
            $value = $args;
            self::$_in_process[] = $name;
            if( $is_event && is_array($value) && count($value) == 1 && isset($value[0]) && is_array($value[0]) ) {
                // attempt to call a hook with this data.
                $data = $value[0];
                \Events::SendEvent($module,$eventname,$data);
                $value[0] = $data; // transitive.
            }
            // $value is an array, or null
            if( isset(self::$_hooks[$name]->handlers) && count(self::$_hooks[$name]->handlers) ) {
                // sort the handlers.
                if( !self::$_hooks[$name]->sorted ) {
                    if( count(self::$_hooks[$name]->handlers) > 1 ) {
                        usort(self::$_hooks[$name]->handlers,function($a,$b){
                                if( $a->priority < $b->priority ) return -1;
                                if( $a->priority > $b->priority ) return 1;
                                return 0;
                            });
                    }
                    self::$_hooks[$name]->sorted = TRUE;
                }

                foreach( self::$_hooks[$name]->handlers as $obj ) {
                    // input is passed to the callback, and can be adjusted.
                    // note it's not certain that the same data will be passed out of the handler
                    $res = null;
                    if( empty($value) || !is_array($value) || $is_assoc($value) ) {
                        $res = call_user_func($obj->callable,$value);
                    } else {
                        $res = call_user_func_array($obj->callable,$value);
                    }
                    if( !is_null($res) ) $value = $res;
                }
            }
            array_pop(self::$_in_process);
            return $value;
        }

        /**
         * Trigger a hook, returning the first non empty value.
         * This method does not call event handlers with similar names.
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
            $is_assoc = function($in) {
                if( !is_array($in) ) return FALSE;
                return array_keys($in) !== range(0, count($in) - 1);
            };
            $args = func_get_args();
            $name = array_shift($args);
            $name = trim($name);
            if( !isset(self::$_hooks[$name]) || !count(self::$_hooks[$name]->handlers)  ) return; // nothing to do.

            // note $args is an array
            $value = $args;
            self::$_in_process[] = $name;
            $res = null;

            if( isset(self::$_hooks[$name]->handlers) && count(self::$_hooks[$name]->handlers) ) {
                // sort the handlers.
                if( !self::$_hooks[$name]->sorted ) {
                    if( count(self::$_hooks[$name]->handlers) > 1 ) {
                        usort(self::$_hooks[$name]->handlers,function($a,$b){
                                if( $a->priority < $b->priority ) return -1;
                                if( $a->priority > $b->priority ) return 1;
                                return 0;
                            });
                    }
                    self::$_hooks[$name]->sorted = TRUE;
                }

                foreach( self::$_hooks[$name]->handlers as $obj ) {
                    // input is passed to the callback, and can be adjusted.
                    // note it's not certain that the same data will be passed out of the handler
                    if( empty($value) || !is_array($value) || $is_assoc($value) ) {
                        $res = call_user_func($obj->callable,$value);
                    } else {
                        $res = call_user_func_array($obj->callable,$value);
                    }
                    if( !empty( $res ) ) break;
                }
            }
            array_pop(self::$_in_process);
            return $res;
        }

        /**
         * Trigger a hook, accumulating the results of each hook handler into an array.
         *
         * This method accepts variable arguments.  The first argument (required) is the name of the hook to execute.
         * Further arguments will be passed to the various handlers.
         *
         * If an event with the same name exists, it will be called first.  Arguments will be passed as the $params array.
         * The data returned in the $params array will be appended to the output array.
         *
         * @return array Mixed data, as it cannot be ascertained what data is passed back from event handlers.
         */
        public static function do_hook_accumulate()
        {
            $is_assoc = function($in) {
                if( !is_array($in) ) return FALSE;
                return array_keys($in) !== range(0, count($in) - 1);
            };
            $args = func_get_args();
            $name = array_shift($args);
            $name = trim($name);
            //if( is_array($args) && count($args) == 1 && is_array($args[0]) && !$is_assoc($args[0]) ) $args = $args[0];
            $is_event = false;
            $module = $eventname = null;
            if( strpos($name,':') !== FALSE ) list($module,$eventname) = explode('::',$name);
            if( $module && $eventname ) $is_event = true;

            if( !$is_event && ( !isset(self::$_hooks[$name]) || !count(self::$_hooks[$name]->handlers) )  ) return; // nothing to do.

            // sort the handlers.
            if( !self::$_hooks[$name]->sorted ) {
                if( count(self::$_hooks[$name]->handlers) > 1 ) {
                    usort(self::$_hooks[$name]->handlers,function($a,$b){
                            if( $a->priority < $b->priority ) return -1;
                            if( $a->priority > $b->priority ) return 1;
                            return 0;
                        });
                }
                self::$_hooks[$name]->sorted = TRUE;
            }

            $out = [];
            $value = $args;
            self::$_in_process[] = $name;
            if( $is_event && is_array($value) && count($value) == 1 && isset($value[0]) && is_array($value[0]) ) {
                $data = $value[0];
                \Events::SendEvent($module,$eventname,$data);
                $value[0] = $data; // this may not be the same as input data.
            }
            foreach( self::$_hooks[$name]->handlers as $obj ) {
                // note: we cannot control what is passed out of the hander.
                if( empty($value) || !is_array($value) || $is_assoc($value) ) {
                    $out[] = call_user_func($obj->callable,$value);
                }
                else {
                    $out[] = call_user_func_array($obj->callable,$value);
                }
            }
            array_pop(self::$_in_process);
            return $out;
        }
    } // end of class

} // namespace CMSMS
