<?php
namespace CMSMS\internal;

// a simple extension on the smarty base templat class
// that provides the cms_*_theme based plugins.
class smarty_theme_template extends smarty_base_template
{
    protected function get_theme_from_resource($tpl)
    {
	$rsrc = $tpl->template_resource;
	if( !$rsrc || !startswith($rsrc,'cms_theme:') ) return;

	$parts = explode(';', substr($rsrc,10));
	if( count($parts) == 2 ) {
	    $this->theme_manager()->set_theme($parts[0]);
	    return $parts[0];
	}
	else {
	    return $this->theme_manager()->get_theme();
	}
    }

    public function __construct()
    {
        parent::__construct();
        $this->registerPlugin('function', 'cms_set_theme', function($params, $tpl) {
                $theme = get_parameter_value($params,'theme');
                if( !$theme ) throw new \InvalidArgumentException('The cms_set_theme plugin requres a theme argument');
                $this->theme_manager()->set_theme($theme);
            });
        $this->registerPlugin('function', 'cms_theme_path', function($params, $tpl) {
                $theme = get_parameter_value($params,'theme');
		if( !$theme ) $theme = $this->get_theme_from_resource($tpl);
		if( !$theme ) $theme = $this->theme_manager()->get_theme();
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
		if( !$theme ) $theme = $this->theme_manager()->get_theme();
                $out = $this->theme_manager()->get_theme_url($theme);
                if( isset($params['assign']) ) {
                    $tpl->assign($params['assign'], $out);
                } else {
                    return $out;
                }
            });
    }

    public function theme_manager() : current_theme_manager
    {
	// todo: this should go in CmsApp
	static $theme_manager;
        if( !$theme_manager ) $theme_manager = new current_theme_manager();
        return $theme_manager;
    }

} // class
