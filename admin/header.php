<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

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
  		content_css : "../style.css",
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
