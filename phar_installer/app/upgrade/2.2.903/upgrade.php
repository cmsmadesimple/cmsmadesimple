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
    if( !is_file($destdir.'/index.html') ) @touch( $destdir.'/index.html' );
    if( !is_file($destdir.'/.htaccess') ) file_put_contents($destdir.'/.htaccess','RedirectMatch 404 ^/.*$');

    $create_simple_plugin = function( array $row, $destdir ) {
        $fn = $destdir.'/'.$row['userplugin_name'].'.cmsplugin';
        if( is_file($fn) ) {
            verbose_msg('simple plugin with name '.$row['userplugin_name'].' already exists');
            return;
        }

        if( $row['description'] ) {
            $code = "/*\n";
            $code .= $row['description'];
            $code .= "*/\n\n";
        }
        $code .= "// for security purposes, we ensure that this file cannot be directly requested by browsers\n";
        $code .= "if !defined('CMS_VERSION\)) exit;\n\n";
        $code .= $row['code'];

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
}

// 2. Move MenuManager, which is no longer a distributed module,  to /Assets/Plugins
$fr = "$destdir/modules/MenuManager";
$to = "$destdir/assets/modules/MenuManager";
if( is_dir( $fr ) && !is_dir( $to ) ) {
   rename( $fr, $to );
}

// 2b. Move News, which is no longer a distributed module,  to /Assets/Plugins
$fr = "$destdir/modules/News";
$to = "$destdir/assets/modules/News";
if( is_dir( $fr ) && !is_dir( $to ) ) {
   rename( $fr, $to );
}

// 2c. Move CMSMailer, which is no longer a distributed module,  to /Assets/Plugins
$fr = "$destdir/modules/CMSMailer";
$to = "$destdir/assets/modules/CMSMailer";
if( is_dir( $fr ) && !is_dir( $to ) ) {
   rename( $fr, $to );
}
status_msg('Moved modules that are no longer considered -core- to assets/modules');

//  3.  convert events table stuff to the new hook_mapping.json file
$t_events = $db->GetArray('SELECT * FROM '.CMS_DB_PREFIX.'events ORDER BY event_id');
$events = null;
foreach( $t_events as $row ) {
    $events[$row['event_id']] = $row;
}
$table_str = "'Search','CmsJobManager'";
$db->Execute( 'DELETE FROM '.CMS_DB_PREFIX.'event_handlers WHERE module_name IN ('.$table_str.')' );
$t_event_handlers = $db->GetArray('SELECT * FROM '.CMS_DB_PREFIX.'event_handlers ORDER BY event_id, handler_order');
foreach( $t_event_handlers as $row ) {
    $event_id = $row['event_id'];
    if( isset($events[$event_id]) ) {
        $events[$event_id]['handlers'][] = $row;
    }
}
unset($t_events,$t_event_handlers);
$mapping_data = null;
foreach( $events as $evt ) {
    if( !isset($evt['handlers']) || !count($evt['handlers']) ) continue;
    $hook = $evt['originator'].'::'.$evt['event_name'];
    $rec = [ 'hook'=>$hook, 'handlers'=>null ];
    foreach( $evt['handlers'] as $evt_handler ) {
        if( $evt_handler['tag_name'] ) {
            $rec['handlers'][] = [ 'type'=>'simple', 'name'=>$evt_handler['tag_name'] ];
        } else if( $evt_handler['module_name'] ) {
            $rec['handlers'][] = [ 'type'=>'module', 'name'=>$evt_handler['module_name'] ];
        }
    }
    $mapping_data[] = $rec;
}
$res = mkdir(CMS_ASSETS_PATH.'/configs',0775,true);
file_put_contents( CMS_ASSETS_PATH.'/configs/hook_mapping.json', json_encode($mapping_data, JSON_PRETTY_PRINT) );
$db->Execute( 'DROP TABLE '.CMS_DB_PREFIX.'event_handler_seq');
$db->Execute( 'DROP TABLE '.CMS_DB_PREFIX.'events_seq');
$db->Execute( 'DROP TABLE '.CMS_DB_PREFIX.'event_handlers');
$db->Execute( 'DROP TABLE '.CMS_DB_PREFIX.'events');
status_msg('Converted events to hooks... and use the new assets/configs/hook_mapping.json file in place of the database');

$db->Execute( 'ALTER TABLE '.CMS_DB_PREFIX.'users MODIFY username VARCHAR(80)' );
$db->Execute( 'ALTER TABLE '.CMS_DB_PREFIX.'users MODIFY password VARCHAR(128)' );
status_msg('Added some size to the password and username columns of the users table');

// tweak callbacks for page and generic layout templatet types.
$page_type = \CmsLayoutTemplateType::load('__CORE__::page');
$page_type->set_lang_callback('\\CMSMS\internal\\std_layout_template_callbacks::page_type_lang_callback');
$page_type->set_content_callback('\\CMSMS\internal\\std_layout_template_callbacks::reset_page_type_defaults');
$page_type->set_help_callback('\\CMSMS\internal\\std_layout_template_callbacks::template_help_callback');
$page_type->save();

$generic_type = \CmsLayoutTemplateType::load('__CORE__::generic');
$generic_type->set_lang_callback('\\CMSMS\internal\\std_layout_template_callbacks::generic_type_lang_callback');
$generic_type->set_help_callback('\\CMSMS\internal\\std_layout_template_callbacks::template_help_callback');
$generic_type->save();

$tmp = cms_siteprefs::get('site_signature');
if( !$tmp ) {
    cms_siteprefs::set('site_signature',sha1(bin2hex(random_bytes(256)))); // a unique signature to identify this site.  Useful for some signatures too.
    status_msg('created a random size signature');
}

verbose_msg(ilang('upgrading_schema',203));
$query = 'UPDATE '.CMS_DB_PREFIX.'version SET version = 203';
$db->Execute($query);
