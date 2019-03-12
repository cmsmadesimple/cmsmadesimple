<?php
function smarty_function_file_url($params,&$template)
{
    $config = cmsms()->GetConfig();
    $dir = $config['uploads_path'];
    $file = trim(get_parameter_value($params, 'file'));
    $add_dir = trim(get_parameter_value($params, 'dir'));
    $assign = trim(get_parameter_value($params, 'assign'));

    if(!$file ) {
        trigger_error('file_url plugin: invalid file parameter');
        return;
    }
    if($add_dir ) {
        if(startswith($add_dir, '/') ) { $add_dir = substr($add_dir, 1);
        }
        $dir = $dir.'/'.$add_dir;
        if(!is_dir($dir) || !is_readable($dir) ) {
            trigger_error("file_url plugin: dir=$add_dir invalid directory name specified");
            return;
        }
    }

    $out = null;
    $fullpath = $dir.'/'.$file;
    if(!is_file($fullpath) || !is_readable($fullpath) ) {
        // no error log here.
        return;
    }

    // convert it to a url
    $out = CMS_UPLOADS_URL.'/';
    if($add_dir ) $out .= $add_dir.'/';
    $out .= $file;

    if($assign ) {
        $template->assign($assign, $out);
        return;
    }
    return $out;
}
