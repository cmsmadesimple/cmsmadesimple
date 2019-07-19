<?php
namespace CMSMS\internal;
use CMSMS\internal\frontend_theme_manager;

// a simple extension on the smarty base templat class
// that provides the cms_*_theme based plugins.
class smarty_theme_template extends smarty_base_template
{
    private $_theme_manager;

    protected function get_theme_from_resource($tpl)
    {
        $rsrc = $tpl->template_resource;
        if( !$rsrc || !startswith($rsrc,'cms_theme:') ) return;

        $parts = explode(';', substr($rsrc,10));
        if( count($parts) == 2 ) {
            $this->_theme_manager->set_current_theme($parts[0]);
            return $parts[0];
        }
        else {
            return $this->_theme_manager->get_current_theme();
        }
    }

    public function __construct( frontend_theme_manager $mgr )
    {
        parent::__construct();
        $this->_theme_manager = $mgr;
        $this->registerPlugin('function', 'cms_set_theme', function($params, $tpl) {
                $theme = get_parameter_value($params,'theme');
                if( !$theme ) throw new \InvalidArgumentException('The cms_set_theme plugin requres a theme argument');
                $this->_theme_manager->set_theme($theme);
            });
        $this->registerPlugin('function', 'cms_theme_path', function($params, $tpl) {
                $theme = get_parameter_value($params,'theme');
                if( !$theme ) $theme = $this->get_theme_from_resource($tpl);
                if( !$theme ) $theme = $this->theme_manager()->get_current_theme();
                $out = $this->theme_manager()->get_theme_path($theme);
                if( isset($params['assign']) ) {
                    $tpl->assign($params['assign'], $out);
                } else {
                    return $out;
                }
            });
        $this->registerPlugin('function', 'cms_theme_url', function($params, $tpl) {
                $theme = get_parameter_value($params,'theme');
                if( !$theme ) $theme = $this->get_theme_from_resource($tpl);
                if( !$theme ) $theme = $this->theme_manager()->get_current_theme();
                $out = $this->theme_manager()->get_theme_url($theme);
                if( isset($params['assign']) ) {
                    $tpl->assign($params['assign'], $out);
                } else {
                    return $out;
                }
            });
    }

    public function theme_manager()
    {
        return $this->_theme_manager;
    }
} // class
