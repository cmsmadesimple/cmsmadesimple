<?php
namespace CMSMS;
use CmsApp;

class ScriptManager
{
    /**
     * @ignore
     */
    private $_scripts = [];

    /**
     * @ignore
     */
    private $_script_priority = 2;

    /**
     * @ignore
     */
    private $_hook_manager;

    public function __construct( CmsApp $app )
    {
        $this->_hook_manager = $app->get_hook_manager();
    }

    public function get_script_priority()
    {
        return $this->_script_priority;
    }

    public function set_script_priority( int $val )
    {
        $this->_script_priority = max(1,min(3,$val));
    }

    public function queue_script( string $filename, int $priority = null )
    {
        if( !is_file($filename) ) return;

        $sig = md5( $filename );
        if( isset( $this->_scripts[$sig]) ) return;
        if( is_null( $priority ) ) $priority = $this->_script_priority;
        $priority = max(1,min(3,$priority));

        $this->_scripts[$sig] = [
          'file' => $filename,
          'mtime' => filemtime( $filename ),
          'priority' => $priority,
          'index' => count( $this->_scripts )
          ];
    }

    public function render_scripts( string $output_path, string $entropy = null, $force = false, $allow_defer = true )
    {
        if( !$this->_scripts && !count($this->_scripts) ) return; // nothing to do
        if( !is_dir($output_path) || !is_writable($output_path) ) return; // nowhere to put it

        // auto append the defer script
        if( $allow_defer ) {
            $defer_script = CMS_ROOT_PATH.'/lib/jquery/js/jquery.cmsms_defer.js';
            $this->queue_script( $defer_script, 3 );
        }

        $scripts = $this->_scripts;
        $t_scripts = $this->_hook_manager->emit( 'Core::PreProcessScripts', $this->_scripts );
        if( $t_scripts ) $scripts = $t_scripts;

        // sort the scripts by their priority, then their index (to preserve order)
        usort( $scripts, function( $a, $b ) {
              if( $a['priority'] < $b['priority'] ) return -1;
              if( $a['priority'] > $b['priority'] ) return 1;
              if( $a['index'] < $b['index'] ) return -1;
              if( $a['index'] > $b['index'] ) return 1;
              return 0;
        });

        $t_sig = $t_mtime = null;
        foreach( $scripts as $sig => $rec ) {
            $t_sig .= $sig;
            $t_mtime = max( $rec['mtime'], $t_mtime );
        }
        $sig = md5( __FILE__.$t_sig.$t_mtime.$entropy );
        $js_filename = "cms_$sig.js";
        $output_file = "$output_path/$js_filename";
        if( $force || !is_file($output_file) || filemtime($output_file) < $t_mtime ) {
            $output = null;
            foreach( $scripts as $sig => $rec ) {
                $content = file_get_contents( $rec['file'] );
                $output .= $content."\n\n";
            }
            $tmp = $this->_hook_manager->emit( 'Core::PostProcessScripts', $output );
            if( $tmp ) $output = $tmp;
            file_put_contents( $output_file, $output );
        }
        return $js_filename;
    }
} // class
