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

?>
<!DOCTYPE html PUBLIC "-//W4C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title><?=$gettext->gettext("CMS Admin System")?></title>

<link rel="stylesheet" type="text/css" href="style.css" />

<?php if (isset($htmlarea_flag)) { ?>

	<script type="text/javascript">
		_editor_url = "<?=$config->root_url?>/htmlarea/";
		_editor_lang = "en";
	</script>

	<script type="text/javascript" src="<?=$config->root_url?>/htmlarea/htmlarea.js"></script>

	<script type="text/javascript">

		HTMLArea.loadPlugin("TableOperations");
		HTMLArea.loadPlugin("ContextMenu");
		var editor = null;
		function initHtmlArea() {
			editor = new HTMLArea("content");
			editor.registerPlugin(TableOperations);
			editor.registerPlugin(ContextMenu);
			editor.config.pageStyle = '<?=get_stylesheet($template_id)?>';
			editor.generate();
		}

	</script>

<?php } ?>

</head>

<body <?php if (isset($htmlarea_flag)) { ?>onload="initHtmlArea();"<?php } ?>>

<img src="../images/cmsadminbanner.png" border="0" id="logo" alt="CMS Made Simple"/>
<div id="header" class="header">

</div>

<?php
include_once("menu.php");
?>

<div class="content">
