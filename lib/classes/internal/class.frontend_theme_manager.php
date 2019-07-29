<?php
namespace CMSMS\internal;
use CMSMS\frontend_theme_placeholder;
use cms_tree;

class frontend_theme_manager
{
    private $theme_list;
    private $theme_tree;
    private $current_theme;
    private $entry_theme;

    /**
     * Create a frontend_theme_placeholder from a path and the theme.json file that it contains.
     * This method is used internally in the registration of themes from the /assets/themes directory.
     *
     * @internal
     * @param string $path The path containing the theme, and the theme.json file
     * @param string $theme_name An optional name, if not specified, attempts will be made infer the name from the pth.
     * @return frontend_theme_placeholder
     */
    public function create_placeholder_from_path(string $path, string $theme_name = null) : frontend_theme_placeholder
    {
        if( !$path || !is_dir($path) ) throw new \InvalidArgumentException('Invalid path passed to '.__METHOD__);
        $json_file = "$path/theme.json";
        if( !is_file($json_file) ) throw new \InvalidArgumentException('Invalid path passed to '.__METHOD__.' (2)');
        if( !$theme_name ) $theme_name = basename($path);
        $json = json_decode(file_get_contents($json_file), TRUE);
        if( !$json || !is_array($json) ) throw new \InvalidArgumentException('No/Invaliid json data found in '.$json_file);
        if( isset($json['name']) && !$theme_name ) $theme_name = $json['name'];
        if( !$theme_name ) throw new \InvalidArgumentException('Invalid path passed to '.__METHOD__.' (3)');
        $json['location'] = $path;

        $assets_path = CMS_ASSETS_PATH;
        $root_path = CMS_ROOT_PATH;
        if( DIRECTORY_SEPARATOR != '/' ) {
            $path = str_replace('\\','/',$path);
            $assets_path = str_replace('\\','/',$assets_path);
            $root_path = str_replace('\\','/',$root_path);
        }
        if( startswith($path,$root_path.'/lib/modules') ) {
            $relative = substr($path,strlen($root_path.'/lib/modules'));
            $json['urlbase'] = CMS_ROOT_URL."/lib/modules/$relative";
        } else if( startswith($path,$assets_path.'/modules') ) {
            $relative = substr($path,strlen($assets_path.'/modules'));
            $json['urlbase'] = CMS_ASSETS_URL."/modules/$relative";
        } else if( startswith($path,$assets_path) ) {
            $json['urlbase'] = CMS_ASSETS_URL."/themes/$theme_name";
        }
        return new frontend_theme_placeholder($theme_name,$json);
    }

    public function get_exported_page_templates()
    {
        if( empty($this->theme_list) ) return;
        $out = null;
        foreach( $this->theme_list as $theme_name => $theme ) {
            $tmp = $theme->get_exported_page_templates();
            if( $tmp ) {
                foreach( $tmp as $one ) {
                    $out[] = [ 'label'=>$theme_name.' : '.$one['label'], 'value'=>"cms_theme:$theme_name;{$one['template']}" ];
                }
            }
        }
        return $out;
    }

    public function set_current_theme(string $theme)
    {
        //if( $this->current_theme ) return;
        $theme = trim($theme);
        $theme_obj = $this->get_theme($theme);
        if( !$theme_obj ) throw new \InvalidArgumentException("cannot set invalid theme $theme");
        $this->current_theme = $theme;
    }

    public function get_current_theme()
    {
        return $this->current_theme;
    }

    public function get_theme_path(string $theme = null) : string
    {
        if( !$theme ) $theme = $this->current_theme;
        if( !$theme ) throw new \LogicException('A theme has not been set');
        $theme_ob = $this->get_theme($theme);
        return $theme_ob->get_location();
    }

    public function get_theme_url(string $theme = null) : string
    {
        if( !$theme ) $theme = $this->current_theme;
        if( !$theme ) throw new \LogicException('A theme has not been set');
        $theme_ob = $this->get_theme($theme);
        return $theme_ob->get_urlbase();
    }

    public function register_theme(frontend_theme_placeholder $placeholder)
    {
        if( isset($this->themes[$placeholder->name]) ) throw new \InvalidArgumentException('A theme by this name is alredy registered');
        $this->theme_list[$placeholder->name] = $placeholder;
    }

    public function get_entry_theme()
    {
        return $this->entry_theme;
    }

    public function get_entry_theme_name()
    {
        if( $this->entry_theme ) return $this->entry_theme->get_tag('name');
    }

    public function resolve_template(string $theme_name, string $template_name)
    {
        if( !$this->entry_theme ) $this->entry_theme = $this->get_theme_node($theme_name);
        $node = $this->entry_theme;

        while($node) {
	    if( !$node->get_tag('name') ) break;
            $theme_obj = $this->get_theme($node->get_tag('name'));
            if( $theme_obj->has_template($template_name) ) return $theme_obj->get_template_file($template_name);
            $node = $node->get_parent();
        }
    }

    protected function get_tree() : cms_tree
    {
        if( !$this->theme_tree ) $this->theme_tree = $this->build_tree();
        return $this->theme_tree;
    }

    protected function get_theme(string $theme_name) : frontend_theme_placeholder
    {
        if( !isset($this->theme_list[$theme_name]) ) {
            throw new \InvalidArgumentException('Could not find them with name '.$theme_name);
        }
        return $this->theme_list[$theme_name];
    }

    protected function get_theme_node(string $theme_name) : cms_tree
    {
        return $this->get_tree()->find_by_tag('name',$theme_name);
    }

    protected function build_tree() : cms_tree
    {
        if( empty($this->theme_list) ) throw new \LogicException('Cannot build a tree with no theme');
        $tree = new cms_tree();
        $list = $this->theme_list;
        while( !empty($list) ) {
            $new_list = null;
            $nchanged = 0;
            foreach( $list as $theme ) {
                $parent = null;
                $node = new cms_tree('name',$theme->name);
                if( !$theme->extends_theme ) {
                    $tree->add_node($node);
                    $nchanged++;
                } else if( ($parent = $tree->find_by_tag('name', $theme->extends_theme)) ) {
                    $parent->add_node($node);
                    $nchanged++;
                }
                else {
                    $new_list[] = $theme;
                }
            }
            if( $nchanged == 0 && !empty($new_list) ) {
                $theme = $new_list[0];
                throw new \LogicException('Problem building theme tree. Could not find parent theme '.$theme->extends_theme.' for '.$theme->name);
            }
            $list = $new_list;
        }
        return $tree;
    }

} // class
