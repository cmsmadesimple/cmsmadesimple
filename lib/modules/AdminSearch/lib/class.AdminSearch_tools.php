<?php
use CMSMS\HookManager;

final class AdminSearch_tools
{
    private function __construct()
    {
    }

    public static function get_slave_classes()
    {
        $key = __CLASS__.'slaves'.get_userid(FALSE);
        $results = $dynamic = null;
        $tmp = HookManager::do_hook_accumulate('AdminSearch::get_slave_classes');
        if( !empty($tmp) ) {
            foreach( $tmp as $one ) {
                $name = $one['name'];
                if( !$name ) continue;
                $dynamic[$name] = $one;
            }
        }

        $driver = CmsApp::get_instance()->get_cache_driver();
        $data = $driver->get($key,__CLASS__);
        if( !$data ) {
            // cache needs refreshing.
            $results = array();

            // get module results.
            $mod = cms_utils::get_module('AdminSearch');
            $modulelist = $mod->GetModulesWithCapability('AdminSearch');
            if( is_array($modulelist) && count($modulelist) ) {
                foreach( $modulelist as $module_name ) {
                    $mod = cms_utils::get_module($module_name);
                    if( !is_object($mod) ) continue;
                    if( !method_exists($mod,'get_adminsearch_slaves') ) continue;

                    $classlist = $mod->get_adminsearch_slaves();
                    if( is_array($classlist) && count($classlist) ) {
                        foreach( $classlist as $class_name ) {
                            if( is_string($class_name) ) {
                                if( !class_exists($class_name) ) continue;
                                if( !is_subclass_of($class_name,'AdminSearch_slave') ) continue;
                                $obj = new $class_name;
                            } else if( is_object($class_name) ) {
                                $obj = $class_name;
                            }
                            if( !is_object($obj) || !is_a( $obj, 'AdminSearch_slave') ) continue;

                            $tmp = array();
                            $tmp['module'] = $module_name;
                            $tmp['class'] = get_class($obj);
                            $name = $tmp['name'] = $obj->get_name();
                            $tmp['description'] = $obj->get_description();
                            $tmp['section_description'] = $obj->get_section_description();
                            $tmp['object'] = $obj;
                            if( !$name ) continue;
                            if( isset($results[$name]) ) continue;

                            $results[$name] = $tmp;
                        }
                    }
                }
            }

            // store the results into the cache.
            $driver->set($key,serialize($results),__CLASS__);
        }
        else {
            $results = unserialize($data);
        }

        if( $dynamic ) $results = $results + $dynamic;
        return $results;
    }

    public static function summarize($text,$len = 255)
    {
        $text = strip_tags($text);
        return substr($text,0,$len);
    }
}
?>
