<?php
namespace cms_autoinstaller;
use \PharData;

class design_importer
{
    private $archive;
    private $phar;
    private $dest;
    private $info;

    public function __construct( string $filename, string $destdir )
    {
        if( !is_file($filename) ) throw new \InvalidArgumentException('Invalid filename passed to '.__METHOD__);
        if( !is_dir($destdir) || !is_writable($destdir) ) throw new \InvalidArgumentException('Invalid destdir passed to '.__METHOD__);
        $this->archive = $filename;
        $this->dest = $destdir;
        $zip = new \ZipArchive;
        if( $zip->open( $filename ) ) {
            $data = $zip->getFromName( 'info.ini' );
            $zip->close();
            if( !$data ) throw new \InvalidArgumentException( 'Invalid archive passed to '.__METHOD__);
            $this->info = $this->read_design_info( $data);
        }
    }

    protected function read_design_info( string $raw_data )
    {
        $info = parse_ini_string( $raw_data, TRUE );
        if( !is_array($info) || !isset($info['design']['name']) ) {
            throw new \InvalidArgumentException( 'Invalid archive passed to '.__METHOD__);
        }

        // must have at least one template
        // stylesheets are optional.
        $keys = array_keys( $info );
        if( !isset($info['design']['templates']) || empty($info['design']['templates']) ) {
            foreach( $keys as $key ) {
                if( startswith( $key, 'template:') ) {
                    $name = substr( $key, strlen('template:') );
                    $info['design']['templates'][] = $name;
                }
            }
        }
        if( !isset($info['design']['templates']) || empty($info['design']['templates']) ) {
            throw new \InvalidArgumentException( 'No templates in archive passed to '.__METHOD__);
        }

        if( !isset($info['design']['stylesheets']) || empty($info['design']['stylesheets']) ) {
            foreach( $keys as $key ) {
                if( startswith( $key, 'stylesheet:') ) {
                    $name = substr( $key, strlen('stylesheet:') );
                    $info['design']['stylesheets'][] = $name;
                }
            }
        }

        // for each defined template, verify it
        foreach( $info['design']['templates'] as $tpl_name ) {
            $key = 'template:'.$tpl_name;
            if( !isset($info[$key]) ) throw new \InvalidArgumentException( 'Invalid data in info.ini (missing section) - '.$key);
            if( !isset($info[$key]['file']) ) throw new \InvalidArgumentException( 'Invalid data in info.ini (invalid template section - '.$key);
        }
        return $info;
    }

    public function get_name()
    {
        return $this->info['design']['name'];
    }

    protected function extract_files()
    {
        $dirname = $this->dest.'/'.$this->get_name();
        if( !is_dir($dirname) ) {
            $res = mkdir( $dirname );
            if( !$res ) throw new \RuntimeException('Problem creating directory at '.$dirname);
        }
        $zip = new \ZipArchive;
        if( $zip->open( $this->archive ) === TRUE ) {
            $zip->extractTo( $dirname );
            $zip->close();
            return $dirname;
        }
    }

    protected function filter_template( $content )
    {
        // intend to be overridden
        return $content;
    }

    protected function filter_css( $content )
    {
        // intend to be overridden
        return $content;
    }

    protected function import_template( $tpl_name, $dirname, \CmsLayoutCollection $design )
    {
        $key = 'template:'.$tpl_name;
        $section = $this->info[$key];
        $filename = $dirname.'/'.$section['file'];
        if( !is_file($filename) ) throw new \RuntimeException('Could not find template file '.$filename.' in extracted directory');
        $data = file_get_contents( $filename );
        if( !$data ) throw new \RuntimeException('Could not read template file '.$filename.' in extracted directory');
        $data = $this->filter_template( $data );

        try {
            $tpl = \CmsLayoutTemplate::load( $tpl_name );
            $tpl->delete();
        }
        catch( \CmsDataNotFoundException $e ) {
            // not an error.
        }

        $type_str = $type_ob = null;
        $type_str = (isset($section['type'])) ? $section['type'] : 'Core::Page';
        $type_ob = \CmsLayoutTemplateType::load( $type_str );
        if( !$type_ob ) {
            $type_ob = \CmsLayoutTemplateType::load( 'Core::Generic' );
        }
        $tpl = new \CmsLayoutTemplate();
        $tpl->set_name( $tpl_name );
        $tpl->set_type( $type_ob );
        $tpl->set_content($data);
        $tpl->add_design( $design );
        if( isset( $section['description']) && !empty($section['description']) ) {
            $tpl->set_description( $section['description'] );
        }
        if( isset( $section['listable']) ) {
            $tpl->set_listable( $section['listable'] );
        }
        $tpl->save();
        return $tpl;
    }

    protected function import_stylesheet( $css_name, $dirname, \CmsLayoutCollection $design )
    {
        $key = 'stylesheet:'.$css_name;
        $section = $this->info[$key];
        $filename = $dirname.'/'.$section['file'];
        if( !is_file($filename) ) throw new \RuntimeException('Could not find css file '.$section['file'].' in extracted directory');
        $data = file_get_contents( $filename );
        if( !$data ) throw new \RuntimeException('Could not read css file '.$section['file'].' in extracted directory');
        $data = $this->filter_css( $data );

        try {
            $css = \CmsLayoutStylesheet::load( $css_name );
            $css->delete();
        }
        catch( \CmsInvalidDataException $e ) {
            // not an error.
        }

        $css_ob = new \CmsLayoutStylesheet();
        $css_ob->set_name( $css_name );
        $css_ob->set_content( $data );
        $css_ob->add_design( $design );
        if( isset( $section['description']) && !empty($section['description']) ) {
            $css_ob->set_description( $section['description'] );
        }
        $css_ob->save();
        return $css_ob;
    }

    public function import_design()
    {
        $page_type = \CmsLayoutTemplateType::load('Core::Page');
        if( !$page_type ) throw new \RuntimeException('Could not get template type object for Core::Page');

        $dirname = $this->extract_files();

        // create the design object
        $design = new \CmsLayoutCollection();
        $label = $this->info['design']['label'];
        if( !$label ) $label = $this->get_name();
        $design->set_name( $label );
        if( ($str = $this->info['design']['description']) ) {
            $design->set_description( $str );
        }
        $design->save(); // now have an id.

        foreach( $this->info['design']['templates'] as $tpl_name ) {
            $tpl = $this->import_template( $tpl_name, $dirname, $design );
        }

        if( !empty($this->info['design']['stylesheets']) ) {
            // import each stylesheet
            foreach( $this->info['design']['stylesheets'] as $css_name ) {
                $this->import_stylesheet( $css_name, $dirname, $design );
            }
        }

        return $design;
    }
} // class
