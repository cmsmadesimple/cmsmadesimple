/*---------------------------------------*\
 Find and Replace Plugin for HTMLArea-3.0
 -----------------------------------------
 author: Cau guanabara 
 e-mail: caugb@ibest.com.br
\*---------------------------------------*/

function FindReplace(editor) {
this.editor = editor;
var cfg = editor.config;
var self = this;

cfg.registerButton("FR-findreplace", FindReplace.I18N["Find and Replace"], 
                   editor.imgURL("ed_find.gif", "FindReplace"), false,
                   function(editor) { self.buttonPress(editor); });

var joincfg = cfg.toolbar[0].join("|"); 
  if(/formatblock\|/.test(joincfg))
    cfg.toolbar[0] = joincfg.replace(/formatblock\|(space\|)?/,
                     "formatblock|space|FR-findreplace|separator|").split("|");
  else if(/fontsize\|/.test(joincfg))
    cfg.toolbar[0] = joincfg.replace(/fontsize\|(space\|)?/,
                     "fontsize|space|FR-findreplace|separator|").split("|");
  else if(/fontname\|/.test(joincfg))
    cfg.toolbar[0] = joincfg.replace(/fontname\|(space\|)?/,
                     "fontname|space|FR-findreplace|separator|").split("|");
  else 
    cfg.toolbar[0].splice(0, 0, "FR-findreplace", "separator");
};

FindReplace.prototype.buttonPress = function(editor) { 
FindReplace.editor = editor;
var sel = editor.getSelectedHTML();
  if(/\w/.test(sel)) {
  sel = sel.replace(/<[^>]*>/g,"");
  sel = sel.replace(/&nbsp;/g,"");
  }
var param = /\w/.test(sel) ? {fr_pattern: sel} : null;
editor._popupDialog("plugin://FindReplace/find_replace", null, param);
};

FindReplace._pluginInfo = {
  name          : "FindReplace",
  version       : "1.0 - beta",
  developer     : "Cau Guanabara",
  developer_url : "mailto:caugb@ibest.com.br",
  c_owner       : "Cau Guanabara",
  sponsor       : "Independent production",
  sponsor_url   : "http://www.netflash.com.br/gb/HA3-rc1/examples/find-replace.html",
  license       : "htmlArea"
};
