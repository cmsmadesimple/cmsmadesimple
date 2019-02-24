<?php
function smarty_function_cms_render_css( $params, $template )
{
    static $sig;
    if( $sig ) throw new \RuntimeException('cms_render_css can only be called once per request');
    $sig = sha1(__FILE__.time().rand());
    $magic_string = '<!-- cms_render_css:$sig -->';
    $force = (isset($params['force'])) ? cms_to_bool($params['force']) : false;
    $nocache = (isset($params['nocache'])) ? cms_to_bool($params['nocache']) : false;

    $on_postrender = function(array $parms) use ($magic_string,$force,$nocache) {
        if( !isset($parms['content']) ) return;
        $out = null;
        $combiner = CmsApp::get_instance()->get_stylesheet_manager();
        $filename = $combiner->render(PUBLIC_CACHE_LOCATION, $force);
        if($filename ) {
            if($nocache ) $filename .= '?t='.time();
            $fmt = "<link rel=\"stylesheet\" href=\"%s\"/>";
            $out = sprintf($fmt, PUBLIC_CACHE_URL."/$filename");
            $parms['content'] = str_replace($magic_string,$out,$parms['content']);
        }
    };

    cmsms()->get_hook_manager()->add_hook( 'Core::ContentPostRender', $on_postrender );

    // output an html placeholder
    return $magic_string;

}
