<?php
function smarty_function_cms_render_scripts( $params, $template )
{
    $combiner = CmsApp::get_instance()->GetScriptManager();
    $force = (isset($params['force'])) ? cms_to_bool($params['force']) : false;
    $nocache = (isset($params['nocache'])) ? cms_to_bool($params['nocache']) : false;
    $defer = (isset($params['defer'])) ? cms_to_bool($params['defer']) : true;
    if($defer ) { $defer = 'defer';
    }

    $out = null;
    $filename = $combiner->render_scripts(PUBLIC_CACHE_LOCATION, $force);
    if($nocache ) { $filename .= '?t='.time();
    }

    if($filename ) {
        $fmt = "<script src=\"%s\" $defer></script>";
        $out = sprintf($fmt, PUBLIC_CACHE_URL."/$filename");
    }

    if(isset($params['assign']) ) {
        $template->assign(trim($params['assign']), $out);
    } else {
        return $out;
    }
}
