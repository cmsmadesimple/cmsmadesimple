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

<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="stylesheet" type="text/css" href="tab.css" />
<script type="text/javascript" language="javascript" src="helparea.js"></script>

<script language="JavaScript" type="text/javascript">
	<!--
	//@param form - current form
	//@param names - string containing a list of the names used for the textarea
	//               each name should be seperated with a comma
	function textarea_submit(form, names){
		var name = names.split(",");
		var appletName = document.getElementsByName("CMSSyntaxHighlight");
		//fixes form not submitting because applet is hidden
		if (eval(document.getElementById('advanced'))) {expandcontent('advanced');}

		for (var i=0; i < name.length; i++){
			try{
				if(appletName[i].getText()){
					form.elements[name[i].toString()].value = appletName[i].getText();
				}
			}catch(error){
				alert("There was an error with the syntax highlighting textarea.");
				return;
			}
		}

		if (eval(document.getElementById('advanced'))) {expandcontent('advanced');}
	}
	-->
</script>
<?php if (isset($htmlarea_flag) && isset($htmlarea_replaceall)) {?>
	<script type="text/javascript">
		<!--
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		<?php echo "_editor_lang = \"".$nls['htmlarea'][$current_language]."\";"  ?>
		-->
	</script>

	<script type="text/javascript" src="<?php echo $config["root_url"]?>/htmlarea/htmlarea.js"></script>
	<script type="text/javascript" defer>
		<!--
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		var editor = null;
		function initHtmlArea() {
			HTMLArea.replaceAll();
		}
		-->
	</script>

<?php } else if (isset($htmlarea_flag)) { ?>

	<script type="text/javascript">
		<!--
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		<?php
			if ($config["disable_htmlarea_translation"] != true)
			{
				echo "_editor_lang = \"{$nls['htmlarea'][$current_language]}\";";
			}
		?>
		-->
	</script>

	<script type="text/javascript" src="<?php echo $config["root_url"]?>/htmlarea/htmlarea.js"></script>
	<script type="text/javascript">
		<!--

		HTMLArea.loadPlugin("ImageManager");
		HTMLArea.loadPlugin("InsertFile");
		HTMLArea.loadPlugin("TableOperations");
		HTMLArea.loadPlugin("ContextMenu");
		HTMLArea.loadPlugin("CharacterMap");
		HTMLArea.loadPlugin("FindReplace");
		HTMLArea.loadPlugin("InvertBackground");
		<?php if ($config["use_Indite"] == true) { ?>	
			HTMLArea.loadPlugin("Indite");	
		<?php } ?>
		var editor = null;
		function initHtmlArea() {
			editor = new HTMLArea("content");
			editor.registerPlugin(ImageManager);
			<?php 
				// Ugly Hack alert! making setting session var to send language setting to insertFile
				if ($config["disable_htmlarea_translation"] != true)
				{
					$_SESSION['InsertFileLang'] = $nls['htmlarea'][$current_language];
				}
				else
				{
					if (isset($_SESSION['InsertFileLang']))
					{
						unset($_SESSION['InsertFileLang']);
					}
				}
		 	?>
			editor.registerPlugin(InsertFile);
			editor.registerPlugin(TableOperations);
			editor.registerPlugin(ContextMenu);
			editor.registerPlugin(CharacterMap);
			editor.registerPlugin(FindReplace);
			editor.registerPlugin(InvertBackground);

			<?php if ($config["use_Indite"] == true)
				echo "editor.registerPlugin(Indite);";
				$template_id = -1;
				if (isset($_POST["template_id"])) 
					$template_id = $_POST["template_id"];
				else if (isset($_GET['page_id'])){
					$query = "SELECT template_id FROM ".cms_db_prefix()."pages WHERE ".$_GET['page_id']." = page_id";
					$template_id = $db->GetOne($query);
				}
			?>	

			editor.config.pageStyle = editor.config.pageStyle+'<?php echo str_replace("'", "\\'", get_stylesheet($template_id))?>';
			editor.generate();
		}
		-->
	</script>
<?php }
$userid = get_userid();
?>

<script type="text/javascript" language="Javascript">;
	<!--
	function page_load(){
		<?php if (get_preference($userid, 'use_wysiwyg') == "1" && isset($htmlarea_flag)){ ?>
			initHtmlArea();
		<?php } ?>

		for (var j=0; j < document.forms.length; j++) {
			for (var i=0; i < (document.forms[j].elements).length; i++) {
				if (document.forms[j].elements[i].id == "plain") {
					document.forms[j].elements[i].style.display='none';
				}
			}
		}
	}
	-->
</script>

</head>
<body onload="page_load()">
<div id="header" class="header">
<img src="../images/cms/cmsadminbanner.gif" border="0" id="logo" alt="CMS Made Simple" />
</div>
<div id="sloganWrapper"><div id="slogan"><?php echo lang('slogan'); ?></div></div>

<?php
include_once("menu.php");
?>

<div class="content">
