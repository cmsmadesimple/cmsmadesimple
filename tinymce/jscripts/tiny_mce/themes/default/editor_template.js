function TinyMCE_default_getEditorTemplate() {
	var template = new Array();

	template['html'] = '\
<table class="mceEditor" border="0" cellpadding="0" cellspacing="0" width="{$width}" height="{$height}">\
<tr><td align="center">\
<iframe id="{$editor_id}" class="mceEditorArea" border="1" frameborder="0" src="{$default_document}" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" style="width:{$area_width};height:{$area_height}" width="{$area_width}" height="{$area_height}"></iframe>\
</td></tr>\
<tr><td class="mceToolbar" align="center" height="1">\
<img id="{$editor_id}_bold" src="{$themeurl}/images/{$lang_bold_img}" title="{$lang_bold_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'Bold\')">\
<img id="{$editor_id}_italic" src="{$themeurl}/images/{$lang_italic_img}" title="{$lang_italic_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'Italic\')">\
<img id="{$editor_id}_underline" src="{$themeurl}/images/underline.gif" title="{$lang_underline_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'Underline\')">\
<img id="{$editor_id}_strikethrough" src="{$themeurl}/images/strikethrough.gif" title="{$lang_striketrough_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'Strikethrough\')">\
<img src="{$themeurl}/images/spacer.gif" width="1" height="15" class="mceSeparatorLine">\
<img id="{$editor_id}_left" src="{$themeurl}/images/left.gif" title="{$lang_justifyleft_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'JustifyLeft\')">\
<img id="{$editor_id}_center" src="{$themeurl}/images/center.gif" title="{$lang_justifycenter_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'JustifyCenter\')">\
<img id="{$editor_id}_right" src="{$themeurl}/images/right.gif" title="{$lang_justifyright_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'JustifyRight\')">\
<img id="{$editor_id}_full" src="{$themeurl}/images/full.gif" title="{$lang_justifyfull_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'JustifyFull\')">\
<img src="{$themeurl}/images/spacer.gif" width="1" height="15" class="mceSeparatorLine">\
<img src="{$themeurl}/images/outdent.gif" title="{$lang_outdent_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'Outdent\')">\
<img src="{$themeurl}/images/indent.gif" title="{$lang_indent_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'Indent\')">\
<br>\
<img id="{$editor_id}_bullist" src="{$themeurl}/images/bullist.gif" title="{$lang_bullist_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'InsertUnorderedList\')">\
<img id="{$editor_id}_numlist" src="{$themeurl}/images/numlist.gif" title="{$lang_numlist_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'InsertOrderedList\')">\
<img src="{$themeurl}/images/spacer.gif" width="1" height="15" width="20" height="20" class="mceSeparatorLine">\
<img src="{$themeurl}/images/undo.gif" title="{$lang_undo_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'Undo\')">\
<img src="{$themeurl}/images/redo.gif" title="{$lang_redo_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'Redo\')">\
<img src="{$themeurl}/images/spacer.gif" width="1" height="15" class="mceSeparatorLine">\
<img id="{$editor_id}_link" src="{$themeurl}/images/link.gif" title="{$lang_link_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'mceLink\', true)">\
<img src="{$themeurl}/images/unlink.gif" title="{$lang_unlink_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'unlink\')">\
<img id="{$editor_id}_image" src="{$themeurl}/images/image.gif" title="{$lang_image_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'mceImage\', true)">\
<img src="{$themeurl}/images/cleanup.gif" title="{$lang_cleanup_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'mceCleanup\')">\
<img src="{$themeurl}/images/help.gif" title="{$lang_help_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execCommand(\'mceHelp\')">\
</td></tr>\
</table>';

	template['delta_width'] = 0;
	template['delta_height'] = -40;

	return template;
}

