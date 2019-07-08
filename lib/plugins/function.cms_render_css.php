<?php
function smarty_function_cms_render_css( $params, $template )
{
    $gCms = cmsms();
    static $sig;
    if( $sig ) throw new \RuntimeException('cms_render_css can only be called once per request');
    $sig = sha1(__FILE__.time().rand());
    $magic_string = "<!-- cms_render_css:$sig -->";
    $config = $gCms->GetConfig();

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

    $do_smarty_postprocess = function( $combined_css ) use ($force, $template) {
        // here we should be creating a new template object
        // without a new template object, we can though use all of the variables that are already set.
        $template->left_delimiter = '[[';
        $template->right_delimiter = ']]';
        $tmp = $template->force_compile;
        $template->force_compile = $force;
        $combined_css = $template->fetch('string:'.$combined_css);  // allows caching of the compiled template.
        $template->force_compile = $tmp;
        $template->left_delimiter = '{';
        $template->right_delimiter = '}';
        return $combined_css;
    };

    $css_url_adjust = function(array $params) {
        // this fixes relative URLS in CSS url statements to prepend a prefix
        // given the filename in $params['file'] convert the path portion to a CMSMS absolute url.
        $pathname = dirname($params['file']);
        if( !startswith($pathname,CMS_ROOT_PATH) ) return;
        $cms_real_root = realpath(CMS_ROOT_PATH);
        if( !$cms_real_root ) return;
        $url_prefix = str_replace(CMS_ROOT_PATH, CMS_ROOT_URL, $pathname);
        $css_search = '#url\(\s*[\'"]?(.*?)[\'"]?\s*\)#';
        $css_url_fix = function($matches) use ($url_prefix, $pathname, $cms_real_root) {
            // we do absolutely nothing with URI's or with anything with ../ in the name.
            if( startswith($matches[1],'data:') ) return $matches[0];
            if( startswith($matches[1],'http:') ) return $matches[0];
            if( startswith($matches[1],'https:') ) return $matches[0];
            if( startswith($matches[1],'//') ) return $matches[0];
            $fullpath = realpath($pathname.'/'.$matches[1]);
            if( !$fullpath || !startswith($fullpath,$cms_real_root) || !is_file($fullpath) ) return $matches[0];
            // all checks done... we can replace the url with
            $in = $matches[0];
            $out = "url('{$url_prefix}/{$matches[1]}')";
            return $out;
        };
        $params['content'] = preg_replace_callback($css_search, $css_url_fix, $params['content']);
    };

    $hook_manager = $gCms->get_hook_manager();
    if( cms_to_bool(get_parameter_value($params, 'adjust_urls')) ) {
        $hook_manager->add_hook('Core::ProcessCSSFile', $css_url_adjust, $hook_manager::PRIORITY_LOW );
    }
    if( cms_to_bool(get_parameter_value($params, 'smarty_processing')) ) {
        $hook_manager->add_hook('Core::PostProcessCSS', $do_smarty_postprocess, $hook_manager::PRIORITY_HIGH );
    }
    $hook_manager->add_hook( 'Core::ContentPostRender', $on_postrender );

    // output an html placeholder
    return $magic_string;

}
