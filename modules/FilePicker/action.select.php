<?php
if( !isset($gCms) ) exit;
if( CmsApp::get_instance()->is_frontend_request() ) exit;

try {
    $name = get_parameter_value($params,'name');
    $value = get_parameter_value($params,'value');
    $type = get_parameter_value($params,'type');
    $type = 'image';

    $profile = $this->get_default_profile();
    if( $type ) {
        $parms = [ 'type' => $type ];
        $profile = $profile->overrideWith( $parms );
    }
    echo $this->get_html($name, $value, $profile );
}
catch( \Exception $e ) {
    echo $this->ShowErrors($e->GetMessage());
}