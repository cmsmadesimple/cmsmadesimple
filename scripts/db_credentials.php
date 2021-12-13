#!/usr/bin/env php
<?php

/* TODO
$parts = [
	'db_hostname' => $this->_data['db_hostname'],
	'db_username' => $this->_data['db_username'],
	'db_password' => $this->_data['db_password'],
	'db_name' => $this->_data['db_name'],
];
if (!empty($this->_data['db_port']) || is_numeric($this->_data['db_port'])) {
	$parts['db_port'] = (int)$this->_data['db_port'];
}
$enc = json_encode($parts, JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
$raw = Crypto::encrypt_string($enc, '', 'internal');
$str = base64_encode($raw);
*/

$output = 'php';
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
    echo "-l - lang - specify a realm to read from in language files (such as .ini) files that support multiple languages in one file\n";
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
