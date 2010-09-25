<?php


global $CMS_INSTALL_PAGE;
// Parse pretty URLS
if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']))
{
  $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
}
$url = substr($_SERVER['REQUEST_URI'],strlen($_SERVER['PHP_SELF']));
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


require('fileloc.php');
require(CONFIG_FILE_LOCATION);
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'misc.functions.php');


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
$hashfile = TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . 'csshash.dat';

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

// connect to the database
require(cms_join_path(dirname(__FILE__),'include.php'));
$db = cmsms()->GetDb();

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

if( !isset($CMS_INSTALL_PAGE) && !isset($CMS_ADMIN_PAGE) )
  {
    if( !class_exists('Events') )
      {
	$fn = cms_join_path($config['root_path'],'lib','classes','class.events.inc.php');
	include($fn);
      }
    $parms = array();
    $parms['content'] =& $css;
    Events::SendEvent('Core','ContentStylesheet',$parms);
  }

if( isset($config['output_compression']) && ($config['output_compression']) && ($config['debug'] != true) )
  {
    @ob_start('ob_gzhandler');
  }
$max_age = (int)get_site_preference('css_max_age',0);
header("Content-Type: text/css; charset=$encoding");
$datestr = gmdate('D, d M Y H:i:s',$hashmtime).' GMT';
header("Last-Modified: ".$datestr);
if( $max_age > 0 )
  {
    $datestr = gmdate('D, d M Y H:i:s',$hashmtime+$max_age).' GMT';
    header("Expires: ".$datestr);
    header("Cache-Control: must-revalidate");
    // no caching?
    //header("Cache-Control: max-age=$max_age, s-max-age=$max_age, must-revalidate");
  }
header('Etag: "'.$etag.'"');
echo $css;

// EOF
?>
