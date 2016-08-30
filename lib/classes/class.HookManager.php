<?php
/**
 * @IGNORE
 */
namespace CMSMS\Hooks {

    use \CMSMS\HookManager;
    /**
     * @ignore
     */
    class HookHandler
    {
        public $callable;
        public $priority;

        public function __construct($callable,$priority)
        {
            // todo: test if is callable.
            $this->priority = max(HookManager::PRIORITY_HIGH,min(HookManager::PRIORITY_LOW,(int)$priority));
            $this->callable = $callable;
        }
    }

    /**
     * @ignore
     */
    class HookDefn
    {
        public $name;
        public $handlers = [];
        public $sorted;

        public function __construct($name)
        {
            $this->name = $name;
        }
    }
} // namespace

namespace CMSMS {

    class HookManager
    {
        const PRIORITY_HIGH = 1;
        const PRIORITY_NORMAL = 2;
        const PRIORITY_LOW = 3;

        private static $_hooks;
        private static $_in_process = [];

        private function __construct() {}

        private static function calc_hash($in)
        {
            if( is_object($in) ) {
                return spl_object_hash($in);
            } else if( is_callable($in) ) {
                return spl_object_hash((object)$in);
            }
        }

        public static function add_hook($name,$callable,$priority = self::PRIORITY_NORMAL)
        {
            $name = trim($name);
            if( !isset(self::$_hooks[$name]) ) self::$_hooks[$name] = new Hooks\HookDefn($name);
            self::$_hooks[$name]->sorted = false;
            $hash = self::calc_hash($callable);
            self::$_hooks[$name]->handlers[$hash] = new Hooks\HookHandler($callable,$priority);
        }

        public static function in_hook($name = null)
        {
            if( !$name ) return (count(self::$_in_process) > 0);
            return in_array($name,self::$_in_process);
        }

        public static function do_hook()
        {
            $is_assoc = function($in) {
                $keys = array_keys($in);
                $n = 0;
                for( $n = 0; $n < count($keys); $n++ ) {
                    if( $keys[$n] != $n ) return FALSE;
                }
                return TRUE;
            };
            $args = func_get_args();
            $name = array_shift($args);
            $name = trim($name);
            if( is_array($args) && count($args) == 1 && is_array($args[0]) && !$is_assoc($args[0]) ) $args = $args[0];
            $is_event = false;
            list($module,$eventname) = explode('::',$name);
            if( $module && $eventname ) $is_event = true;

            if( !isset(self::$_hooks[$name]) ) return; // nothing to do.
            if( !count(self::$_hooks[$name]->handlers) ) return; // nothing to do.

            // sort the handlers.
            if( !self::$_hooks[$name]->sorted && count(self::$_hooks[$name]->handlers) > 1 ) {
                usort(self::$_hooks[$name]->handlers,function($a,$b){
                        if( $a->priority < $b->priority ) return -1;
                        if( $a->priority > $b->priority ) return 1;
                        return 0;
                    });
                self::$_hooks[$name]->sorted = TRUE;
            }

            self::$_in_process[] = $name;
            $prev_priority = 0;
            foreach( self::$_hooks[$name]->handlers as $obj ) {
                if( $is_event && $obj->priority == self::PRIORITY_LOW && $prev_priority = self::PRIORITY_NORMAL ) {
                    $tmp = $args;
                    $tmp['from_hook'] = 1;
                    \Events::SendEvent($module,$eventname,$tmp);
                }
                call_user_func_array($obj->callable,$args);
                $prev_priority = $obj->priority;
            }
            array_pop(self::$_in_process);
        }
    } // end of class

} // namespace CMSMS
