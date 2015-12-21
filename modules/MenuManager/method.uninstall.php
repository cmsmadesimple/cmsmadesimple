<?php

$this->RemovePreference();
$this->DeleteTemplate(); // old templates
$this->RemoveSmartyPlugin();

try {
    $types = CmsLayoutTemplateType::load_all_by_originator($this->GetName());
    foreach( $types as $type ) {
        $templates = $type->get_template_list();
        if( is_array($templates) && count($templates) ) {
            foreach( $templates as $tpl ) {
                $tpl->delete();
            }
        }
        $type->delete();
    }
}
catch( CmsException $e ) {
    // log it
    audit('',$this->GetName(),'Uninstall Error: '.$e->GetMessage());
    return FALSE;
}

?>
