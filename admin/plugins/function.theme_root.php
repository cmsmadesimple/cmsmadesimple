<?php
function smarty_function_theme_root($params,&$template)
{
    $theme = \cms_utils::get_theme_object();
    $url = $theme->root_url;

    $assign = get_parameter_value( $params, 'assign' );
    if( $assign ) {
        $template->assign( $assign, $url );
        return;
    }
    return $url;
}
