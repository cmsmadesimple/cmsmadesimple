#!/usr/bin/env php
<?php
/**
 * Take two lang files, and output a new one with missing values, or report on missing and extra strings.
 * when not using 'missing only'  will skip keys in the translation file that are not in the master.
 *
 * may have problems with code that is arleady escaped
 */

$report_only = $missing_only = false;
$output = 'php';
$use_master_val = false;
$master = $slave = null;
$thisscript = basename( $argv[0] );

function usage()
{
    global $thisscript;
    echo "{$thisscript} [options] -a <master en_US file> -b <translation file>\n";
    echo "This script can be used to aid third party translation by comparing a master file and a translation file, and outputting information about missing translations\n";
    echo "This script attempts to be flexible and can read language files in php, csv, ini, or json format\n";
    echo "\n";
    echo "options:\n";
    echo "-a filename - The path to the master english language file i.e:  en_US.php\n";
    echo "-b filename - The path to the slave (translated) language file. i.e: de_DE.php\n";
    echo "-r - Output a report of missing an extra keys and then exit\n";
    echo "-m - Output missing translations only\n";
    echo "-u - When outputting missing translations, copy in the master value\n";
    echo "-f - Output format.  Possible values are: php, ini, csv, json\n";
    echo "-h - output this help\n";
    echo "\n";
}

$opts = getopt('a:b:f:umrh');
foreach( $opts as $opt => $val ) {
    switch( $opt ) {
    case 'a':
        $master = $val;
    case 'b':
        $slave = $val;
        break;
    case 'r':
        $report_only = true;
        break;
    case 'm':
        $missing_only = true;
    case 'f':
        switch( $val ) {
        case 'php':
        case 'json':
        case 'ini':
        case 'csv':
            $output = $val;
            break;
        }
        break;
    case 'u':
        $use_master_val = true;
        break;
    case 'h':
        usage();
        exit(0);
    }
}

if( !$master || !$slave ) {
    usage();
    exit(0);
}
if( !is_file( $master) ) die("Master file: $master not found\n");
if( !is_file( $slave) ) die("Slave file: $slave not found\n");

function load_php_lang( $filename, &$data, &$realm )
{
    include( $filename );
    if( !isset($lang) || !is_array($lang) ) return false;

    $realm = null;
    if( count($lang) == 1 ) {
        $keys = array_keys($lang);
        $realm = $keys[0];
        $data = $lang[$realm];
    } else {
        $data = $lang;
    }
    return true;
}

function load_json_lang( $filename, &$data )
{
    $content = file_get_contents( $filename );
    if( !$content ) return false;

    $tmp = json_decode( $content );
    if( !$tmp ) return false;

    $data = $tmp;
    return true;
}

function load_ini_lang( $filename, &$data, $realm )
{
    $tmp = parse_ini_file( $filename, TRUE );
    if( !$realm ) $realm = 'lang';
    // if we only have one section, and it is 'lang' then we are good.
    if( !$tmp || !isset($tmp[$realm]) ) return false;

    $data = $tmp['lang'];
    return true;
}

function load_csv_lang( $filename, &$data )
{
    $fh = fopen( $filename, 'r' );
    $out = [];
    while( !feof( $fh ) ) {
        $result = fgetcsv( $fh );
        if( array(null) === $result ) continue;

        $key = trim($result[0]);
        $val = ( isset($result[1]) ) ? trim( $result[1] ) : null;
        $out[$key] = $val;
    }
    if( !$out ) return false;

    $data = $out;
    return true;
}

function load_lang( $filename, &$data, &$out_realm, $in_realm = null )
{
    $out_realm = $in_realm;
    $p = strrpos( $filename, '.' );
    $ext = strtolower( substr( $filename, $p ) );
    switch( $ext ) {
    case '.php':
        // realm can be detected.
        return load_php_lang( $filename, $data, $out_realm );
    case '.json':
        // no realm data in file format
        return load_json_lang( $filename, $data );
    case '.ini':
        // onlyload from the specified realm.
        return load_ini_lang( $filename, $data, $in_realm );
    case '.csv':
    case '.txt':
        // no realm data in file format
        return load_csv_lang( $filename, $data );
    default:
        die("Unknown file type $ext");
    }
}

