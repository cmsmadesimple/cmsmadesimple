<?php
namespace FilePicker;

class PathAssistant
{
    private $_topdir;
    private $_topurl;

    public function __construct(\cms_config $config, $topdir)
    {
        if( !$topdir || !is_dir($topdir) ) throw new \LogicException('Invalid topdir passed to '.__METHOD__);
        if( !$this->is_relative_to( $topdir, $config['root_path'] ) ) throw new \LogicException('Invalid topdir passed to '.__METHOD__);

        if( endswith( $topdir, '/') ) $topdir = substr($topdir,0,-1);
        $this->_topdir = trim($topdir);

        // now, look at the image uplaods path, the image path, the admin path, and the root path
        if( $this->is_relative_to( $this->_topdir, $config['image_uploads_path']) ) {
            $rel_url = $this->to_relative_sub( $this->_topdir, $config['image_uploads_path'] );
            $this->_topurl = $config['image_uploads_url'].'/'.$rel_url;
        }
        else if( $this->is_relative_to( $this->_topdir, $config['uploads_path']) ) {
            $rel_url = $this->to_relative_sub( $this->_topdir, $config['uploads_path'] );
            $this->_topurl = $config['uploads_url'].'/'.$rel_url;
        }
        else if( $this->is_relative_to( $this->_topdir, $config['admin']) ) {
            $rel_url = $this->to_relative_sub( $this->_topdir, $config['admin'] );
            $this->_topurl = $config['admin_url'].'/'.$rel_url;
        }
        else if( $this->is_relative_to( $this->_topdir, $config['root_path']) ) {
            $rel_url = $this->to_relative_sub( $this->_topdir, $config['root_path'] );
            $this->_topurl = $config['root_url'].'/'.$rel_url;
        }
    }

    protected function to_relative_sub( $path_a, $path_b )
    {
        $path_a = realpath( $path_a );
        $path_b = realpath( $path_b );
        if( !is_dir($path_a) && !is_file($path_a) ) throw new \LogicException('Invalid path_a passed to '.__METHOD__.': '.$path_a);
        if( !is_dir($path_b) ) throw new \LogicException('Invalid path_b passed to '.__METHOD__.': '.$path_b);

        if( !$this->is_relative_to( $path_a, $path_b ) ) throw new \LogicException("$path_a is not relative to $path_b");
        $out = substr($path_a,strlen($path_b));
        if( startswith($out,'/') ) $out = substr($out,1);
        return $out;

    }

    public function get_top_url()
    {
        return $this->_topurl;
    }

    public function is_relative_to( $path_a, $path_b )
    {
        $path_a = realpath( $path_a );
        $path_b = realpath( $path_b );
        if( !$path_a || ! $path_b ) return FALSE;

        return startswith( $path_a, $path_b);
    }

    public function is_relative( $path )
    {
        return $this->is_relative_to( $path, $this->_topdir );
    }

    public function to_relative( $path )
    {
        return $this->to_relative_sub( $path, $this->_topdir );
    }

    public function to_absolute( $relative )
    {
        return $this->_topdir.'/'.$relative;
    }

    public function relative_path_to_url( $relative )
    {
        $relative = trim($relative);
        if( startswith($relative,'/') ) $relative = substr($relative,1);
        $prefix = $this->get_top_url();
        if( endswith( $prefix, '/') ) $prefix = substr($prefix,0,-1);
        return $prefix.'/'.$relative;
    }

    public function is_valid_relative_path( $str )
    {
        $str = trim($str);
        $absolute = $this->to_absolute($str);
        return $this->is_relative( $absolute );
    }
} // end of class
