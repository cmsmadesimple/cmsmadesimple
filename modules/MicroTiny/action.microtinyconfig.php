<?php
if( !cmsms() ) exit();
header("Content-type:text/javascript; charset=utf-8");

// Adapted from http://www.php.net/manual/en/function.session-id.php#54084
// session_id() returns an empty string if there is no current session, sotest if a session already exists before calling session_start() to prevent error notices:
if(session_id() == '') {
	session_start();
}

$frontend=false;
if (isset($params['frontend']) && $params['frontend'] == true) {
  $frontend=true;
  echo 'hi'; // Stikki says: hi hi, how you doing?
}

$templateid='';
if (isset($params['templateid'])) $templateid=$params['templateid'];

$languageid='';
if (isset($params['languageid'])) $languageid=$params['languageid'];



$configcontent = microtiny_utils::GenerateConfig($frontend,$templateid,$languageid);
echo $configcontent;

exit();
?>