function output_php_lang( $slave_data, $master_data, $output_master_val )
{
    $out = '<?php'.PHP_EOL;
    foreach( $master_data as $key => $mval ) {
        $key = trim($key);
        if( !$key ) continue;

        $val = $mval;
        if( !$output_master_val ) $val = null;
        if( isset( $slave_data[$key]) ) $val = $slave_data[$key];

        // unescape slashes
        $val = stripslashes($val);
        $val = addslashes($val);
        $out .= "\$lang['{$key}'] = '{$val}';".PHP_EOL;
    }
    return $out;
}

function output_ini_lang( $slave_data, $master_data, $output_master_val )
{
    $out = '[lang]'.PHP_EOL;
    foreach( $master_data as $key => $mval ) {
        $key = trim($key);
        if( !$key ) continue;

        $val = $mval;
        if( !$output_master_val ) $val = null;
        if( isset( $slave_data[$key]) ) $val = $slave_data[$key];

        // unescape slashes
        $val = stripslashes($val);
        $val = addslashes($val);
        $out .= "$key=\"$val\"".PHP_EOL;
    }
    return $out;
}

function output_json_lang( $slave_data, $master_data, $output_master_val )
{
    $out = [];
    foreach( $master_data as $key => $mval ) {
        $key = trim($key);
        if( !$key ) continue;

        $val = $mval;
        if( !$output_master_val ) $val = null;
        if( isset( $slave_data[$key]) ) $val = $slave_data[$key];

        // unescape slashes
        $val = stripslashes($val);
        $out[$key] = $val;
    }
    return json_encode( $out, JSON_PRETTY_PRINT ).PHP_EOL;
}

function output_csv_lang( $slave_data, $master_data, $output_master_val )
{
    $out = null;
    foreach( $master_data as $key => $mval ) {
        $key = trim($key);
        if( !$key ) continue;

        $val = $mval;
        if( !$output_master_val ) $val = null;
        if( isset( $slave_data[$key]) ) $val = $slave_data[$key];

        // unescape slashes
        $val = stripslashes($val);
        $val = addslashes($val);

        $key = '"'.$key.'"';
        $val = '"'.$val.'"';
        $row = [ $key, $val ];
        $out .= implode(',',$row).PHP_EOL;
    }
    return $out;
}

$master_data = $master_realm = $slave_data = $slave_realm = null;
if( !load_lang( $master, $master_data, $master_realm ) ) {
    die("Problem loading lang file from $master\n");
}
if( !load_lang( $slave, $slave_data, $slave_realm ) ) {
    die("Problem loading lang file from $master\n");
}
if( $master_realm != $slave_realm ) {
    die("Cannot work on two different lang realms\n");
}

ksort( $master_data );
ksort( $slave_data );

// 1.  Get MISSING translations
$master_keys = array_keys( $master_data );
$slave_keys = array_keys( $slave_data );
$missing_keys = array_diff( $master_keys, $slave_keys );
if( $report_only && count($missing_keys) ) {
    echo 'WARNING: '.count($missing_keys)." strings are not translated\n";
    echo "--\n";
    foreach( $missing_keys as $one ) {
        echo "$one\n";
    }
}

// 2.  Get Extra translations
$extra_keys = array_diff( $slave_keys, $master_keys );
if( $report_only && count($extra_keys) ) {
    echo 'WARNING: '.count($extra_keys)." strings are no longer valid\n";
    echo "--\n";
    foreach( $extra_keys as $one ) {
        echo "$one\n";
    }
}

if( $report_only ) exit(0);

if( $missing_only ) {
    if( !$missing_keys ) exit(0);

    $missing_data = [];
    foreach( $missing_keys as $key  ) {
        $missing_data[$key] = $master_data[$key];
    }
    $master_data = $missing_data;
}

// now output a new slave file.
switch( $output ) {
case 'php':
    echo output_php_lang( $slave_data, $master_data, $use_master_val );
    break;
case 'json':
    echo output_json_lang( $slave_data, $master_data, $use_master_val );
    break;
case 'ini':
    echo output_ini_lang( $slave_data, $master_data, $use_master_val );
    break;
case 'csv':
    echo output_csv_lang( $slave_data, $master_data, $use_master_val );
}
