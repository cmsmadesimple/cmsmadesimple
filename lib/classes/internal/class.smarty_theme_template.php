<?php
namespace CMSMS\internal;

// a simple extension on the smarty base templat class
// that provides the cms_*_theme based plugins.
class smarty_theme_template extends smarty_base_template
{
    private $theme_manager;

    public function __construct()
    {
        parent::__construct();
        $this->registerPlugin('function', 'cms_set_theme', function($params, $tpl) {
                $theme = get_parameter_value($params,'theme');
                if( !$theme ) throw new \InvalidArgumentException('The cms_set_theme plugin requres a theme argument');
                $this->theme_manager()->set_theme($theme);
            });
        $this->registerPlugin('function', 'cms_theme_path', function($params, $tpl) {
                $out = $this->theme_manager()->get_theme_path();
                if( isset($params['assign']) ) {
                    $tpl->assign($params['assign'], $out);
                } else {
                    return $out;
                }
            });
        $this->registerPlugin('function', 'cms_theme_url', function($params, $tpl) {
                $out = $this->theme_manager()->get_theme_url();
                if( isset($params['assign']) ) {
                    $tpl->assign($params['assign'], $out);
                } else {
                    return $out;
                }
            });
    }

    public function theme_manager() : current_theme_manager
    {
        if( !$this->theme_manager ) $this->theme_manager = new current_theme_manager();
        return $this->theme_manager;
    }

} // class
