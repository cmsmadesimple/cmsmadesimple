<?php
// 1. Convert UDT's to be simple_plugins
$app = \__appbase\get_app();
$destdir = $app->get_destdir();

$udt_list = $db->GetArray('SELECT * FROM '.CMS_DB_PREFIX.'userplugins');
if( count($udt_list) ) {
    if( !$destdir || !is_dir($destdir) ) {
        throw new \LogicException('Destination directory does not exist');
    }
    $destdir .= '/assets/simple_plugins';
    if( !is_dir($destdir) ) @mkdir( $destdir, 0777, true );
    if( !is_dir($destdir) ) throw new \LogicException("Could not create $destdir directory");

    $create_simple_plugin = function( array $row, $destdir ) {
        $fn = $destdir.'/'.$row['userplugin_name'];
        if( is_file($fn) ) {
            verbose_msg('simple plugin with name '.$row['userplugin_name'].' already exists');
            return;
        }

        $code = $row['code'];
        if( !startswith( $code, '<?php') ) $code = "<?php\n".$code;
        file_put_contents($fn,$code);
        verbose_msg('Converted UDT '.$row['userplugin_name'].' to a simple plugin');
    };
    foreach( $udt_list as $udt ) {
        $create_simple_plugin( $udt, $destdir );
    }

    $dict = NewDataDictionary($db);
    $sqlarr = $dict->DropTableSQL(CMS_DB_PREFIX.'userplugins_seq');
    $dict->ExecuteSQLArray($sqlarr);
    $sqlarr = $dict->DropTableSQL(CMS_DB_PREFIX.'userplugins');
    $dict->ExecuteSQLArray($sqlarr);
    status_msg('Converted User Defined Tags to simple_plugin structure');

    $db->Execute( 'ALTER TABLE '.CMS_DB_PREFIX.'users MODIFY username VARCHAR(80)' );
    $db->Execute( 'ALTER TABLE '.CMS_DB_PREFIX.'users MODIFY password VARCHAR(128)' );

    verbose_msg(ilang('upgrading_schema',202));
    $query = 'UPDATE '.CMS_DB_PREFIX.'version SET version = 202';
    $db->Execute($query);


}

// 2. Move MenuManager, which is no longer a distributed module,  to /Assets/Plugins
$fr = "$destdir/modules/MenuManager";
$to = "$destdir/assets/modules/MenuManager";
if( is_dir( $fr ) && !is_dir( $to ) ) {
   rename( $fr, $to );
}

// tweak callbacks for page and generic layout templatet types.
$page_type = \CMSLayoutTemplateType::load('__CORE__::page');
$page_type->set_lang_callback('\\CMSMS\internal\\std_layout_template_callbacks::page_type_lang_callback');
$page_type->set_content_callback('\\CMSMS\internal\\std_layout_template_callbacks::reset_page_type_defaults');
$page_type->set_help_callback('\\CMSMS\internal\\std_layout_template_callbacks::template_help_callback');
$page_type->save();

$generic_type = \CMSLayoutTemplateType::load('__CORE__::generic');
$generic_type_type->set_lang_callback('\\CMSMS\internal\\std_layout_template_callbacks::generic_type_lang_callback');
$generic_type_type->set_help_callback('\\CMSMS\internal\\std_layout_template_callbacks::template_help_callback');
$page_type->save();
