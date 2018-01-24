#!/usr/bin/env php
<?php
/**
 * Takes a lang file, sorts the keys, and outputs the new lang file.
 * uses single quotes, and escapes those in output
 *
 * may have problems with code that is arleady escaped
 */

$output = 'php';
$in_lang = null;
$known_only = false;
$a_file = $b_file = $c_file = null;
$thisscript = basename( $argv[0] );

function usage()
{
    global $thisscript;
    echo "{$thisscript} [options] -a file_a -b file_b [-c file_c]\n";
    echo "This script is useful for merging information from 2 or 3 language files to generate a new one\n";
    echo "This script attempts to be flexible and can read language files in php, csv, ini, or json format\n";
    echo "\n";
    echo "-f - Output format.  Possible values are: php, ini, csv, json\n";
    echo "-k - Merge strings that only exist in the master file.  This allows overwriting data but not adding new keys\n";
    echo "-l - lang - specify a realm to read from in language files (such as .ini) files that support multiple languages in one file";
    echo "-h - output this help\n";
    echo "\n";
}

function array_merge_known( $a, $b )
{
    if( !is_array($a) || !is_array($b) ) return;

    foreach( $a as $key => $val ) {
        if( isset( $b[$key]) ) $a[$key] = $b[$ke];
    }
    return $a;
}

$opts = getopt('a:b:c:f:r:hk');
foreach( $opts as $opt => $val ) {
    switch( $opt ) {
    case 'a':
        $a_file = $val;
    case 'b':
        $b_file = $val;
        break;
    case 'c':
        $c_file = $val;
        break;
    case 'l':
        $in_lang = $val;
        break;
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
    case 'k':
        $known_only = true;
        break;
    case 'h':
        usage();
        exit(0);
    }
}

if( !$a_file || !$b_file ) {
    usage();
    exit(0);
}
if( !is_file( $a_file) ) die("First file: $a_file not found\n");
if( !is_file( $b_file) ) die("Second file: $b_file not found\n");
if( $c_file && !is_file( $c_file ) ) die("Third file: $c_file not found\n");

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

function load_ini_lang( $filename, &$data, $lang )
{
    $tmp = parse_ini_file( $filename, TRUE );
    if( !$lang ) $lang = 'lang';
    // if we only have one section, and it is 'lang' then we are good.
    if( !$tmp || !isset($tmp[$lang]) ) return false;

    $data = $tmp['lang'];
    return true;
}

function load_csv_lang( $filename, &$data )
{
    $fh = fopen( $filename, 'r' );
    $out = null;
    while( !feof( $fh ) ) {
        $result = fgetcsv( $fh );
        if( array(null) === $result ) continue;

        $key = trim($result[0]);
        $val = ( isset($result[1]) ) ? trim( $result[1] ) : null;
        $out[$key] = $val;
    }
    if( !count($out) ) return false;

    $data = $out;
    return true;
}

function load_lang( $filename, &$data, &$out_realm, $in_lang = null )
{
    $out_realm = null;
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
        return load_ini_lang( $filename, $data, $in_lang );
    case '.csv':
    case '.txt':
        // no realm data in file format
        return load_csv_lang( $filename, $data );
    default:
        die("Unknown file type $ext");
    }
}

function output_php_lang( $master_data )
{
    $out = "<?php\n";
    foreach( $master_data as $key => $val ) {
        $key = trim($key);
        if( !$key ) continue;
        // unescape slashes
        $val = stripslashes($val);
        $val = addslashes($val);
        $out .= "\$lang['{$key}'] = '{$val}';\n";
    }
    return $out;
}

function output_ini_lang( $master_data, $lang )
{
    if( !$lang ) $lang = 'lang';
    $out = "[$lang]\n";
    foreach( $master_data as $key => $val ) {
        $key = trim($key);
        if( !$key ) continue;
        // unescape slashes
        $val = stripslashes($val);
        $val = addslashes($val);
        $out .= "$key=\"$val\"\n";
    }
    return $out;
}

function output_json_lang( $master_data )
{
    $out = [];
    foreach( $master_data as $key => $val ) {
        $key = trim($key);
        if( !$key ) continue;
        // unescape slashes
        $val = stripslashes($val);
        $out[$key] = $val;
    }
    return json_encode( $out, JSON_PRETTY_PRINT )."\n";
}

function output_csv_lang( $master_data )
{
    $out = null;
    foreach( $master_data as $key => $val ) {
        $key = trim($key);
        if( !$key ) continue;
        // unescape slashes
        $val = stripslashes($val);
        $val = addslashes($val);

        $key = '"'.$key.'"';
        $val = '"'.$val.'"';
        $row = [ $key, $val ];
        $out .= implode(',',$row)."\n";
    }
    return $out;
}

$out_data = $a_data = $a_realm = $b_data = $b_realm = $c_data = $c_realm = null;
if( !load_lang( $a_file, $a_data, $a_realm, $in_lang ) ) die("Problem loading lang file from $master\n");
if( !load_lang( $b_file, $b_data, $b_realm, $in_lang ) ) die("Problem loading lang file from $master\n");
if( $a_realm != $b_realm ) die("Cannot work on two different lang realms\n");

if( $known_only ) {
    $out_data = array_merge_known( $a_data, $b_data );
} else {
    $out_data = array_merge( $a_data, $b_data );
}

if( $c_file ) {
    if( !load_lang( $c_file, $c_data, $c_realm, $in_lang ) ) die("Problem loading lang file from $master\n");
    if( $c_realm != $a_realm ) die("Cannot merge different lang realms\n");
    if( $known_only ) {
        $out_data = array_merge_known( $out_data, $c_data );
    } else {
        $out_data = array_merge( $out_data, $c_data );
    }
}

ksort( $out_data );

switch( $output ) {
case 'csv':
    echo output_csv_lang( $out_data );
    break;
case 'ini':
    echo output_ini_lang( $out_data );
    break;
case 'json':
    echo output_json_lang( $out_data );
    break;
case 'php':
default:
    echo output_php_lang( $out_data );
    break;
}