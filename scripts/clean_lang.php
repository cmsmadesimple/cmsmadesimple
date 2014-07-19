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

$options = getopt('nm:s:o:q');
$master = $slave = null;
//$outfile = 'php://stdout';
$outfile = null;
$quiet = false;
$backup = true;
for( $i = 1; $i < $argc ; $i++ ) {
    switch( $argv[$i] ) {
    case '-m':
        $i++;
        $master = $argv[$i];
        if( !is_file($master) || !is_readable($master) ) die("master file $v is not readable\n");
        break;

    case '-s':
        $i++;
        $slave = $argv[$i];
        if( !is_file($slave) || !is_readable($slave) ) die("slave file $v is not readable\n");
        break;

    case '-q':
        $quiet = true;
        break;

    case '-o':
        $i++;
        $outfile = $argv[$i];
        break;

    default:
        if( $i == $argc - 1 ) {
            if( !$slave ) $slave = $argv[$i];
        }
        break;
    }
}

if( !$outfile ) $outfile = $slave;

// validate that the filenames end in .php
// maybe check mime type.

// read the master file
$lang = null;
@require_once($master);
$master_strs = $lang;
unset($lang);
output('Processing: '.$slave);
output('Number of strings in master: '.count($master_strs));

// read the slave file
$lang = null;
@require_once($slave);
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

uksort($slave_strs,'strnatcasecmp');

// do our output.
function ob_file_callback($buffer) {
  global $ob_file;
  fwrite($ob_file,$buffer);
}

if( $outfile == $slave && $backup ) copy($slave,$slave.'~');
$ob_file = fopen($outfile,'w');
ob_start('ob_file_callback');
echo "<?php\n";
$letter = null;
foreach( $slave_strs as $key => $val ) {
    $tmp = strtoupper($key[0]);
    $val = trim($val);
    if( !$val ) continue;
    if( $tmp != $letter ) {
        $letter = $tmp;
        echo "\n## {$letter}\n";
    }
    echo "\$lang['{$key}'] = '{$val}';\n";
}
echo "?>";
ob_end_flush();

echo "Done: $slave\n";
?>