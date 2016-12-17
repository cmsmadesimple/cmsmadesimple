<?php
if( !isset($gCms) ) exit;

if( version_compare($oldversion,'1.0.5') < 0 ) {
    try {
        $types = CmsLayoutTemplateType::load_all_by_originator($this->GetName());
        if( is_array($types) && count($types) ) {
            foreach( $types as $type_obj ) {
                $type_obj->set_help_callback('Navigator::template_help_callback');
                $type_obj->save();
            }
        }
    }
    catch( Exception $e ) {
        // log it
        audit('',$this->GetName(),'Upgrade error: '.$e->GetMessage());
        return FALSE;
    }
}
