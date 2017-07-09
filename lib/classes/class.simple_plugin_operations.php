<?php
namespace CMSMS;

final class simple_plugin_operations
{
    /**
     * @ignore
     */
    private $_loaded = [];

    /**
     * List all known simple plugins.
     * Reads from the assets/simple_plugins directory.
     *
     * @since 2.3
     * @return string[]|null
     */
    public function get_list()
    {
        $config = \cms_config::get_instance();
        $dir = $config['assets_path'].'/simple_plugins';
        $files = glob($dir.'/*.php');
        if( !count($files) ) return null;

        $out = null;
        foreach( $files as $file ) {
            $name = substr(basename($file),0,-4);
            if( !$this->is_valid_plugin_name( $name ) ) continue;
            $out[] = $name;
        }
        return $out;
    }

    protected function get_plugin_filename( $name )
    {
        $config = \cms_config::get_instance();
        $name = $config['assets_path'].'/simple_plugins/'.$name.'.php';
        return $name;
    }

    public function plugin_exists( $name )
    {
        if( !$this->is_valid_plugin_name( $name ) ) throw new \LogicException("Invalid name passed to ".__METHOD__);
        $filename = $this->get_plugin_filename( $name );
        if( is_file($name) ) return TRUE;
    }

    public function is_valid_plugin_name($name)
    {
        $name = trim($name);
        if( !$name ) return FALSE;
        if( preg_match('<^[ a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$>',$name) == 0 ) return FALSE;
        return TRUE;
    }

    public function load_plugin($name)
    {
        $name = trim($name);
        if( !$this->is_valid_plugin_name( $name ) ) throw new \LogicException("Invalid name passed to ".__METHOD__);
        if( !isset($this->_loaded[$name]) ) {
            $file_name = $this->get_plugin_filename( $name );
            if( !is_file($file_name) ) throw new \RuntimeException('Could not find simple plugin named '.$name);
            $function_name = 'cms_simple_plugin_'.$name;

            $code = trim(file_get_contents($file_name));
            if( startswith( $code, '<?php' ) ) $code = substr($code,5);
			if( endswith($code,'?>') ) $code = substr($code,0,-2);
            $code = trim($code);
            if( !$code ) throw new \RuntimeException('Empty simple_plugin named '.$name);
            $code = 'function '.$function_name.'($params,&$template) {'.$code."\n}";
            @eval($code);
            $this->_loaded[$name] = $function_name;
        }
        return $this->_loaded[$name];
    }

    /*
    public function call_plugin($name,array $params,\CMS_Smarty_Template &$template)
    {
        if( !$this->is_valid_plugin_name( $name ) ) throw new \LogicException("Invalid name passed to ".__METHOD__);

        $function_name = $this->load_plugin( $name );
        if( $function_name ) {
            $result = call_user_func_array($function_name, array($params, $template));
            return $result;
        }
    }
    */
} // end of filex