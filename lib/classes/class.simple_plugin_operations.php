<?php

/**
 * A class for working with simple plugins
 * @package CMS
 * @license GPL
 */
namespace CMSMS;
use cms_config;

/**
 * This operations class handles reading and writing simple plugins.
 *
 * this is a singleton class.
 *
 * @package CMS
 * @license GPL
 * @since 2.3
 * @author Robert Campbell
 */
final class simple_plugin_operations
{

    /**
     * @ignore
     */
    private static $_instance;

    /**
     * @ignore
     */
    private $_loaded = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        if( self::$_instance ) throw new \LogicException('Only one instance of '.__CLASS__.' is permitted');
        self::$_instance = $this;
    }

    /**
     * Get the single instance
     *
     * @return simple_plugin_operations
     */
    public static function get_instance() : simple_plugin_operations
    {
        if( !self::$_instance ) throw new \LogicException('An instance of '.__CLASS__.' has not been created yet');
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
        $dir = CMS_ASSETS_PATH.'/simple_plugins';
        $files = glob($dir.'/*.cmsplugin');
        if( !count($files) ) return null;

        $out = null;
        foreach( $files as $file ) {
            $name = substr(basename($file),0,strlen('.cmsplugin')*-1);
            if( !$this->is_valid_plugin_name( $name ) ) continue;
            $out[] = $name;
        }
        return $out;
    }

    /**
     * Get the filename where a plugin should be stored or read from.
     *
     * @param string $name The plugin name
     * @return string
     */
    protected function get_plugin_filename( string $name ) : string
    {
        $name = CMS_ASSETS_PATH.'/simple_plugins/'.$name.'.cmsplugin';
        return $name;
    }

    /**
     * Test if a plugin exists
     *
     * @param string $name The plugin name
     * @return bool
     */
    public function plugin_exists( string $name )
    {
        if( !$this->is_valid_plugin_name( $name ) ) throw new \LogicException("Invalid name passed to ".__METHOD__);
        $filename = $this->get_plugin_filename( $name );
        if( is_file($filename) ) return TRUE;
    }

    /**
     * Test whether a name is a suitable plugin name
     *
     * @param string $name The plugin name
     * @return bool
     */
    public function is_valid_plugin_name($name) : bool
    {
        $name = trim($name);
        if( !$name ) return FALSE;
        if( preg_match('<^[ a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$>',$name) == 0 ) return FALSE;
        return TRUE;
    }

    /**
     * Load a plugin
     *
     * @param string $name The plugin name
     * @return string The name of the callable that this plugin is loaded into.
     */
    public function load_plugin(string $name)
    {
        // test if the simple plugin exists
        // output is a string like: '\\CMSMS\\simple_plugin::the_name';
        // uses callstatic to invoke,  which finds the file and includes it.
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

    /**
     * Call a user plugin
     *
     * @param string $name The plugin name to call
     * @param array $args Optional plugin arguments
     * @return mixed
     */
    public function call_plugin( string $name, array $args = [] )
    {
        return self::__callStatic( $name, [ $args, cmsms()->GetSmarty() ] );
    }

    /**
     * @ignore
     */
    public static function __callStatic(string $name,array $args = null)
    {
        // invoking simple_plugin_operations::call_abcdefg
        // get the appropriate filename
        // include it.
        $fn = self::get_instance()->get_plugin_filename( $name );
        if( !is_file($fn) ) throw new \RuntimeException('Could not find simple plugin named '.$name);

        // these variables are created for plugins to use in scope.
        $params = $args[0];
        $smarty = null;
        $gCms = cmsms(); // put this in scope.
        if( isset($args[1]) ) $smarty = $args[1];
        include( $fn );
    }
} // end of filex
