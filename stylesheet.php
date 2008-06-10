<?php

// Parse pretty URLS
$url = substr($_SERVER['REQUEST_URI'],strlen($_SERVER['SCRIPT_NAME']));
$url = rtrim($url,'/');
$matches = array();
if( preg_match('+^/[0-9]*/.*?$+',$url,$matches) )
  {
    $tmp = substr($url,1);
    list($_GET['cssid'],$_GET['mediatype']) = explode('/',$tmp);
  }
else if( preg_match('+^/[0-9]*$+',$url,$matches) )
  {
    $_GET['cssid'] = (int)substr($url,1);
  }

require('config.php');
require($config['root_path'].DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'misc.functions.php');

$mediatype = '';
if (isset($_GET["mediatype"])) $mediatype = $_GET["mediatype"];

$cssid = '';
if (isset($_GET['cssid'])) $cssid = $_GET['cssid'];

$name = '';
if (isset($_GET['name'])) $name = $_GET['name'];

$stripbackground = false;
if (isset($_GET["stripbackground"])) $stripbackground = true;

if ($name == '' && $cssid == '') return '';

// Get the hash filename
$hashfile = $config['root_path'].DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'csshash.dat';

// Get the cache
$hashmtime = @filemtime($hashfile);
$hash = csscache_csvfile_to_hash($hashfile);

// Get the etag header if set
//print_r( $_SERVER ); echo "\n";
$etag = '';
if( function_exists('getallheaders') )
  {
    $headers = getallheaders();
    if (isset($headers['If-None-Match'])  )
      {
	$etag = trim($headers['If-None-Match'],'"');
      }
  }
else if( isset($_SERVER['HTTP_IF_NONE_MATCH']) )
  {
    $etag = trim($_SERVER['HTTP_IF_NONE_MATCH']);
    $etag = trim($etag,'"');
  }

// connect to the database
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'version.php');
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'fileloc.php');
require(cms_join_path(dirname(__FILE__),'lib','config.functions.php'));
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'misc.functions.php');
require(cms_join_path(dirname(__FILE__),'lib','classes','class.global.inc.php'));
require(cms_join_path(dirname(__FILE__),'lib','adodb.functions.php'));
$gCms =& new CmsObject();
load_adodb();
$db =& adodb_connect();

//echo "DEBUG: cssid = $cssid, hashval = \"{$hash[$cssid]}\" etag = \"$etag\" \n";
//echo "DEBUG: ".strcmp($hash[$cssid],$etag)."\n";
//if( $hash[$cssid] != $etag ) die('uhoh');
if( isset($hash[$cssid]) && strcmp($hash[$cssid],$etag) == 0 && 
    $config['debug'] != true )
  {
    // we have a value
    // and it's fine
    // just have to output a 304
    header('Etag: "'.$etag.'"');
    header('HTTP/1.1 304 Not Modified');
    exit;
  }

//
// Either we don't have a value for this cache
// or the hash is out of date
// so get the styesheets, 
//

// extract the stylesheet(s)
$sql="SELECT css_text, css_name FROM ".$config['db_prefix']."css WHERE css_id = ".$db->qstr($cssid);
$row = $db->GetRow($sql);

// calculate the new etag
$etag = md5($row['css_text']);

// update the hash cache
$hash[$cssid] = $etag;
csscache_hash_to_csvfile($hashfile,$hash);

// add a comment at the start
$css = "/* Start of CMSMS style sheet '{$row['css_name']}' */\n{$row['css_text']}\n/* End of '{$row['css_name']}' */\n\n";

// set encoding
$encoding = '';
if ($config['admin_encoding'] != '')
  $encoding = $config['admin_encoding'];
elseif ($config['default_encoding'] != '')
  $encoding = $config['default_encoding'];
 else
   $encoding = 'UTF-8';

//
// Begin output
//

// postprocess
if ($stripbackground)
{
  #$css = preg_replace('/(\w*?background-color.*?\:\w*?).*?(;.*?)/', '', $css);
  $css = preg_replace('/(\w*?background-color.*?\:\w*?).*?(;.*?)/', '\\1transparent\\2', $css);
  $css = preg_replace('/(\w*?background-image.*?\:\w*?).*?(;.*?)/', '', $css);
}

header("Content-Type: text/css; charset=$encoding");
header("Last-Modified: ".gmdate('D, d M Y H:i:s', $hashmtime - 5).' GMT');
header("Cache-Control: max-age=3600, s-max-age=3600, must-revalidate");
header('Etag: "'.$etag.'"');
echo $css;

// EOF
?>
