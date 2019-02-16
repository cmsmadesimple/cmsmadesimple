<?php
namespace CMSMS\internal;
use CMSMS\HookManager;
use CMSMS\simple_plugin_operations;

// immutable
class hook_mapping_manager
{

    private $filename;

    private $data;

    static $_obj;

    public function __construct($filename)
    {
        if( $this::$_obj ) throw new \LogicException('Only one instance of '.__CLASS__.' is allowed per runtime');
        $this::$_obj = $this;

        $this->filename = $filename;
        $this->load_mapping($filename);
        $this->add_hooks();
    }

    protected function load_mapping(string $filename)
    {
        if( !is_file($filename) ) return;
        $str_data = file_get_contents($filename);
        $str_data = trim($str_data);
        if( !$str_data || $str_data == 'null' ) return;

        $data = json_decode($str_data,TRUE);
        if( !$data ) throw new \RuntimeException("Could not parse json data from ".$filename);
        if( empty($data) || !count($data) ) throw new \RuntimeException("Could not find hook mapping data from ".$filename);

        foreach( $data as $one ) {
            $this->data[] = hook_mapping::from_array( $one );
        }
    }

    protected function create_module_event_handler_wrapper(string $function, string $hook_name, string $module_name)
    {
        $filename = TMP_CACHE_LOCATION."/__{$function}.php";
        list($originator,$event_name) = explode('::',$hook_name,2);
        if( !is_file($filename) ) {
            $out = <<<EOT
<?php
function $function(\$params) {
     \$mod = \cms_utils::get_module('$module_name');
     if( \$mod ) {
         \$mod->DoEvent('$originator','$event_name',\$params);
     }
}
EOT;
            file_put_contents($filename,$out);
        }
        include_once($filename);
        return $filename;
    }

    protected function get_module_event_handler_wrapper(string $hook_name, string $module_name)
    {
        $mod = \cms_utils::get_module($module_name);
        if( !$mod ) return;
        $key = md5(__FILE__.$hook_name.$module_name);
        $function = "__hooktoevent_$key";
        $filename = $this->create_module_event_handler_wrapper($function, $hook_name, $module_name);
        if( is_file($filename) ) {
            return $function;
        }
    }

    protected function add_hooks()
    {
        if( empty($this->data) ) return;
        $spi = simple_plugin_operations::get_instance();
        array_walk( $this->data, function(hook_mapping $mapping) use ($spi) {
            if( $mapping->handlers ) {
                foreach( $mapping->handlers as $handler_name_name => $handler ) {
                    switch( $handler['type'] ) {
                        case $mapping::TYPE_SIMPLE:
                            if( $spi->plugin_exists($handler['name']) ) {
                                HookManager::add_hook($mapping->hook, [get_class($spi), $handler['name'] ]);
                            }
                            break;
                        case $mapping::TYPE_CALLABLE:
                            if( is_callable($handler['name']) ) {
                                HookManager::add_hook($mapping->hook, $handler['name']);
                            }
                            break;
                        default:
                            $function_name = $this->get_module_event_handler_wrapper($mapping->hook, $handler['name']);
                            if( $function_name ) HookManager::add_hook($mapping->hook, $function_name);
                            break;
                    }
                }
            }
        });
    }

    protected function get_mapping( string $hook )
    {
        if( is_array($this->data) && count($this->data) ) {
            foreach( $this->data as $mapping ) {
                if( $mapping->hook == $hook ) return $mapping;
            }
        }
    }

    protected function set_mapping( hook_mapping $mapping )
    {
        $out = null;
        if( is_array($this->data) && count($this->data) ) {
            foreach( $this->data as $existing ) {
                if( $mapping->hook == $existing->hook ) {
                    $out[] = $mapping;
                } else {
                    $out[] = $existing;
                }
            }
        }
        else {
            $out[] = $mapping;
        }
        $this->data = $out;
    }

    protected function remove_mapping( hook_mapping $mapping )
    {
        $out = null;
        foreach( $this->data as $existing ) {
            if( $mapping->hook == $existing->hook ) continue;
            $out[] = $mapping;
        }
        $this->data = $out;
    }

    public function write_mapping()
    {
        if( is_file($this->filename) && !is_writable($this->filename) ) {
            throw new \RuntimeException('Cannot write to '.$this->filename);
        }

        $dir = dirname($this->filename);
        if( !is_dir($dir) ) {
            $res = mkdir($dir,0775,TRUE);
            if( !$res ) throw new \RuntimeException('Could not create directory at '.$dir);
        }

        $data = json_encode($this->data, JSON_PRETTY_PRINT );
        $res = file_put_contents( $this->filename, $data );
        if( !$res ) throw new \RuntimeException('Failed writing to '.$this->filename);
    }

    public function add_handler( string $hook, string $type, string $handler )
    {
        // does not add hooks.
        $mapping = $this->get_mapping( $hook );
        if( !$mapping ) {
            $mapping = hook_mapping::from_array( [ 'hook'=>$hook, 'handlers'=>[ ['type'=>$type, 'name'=>$handler]] ] );
        }
        else {
            $mapping = $mapping->add_handler( $type, $handler );
        }
        $this->set_mapping( $mapping );
    }

    public function remove_handler( string $hook, string $type, string $handler )
    {
        $mapping = $this->get_mapping( $hook );
        if( $mapping && $mapping->has_handlers() ) {
            $mapping = $mapping->remove_handler( $type, $handler);
            if( $mapping->has_handlers() ) {
                // gotta remove this mapping all together.
                $this->remove_mapping( $mapping );
            } else {
                $this->set_mapping( $mapping );
            }
        }
    }

    /**
     * @internal
     */
    public function remove_hook( string $hook )
    {
        if( is_array($this->data) && count($this->data) ) {
            $out = null;
            foreach( $this->data as $existing ) {
                if( $hook == $existing->hook ) continue;
                $out[] = $existing;
            }
            $this->data = $out;
        }
    }
} // class
