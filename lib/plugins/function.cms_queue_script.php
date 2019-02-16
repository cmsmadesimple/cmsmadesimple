<?php
function smarty_function_cms_queue_script( $params, &$template )
{
    // produces no output.
    if(!isset($params['file']) ) { return;
    }
    $combiner = CmsApp::get_instance()->GetScriptManager();

    $file = trim($params['file']);
    if(is_file($file) ) {
        $combiner->queue_script($file);
        return;
    }

    // if it's relative to a CMSMS path
    if(!startswith($file, DIRECTORY_SEPARATOR) ) { $file = "/$file";
    }
    $config = \cms_config::get_instance();
    $paths = [ CMS_ASSETS_PATH.$file, $config['uploads_path'].$file, CMS_ROOT_PATH.$file ];
    foreach( $paths as $one ) {
        if(is_file($one) ) { $combiner->queue_script($one);
        }
    }
}
