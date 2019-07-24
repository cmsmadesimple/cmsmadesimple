<?php

/**
 * This file implements CMSMS hook functionality.
 *
 * @deprecated.
 * @see CmsApp::get_hook_manager()
 * @package CMS
 * @license GPL
 * @since 2.2
 */

namespace CMSMS;
use CMSMS\internal\hook_defn;
use CMSMS\internal\hook_handler;

/**
 * A class to add hook handlers and emit hooks of various types.
 *
 * This class is capable of managing a flexible list of hooks, registering handlers for those hooks, and calling the handlers
 *
 * @package CMS
 * @license GPL
 * @since 2.3
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class hook_manager
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
    public static function add_hook(string $name,callable $callable,int $priority = self::PRIORITY_NORMAL)
    {
        $name = trim($name);
        if( !isset(self::$_hooks[$name]) ) self::$_hooks[$name] = new hook_defn($name);
        self::$_hooks[$name]->sorted = false;
        $hash = self::calc_hash($callable);
        self::$_hooks[$name]->handlers[$hash] = new hook_handler($callable,$priority);
    }

    /**
     * Test if we are currently handling a hook or not.
     *
     * @param null|string $name The hook name to test for.  If null is provided, the system will return true if any hook is being processed.
     * @return bool
     */
    public static function in_hook(string $name = null)
    {
        if( !$name ) return (count(self::$_in_process) > 0);
        return in_array($name,self::$_in_process);
    }

    /**
     * Emit a hook, progressively altering the value of the input.  i.e: a filter.
     *
     * This method accepts variable arguments.  The first argument (required) is the name of the hook to execute.
     * Further arguments will be passed to the various handlers.
     *
     * @return mixed The output of this method depends on the hook.
     */
    public static function emit()
    {
        $is_assoc = function($in) {
            if( !is_array($in) ) return FALSE;
            return array_keys($in) !== range(0, count($in) - 1);
        };
        $args = func_get_args();
        $name = array_shift($args);
        $name = trim($name);
        $value = $args;
        if( is_array($value) && count($value) == 1 && isset($value[0]) ) {
            $value = $value[0];
        }

        if( !isset(self::$_hooks[$name]) || !count(self::$_hooks[$name]->handlers) ) return $value; // nothing to do

        // note: $args is an array
        self::$_in_process[] = $name;

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
                    $res = call_user_func($obj->callable, $value);
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
     * Emit a hook, returning the first non empty value.
     *
     * This method accepts variable arguments.  The first argument (required) is the name of the hook to execute.
     * Further arguments will be passed to the various handlers.
     *
     * This method will always pass the same input arguments to each hook handler.
     *
     * @return mixed The output of this method depends on the hook.
     */
    public static function emit_first_result()
    {
        $is_assoc = function($in) {
            if( !is_array($in) ) return FALSE;
            return array_keys($in) !== range(0, count($in) - 1);
        };
        $args = func_get_args();
        $name = array_shift($args);
        $name = trim($name);
        $value = $args;

        if( !isset(self::$_hooks[$name]) || !count(self::$_hooks[$name]->handlers)  ) return $value; // nothing to do.

        // note $args is an array
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
     * Emit a hook, accumulating the results of each hook handler into an array.
     *
     * This method accepts variable arguments.  The first argument (required) is the name of the hook to execute.
     * Further arguments will be passed to the various handlers.
     *
     * The data returned in the $params array will be appended to the output array.
     *
     * @return array Mixed data, as it cannot be ascertained what data is passed back from handlers.
     */
    public static function emit_accumulate()
    {
        $is_assoc = function($in) {
            if( !is_array($in) ) return FALSE;
            return array_keys($in) !== range(0, count($in) - 1);
        };
        $args = func_get_args();
        $name = array_shift($args);
        $name = trim($name);
        $value = $args;

        if( !isset(self::$_hooks[$name]) || !count(self::$_hooks[$name]->handlers) ) return $value; // nothing to do.

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
        self::$_in_process[] = $name;

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

} // class
