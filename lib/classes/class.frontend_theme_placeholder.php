<?php

/**
 * This file contains the class that defines a placeholder for a file based theme.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 */

namespace CMSMS;

/**
 * A class that acts as a placeholder for a frontend theme.
 * the frontend theme is used by the cms_theme resource to display content
 *
 * @since 2.3
 * @package CMS
 * @license GPL
 * @property-read string $name  The name for this theme.  Must be unique for the instalation.
 * @property-read string $extends_theme Optional name of a theme that this theme extends.
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 */
class frontend_theme_placeholder
{
    /**
     * @ignore
     */
    private $name;

    /**
     * @ignore
     */
    private $data;

    /**
     * Constructor
     *
     * Valid options include:
     *   location: (string) The file system path (or uri) where the theme files can be found.
     *   urlbase: (string) the complete URL to the directory where this theme exists.  Used for creating font, js, and image URL's.
     *  'extends_theme': (string, optional) - The name of the theme that this theme extends
     *  'page_templates': array - An array of records containing information about page templates exported by this theme.
     *     Each page templates element contains a 'label', and 'template' key defining the page templates to allow use in the content manager.
     *   i.e:
     *  page_templates = [
     *     [ 'label'=>'Home Page', 'template'=>'HomePage.tpl' ],
     *     [ 'label'=>'Internal Page', 'template'=>'InternalPage.tpl' ],
     *  ];
     *
     * @param string $name The theme name
     * @param array $options An associative array of options.
     */
    public function __construct(string $name, array $options)
    {
        $name = trim($name);
        if( !$name ) throw new \InvalidArgumentException('Invalid parameters passed to '.__METHOD__);
        $options['name'] = $name;
        // todo: validate options
        $this->data = $options;
    }

    /**
     * @ignore
     */
    public function __get(string $key)
    {
        switch( $key ) {
        case 'name':
        case 'extends_theme':
            if( isset($this->data[$key]) ) return $this->data[$key];
            return;

        default:
            throw new \LogicException("$key is not a gettable property of ".__CLASS__);
        }
    }

    /**
     * Get the list of page templates exported by this module.
     *
     * @return array|null An array of page template definitions.  Each element of the array is an associative array with label and template keys.
     */
    public function get_exported_page_templates()
    {
        if( isset($this->data['page_templates']) ) return $this->data['page_templates'];
        return;
    }

    /**
     * Get the location where theme files can be located.
     *
     * @return string
     */
    public function get_location() : string
    {
        if( !isset($this->data['location']) ) throw new \LogicException('No location available for files in this theme');
        return $this->data['location'];
    }

    /**
     * Get the URL where theme files can be located.
     * 
     * @return string
     */
    public function get_urlbase() : string
    {
        if( !isset($this->data['urlbase']) ) throw new \LogicException('No urlbase available for files in this theme');
        return $this->data['urlbase'];
    }

    /**
     * Test whether this theme has the named template.
     *
     * @param string $template_name
     * @return bool
     */
    public function has_template(string $template_name) : bool
    {
        return is_file($this->get_template_file($template_name));
    }

    /**
     * Get the filesystem path (or URI) to the named template.
     *
     * @param string $template_name
     * @return string
     */
    public function get_template_file(string $template_name) : string
    {
        return $this->get_location().'/templates/'.$template_name;
    }

} // class
