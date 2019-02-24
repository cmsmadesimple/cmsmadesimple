<?php
function smarty_function_cms_queue_css( $params, &$template )
{
    // produces no output.
    if(!isset($params['file']) ) return;
    $combiner = CmsApp::get_instance()->get_stylesheet_manager();
    $priority = (int) get_parameter_value($params,'priority',2);
    $priority = max(1,min(3,$priority));

    $file = trim($params['file']);
    if(is_file($file) ) {
        $combiner->queue($file,$priority);
        return;
    }

    // if it's relative to a CMSMS path
    if(!startswith($file, DIRECTORY_SEPARATOR) ) $file = "/$file";
    $config = \cms_config::get_instance();
    $paths = [ CMS_ASSETS_PATH.$file, $config['uploads_path'].$file, CMS_ROOT_PATH.$file ];
    foreach( $paths as $one ) {
        if(is_file($one) ) $combiner->queue($one);
    }
}
