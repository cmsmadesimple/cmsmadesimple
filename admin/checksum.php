<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: supportinfo.php 4216 2007-10-06 19:28:55Z wishy $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
include_once("header.php");

define('CMS_BASE', dirname(dirname(__FILE__)));
require_once cms_join_path(CMS_BASE, 'lib', 'test.functions.php');

function checksum_lang($params,&$smarty)
{
  if( isset($params['key']) )
    {
      return lang($params['key']);
    }
}

// check for login
check_login();

function check_checksum_data(&$report)
{
  if( (!isset($_FILES['cksumdat'])) || empty($_FILES['cksumdat']['name']) )
    {
      return lang('error_nofileuploaded');
    }
  else if( $_FILES['cksumdat']['error'] > 0 )
    {
      return lang('error_uploadproblem');
    }
  
  $fh = fopen($_FILES['cksumdat']['tmp_name'],'r');
  if( !$fh )
    {
      return lang('error_uploadproblem');
    }
  
  global $gCms;
  $config =& $gCms->GetConfig();
  $filenotfound = array();
  $notreadable = 0;
  $md5failed = 0;
  $filesfailed = array();
  while( !feof($fh) )
    {
      // get a line
      $line = fgets($fh,4096);

      // strip out comments
      $pos = strpos($line,'#');
      if( $pos )
	{
	  $line = substr($line,0,$pos);
	}

      // trim the line
      $line = trim($line);

      // skip empty line
      if( empty($line) ) continue;

      // split it into fields
      list($md5sum,$file) = explode(' *',$line,2);
      $md5sum = trim($md5sum);
      $file = trim($file);

      $fn = cms_join_path($config['root_path'],$file);
      if( !file_exists( $fn ) )
	{
	  $filenotfound[] = $file;
	  continue;
	}

      if( !is_readable( $fn ) )
	{
	  $notreadable++;
	  continue;
	}

      $md5 = md5_file($fn);
      if( !$md5 )
	{
	  $md5failed++;
	  continue;
	}

      if( $md5sum != $md5 )
	{
	  $filesfailed[] = $file;
	}
    }
  fclose($fh);

  if( count($filenotfound) || $notreadable || $md5failed || count($filesfailed) )
    {
      // build the error report
      $tmp2 = array();
      if( count($filenotfound) )
	{
	  $tmp2[] = sprintf("%d %s",count($filenotfound),lang('files_not_found'));
	}
      if( $notreadable )
	{
	  $tmp2[] = sprintf("%d %s",$notreadable,lang('files_not_readable'));
	}
      if( $md5failed )
	{
	  $tmp2[] = sprintf("%d %s",$md5failed,lang('files_checksum_failed'));
	}
      $tmp = implode( "<br/>", $tmp2 );
      if( count($filenotfound) )
	{
	  $tmp .= "<br/>".lang('files_not_found').':';
	  $tmp .= "<br/>".implode("<br/>",$filenotfound)."<br/>";
	}
      if( count($filesfailed) )
	{
	  $tmp .= "<br/>".count($filesfailed).' '.lang('files_failed').':';
	  $tmp .= "<br/>".implode("<br/>",$filesfailed)."<br/>";
	}
      
      $report = $tmp;
      return false;
    }

  return true;
}


function generate_checksum_file()
{
  echo 'GENERATE';
}

// Get ready
global $gCms;
$smarty =& $gCms->GetSmarty();
$smarty->register_function('lang','checksum_lang');
$smarty->caching = false;
$smarty->force_compile = true;
$db = &$gCms->GetDb();

// Handle output
$res = '';
$report = '';
if( isset($_GET['action']) )
  {
    switch($_GET['action'])
      {
      case 'upload':
	$res = check_checksum_data($report);
	break;
      case 'download':
	$res = generate_checksum_file($report);
	break;
      }
  }

if( !$res )
  {
    $smarty->assign('message',$report);
  }
// Display the output
echo $smarty->fetch('checksum.tpl');
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
