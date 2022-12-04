<?php
function smarty_function_cms_filepicker($params,$template)
{
    $filepicker = \cms_utils::get_filepicker_module();
    if( !$filepicker ) return;

    $name = trim(get_parameter_value($params,'name'));
    if( !$name ) return;
    $profile_name = trim(get_parameter_value($params,'profile'));
    $prefix = trim(get_parameter_value($params,'prefix'));
    $value = trim(get_parameter_value($params,'value'));
    $top = trim(get_parameter_value($params,'top'));
    $type = trim(get_parameter_value($params,'type'));
    $required = cms_to_bool(get_parameter_value($params,'required'));

    $profile = $filepicker->get_profile_or_default($profile_name);
    $parms = [];
    if( $top ) {
        // TODO $top might be Windoze-style absolute path and separator might be \ or /
        if( !startswith($top,'/') ) $top = cmsms()->GetConfig()['uploads_path'].'/'.$top;
        if( startswith($top, CMS_ROOT_PATH ) ) $parms['top'] = $top;
    }
    if( $type ) $parms['type'] = $type;
    if( $parms ) {
        $profile = $profile->overrideWith( $parms );
    }

    // todo: something with required.
    $out = $filepicker->get_html( $prefix.$name, $value, $profile, $required );
    if( isset($params['assign']) ) {
        $template->assign( $params['assign'], $out );
    } else {
        return $out;
    }
}
