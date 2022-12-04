<?php
function smarty_function_thumbnail_url($params,&$template)
{
    $config = \cms_config::get_instance();
    $dir = $config['uploads_path'];
    $file = trim(get_parameter_value($params,'file'));
    $add_dir = trim(get_parameter_value($params,'dir'));
    $assign = trim(get_parameter_value($params,'assign'));

    if( !$file ) {
        trigger_error('thumbnail_url plugin: invalid file parameter');
        return;
    }

    if( $add_dir ) {
        if( startswith( $add_dir, '/') ) $add_dir = substr($add_dir,1);
        $test = $dir.'/'.$add_dir;
        if( !is_dir($test) || !is_readable($test) ) {
            trigger_error("thumbnail_url plugin: dir=$add_dir invalid directory name specified");
            return;
        }
    }

    $out = null;
    $file = 'thumb_'.$file;
    $fullpath = $dir.'/'.$file;
    if( is_file($fullpath) && is_readable($fullpath) ) {
        // convert it to a url
        $out = $config['uploads_url'].'/';
        if( $add_dir ) $out .= $add_dir.'/';
        $out .= $file;
    }

    if( $assign ) {
        $template->assign($assign,$out);
        return;
    }
    return $out;
}
