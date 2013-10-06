<?php

  /* clean lang file
     given - master file, slave lang file
    
     a: clean all keys from slave that are not in master
        - count cleaned keys
     b: clean all keys from slave where value of master == value of slave
        - count cleaned keys
     c: output cleaned file
        - count total keys
  */

function output($str)
{
  global $quiet;
  if( !$quiet ) echo $str."\n";
}

$options = getopt('m:s:o:q');
$master = $slave = null;
$outfile = 'php://stdout';
$quiet = false;
foreach( $options as $k => $v ) {
  switch( $k ) {
  case 'm':
   $master = trim($v);
   if( !is_file($master) || !is_readable($master) ) die("master file $v is not readable\n");
   break;

 case 's':
   $slave = trim($v);
   if( !is_file($slave) || !is_readable($slave) ) die("slave file $v is not readable\n");
   break;

 case 'o':
   $outfile = trim($v);
   break;
   
 case 'q':
   $quiet = true;
   break;
  }
}

// validate that the filenames end in .php
// maybe check mime type.

// read the master file
$lang = null;
require_once($master);
$master_strs = $lang;
unset($lang);
output('Processing: '.$slave);
output('Number of strings in master: '.count($master_strs));

// read the slave file
$lang = null;
require_once($slave);
$slave_strs = $lang;
unset($lang);
output('Number of strings in slave: '.count($master_strs));

// do the work
$master_keys = array_keys($master_strs);
$slave_keys = array_keys($slave_strs);
$count_extra_keys = 0;
$count_dup_keys = 0;
foreach( $slave_keys as $key ) {
  if( !in_array($key,$master_keys) ) {
    // key is not in master.
    $count_extra_keys++;
    unset($slave_strs[$key]);
    continue;
  }
  if( $master_strs[$key] == $slave_strs[$key] ) {
    $count_dup_keys++;
    unset($slave_strs[$key]);
    continue;
  }
}
if( $count_extra_keys ) output('Number of extra entries in slave file: '.$count_extra_keys);
if( $count_extra_keys ) output('Number of duplicated strings in slave file: '.$count_dup_keys);
output('Number of strings in output file '.count($slave_strs));
echo "\n";

ksort($slave_strs);

// do our output.
if( $outfile == $slave ) copy($slave,$slave.'~');
$fh = fopen($outfile,'w');
fputs($fh,"<?php\n");
foreach( $slave_strs as $key => $value ) {
  fputs($fh,'$lang[\''.$key.'\']=\''.$value."';\n");
}
fputs($fh,"?>");
fclose($fh);
?>