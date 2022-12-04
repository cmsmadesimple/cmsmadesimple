<?php

class CMS_Smarty_Template extends Smarty_Internal_Template
{
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        $global_cache_id = \Smarty_CMS::get_instance()->get_global_cacheid();
        if( is_null($cache_id) || $cache_id === '' ) {
            $cache_id = $global_cache_id;
        }
        else if( $cache_id[0] == '|' ) {
            $cache_id = $global_cache_id . $cache_id;
        }

        // send an event before fetching...this allows us to change template stuff.
        if( CmsApp::get_instance()->is_frontend_request() ) {
            $parms = array('template'=>&$template,'cache_id'=>&$cache_id,'compile_id'=>&$compile_id,'display'=>&$display);
            \CMSMS\HookManager::do_hook( 'Core::TemplatePrefetch', $parms );
        }
        return parent::fetch($template,$cache_id,$compile_id,$parent);
    }
}
