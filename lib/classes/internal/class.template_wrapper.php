<?php
namespace CMSMS\internal;
use CmsApp;

/**
 * A simple class to extend the smarty simple tempmlate
 * to provide some hook functionality.
 *
 * This is set as the default template class in smarty.
 */
class template_wrapper extends \Smarty_Internal_Template
{
    public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true)
    {
        if( is_null($parent) ) $parent = $this;
        return parent::createTemplate($template, $cache_id, $compile_id, $parent, $do_clone);
    }

    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        // send an event before fetching...this allows us to change template stuff.
        $gCms = CmsApp::get_instance();
        if( $gCms->is_frontend_request() ) {
            $parms = array('template'=>&$template,'cache_id'=>&$cache_id,'compile_id'=>&$compile_id,'display'=>&$display);
            $gCms->get_hook_manager()->do_hook( 'Core::TemplatePrefetch', $parms );
        }
        return parent::fetch($template,$cache_id,$compile_id,$parent);
    }
}
