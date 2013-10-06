<?php
function usage() {
  echo "todo\n";
}

function error($str) 
{
  echo $str."\n"; exit;
}

if( $argc != 2 ) {
  usage(); exit;
}
$filename = $argv[1];

if( !file_exists($filename) ) {
  error("File not found: ".$filename);
}

require_once($filename);
if( !isset($lang) ) {
  error("No lang variable: ".$filename);
}

// check if there is a realm attached.
if( !is_array($lang) ) exit;

if( count($lang) == 1 ) {
  $keys = array_keys($lang);
  $lang = $lang[$keys[0]];
}

$dup_keys = array();
{
  $keys = array_keys($lang);
  $keys2 = array_count_values($keys);
  foreach( $keys2 as $k => $v ) {
    if( $v > 1 ) {
      $dup_keys[] = $k;
    }
  }
}
if( count($dup_keys) ) {
  echo "DUPLICATE KEYS:\n";
  print_r($dup_keys);
  echo "\n=============\n\n";
}

$dup_vals = array();
{
  $vals = array_values($lang);
  for( $i = 0; $i < count($vals); $i++ ) {
    $vals[$i] = strtolower($vals[$i]);
  }
  $vals2 = array_count_values($vals);
  foreach( $vals2 as $k => $v ) {
    if( $v > 1 ) {
      $dup_valss[] = $k;
    }
  }
}
if( count($dup_vals) ) {
  echo "DUPLICATE Values:\n";
  print_r($dup_vals);
  echo "\n=============\n\n";
}

if( !count($dup_keys) || !count($dup_vals) ) {
  echo "No duplicates found\n";
}
?>