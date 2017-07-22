<?php
namespace CMSMS;

final class simple_plugin_operations
{
    /**
     * @ignore
     */
    private static $_instance;
    private $_loaded = [];

    public function __construct()
    {
        if( self::$_instance ) throw new \LogicException('Cannot create more than one instance of '.__CLASS__);
        self::$_instance = $this;
    }

    protected static function get_instance()
    {
        return self::$_instance;
    }

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
        // test if the simple plugin exists
        // output is a string like: '\\CMSMS\\simple_plugin::the_name';
        // uses callstatic to  invoke,  which finds the file and includes it.
        $name = trim($name);
        if( !$this->is_valid_plugin_name( $name ) ) throw new \LogicException("Invalid name passed to ".__METHOD__);
        if( !isset($this->_loaded[$name]) ) {
            $file_name = $this->get_plugin_filename( $name );
            if( !is_file($file_name) ) throw new \RuntimeException('Could not find simple plugin named '.$name);

            $code = trim(file_get_contents($file_name));
            if( !startswith( $code, '<?php' ) ) throw new \RuntimeException('Invalid format for simple plugin '.$name);

            $this->_loaded[$name] = "\\CMSMS\\simple_plugin_operations::$name";
        }
        return $this->_loaded[$name];
    }

    public static function __callStatic($name,$args)
    {
        // invoking simple_plugin_operations::call_abcdefg
        // get the appropriate filename
        // include it.
        $fn = self::get_instance()->get_plugin_filename( $name );
        if( !is_file($fn) ) throw new \RuntimeException('Could not find simple plugin named '.$name);

        // these variables are created for plugins to use in scope.
        $params = $args[0];
        $smarty = $args[1];
        include( $fn );
    }
} // end of filex
