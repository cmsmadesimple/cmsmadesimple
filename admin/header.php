<?

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

?>
<!DOCTYPE html PUBLIC "-//W4C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title><?=$gettext->gettext("CMS Admin System")?></title>

<link rel="stylesheet" type="text/css" href="style.css" />

<?php 

if (isset($tinymce_flag)) { ?>

<!-- tinyMCE -->
 <script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
 <script language="javascript" type="text/javascript">
  	tinyMCE.init({
  		theme : "advanced",
  		mode : "textareas",
  		content_css : "../stylesheet.php?templateid=<?=$template_id?>",
		theme_advanced_source_editor_width : "640",
		theme_advanced_source_editor_height : "480",
		theme_advanced_source_editor_area_width : "600",
		theme_advanced_source_editor_area_height : "380",		
		theme_advanced_buttons2: "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,image,cleanup,code",
  		debug : false
	});
 </script>
 <!-- /tinyMCE -->
 
 <?php } ?>
 


</head>
</head>

<body>

<img src="../images/cmsadminbanner.png" border="0" id="logo" alt="CMS Made Simple"/>
<div id="header" class="header">

</div>

<?php
include_once("menu.php");
?>

<div class="content">
