<?php
/**
 * A class to manage the queuing and output of stylesheets.
 *
 * @package CMSMS
 * @license GPL
 */
namespace CMSMS;
use CmsApp;

/**
 * This class is the backend behind the {cms_queue_css} and {cms_render_css} plugins.
 * It handles queueing CSS files, and then concatenating them all on output, and generating the
 * appropriate HTML meta tag.
 *
 * @package CMS
 * @license GPL
 * @since 2.3
 */
class StylesheetManager
{

    /**
     * @ignore
     */
    private $_files = [];

    /**
     * @ignore
     */
    private $_priority = 2;

    /**
     * @ignore
     */
    private $_hook_manager;

    /**
     * @ignore
     */
    public function __construct( CmsApp $app )
    {
        $this->_hook_manager = $app->get_hook_manager();
    }

    /**
     * enqueue a CSS file.
     *
     * @param string $filename The absolute path to the CSS file
     * @param int $priority An optional priroity, this is useful in sorting on render.
     */
    public function queue( string $filename, int $priority = null )
    {
        if( !is_file($filename) ) return;

        $sig = md5( $filename );
        if( isset( $this->_files[$sig]) ) return;
        if( is_null( $priority ) ) $priority = $this->_script_priority;
        $priority = max(1,min(3,$priority));

        $this->_files[$sig] = [
            'file'=>$filename,
            'mtime'=>filemtime( $filename ),
            'priority'=>$priority,
            'index' => count( $this->_files )
        ];
    }

    /**
     * Process queued CSS files, and render the appropriate meta tag.
     * This method will sort the CSS files by their priority and the order added.
     * Then call hooks on each file to preprocess them.
     * Then concatenate the processed output.
     * Call a hook on the resulting concatenated file to post process it
     * Then output the file to the specified directory
     * And return the filename.
     *
     * This method is intelligent in that it will not perform CSS processing IF the input files have not changed
     * since the last time the output file was generated.
     *
     * @param string $output_path The name of the directory where the output CSS file should be written.
     * @param string $entropy Additional data to use when generating the filename.
     * @param bool $force Force the output stylesheet to be regenerated.
     * @return string The output CSS filename.
     */
    public function render( string $output_path, string $entropy = null, bool $force = false )
    {
        if( !$this->_files && !count($this->_files) ) return; // nothing to do
        if( !is_dir($output_path) || !is_writable($output_path) ) return; // nowhere to put it

        $files = $this->_files;
        $t_files = $this->_hook_manager->do_hook( 'Core::PreProcessCSS', $this->_files );
        if( $t_files ) $files = $t_files;

        // sort the scripts by their priority, then their index (to preserve order)
        usort( $files, function( $a, $b ) {
              if( $a['priority'] < $b['priority'] ) return -1;
              if( $a['priority'] > $b['priority'] ) return 1;
              if( $a['index'] < $b['index'] ) return -1;
              if( $a['index'] > $b['index'] ) return 1;
              return 0;
        });

        // accuumulate a signature, and the max time
        $t_sig = $t_mtime = null;
        foreach( $files as $sig => $rec ) {
            $t_sig .= $sig;
            $t_mtime = max( $rec['mtime'], $t_mtime );
        }
        $sig = md5( __FILE__.$t_sig.$t_mtime.$entropy );

        $css_file = "cms_$sig.css";
        $output_file = "$output_path/$css_file";
        if( $force || !is_file($output_file) || filemtime($output_file) < $t_mtime ) {
            $output = null;
            foreach( $files as $sig => $rec ) {
                $content = file_get_contents($rec['file']);
                $output .= $content."\n\n";
            }
            $tmp = $this->_hook_manager->do_hook( 'Core::PostProcessCSS', $output );
            if( $tmp ) $output = $tmp;
            file_put_contents( $output_file, $output );
        }
        return $css_file;
    }
} // class
