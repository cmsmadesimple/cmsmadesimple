<?php
/**
 * This class provides tools to interact with javascript files
 *
 * @package CMS
 * @since 2.3
 * @license GPL
 */
namespace CMSMS;
use CmsApp;

/**
 * The ScriptManager class provides the ability to queue javascript files for rendering on the frontend
 *
 * @package CMS
 * @since 2.3
 * @license GPL
 */
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

    /**
     * Constructor
     *
     * @param CmsApp $app
     */
    public function __construct( CmsApp $app )
    {
        $this->_hook_manager = $app->get_hook_manager();
    }

    /**
     * Returns the current script priority for addng new scripts.
     *
     * @return int
     */
    public function get_script_priority() : int
    {
        return $this->_script_priority;
    }

    /**
     * Set the priority for scripts that are subsequently queued
     *
     * @param int $val An integer between 1 and 3.  1 being the higest priority.
     */
    public function set_script_priority( int $val )
    {
        $this->_script_priority = max(1,min(3,$val));
    }

    /**
     * Add a script to the queue.
     *
     * @param string $filename The absolute path to the filename
     * @param int $priority An optional priority.  If not specified the 'current' script priority will be used.  Which defaults to 2.
     */
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

    /**
     * Render scripts.
     * This method will analyze the queued scripts, and generate an output filename.
     * if any of the queued scripts is newer than the generated output file (or the file does not exist)
     * Then the scripts will be concatenated and placed in the output file.
     *
     * @param string $output_path The location to place the concatenated scripts
     * @param string $entropy Some aditional entropty to use when generating the output filename.
     * @param bool $force If true the output will be generated without analyzing whether input files have changed.
     * @param bool $allow_defer Enable the defer attribute on the script tag
     */
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
        $t_scripts = $this->_hook_manager->emit( 'Core::PreDetermineScripts', $this->_scripts );
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
                // process each individual script, useful for things like changing paths
                $this->_hook_manager->emit('Core::ProcessScript', [ 'content'=>&$content, 'file'=>$rec['file']]);
                $output .= $content."\n\n";
            }
            // post process the combined script, for minifying etc.
            $tmp = $this->_hook_manager->emit('Core::PostProcessScripts', $output);
            if( $tmp ) $output = $tmp;
            file_put_contents( $output_file, $output );
        }
        return $js_filename;
    }
} // class
