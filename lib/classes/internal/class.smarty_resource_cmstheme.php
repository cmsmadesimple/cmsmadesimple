<?php
namespace CMSMS\Internal;
use \Smarty_Template_Source;
use \Smarty_Internal_Template;
use cms_utils;

/**
 * A resource like the standard file: resource in smarty
 * except that it enforces that templates exist below one of the template directories (no absolute filenames, or ../../ stuff to get out of it.
 * and does not support dot files.
 *
 * it also supports the ;top ;head and ;body section suffixes to process only a portion of the template.
 */
class smarty_resource_cmstheme extends fixed_smarty_custom_resource
{
    protected function fetch($name,&$source,&$mtime)
    {
        // cms_theme:template
        // cms_theme:theme;template
        // cms_theme:theme;template;section
        $theme = $template = $section = null;
        $parts = explode(';',$name);

        switch( count($parts) ) {
        case 1:
            $template = $parts[0];
            break;
        case 2:
            $theme = $parts[0];
            $template = $parts[1];
            break;
        case 3:
        default:
            $theme = $parts[0];
            $template = $parts[1];
            $section = $parts[1];
            break;
        }
        $theme = trim($theme);
        $template = trim($template);
        $section = trim($section);

        while(startswith($template,'/')) {
            $template = substr($template,1);
        }
        $bn = basename( $template );
        if( startswith($bn, '.') ) return;
        if( !$template ) return;

        if( !$theme ) {
            $theme = $this->smarty->theme_manager()->get_current_theme();
            if( !$theme ) throw new \LogicException("No current theme name detected when using resource cms_theme:$name");
        } else {
            $this->smarty->theme_manager()->set_current_theme($theme);
        }

        $real_file = $this->smarty->theme_manager()->resolve_template($theme, $template);
        if( !$real_file ) die('failed to resolve');
        if( !$real_file ) return;

        // if we got here, we're golden
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
