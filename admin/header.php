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
header("Content-Encoding: " . get_encoding());
header("Content-Language: " . $current_language);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html40/loose.dtd">
<html>
<head>

<title><?php echo lang('adminsystemtitle')?></title>

<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" language="javascript" src="helparea.js"></script>

<?php if (isset($htmlarea_flag) && isset($htmlarea_replaceall)) {?>
	<script type="text/javascript">
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		_editor_lang = "en";
	</script>

	<script type="text/javascript" src="<?php echo $config["root_url"]?>/htmlarea/htmlarea.js"></script>
	<script type="text/javascript" defer>
		var editor = null;
		function initHtmlArea() {
			HTMLArea.replaceAll();
		}
	</script>

<?php } else if (isset($htmlarea_flag)) { ?>

	<script type="text/javascript">
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		_editor_lang = "en";
	</script>

	<script type="text/javascript" src="<?php echo $config["root_url"]?>/htmlarea/htmlarea.js"></script>

	<script type="text/javascript">

		HTMLArea.loadPlugin("TableOperations");
		HTMLArea.loadPlugin("ContextMenu");
		HTMLArea.loadPlugin("CharacterMap");
		var editor = null;
		function initHtmlArea() {
			editor = new HTMLArea("content");
			editor.registerPlugin(TableOperations);
			editor.registerPlugin(ContextMenu);
			editor.registerPlugin(CharacterMap);
			editor.config.pageStyle = '<?php echo str_replace("'", "\\'", get_stylesheet($template_id))?>';
			editor.generate();
		}
	</script>
<?php }
$userid = get_userid();
if (get_preference($userid, 'use_wysiwyg') == "1" && isset($htmlarea_flag)){
	$load = "onload=\"initHtmlArea();\""; ?>
<?php } ?>

</head>
<body <?php echo (isset($load)?$load:""); ?>>

<div id="header" class="header">
<img src="../images/cms/cmsadminbanner.png" border="0" id="logo" alt="CMS Made Simple">
</div>

<?php
include_once("menu.php");
?>

<div class="content">
