function TinyMCE_test_getInsertImageTemplate() {
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

function TinyMCE_test_execCommand(editor_id, element, command, user_interface, value) {
	//alert(editor_id + "," + element.innerHTML + "," + command + "," + user_interface + "," + value);

	return false;
}