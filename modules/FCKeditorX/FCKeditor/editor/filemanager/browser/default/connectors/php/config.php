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
 * 	Configuration file for the File Manager Connector for PHP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */
include_once('../../../../../../../../../include.php');
check_login();

global $Config ;

include_once 'io.php';

// SECURITY: You must explicitelly enable this "connector". (Set it to "true").
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

$logger = new Logger($config["root_path"]."/tmp/cache/LogBrowser");

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



$logger->close();
////////////////////////////////////////////////
// Path to uploaded files relative to the document root.
//  $Config['UserFilesPath'] = '/cms-daily/modules/FCKeditorX/FCKeditor/UserFiles/' ;

$Config['AllowedExtensions']['File']	= array() ;
$Config['DeniedExtensions']['File']		= array('php','php3','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi') ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png') ;
$Config['DeniedExtensions']['Image']	= array() ;

$Config['AllowedExtensions']['Flash']	= array('swf','fla') ;
$Config['DeniedExtensions']['Flash']	= array() ;

$Config['AllowedExtensions']['Media']	= array('swf','fla','jpg','gif','jpeg','png','avi','mpg','mpeg') ;
$Config['DeniedExtensions']['Media']	= array() ;

?>
