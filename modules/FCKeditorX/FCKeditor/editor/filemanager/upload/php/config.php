<?php
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * File Name: config.php
 * 	Configuration file for the PHP File Uploader.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

global $Config ;

include_once('../../../../../../../include.php');
check_login();

require('util.php') ;


// SECURITY: You must explicitelly enable this "uploader". 
$Config['Enabled'] = true ;

////////////////////////////////////////////////

$cfgfilename = '../config.php';

while(!@file_exists($cfgfilename)){
            $cfgfilename = "../".$cfgfilename;
}
@include($cfgfilename);


$Logfilename = '../logger.php';
while(!@file_exists($Logfilename)){
            $Logfilename = "../".$Logfilename;
}
@include($Logfilename) ;

$logger = new Logger($config["root_path"]."/tmp/cache/LogUpload");

$logger->log("0_5_3");
$root_p = GetRootPath();
$logger->log($root_p);
$logger->log("uploads_path--|".$config["uploads_path"]);

$config["uploads_path"] = trim(str_replace( '\\', '/', $config["uploads_path"] ));

$logger->log("uploads_path--|".$config["uploads_path"]);

$toks =  explode('/', $config["uploads_path"]);
$ntoks = count($toks);

$a = $toks[$ntoks-2];
$b = $toks[$ntoks-1];

$logger->log("a--|".$a);
$logger->log("b--|".$b);

$toks =  explode('/', $root_p);
$ntoks = count($toks);
$c = $toks[$ntoks-1];

$logger->log("c--|".$c);

if (strcmp($a, $c) == 0)
  $Config['UserFilesPath'] = '/'.$b.'/' ;
else 
  $Config['UserFilesPath'] = '/'.$a.'/'.$b.'/' ;


$logger->log("UserFilesPath--|".$Config['UserFilesPath']);

if (!file_exists($root_p.$Config['UserFilesPath']."images"))
  mkdir($root_p.$Config['UserFilesPath']."images", 0755);

if (!file_exists($root_p.$Config['UserFilesPath']."File"))
  mkdir($root_p.$Config['UserFilesPath']."File", 0755);

if (!file_exists($root_p.$Config['UserFilesPath']."Flash"))
  mkdir($root_p.$Config['UserFilesPath']."Flash", 0755);
  
if (!file_exists($root_p.$Config['UserFilesPath']."Media"))
  mkdir($root_p.$Config['UserFilesPath']."Media", 0755);  

$logger->close();
////////////////////////////////////////////////
// Path to uploaded files relative to the document root.
// $Config['UserFilesPath'] = '/cms-daily/modules/FCKeditorX/FCKeditor/UserFiles/' ;

$Config['AllowedExtensions']['File']	= array() ;
$Config['DeniedExtensions']['File']		= array('php','php3','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi') ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png') ;
$Config['DeniedExtensions']['Image']	= array() ;

$Config['AllowedExtensions']['Flash']	= array('swf','fla') ;
$Config['DeniedExtensions']['Flash']	= array() ;

?>
