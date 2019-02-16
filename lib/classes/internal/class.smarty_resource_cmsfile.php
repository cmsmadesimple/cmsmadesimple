<?php
namespace CMSMS\Internal;
use \Smarty_Template_Source;
use \Smarty_Internal_Template;

/**
 * A resource like the standard file: resource in smarty
 * except that it enforces that templates exist below one of the template directories (no absolute filenames, or ../../ stuff to get out of it.
 * and does not support dot files.
 *
 * it also supports the ;top ;head and ;body section suffixes to process only a portion of the template.
 */
class smarty_resource_cmsfile extends fixed_smarty_custom_resource
{
    protected function fetch($name,&$source,&$mtime)
    {
        $parts = explode(';',$name);
        $section = (count($parts) == 2 ) ? $parts[1] : null;
        $name = $parts[0];

        $bn = basename( $name );
        if( startswith($bn, '.') ) return;

        // has to be a file in, or below a template directory.
        $_p_dirs = $this->smarty->getTemplateDir();
        foreach( $_p_dirs as $dir ) {
            $fn = $dir.'/'.$name;
            if( !is_file($fn) ) continue;

            // now verify that it is below $dir
            $real_file = realpath($fn);
            $real_dir = realpath($dir);
            if( !$real_file || !startswith( $real_file, $real_dir ) ) return;
        }
        if( !$real_file ) return;
        $content = file_get_contents( $real_file );
        $mtime = filemtime( $real_file );

        $source = null;
        switch( $section ) {
            case 'top':
                $pos1 = stripos($content,'<head');
                $pos2 = stripos($content,'<header');
                if( $pos1 === FALSE || $pos1 == $pos2 ) return;
                $source = trim(substr($content,0,$pos1));
                break;

            case 'head':
                $pos1 = stripos($content,'<head');
                $pos1a = stripos($content,'<header');
                $pos2 = stripos($content,'</head>');
                if( $pos1 === FALSE || $pos1 == $pos1a || $pos2 === FALSE ) return;
                $source = trim(substr($content,$pos1,$pos2-$pos1+7));
                break;

            case 'body':
                $pos = stripos($content,'</head>');
                if( $pos !== FALSE ) {
                    $source = trim(substr($content,$pos+7));
                }
                else {
                    $source = $content;
                }
                break;

            default:
                $source = $content;
                break;
        }
    } // fetch
} // class
