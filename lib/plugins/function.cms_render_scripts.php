<?php
function smarty_function_cms_render_scripts( $params, $template )
{
    // output a placeholder
    // add a hook to replace the placeholder with a <script> tag after render
    static $sig;
    if( $sig ) throw new \RuntimeException('cms_render_css can only be called once per request');
    $sig = sha1(__FILE__.time().rand());
    $magic_string = "<!-- cms_render_scripts:$sig -->";

    $force = (isset($params['force'])) ? cms_to_bool($params['force']) : false;
    $nocache = (isset($params['nocache'])) ? cms_to_bool($params['nocache']) : false;
    $defer = (isset($params['defer'])) ? cms_to_bool($params['defer']) : true;
    if($defer ) $defer = 'defer';
    $prefix = get_parameter_value($params,'prefix',PUBLIC_CACHE_URL);

    $on_postrender = function(array $parms) use ($magic_string,$force,$nocache,$prefix,$defer) {
        if( !isset($parms['content']) ) return;
        $out = null;
        $combiner = cmsms()->get_script_manager();
        $filename = $combiner->render_scripts(PUBLIC_CACHE_LOCATION, $force);
        if($filename ) {
            if($nocache ) $filename .= '?t='.time();
            $fmt = "<script src=\"%s\" $defer></script>";
            $out = sprintf($fmt, "$prefix/$filename");
            $parms['content'] = str_replace($magic_string,$out,$parms['content']);
        }
    };

    $app = cmsms();
    if( $app->is_frontend_request() ) {
        cmsms()->get_hook_manager()->add_hook( 'Core::ContentPostRender', $on_postrender );
    }
    else {
        cmsms()->get_hook_manager()->add_hook( 'admin_content_postrender', $on_postrender );
    }
    return $magic_string;
}
