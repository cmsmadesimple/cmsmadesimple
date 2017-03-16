<?php
status_msg('Performing structure changes for CMSMS 2.2');

$create_private_dir = function($relative_dir) {
    $app = \__appbase\get_app();
    $destdir = $app->get_destdir();
    $relative_dir = trim($relative_dir);
    if( !$relative_dir ) return;

    $dir = $destdir.'/'.$relative_dir;
    if( !is_dir($dir) ) {
        @mkdir($dir,0777,true);
    }
    @touch($dir.'/index.html');
};

$move_directory_files = function($srcdir,$destdir) {
    $srcdir = trim($srcdir);
    $destdir = trim($destdir);
    if( !is_dir($srcdir) ) return;

    $files = glob($srcdir.'/*');
    if( !count($files) ) return;

    foreach( $files as $src ) {
        $bn = basename($src);
        $dest = $destdir.'/'.$bn;
        rename($src,$dest);
    }
    @touch($dir.'/index.html');
};

//$gCms = cmsms();
$dbdict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

$sqlarray = $dbdict->AddColumnSQL(CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME,'help_content_cb C(255), one_only I1');
$dbdict->ExecuteSQLArray($sqlarray);

verbose_msg(ilang('upgrading_schema',202));
$query = 'UPDATE '.CMS_DB_PREFIX.'version SET version = 202';
$db->Execute($query);

$type = \CmsLayoutTemplateType::load('__CORE__::page');
$type->set_help_callback('CmsTemplateResource::template_help_callback');
$type->save();

$type = \CmsLayoutTemplateType::load('__CORE__::generic');
$type->set_help_callback('CmsTemplateResource::template_help_callback');
$type->save();

// create the assets directory structure
verbose_msg('Creating assets structure');
$create_private_dir('assets/templates');
$create_private_dir('assets/configs');
$create_private_dir('assets/module_custom');
$create_private_dir('assets/admin_custom');
$create_private_dir('assets/plugins');
$create_private_dir('assets/images');
$create_private_dir('assets/css');
$destdir = \__appbase\get_app()->get_destdir();
$srcdir = $destdir.'/module_custom';
if( is_dir($srcdir) ) {
    $move_directory_files($srcdir,$destdir.'/assets/module_custom');
}
$srcdir = $destdir.'/admin/custom';
if( is_dir($srcdir) ) {
    $move_directory_files($srcdir,$destdir.'/assets/admin_custom');
}
$srcdir = $destdir.'/tmp/configs';
if( is_dir($srcdir) ) {
    $move_directory_files($srcdir,$destdir.'/assets/configs');
}
$srcdir = $destdir.'/tmp/templates';
if( is_dir($srcdir) ) {
    $move_directory_files($srcdir,$destdir.'/assets/templates');
}
