<?php

@ob_start();

// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
 
// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

// Language shizzle
//header("Content-Encoding: " . get_encoding());
//header("Content-Language: " . $current_language);
if (!isset($charsetsent))
{
	header("Content-Type: text/html; charset=" . get_encoding());
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<title><?php echo lang('adminsystemtitle')?></title>

<link rel="stylesheet" type="text/css" href="style.php" />
<link rel="stylesheet" type="text/css" href="tab.php" />

<script type="text/javascript" src="../lib/helparea.js"></script>

<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->

</head>

<body##BODYSUBMITSTUFFGOESHERE##>

<?php include_once('topmenu.php') ?>

<div id="MainContent">

<div class="DashboardCallout">

<p class="DashboardCalloutTitle">Bookmarks</p>

</div> <!-- end DashboardCallout -->
