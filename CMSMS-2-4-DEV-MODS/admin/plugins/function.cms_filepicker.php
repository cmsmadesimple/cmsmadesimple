<?php
function smarty_function_cms_filepicker($params,&$template)
{
    $profile_name = trim(get_parameter_value($params,'profile'));
    $prefix = trim(get_parameter_value($params,'prefix'));
    $name = trim(get_parameter_value($params,'name'));
    $value = trim(get_parameter_value($params,'value'));
    $top = trim(get_parameter_value($params,'top'));
    $type = trim(get_parameter_value($params,'type'));
    $required = cms_to_bool(get_parameter_value($params,'required'));
    if( !$name ) return;

    $name = $prefix.$name;
    $filepicker = \cms_utils::get_filepicker_module();
    if( !$filepicker ) return;

    $profile = $filepicker->get_profile_or_default($profile_name);
    $parms = [];
    if( $top ) {
	if( !startswith($top,'/') ) $top = cmsms()->GetConfig()['uploads_path'].'/'.$top;
	if( startswith($top, CMS_ROOT_PATH ) ) $parms['top'] = $top;
    }
    if( $type ) $parms['type'] = $type;
    if( count($parms) ) $profile = $profile->overrideWith( $parms );

    // todo: something with required.
    $out = $filepicker->get_html( $name, $value, $profile, $required );
    if( isset($params['assign']) ) {
        $template->assign( $params['assign'], $out );
    } else {
        return $out;
    }
}
