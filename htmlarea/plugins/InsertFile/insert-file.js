// Character Map plugin for HTMLArea
// Sponsored by http://www.systemconcept.de
// Implementation by Holger Hees based on HTMLArea XTD 1.5 (http://mosforge.net/projects/htmlarea3xtd/)
// Original Author - Bernhard Pfeifer novocaine@gmx.net 
//
// (c) systemconcept.de 2004
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).
function InsertFile(editor) {
        this.editor = editor;
	        
        var cfg = editor.config;
	var toolbar = cfg.toolbar;
	var self = this;
	var i18n = InsertFile.I18N;
        
	cfg.registerButton({
                id       : "insertfile",
                tooltip  : i18n["InsertFileTooltip"],
                image    : editor.imgURL("insert-file.gif", "InsertFile"),
                textMode : false,
                action   : function(editor) {
                                self.buttonPress(editor);
                           }
            })

	var a, i, j, found = false;
	for (i = 0; !found && i < toolbar.length; ++i) {
		a = toolbar[i];
		for (j = 0; j < a.length; ++j) {
			if (a[j] == "inserthorizontalrule") {
				found = true;
				break;
			}
		}
	}
	if (found)
	    a.splice(j, 0, "insertfile");
        else{                
            toolbar[1].splice(0, 0, "separator");
	    toolbar[1].splice(0, 0, "insertfile");
        }
}

InsertFile._pluginInfo = {
	name          : "InsertFile",
	version       : "1.1",
	developer     : "Al Rashid",
	developer_url : "http://alrashid.klokan.sk",
	c_owner       : "Al Rashid",
	sponsor       : "",
	sponsor_url   : "",
	license       : "htmlArea"
}

InsertFile.prototype.buttonPress = function(editor) {
	var sel = editor._getSelection();
	var range = editor._createRange(sel);
	
    
	var outparam = null;
/*
	if (typeof image == "undefined") {
		image = this.getParentElement();
		if (image && !/^img$/i.test(image.tagName))
			image = null;
	}
	if (image) outparam = {
		f_url    : HTMLArea.is_ie ? image.src : image.getAttribute("src"),
		f_alt    : image.alt,
		f_border : image.border,
		f_align  : image.align,
		f_vert   : image.vspace,
		f_horiz  : image.hspace,
		f_width  : image.width,
		f_height  : image.height
	}
*/
	var manager = _editor_url + 'plugins/InsertFile/insert_file.php';

	Dialog(manager, function(param) {
		if (!param) {	// user must have pressed Cancel
			return false;
		}
	var doc = editor._doc;
	var insertText = "";
	for (var k in param["folders"]){
			var folderValues = param["folders"][k];
			var alink = new String(param["format"]);
			alink = alink.replace(/IF_URL/g, param["path"]+folderValues[1]);
			alink = alink.replace(/IF_CAPTION/g, folderValues[1]);
			alink =	alink.replace(/IF_ICON/g, folderValues[0]);
			alink = alink.replace(/IF_SIZE/g, folderValues[2]);
			alink = alink.replace(/IF_DATE/g, folderValues[3]);
			alink = alink.replace(/_editor_url/g, _editor_url);
			insertText = insertText + alink.toString() + "<br />";
	}

	for (var k in param["files"]){
			var fileValues = param["files"][k];
			var alink = new String(param["format"]);
			alink = alink.replace(/IF_URL/g, param["path"]+fileValues[1]);
			alink = alink.replace(/IF_CAPTION/g, fileValues[1]);
			alink =	alink.replace(/IF_ICON/g, fileValues[0]);
			alink = alink.replace(/IF_SIZE/g, fileValues[2]);
			alink = alink.replace(/IF_DATE/g, fileValues[3]);
			alink = alink.replace(/_editor_url/g, _editor_url);
			insertText = insertText + alink.toString() + "<br />";
	}
	editor.insertHTML(insertText);
		
	}, outparam);
    
}

