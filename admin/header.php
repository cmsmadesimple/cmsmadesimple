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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html40/loose.dtd">
<HTML>
<HEAD>

<TITLE><?php echo lang('adminsystemtitle')?></TITLE>

<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">
<LINK REL="stylesheet" TYPE="text/css" HREF="tab.css">
<SCRIPT TYPE="text/javascript" LANGUAGE="javascript" SRC="helparea.js"></SCRIPT>

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
  //@param form - current form
  //@param names - string containing a list of the names used for the textarea
  //               each name should be seperated with a comma
  function textarea_submit(form, names){
    var name = names.split(",");
    var appletName = document.getElementsByName("CMSSyntaxHighlight");
    
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
  }
</SCRIPT>

<?php if (isset($htmlarea_flag) && isset($htmlarea_replaceall)) {?>
	<SCRIPT TYPE="text/javascript">
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		<?php echo "_editor_lang = \"".$nls['htmlarea'][$current_language]."\";"  ?>
	</SCRIPT>

	<SCRIPT TYPE="text/javascript" SRC="<?php echo $config["root_url"]?>/htmlarea/htmlarea.js"></SCRIPT>
	<SCRIPT TYPE="text/javascript" DEFER>
		var editor = null;
		function initHtmlArea() {
			HTMLArea.replaceAll();
		}
	</SCRIPT>

<?php } else if (isset($htmlarea_flag)) { ?>

	<SCRIPT TYPE="text/javascript">
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		<?php echo "_editor_lang = \"{$nls['htmlarea'][$current_language]}\";"  ?>
	</SCRIPT>

	<SCRIPT TYPE="text/javascript" SRC="<?php echo $config["root_url"]?>/htmlarea/htmlarea.js"></SCRIPT>
	<SCRIPT TYPE="text/javascript">

		HTMLArea.loadPlugin("ImageManager");
		HTMLArea.loadPlugin("InsertFile");
		HTMLArea.loadPlugin("TableOperations");
		HTMLArea.loadPlugin("ContextMenu");
		HTMLArea.loadPlugin("CharacterMap");
		HTMLArea.loadPlugin("FindReplace");
		HTMLArea.loadPlugin("InvertBackground");
<?php if ($config["use_Indite"] == true)	{ ?>	
		HTMLArea.loadPlugin("Indite");
<?php } ?>	
		var editor = null;
		function initHtmlArea() {
			editor = new HTMLArea("content");
			editor.registerPlugin(ImageManager);
			<?php 
				// Ugly Hack alert! making setting session var to send language setting to insertFile
				$_SESSION['InsertFileLang'] = $nls['htmlarea'][$current_language];		
		 	?>			
			editor.registerPlugin(InsertFile);
			editor.registerPlugin(TableOperations);
			editor.registerPlugin(ContextMenu);
			editor.registerPlugin(CharacterMap);
			editor.registerPlugin(FindReplace);
			editor.registerPlugin(InvertBackground);
<?php if ($config["use_Indite"] == true)	{ ?>	
			editor.registerPlugin(Indite);
<?php } 
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
	</SCRIPT>
<?php }
$userid = get_userid();
?>

<SCRIPT TYPE="text/javascript" LANGUAGE="Javascript">;
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
</SCRIPT>

</HEAD>
<BODY onLoad="page_load()">
<DIV ID="header" CLASS="header">
<IMG SRC="../images/cms/cmsadminbanner.gif" BORDER="0" ID="logo" ALT="CMS Made Simple">
</DIV>
<DIV ID="sloganWrapper"><DIV ID="slogan"><?php echo lang('slogan'); ?></DIV></DIV>

<?php
include_once("menu.php");
?>

<DIV CLASS="content">