function TinyMCE_default_getInsertLinkTemplate() {
	var template = new Array();

	template['html'] = '\
<html><head><title>{$lang_insert_link_title}</title>\
<link href="{$css}" rel="stylesheet" type="text/css">\
<script language="javascript">\
function init() {\
	for (var i=0; i<document.forms[0].target.options.length; i++) {\
		var option = document.forms[0].target.options[i];\
\
		if (option.value == \'{$target}\')\
			option.selected = true;\
	}\
\
	window.focus();\
}\
\
function insertLink() {\
	if (window.opener) {\
		var href = document.forms[0].href.value;\
		var target = document.forms[0].target.options[document.forms[0].target.selectedIndex].value;\
\
		window.opener.tinyMCE.insertLink(href, target);\
		top.close();\
	}\
}\
\
function cancelAction() {\
	top.close();\
}\
</script>\
</head><body onload="init();">\
\
<form onsubmit="insertLink();return false;">\
<table border="0" cellpadding="0" cellspacing="0" width="100%">\
<tr><td align="center" valign="middle">\
<table border="0" cellpadding="4" cellspacing="0">\
<tr><td colspan="2" class="title">{$lang_insert_link_title}</td></tr>\
<tr><td>{$lang_insert_link_url}:</td><td><input name="href" type="text" id="href" value="{$href}" style="width: 200px"></td></tr>\
<tr><td>{$lang_insert_link_target}:</td>\
<td><select name="target" style="width: 200px">\
<option value="_self">{$lang_insert_link_target_same}</option>\
<option value="_blank">{$lang_insert_link_target_blank}</option>\
</select></td></tr>\
<tr><td><input type="button" name="insert" value="{$lang_insert}" onclick="insertLink();">\
</td><td align="right"><input type="button" name="cancel" value="{$lang_cancel}" onclick="cancelAction();"></td></tr>\
</table>\
</td></tr></table>\
</form></body></html>';

	template['width'] = 320;
	template['height'] = 130;

	return template;
}

function TinyMCE_default_getInsertImageTemplate() {
	var template = new Array();

	template['html'] = '\
<html><head><title>{$lang_insert_image_title}</title>\
<link href="{$css}" rel="stylesheet" type="text/css">\
\
<script language="javascript">\
function insertImage() {\
	if (window.opener) {\
		var src = document.forms[0].src.value;\
		var alt = document.forms[0].alt.value;\
\
		window.opener.tinyMCE.insertImage(src, alt, "0");\
		top.close();\
	}\
}\
\
function cancelAction() {\
	top.close();\
}\
</script>\
</head><body onload="window.focus();">\
<form onsubmit="insertImage();return false;">\
<table border="0" cellpadding="0" cellspacing="0" width="100%">\
<tr><td align="center" valign="middle">\
<table border="0" cellpadding="4" cellspacing="0">\
<tr><td colspan="2" class="title">{$lang_insert_image_title}</td></tr>\
<tr><td>{$lang_insert_image_src}:</td><td><input name="src" type="text" id="src" value="{$src}" style="width: 200px"></td></tr>\
<tr><td>{$lang_insert_image_alt}:</td>\
<td><input name="alt" type="text" id="alt" value="{$alt}" style="width: 200px"></td></tr>\
<tr><td><input type="button" name="insert" value="{$lang_insert}" onclick="insertImage();">\
</td><td align="right"><input type="button" name="cancel" value="{$lang_cancel}" onclick="cancelAction();"></td></tr>\
</table>\
</td></tr></table>\
</form></body></html>';

	template['width'] = 340;
	template['height'] = 130;

	return template;
}

function TinyMCE_default_handleNodeChange(editor_id, node) {
	// Reset old states
	tinyMCE.switchClassSticky(editor_id + '_left', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_right', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_center', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_full', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_bold', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_italic', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_underline', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_strikethrough', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_bullist', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_numlist', 'mceButtonNormal');

	// Handle align attributes
	alignNode = node;
	breakOut = false;
	do {
		if (!alignNode.getAttribute || !alignNode.getAttribute('align'))
			continue;

		switch (alignNode.getAttribute('align').toLowerCase()) {
			case "left":
				tinyMCE.switchClassSticky(editor_id + '_left', 'mceButtonSelected');
				breakOut = true;
			break;

			case "right":
				tinyMCE.switchClassSticky(editor_id + '_right', 'mceButtonSelected');
				breakOut = true;
			break;

			case "middle":
			case "center":
				tinyMCE.switchClassSticky(editor_id + '_center', 'mceButtonSelected');
				breakOut = true;
			break;

			case "justify":
				tinyMCE.switchClassSticky(editor_id + '_full', 'mceButtonSelected');
				breakOut = true;
			break;
		}
	} while (!breakOut && (alignNode = alignNode.parentNode));

	// Handle elements
	do {
		switch (node.nodeName.toLowerCase()) {
			case "b":
			case "strong":
				tinyMCE.switchClassSticky(editor_id + '_bold', 'mceButtonSelected');
			break;

			case "i":
			case "em":
				tinyMCE.switchClassSticky(editor_id + '_italic', 'mceButtonSelected');
			break;

			case "u":
				tinyMCE.switchClassSticky(editor_id + '_underline', 'mceButtonSelected');
			break;

			case "strike":
				tinyMCE.switchClassSticky(editor_id + '_strikethrough', 'mceButtonSelected');
			break;
			
			case "ul":
				tinyMCE.switchClassSticky(editor_id + '_bullist', 'mceButtonSelected');
			break;

			case "ol":
				tinyMCE.switchClassSticky(editor_id + '_numlist', 'mceButtonSelected');
			break;
		}
	} while ((node = node.parentNode));
}
