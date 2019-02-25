<?php
namespace CMSMS;
use CmsApp;

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

    public function __construct( CmsApp $app )
    {
        $this->_hook_manager = $app->get_hook_manager();
    }

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

    public function render( string $output_path, $force = false )
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
        $sig = md5( __FILE__.$t_sig.$t_mtime );

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