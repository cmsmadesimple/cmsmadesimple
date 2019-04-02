<?php
function smarty_function_cms_render_css( $params, $template )
{
    static $sig;
    if( $sig ) throw new \RuntimeException('cms_render_css can only be called once per request');
    $sig = sha1(__FILE__.time().rand());
    $magic_string = "<!-- cms_render_css:$sig -->";
    $config = cmsms()->GetConfig();

    $force = (isset($params['force'])) ? cms_to_bool($params['force']) : false;
    $nocache = (isset($params['no_cache'])) ? cms_to_bool($params['no_cache']) : false;
    $prefix = get_parameter_value($params,'prefix',$config['css_url']);

    $on_postrender = function(array $parms) use ($magic_string,$force,$nocache,$prefix,$params,$config) {
        if( !isset($parms['content']) ) return;
        $out = null;
        $combiner = cmsms()->get_stylesheet_manager();
        $entropy = sha1(__FILE__.json_encode($params));
        $filename = $combiner->render($config['css_path'], $entropy, $force);
        if( $filename ) {
            if($nocache ) $filename .= '?t='.time();
            $fmt = "<link rel=\"stylesheet\" href=\"%s\"/>";
            $out = sprintf($fmt, "$prefix/$filename");
            $parms['content'] = str_replace($magic_string,$out,$parms['content']);
        }
    };

    cmsms()->get_hook_manager()->add_hook( 'Core::ContentPostRender', $on_postrender );

    // output an html placeholder
    return $magic_string;

}
