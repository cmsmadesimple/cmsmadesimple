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
header("Content-Type: text/html; charset=" . get_encoding());

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html40/loose.dtd">
<HTML>
<HEAD>

<TITLE><?php echo lang('adminsystemtitle')?></TITLE>

<LINK rel="stylesheet" type="text/css" href="style.css">
<SCRIPT type="text/javascript" language="javascript" src="helparea.js"></SCRIPT>

<!--
<script type="text/javascript" language="Javascript">
	function silent_error(){
		return true;
	}
	//turn off all javascript errors
	//window.onerror=silent_error;

	function saveCaret(elem){
		if (document.selection.createRange){
			elem.caretPos = document.selection.createRange();
		}
	}

	function getCaretPos(elem)
	{
		if (elem.caretPos){
			var bookmark = "~";
			var orig = elem.value;
			var caretPos = elem.caretPos;
			caretPos.text = bookmark;
			var i = elem.innerText.search( bookmark );
			elem.value = orig;
			return i;
		}
	}

	function syntax_highlight_remove(called_by, element, plainName){
		var syntax_highlight = element;
		var plain;

		for (var j=0; j < document.forms.length; j++) {
			if (document.forms[j].elements[plainName]) {
				plain = document.forms[j].elements[plainName];
			}
		}

		if (called_by == 'onClick' && !(plain.createTextRange)) {
			syntax_highlight.style.display='none'; 
			plain.style.display='block';
		}else if(called_by == 'onKeyDown' && plain.createTextRange){
			saveCaret(syntax_highlight);
			syntax_highlight.style.display='none'; 
			plain.style.display='block';
			plain.focus();
			var pos = getCaretPos(syntax_highlight);
			var r = plain.createTextRange();
			r.moveStart('character', pos) ;
			r.collapse();
			r.select();
		}
	}

</script>
-->

<?php if (isset($htmlarea_flag) && isset($htmlarea_replaceall)) {?>
	<SCRIPT type="text/javascript">
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		<?php echo "_editor_lang = \"".$nls['htmlarea'][$current_language]."\";"  ?>
	</SCRIPT>

	<SCRIPT type="text/javascript" src="<?php echo $config["root_url"]?>/htmlarea/htmlarea.js"></SCRIPT>
	<SCRIPT type="text/javascript" defer>
		var editor = null;
		function initHtmlArea() {
			HTMLArea.replaceAll();
		}
	</SCRIPT>

<?php } else if (isset($htmlarea_flag)) { ?>

	<SCRIPT type="text/javascript">
		_editor_url = "<?php echo $config["root_url"]?>/htmlarea/";
		<?php echo "_editor_lang = \"{$nls['htmlarea'][$current_language]}\";"  ?>
	</SCRIPT>

	<SCRIPT type="text/javascript" src="<?php echo $config["root_url"]?>/htmlarea/htmlarea.js"></SCRIPT>

	<SCRIPT type="text/javascript">

		HTMLArea.loadPlugin("TableOperations");
		HTMLArea.loadPlugin("ContextMenu");
		HTMLArea.loadPlugin("CharacterMap");
		HTMLArea.loadPlugin("Indite");
		var editor = null;
		function initHtmlArea() {
			editor = new HTMLArea("content");
			editor.registerPlugin(TableOperations);
			editor.registerPlugin(ContextMenu);
			editor.registerPlugin(CharacterMap);
			editor.registerPlugin(Indite);
			editor.config.pageStyle = '<?php echo str_replace("'", "\\'", get_stylesheet($template_id))?>';
			editor.generate();
		}
	</SCRIPT>
<?php }
$userid = get_userid();
?>

<SCRIPT type="text/javascript" language="Javascript">;
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

<DIV id="header" class="header">
<IMG src="../images/cms/cmsadminbanner.png" border="0" id="logo" alt="CMS Made Simple">
</DIV>

<?php
include_once("menu.php");
?>

<DIV class="content">
