<?php
namespace CMSMS\internal;
use CmsApp;
use cms_config;
use cms_userprefs;
use cms_siteprefs;

class AdminThemeManager
{
    private $theme_path;

    public function __construct( string $theme_path )
    {
        if( !is_dir($theme_path) ) throw new \InvalidArgumentException('Invalid theme_path passed to '.__METHOD__);
        $this->theme_path = $theme_path;
    }

    public function list_themes()
    {
        $files = glob($this->theme_path.'/*/*Theme.php');
        if( empty($files) ) return;

        $out = null;
        foreach( $files as $file ) {
            $file_name = basename($file);
            $theme_name = basename(dirname($file));
            if( startswith($file_name,$theme_name) ) {
                $class_name = substr($file_name,0,-4);
                $out[] = [ 'theme'=>$theme_name, 'class'=>$class_name, 'file'=>$file ];
            }
        }
        return $out;
    }

    public function get_default_themename() : string
    {
        $themes = $this->list_themes();
        if( empty($themes) ) throw new \LogicException('Could not find a default admin theme');
        return $themes[0]['theme'];
    }

    public function load_theme(string $theme_name, CmsApp $app, int $uid)
    {
        $themes = $this->list_themes();
        $found = null;
        foreach( $themes as $rec ) {
            if( $rec['theme'] == $theme_name ) {
                $found = $rec;
                break;
            }
        }
        if( !$found ) return;

        $class = $found['class'];
        if( !class_exists($class) ) {
            include_once( $found['file'] );
        }
        $obj = new $class($app, $uid);
        return $obj;
    }

} // class