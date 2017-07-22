<?php
// 1. Convert UDT's to be simple_plugins

$udt_list = $db->GetArray('SELECT * FROM '.CMS_DB_PREFIX.'userplugins');
if( count($udt_list) ) {
    $app = \__appbase\get_app();
    $destdir = $app->get_destdir();
    if( !$destdir || !is_dir($destdir) ) {
        throw new \LogicException('Destination directory does not exist');
    }
    $destdir .= '/assets/simple_plugins';
    if( !is_dir($destdir) ) @mkdir( $destdir, 0777, true );
    if( !is_dir($destdir) ) throw new \LogicException("Could not create $destdir directory");

    $create_simple_plugin = function( array $row, $destdir ) {
        $fn = $destdir.'/'.$row['name'];
        if( is_file($fn) ) {
            verbose_msg('simple plugin with name '.$row['name'].' already exists');
            continue;
        }

        $code = $row['code'];
        if( !startswith( $code, '<?php') ) $code = "<?php\n".$code;
        file_put_contents($fn,$code);
        verbose_msg('Converted udt '.$row['name'].' to a simple plugin');
    }
    foreach( $udt_list as $udt ) {
        $create_simple_plugin( $udt, $destdir );
    }

    $sqlarr = $dict->DropTableSQL(CMS_DB_PREFIX.'userplugins_seq');
    $dict->ExcuteSQLArray($sqlarr);
    $sqlarr = $dict->DropTableSQL(CMS_DB_PREFIX.'userplugins');
    $dict->ExcuteSQLArray($sqlarr);
    status_msg('Converted User Defined Tags to simple_plugin structure');
}
