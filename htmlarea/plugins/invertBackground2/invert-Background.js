
function invertBackground(editor) {
	this.editor = editor;
	var cfg = editor.config;
	var toolbar = cfg.toolbar;
	var self = this;

	cfg.registerButton({
		id       : "invertBackground",
		tooltip  : "Invert Background/Foreground",
		image    : editor.imgURL("invertBackground.gif", "invertBackground"),
		textMode : false,
		action   : function(editor) {self.buttonPress(editor);}
	})

	//Finds location to place new icon
	var a, i, j, found = false;
	for (i = 0; !found && i < toolbar.length; ++i) {
		a = toolbar[i];
		for (j = 0; j < a.length; ++j) {
			if (a[j] == "popupeditor") {
				found = true;
				break;
			}
		}
	}

	//inserts new icon
	if (found)
		a.splice(j, 0, "invertBackground");
	else{
		toolbar[1].splice(0, 0, "separator");
		toolbar[1].splice(0, 0, "invertBackground");
	}
};

invertBackground._pluginInfo = {
	name          : "invertBackground",
	version       : "1.0",
	developer     : "Brett Batie",
	developer_url : "http://www.provisiontech.net/",
	c_owner       : "Brett Batie",
	sponsor       : "Provision Tech",
	sponsor_url   : "http://www.provisiontech.net/",
	license       : "Free"
};

invertBackground.prototype.buttonPress = function(editor) {
	//invert background color
	if ((editor._doc.body.style.background).indexOf("black") < 0){
		editor._doc.body.style.background = 'black';
		editor._doc.body.style.color = 'white';
	}else{
		editor._doc.body.style.background = 'white';
		editor._doc.body.style.color = 'black';
	}
	
}

