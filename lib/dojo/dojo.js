/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/

/*
	This is a compiled version of Dojo, built for deployment and not for
	development. To get an editable version, please visit:

		http://dojotoolkit.org

	for documentation and information on getting the source.
*/

if(typeof dojo=="undefined"){
var dj_global=this;
var dj_currentContext=this;
function dj_undef(_1,_2){
return (typeof (_2||dj_currentContext)[_1]=="undefined");
}
if(dj_undef("djConfig",this)){
var djConfig={};
}
if(dj_undef("dojo",this)){
var dojo={};
}
dojo.global=function(){
return dj_currentContext;
};
dojo.locale=djConfig.locale;
dojo.version={major:0,minor:0,patch:0,flag:"dev",revision:Number("$Rev: 6912 $".match(/[0-9]+/)[0]),toString:function(){
with(dojo.version){
return major+"."+minor+"."+patch+flag+" ("+revision+")";
}
}};
dojo.evalProp=function(_3,_4,_5){
if((!_4)||(!_3)){
return undefined;
}
if(!dj_undef(_3,_4)){
return _4[_3];
}
return (_5?(_4[_3]={}):undefined);
};
dojo.parseObjPath=function(_6,_7,_8){
var _9=(_7||dojo.global());
var _a=_6.split(".");
var _b=_a.pop();
for(var i=0,l=_a.length;i<l&&_9;i++){
_9=dojo.evalProp(_a[i],_9,_8);
}
return {obj:_9,prop:_b};
};
dojo.evalObjPath=function(_e,_f){
if(typeof _e!="string"){
return dojo.global();
}
if(_e.indexOf(".")==-1){
return dojo.evalProp(_e,dojo.global(),_f);
}
var ref=dojo.parseObjPath(_e,dojo.global(),_f);
if(ref){
return dojo.evalProp(ref.prop,ref.obj,_f);
}
return null;
};
dojo.errorToString=function(_11){
if(!dj_undef("message",_11)){
return _11.message;
}else{
if(!dj_undef("description",_11)){
return _11.description;
}else{
return _11;
}
}
};
dojo.raise=function(_12,_13){
if(_13){
_12=_12+": "+dojo.errorToString(_13);
}else{
_12=dojo.errorToString(_12);
}
try{
if(djConfig.isDebug){
dojo.hostenv.println("FATAL exception raised: "+_12);
}
}
catch(e){
}
throw _13||Error(_12);
};
dojo.debug=function(){
};
dojo.debugShallow=function(obj){
};
dojo.profile={start:function(){
},end:function(){
},stop:function(){
},dump:function(){
}};
function dj_eval(_15){
return dj_global.eval?dj_global.eval(_15):eval(_15);
}
dojo.unimplemented=function(_16,_17){
var _18="'"+_16+"' not implemented";
if(_17!=null){
_18+=" "+_17;
}
dojo.raise(_18);
};
dojo.deprecated=function(_19,_1a,_1b){
var _1c="DEPRECATED: "+_19;
if(_1a){
_1c+=" "+_1a;
}
if(_1b){
_1c+=" -- will be removed in version: "+_1b;
}
dojo.debug(_1c);
};
dojo.render=(function(){
function vscaffold(_1d,_1e){
var tmp={capable:false,support:{builtin:false,plugin:false},prefixes:_1d};
for(var i=0;i<_1e.length;i++){
tmp[_1e[i]]=false;
}
return tmp;
}
return {name:"",ver:dojo.version,os:{win:false,linux:false,osx:false},html:vscaffold(["html"],["ie","opera","khtml","safari","moz"]),svg:vscaffold(["svg"],["corel","adobe","batik"]),vml:vscaffold(["vml"],["ie"]),swf:vscaffold(["Swf","Flash","Mm"],["mm"]),swt:vscaffold(["Swt"],["ibm"])};
})();
dojo.hostenv=(function(){
var _21={isDebug:false,allowQueryConfig:false,baseScriptUri:"",baseRelativePath:"",libraryScriptUri:"",iePreventClobber:false,ieClobberMinimal:true,preventBackButtonFix:true,delayMozLoadingFix:false,searchIds:[],parseWidgets:true};
if(typeof djConfig=="undefined"){
djConfig=_21;
}else{
for(var _22 in _21){
if(typeof djConfig[_22]=="undefined"){
djConfig[_22]=_21[_22];
}
}
}
return {name_:"(unset)",version_:"(unset)",getName:function(){
return this.name_;
},getVersion:function(){
return this.version_;
},getText:function(uri){
dojo.unimplemented("getText","uri="+uri);
}};
})();
dojo.hostenv.getBaseScriptUri=function(){
if(djConfig.baseScriptUri.length){
return djConfig.baseScriptUri;
}
var uri=new String(djConfig.libraryScriptUri||djConfig.baseRelativePath);
if(!uri){
dojo.raise("Nothing returned by getLibraryScriptUri(): "+uri);
}
var _25=uri.lastIndexOf("/");
djConfig.baseScriptUri=djConfig.baseRelativePath;
return djConfig.baseScriptUri;
};
(function(){
var _26={pkgFileName:"__package__",loading_modules_:{},loaded_modules_:{},addedToLoadingCount:[],removedFromLoadingCount:[],inFlightCount:0,modulePrefixes_:{dojo:{name:"dojo",value:"src"}},registerModulePath:function(_27,_28){
this.modulePrefixes_[_27]={name:_27,value:_28};
},moduleHasPrefix:function(_29){
var mp=this.modulePrefixes_;
return Boolean(mp[_29]&&mp[_29].value);
},getModulePrefix:function(_2b){
if(this.moduleHasPrefix(_2b)){
return this.modulePrefixes_[_2b].value;
}
return _2b;
},getTextStack:[],loadUriStack:[],loadedUris:[],post_load_:false,modulesLoadedListeners:[],unloadListeners:[],loadNotifying:false};
for(var _2c in _26){
dojo.hostenv[_2c]=_26[_2c];
}
})();
dojo.hostenv.loadPath=function(_2d,_2e,cb){
var uri;
if(_2d.charAt(0)=="/"||_2d.match(/^\w+:/)){
uri=_2d;
}else{
uri=this.getBaseScriptUri()+_2d;
}
if(djConfig.cacheBust&&dojo.render.html.capable){
uri+="?"+String(djConfig.cacheBust).replace(/\W+/g,"");
}
try{
return !_2e?this.loadUri(uri,cb):this.loadUriAndCheck(uri,_2e,cb);
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.hostenv.loadUri=function(uri,cb){
if(this.loadedUris[uri]){
return true;
}
var _33=this.getText(uri,null,true);
if(!_33){
return false;
}
this.loadedUris[uri]=true;
if(cb){
_33="("+_33+")";
}
var _34=dj_eval(_33);
if(cb){
cb(_34);
}
return true;
};
dojo.hostenv.loadUriAndCheck=function(uri,_36,cb){
var ok=true;
try{
ok=this.loadUri(uri,cb);
}
catch(e){
dojo.debug("failed loading ",uri," with error: ",e);
}
return Boolean(ok&&this.findModule(_36,false));
};
dojo.loaded=function(){
};
dojo.unloaded=function(){
};
dojo.hostenv.loaded=function(){
this.loadNotifying=true;
this.post_load_=true;
var mll=this.modulesLoadedListeners;
for(var x=0;x<mll.length;x++){
mll[x]();
}
this.modulesLoadedListeners=[];
this.loadNotifying=false;
dojo.loaded();
};
dojo.hostenv.unloaded=function(){
var mll=this.unloadListeners;
while(mll.length){
(mll.pop())();
}
dojo.unloaded();
};
dojo.addOnLoad=function(obj,_3d){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.modulesLoadedListeners.push(obj);
}else{
if(arguments.length>1){
dh.modulesLoadedListeners.push(function(){
obj[_3d]();
});
}
}
if(dh.post_load_&&dh.inFlightCount==0&&!dh.loadNotifying){
dh.callLoaded();
}
};
dojo.addOnUnload=function(obj,_40){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.unloadListeners.push(obj);
}else{
if(arguments.length>1){
dh.unloadListeners.push(function(){
obj[_40]();
});
}
}
};
dojo.hostenv.modulesLoaded=function(){
if(this.post_load_){
return;
}
if(this.loadUriStack.length==0&&this.getTextStack.length==0){
if(this.inFlightCount>0){
dojo.debug("files still in flight!");
return;
}
dojo.hostenv.callLoaded();
}
};
dojo.hostenv.callLoaded=function(){
if(typeof setTimeout=="object"){
setTimeout("dojo.hostenv.loaded();",0);
}else{
dojo.hostenv.loaded();
}
};
dojo.hostenv.getModuleSymbols=function(_42){
var _43=_42.split(".");
for(var i=_43.length;i>0;i--){
var _45=_43.slice(0,i).join(".");
if((i==1)&&!this.moduleHasPrefix(_45)){
_43[0]="../"+_43[0];
}else{
var _46=this.getModulePrefix(_45);
if(_46!=_45){
_43.splice(0,i,_46);
break;
}
}
}
return _43;
};
dojo.hostenv._global_omit_module_check=false;
dojo.hostenv.loadModule=function(_47,_48,_49){
if(!_47){
return;
}
_49=this._global_omit_module_check||_49;
var _4a=this.findModule(_47,false);
if(_4a){
return _4a;
}
if(dj_undef(_47,this.loading_modules_)){
this.addedToLoadingCount.push(_47);
}
this.loading_modules_[_47]=1;
var _4b=_47.replace(/\./g,"/")+".js";
var _4c=_47.split(".");
var _4d=this.getModuleSymbols(_47);
var _4e=((_4d[0].charAt(0)!="/")&&!_4d[0].match(/^\w+:/));
var _4f=_4d[_4d.length-1];
var ok;
if(_4f=="*"){
_47=_4c.slice(0,-1).join(".");
while(_4d.length){
_4d.pop();
_4d.push(this.pkgFileName);
_4b=_4d.join("/")+".js";
if(_4e&&_4b.charAt(0)=="/"){
_4b=_4b.slice(1);
}
ok=this.loadPath(_4b,!_49?_47:null);
if(ok){
break;
}
_4d.pop();
}
}else{
_4b=_4d.join("/")+".js";
_47=_4c.join(".");
var _51=!_49?_47:null;
ok=this.loadPath(_4b,_51);
if(!ok&&!_48){
_4d.pop();
while(_4d.length){
_4b=_4d.join("/")+".js";
ok=this.loadPath(_4b,_51);
if(ok){
break;
}
_4d.pop();
_4b=_4d.join("/")+"/"+this.pkgFileName+".js";
if(_4e&&_4b.charAt(0)=="/"){
_4b=_4b.slice(1);
}
ok=this.loadPath(_4b,_51);
if(ok){
break;
}
}
}
if(!ok&&!_49){
dojo.raise("Could not load '"+_47+"'; last tried '"+_4b+"'");
}
}
if(!_49&&!this["isXDomain"]){
_4a=this.findModule(_47,false);
if(!_4a){
dojo.raise("symbol '"+_47+"' is not defined after loading '"+_4b+"'");
}
}
return _4a;
};
dojo.hostenv.startPackage=function(_52){
var _53=String(_52);
var _54=_53;
var _55=_52.split(/\./);
if(_55[_55.length-1]=="*"){
_55.pop();
_54=_55.join(".");
}
var _56=dojo.evalObjPath(_54,true);
this.loaded_modules_[_53]=_56;
this.loaded_modules_[_54]=_56;
return _56;
};
dojo.hostenv.findModule=function(_57,_58){
var lmn=String(_57);
if(this.loaded_modules_[lmn]){
return this.loaded_modules_[lmn];
}
if(_58){
dojo.raise("no loaded module named '"+_57+"'");
}
return null;
};
dojo.kwCompoundRequire=function(_5a){
var _5b=_5a["common"]||[];
var _5c=_5a[dojo.hostenv.name_]?_5b.concat(_5a[dojo.hostenv.name_]||[]):_5b.concat(_5a["default"]||[]);
for(var x=0;x<_5c.length;x++){
var _5e=_5c[x];
if(_5e.constructor==Array){
dojo.hostenv.loadModule.apply(dojo.hostenv,_5e);
}else{
dojo.hostenv.loadModule(_5e);
}
}
};
dojo.require=function(_5f){
dojo.hostenv.loadModule.apply(dojo.hostenv,arguments);
};
dojo.requireIf=function(_60,_61){
var _62=arguments[0];
if((_62===true)||(_62=="common")||(_62&&dojo.render[_62].capable)){
var _63=[];
for(var i=1;i<arguments.length;i++){
_63.push(arguments[i]);
}
dojo.require.apply(dojo,_63);
}
};
dojo.requireAfterIf=dojo.requireIf;
dojo.provide=function(_65){
return dojo.hostenv.startPackage.apply(dojo.hostenv,arguments);
};
dojo.registerModulePath=function(_66,_67){
return dojo.hostenv.registerModulePath(_66,_67);
};
dojo.exists=function(obj,_69){
var p=_69.split(".");
for(var i=0;i<p.length;i++){
if(!obj[p[i]]){
return false;
}
obj=obj[p[i]];
}
return true;
};
dojo.hostenv.normalizeLocale=function(_6c){
var _6d=_6c?_6c.toLowerCase():dojo.locale;
if(_6d=="root"){
_6d="ROOT";
}
return _6d;
};
dojo.hostenv.searchLocalePath=function(_6e,_6f,_70){
_6e=dojo.hostenv.normalizeLocale(_6e);
var _71=_6e.split("-");
var _72=[];
for(var i=_71.length;i>0;i--){
_72.push(_71.slice(0,i).join("-"));
}
_72.push(false);
if(_6f){
_72.reverse();
}
for(var j=_72.length-1;j>=0;j--){
var loc=_72[j]||"ROOT";
var _76=_70(loc);
if(_76){
break;
}
}
};
dojo.hostenv.localesGenerated=["ROOT","es-es","es","it-it","pt-br","de","fr-fr","zh-cn","pt","en-us","zh","fr","zh-tw","it","en-gb","xx","de-de","ko-kr","ja-jp","ko","en","ja"];
dojo.hostenv.registerNlsPrefix=function(){
dojo.registerModulePath("nls","nls");
};
dojo.hostenv.preloadLocalizations=function(){
if(dojo.hostenv.localesGenerated){
dojo.hostenv.registerNlsPrefix();
function preload(_77){
_77=dojo.hostenv.normalizeLocale(_77);
dojo.hostenv.searchLocalePath(_77,true,function(loc){
for(var i=0;i<dojo.hostenv.localesGenerated.length;i++){
if(dojo.hostenv.localesGenerated[i]==loc){
dojo["require"]("nls.dojo_"+loc);
return true;
}
}
return false;
});
}
preload();
var _7a=djConfig.extraLocale||[];
for(var i=0;i<_7a.length;i++){
preload(_7a[i]);
}
}
dojo.hostenv.preloadLocalizations=function(){
};
};
dojo.requireLocalization=function(_7c,_7d,_7e,_7f){
dojo.hostenv.preloadLocalizations();
var _80=dojo.hostenv.normalizeLocale(_7e);
var _81=[_7c,"nls",_7d].join(".");
var _82="";
if(_7f){
var _83=_7f.split(",");
for(var i=0;i<_83.length;i++){
if(_80.indexOf(_83[i])==0){
if(_83[i].length>_82.length){
_82=_83[i];
}
}
}
if(!_82){
_82="ROOT";
}
}
var _85=_7f?_82:_80;
var _86=dojo.hostenv.findModule(_81);
var _87=null;
if(_86){
if(djConfig.localizationComplete&&_86._built){
return;
}
var _88=_85.replace("-","_");
var _89=_81+"."+_88;
_87=dojo.hostenv.findModule(_89);
}
if(!_87){
_86=dojo.hostenv.startPackage(_81);
var _8a=dojo.hostenv.getModuleSymbols(_7c);
var _8b=_8a.concat("nls").join("/");
var _8c;
dojo.hostenv.searchLocalePath(_85,_7f,function(loc){
var _8e=loc.replace("-","_");
var _8f=_81+"."+_8e;
var _90=false;
if(!dojo.hostenv.findModule(_8f)){
dojo.hostenv.startPackage(_8f);
var _91=[_8b];
if(loc!="ROOT"){
_91.push(loc);
}
_91.push(_7d);
var _92=_91.join("/")+".js";
_90=dojo.hostenv.loadPath(_92,null,function(_93){
var _94=function(){
};
_94.prototype=_8c;
_86[_8e]=new _94();
for(var j in _93){
_86[_8e][j]=_93[j];
}
});
}else{
_90=true;
}
if(_90&&_86[_8e]){
_8c=_86[_8e];
}else{
_86[_8e]=_8c;
}
if(_7f){
return true;
}
});
}
if(_7f&&_80!=_82){
_86[_80.replace("-","_")]=_86[_82.replace("-","_")];
}
};
(function(){
var _96=djConfig.extraLocale;
if(_96){
if(!_96 instanceof Array){
_96=[_96];
}
var req=dojo.requireLocalization;
dojo.requireLocalization=function(m,b,_9a,_9b){
req(m,b,_9a,_9b);
if(_9a){
return;
}
for(var i=0;i<_96.length;i++){
req(m,b,_96[i],_9b);
}
};
}
})();
}
if(typeof window!="undefined"){
(function(){
if(djConfig.allowQueryConfig){
var _9d=document.location.toString();
var _9e=_9d.split("?",2);
if(_9e.length>1){
var _9f=_9e[1];
var _a0=_9f.split("&");
for(var x in _a0){
var sp=_a0[x].split("=");
if((sp[0].length>9)&&(sp[0].substr(0,9)=="djConfig.")){
var opt=sp[0].substr(9);
try{
djConfig[opt]=eval(sp[1]);
}
catch(e){
djConfig[opt]=sp[1];
}
}
}
}
}
if(((djConfig["baseScriptUri"]=="")||(djConfig["baseRelativePath"]==""))&&(document&&document.getElementsByTagName)){
var _a4=document.getElementsByTagName("script");
var _a5=/(__package__|dojo|bootstrap1)\.js([\?\.]|$)/i;
for(var i=0;i<_a4.length;i++){
var src=_a4[i].getAttribute("src");
if(!src){
continue;
}
var m=src.match(_a5);
if(m){
var _a9=src.substring(0,m.index);
if(src.indexOf("bootstrap1")>-1){
_a9+="../";
}
if(!this["djConfig"]){
djConfig={};
}
if(djConfig["baseScriptUri"]==""){
djConfig["baseScriptUri"]=_a9;
}
if(djConfig["baseRelativePath"]==""){
djConfig["baseRelativePath"]=_a9;
}
break;
}
}
}
var dr=dojo.render;
var drh=dojo.render.html;
var drs=dojo.render.svg;
var dua=(drh.UA=navigator.userAgent);
var dav=(drh.AV=navigator.appVersion);
var t=true;
var f=false;
drh.capable=t;
drh.support.builtin=t;
dr.ver=parseFloat(drh.AV);
dr.os.mac=dav.indexOf("Macintosh")>=0;
dr.os.win=dav.indexOf("Windows")>=0;
dr.os.linux=dav.indexOf("X11")>=0;
drh.opera=dua.indexOf("Opera")>=0;
drh.khtml=(dav.indexOf("Konqueror")>=0)||(dav.indexOf("Safari")>=0);
drh.safari=dav.indexOf("Safari")>=0;
var _b1=dua.indexOf("Gecko");
drh.mozilla=drh.moz=(_b1>=0)&&(!drh.khtml);
if(drh.mozilla){
drh.geckoVersion=dua.substring(_b1+6,_b1+14);
}
drh.ie=(document.all)&&(!drh.opera);
drh.ie50=drh.ie&&dav.indexOf("MSIE 5.0")>=0;
drh.ie55=drh.ie&&dav.indexOf("MSIE 5.5")>=0;
drh.ie60=drh.ie&&dav.indexOf("MSIE 6.0")>=0;
drh.ie70=drh.ie&&dav.indexOf("MSIE 7.0")>=0;
var cm=document["compatMode"];
drh.quirks=(cm=="BackCompat")||(cm=="QuirksMode")||drh.ie55||drh.ie50;
dojo.locale=dojo.locale||(drh.ie?navigator.userLanguage:navigator.language).toLowerCase();
dr.vml.capable=drh.ie;
drs.capable=f;
drs.support.plugin=f;
drs.support.builtin=f;
var _b3=window["document"];
var tdi=_b3["implementation"];
if((tdi)&&(tdi["hasFeature"])&&(tdi.hasFeature("org.w3c.dom.svg","1.0"))){
drs.capable=t;
drs.support.builtin=t;
drs.support.plugin=f;
}
if(drh.safari){
var tmp=dua.split("AppleWebKit/")[1];
var ver=parseFloat(tmp.split(" ")[0]);
if(ver>=420){
drs.capable=t;
drs.support.builtin=t;
drs.support.plugin=f;
}
}else{
}
})();
dojo.hostenv.startPackage("dojo.hostenv");
dojo.render.name=dojo.hostenv.name_="browser";
dojo.hostenv.searchIds=[];
dojo.hostenv._XMLHTTP_PROGIDS=["Msxml2.XMLHTTP","Microsoft.XMLHTTP","Msxml2.XMLHTTP.4.0"];
dojo.hostenv.getXmlhttpObject=function(){
var _b7=null;
var _b8=null;
try{
_b7=new XMLHttpRequest();
}
catch(e){
}
if(!_b7){
for(var i=0;i<3;++i){
var _ba=dojo.hostenv._XMLHTTP_PROGIDS[i];
try{
_b7=new ActiveXObject(_ba);
}
catch(e){
_b8=e;
}
if(_b7){
dojo.hostenv._XMLHTTP_PROGIDS=[_ba];
break;
}
}
}
if(!_b7){
return dojo.raise("XMLHTTP not available",_b8);
}
return _b7;
};
dojo.hostenv._blockAsync=false;
dojo.hostenv.getText=function(uri,_bc,_bd){
if(!_bc){
this._blockAsync=true;
}
var _be=this.getXmlhttpObject();
function isDocumentOk(_bf){
var _c0=_bf["status"];
return Boolean((!_c0)||((200<=_c0)&&(300>_c0))||(_c0==304));
}
if(_bc){
var _c1=this,_c2=null,gbl=dojo.global();
var xhr=dojo.evalObjPath("dojo.io.XMLHTTPTransport");
_be.onreadystatechange=function(){
if(_c2){
gbl.clearTimeout(_c2);
_c2=null;
}
if(_c1._blockAsync||(xhr&&xhr._blockAsync)){
_c2=gbl.setTimeout(function(){
_be.onreadystatechange.apply(this);
},10);
}else{
if(4==_be.readyState){
if(isDocumentOk(_be)){
_bc(_be.responseText);
}
}
}
};
}
_be.open("GET",uri,_bc?true:false);
try{
_be.send(null);
if(_bc){
return null;
}
if(!isDocumentOk(_be)){
var err=Error("Unable to load "+uri+" status:"+_be.status);
err.status=_be.status;
err.responseText=_be.responseText;
throw err;
}
}
catch(e){
this._blockAsync=false;
if((_bd)&&(!_bc)){
return null;
}else{
throw e;
}
}
this._blockAsync=false;
return _be.responseText;
};
dojo.hostenv.defaultDebugContainerId="dojoDebug";
dojo.hostenv._println_buffer=[];
dojo.hostenv._println_safe=false;
dojo.hostenv.println=function(_c6){
if(!dojo.hostenv._println_safe){
dojo.hostenv._println_buffer.push(_c6);
}else{
try{
var _c7=document.getElementById(djConfig.debugContainerId?djConfig.debugContainerId:dojo.hostenv.defaultDebugContainerId);
if(!_c7){
_c7=dojo.body();
}
var div=document.createElement("div");
div.appendChild(document.createTextNode(_c6));
_c7.appendChild(div);
}
catch(e){
try{
document.write("<div>"+_c6+"</div>");
}
catch(e2){
window.status=_c6;
}
}
}
};
dojo.addOnLoad(function(){
dojo.hostenv._println_safe=true;
while(dojo.hostenv._println_buffer.length>0){
dojo.hostenv.println(dojo.hostenv._println_buffer.shift());
}
});
function dj_addNodeEvtHdlr(_c9,_ca,fp){
var _cc=_c9["on"+_ca]||function(){
};
_c9["on"+_ca]=function(){
fp.apply(_c9,arguments);
_cc.apply(_c9,arguments);
};
return true;
}
function dj_load_init(e){
var _ce=(e&&e.type)?e.type.toLowerCase():"load";
if(arguments.callee.initialized||(_ce!="domcontentloaded"&&_ce!="load")){
return;
}
arguments.callee.initialized=true;
if(typeof (_timer)!="undefined"){
clearInterval(_timer);
delete _timer;
}
var _cf=function(){
if(dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
};
if(dojo.hostenv.inFlightCount==0){
_cf();
dojo.hostenv.modulesLoaded();
}else{
dojo.hostenv.modulesLoadedListeners.unshift(_cf);
}
}
if(document.addEventListener){
if(dojo.render.html.opera||(dojo.render.html.moz&&(djConfig["enableMozDomContentLoaded"]===true))){
document.addEventListener("DOMContentLoaded",dj_load_init,null);
}
window.addEventListener("load",dj_load_init,null);
}
if(dojo.render.html.ie&&dojo.render.os.win){
document.write("<scr"+"ipt defer src=\"//:\" "+"onreadystatechange=\"if(this.readyState=='complete'){dj_load_init();}\">"+"</scr"+"ipt>");
}
if(/(WebKit|khtml)/i.test(navigator.userAgent)){
var _timer=setInterval(function(){
if(/loaded|complete/.test(document.readyState)){
dj_load_init();
}
},10);
}
if(dojo.render.html.ie){
dj_addNodeEvtHdlr(window,"beforeunload",function(){
dojo.hostenv._unloading=true;
window.setTimeout(function(){
dojo.hostenv._unloading=false;
},0);
});
}
dj_addNodeEvtHdlr(window,"unload",function(){
if((!dojo.render.html.ie)||(dojo.render.html.ie&&dojo.hostenv._unloading)){
dojo.hostenv.unloaded();
}
});
dojo.hostenv.makeWidgets=function(){
var _d0=[];
if(djConfig.searchIds&&djConfig.searchIds.length>0){
_d0=_d0.concat(djConfig.searchIds);
}
if(dojo.hostenv.searchIds&&dojo.hostenv.searchIds.length>0){
_d0=_d0.concat(dojo.hostenv.searchIds);
}
if((djConfig.parseWidgets)||(_d0.length>0)){
if(dojo.evalObjPath("dojo.widget.Parse")){
var _d1=new dojo.xml.Parse();
if(_d0.length>0){
for(var x=0;x<_d0.length;x++){
var _d3=document.getElementById(_d0[x]);
if(!_d3){
continue;
}
var _d4=_d1.parseElement(_d3,null,true);
dojo.widget.getParser().createComponents(_d4);
}
}else{
if(djConfig.parseWidgets){
var _d4=_d1.parseElement(dojo.body(),null,true);
dojo.widget.getParser().createComponents(_d4);
}
}
}
}
};
dojo.addOnLoad(function(){
if(!dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
});
try{
if(dojo.render.html.ie){
document.namespaces.add("v","urn:schemas-microsoft-com:vml");
document.createStyleSheet().addRule("v\\:*","behavior:url(#default#VML)");
}
}
catch(e){
}
dojo.hostenv.writeIncludes=function(){
};
if(!dj_undef("document",this)){
dj_currentDocument=this.document;
}
dojo.doc=function(){
return dj_currentDocument;
};
dojo.body=function(){
return dojo.doc().body||dojo.doc().getElementsByTagName("body")[0];
};
dojo.byId=function(id,doc){
if((id)&&((typeof id=="string")||(id instanceof String))){
if(!doc){
doc=dj_currentDocument;
}
var ele=doc.getElementById(id);
if(ele&&(ele.id!=id)&&doc.all){
ele=null;
eles=doc.all[id];
if(eles){
if(eles.length){
for(var i=0;i<eles.length;i++){
if(eles[i].id==id){
ele=eles[i];
break;
}
}
}else{
ele=eles;
}
}
}
return ele;
}
return id;
};
dojo.setContext=function(_d9,_da){
dj_currentContext=_d9;
dj_currentDocument=_da;
};
dojo._fireCallback=function(_db,_dc,_dd){
if((_dc)&&((typeof _db=="string")||(_db instanceof String))){
_db=_dc[_db];
}
return (_dc?_db.apply(_dc,_dd||[]):_db());
};
dojo.withGlobal=function(_de,_df,_e0,_e1){
var _e2;
var _e3=dj_currentContext;
var _e4=dj_currentDocument;
try{
dojo.setContext(_de,_de.document);
_e2=dojo._fireCallback(_df,_e0,_e1);
}
finally{
dojo.setContext(_e3,_e4);
}
return _e2;
};
dojo.withDoc=function(_e5,_e6,_e7,_e8){
var _e9;
var _ea=dj_currentDocument;
try{
dj_currentDocument=_e5;
_e9=dojo._fireCallback(_e6,_e7,_e8);
}
finally{
dj_currentDocument=_ea;
}
return _e9;
};
}
(function(){
if(typeof dj_usingBootstrap!="undefined"){
return;
}
var _eb=false;
var _ec=false;
var _ed=false;
if((typeof this["load"]=="function")&&((typeof this["Packages"]=="function")||(typeof this["Packages"]=="object"))){
_eb=true;
}else{
if(typeof this["load"]=="function"){
_ec=true;
}else{
if(window.widget){
_ed=true;
}
}
}
var _ee=[];
if((this["djConfig"])&&((djConfig["isDebug"])||(djConfig["debugAtAllCosts"]))){
_ee.push("debug.js");
}
if((this["djConfig"])&&(djConfig["debugAtAllCosts"])&&(!_eb)&&(!_ed)){
_ee.push("browser_debug.js");
}
var _ef=djConfig["baseScriptUri"];
if((this["djConfig"])&&(djConfig["baseLoaderUri"])){
_ef=djConfig["baseLoaderUri"];
}
for(var x=0;x<_ee.length;x++){
var _f1=_ef+"src/"+_ee[x];
if(_eb||_ec){
load(_f1);
}else{
try{
document.write("<scr"+"ipt type='text/javascript' src='"+_f1+"'></scr"+"ipt>");
}
catch(e){
var _f2=document.createElement("script");
_f2.src=_f1;
document.getElementsByTagName("head")[0].appendChild(_f2);
}
}
}
})();
dojo.provide("dojo.dom");
dojo.dom.ELEMENT_NODE=1;
dojo.dom.ATTRIBUTE_NODE=2;
dojo.dom.TEXT_NODE=3;
dojo.dom.CDATA_SECTION_NODE=4;
dojo.dom.ENTITY_REFERENCE_NODE=5;
dojo.dom.ENTITY_NODE=6;
dojo.dom.PROCESSING_INSTRUCTION_NODE=7;
dojo.dom.COMMENT_NODE=8;
dojo.dom.DOCUMENT_NODE=9;
dojo.dom.DOCUMENT_TYPE_NODE=10;
dojo.dom.DOCUMENT_FRAGMENT_NODE=11;
dojo.dom.NOTATION_NODE=12;
dojo.dom.dojoml="http://www.dojotoolkit.org/2004/dojoml";
dojo.dom.xmlns={svg:"http://www.w3.org/2000/svg",smil:"http://www.w3.org/2001/SMIL20/",mml:"http://www.w3.org/1998/Math/MathML",cml:"http://www.xml-cml.org",xlink:"http://www.w3.org/1999/xlink",xhtml:"http://www.w3.org/1999/xhtml",xul:"http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul",xbl:"http://www.mozilla.org/xbl",fo:"http://www.w3.org/1999/XSL/Format",xsl:"http://www.w3.org/1999/XSL/Transform",xslt:"http://www.w3.org/1999/XSL/Transform",xi:"http://www.w3.org/2001/XInclude",xforms:"http://www.w3.org/2002/01/xforms",saxon:"http://icl.com/saxon",xalan:"http://xml.apache.org/xslt",xsd:"http://www.w3.org/2001/XMLSchema",dt:"http://www.w3.org/2001/XMLSchema-datatypes",xsi:"http://www.w3.org/2001/XMLSchema-instance",rdf:"http://www.w3.org/1999/02/22-rdf-syntax-ns#",rdfs:"http://www.w3.org/2000/01/rdf-schema#",dc:"http://purl.org/dc/elements/1.1/",dcq:"http://purl.org/dc/qualifiers/1.0","soap-env":"http://schemas.xmlsoap.org/soap/envelope/",wsdl:"http://schemas.xmlsoap.org/wsdl/",AdobeExtensions:"http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"};
dojo.dom.isNode=function(wh){
if(typeof Element=="function"){
try{
return wh instanceof Element;
}
catch(e){
}
}else{
return wh&&!isNaN(wh.nodeType);
}
};
dojo.dom.getUniqueId=function(){
var _f4=dojo.doc();
do{
var id="dj_unique_"+(++arguments.callee._idIncrement);
}while(_f4.getElementById(id));
return id;
};
dojo.dom.getUniqueId._idIncrement=0;
dojo.dom.firstElement=dojo.dom.getFirstChildElement=function(_f6,_f7){
var _f8=_f6.firstChild;
while(_f8&&_f8.nodeType!=dojo.dom.ELEMENT_NODE){
_f8=_f8.nextSibling;
}
if(_f7&&_f8&&_f8.tagName&&_f8.tagName.toLowerCase()!=_f7.toLowerCase()){
_f8=dojo.dom.nextElement(_f8,_f7);
}
return _f8;
};
dojo.dom.lastElement=dojo.dom.getLastChildElement=function(_f9,_fa){
var _fb=_f9.lastChild;
while(_fb&&_fb.nodeType!=dojo.dom.ELEMENT_NODE){
_fb=_fb.previousSibling;
}
if(_fa&&_fb&&_fb.tagName&&_fb.tagName.toLowerCase()!=_fa.toLowerCase()){
_fb=dojo.dom.prevElement(_fb,_fa);
}
return _fb;
};
dojo.dom.nextElement=dojo.dom.getNextSiblingElement=function(_fc,_fd){
if(!_fc){
return null;
}
do{
_fc=_fc.nextSibling;
}while(_fc&&_fc.nodeType!=dojo.dom.ELEMENT_NODE);
if(_fc&&_fd&&_fd.toLowerCase()!=_fc.tagName.toLowerCase()){
return dojo.dom.nextElement(_fc,_fd);
}
return _fc;
};
dojo.dom.prevElement=dojo.dom.getPreviousSiblingElement=function(_fe,_ff){
if(!_fe){
return null;
}
if(_ff){
_ff=_ff.toLowerCase();
}
do{
_fe=_fe.previousSibling;
}while(_fe&&_fe.nodeType!=dojo.dom.ELEMENT_NODE);
if(_fe&&_ff&&_ff.toLowerCase()!=_fe.tagName.toLowerCase()){
return dojo.dom.prevElement(_fe,_ff);
}
return _fe;
};
dojo.dom.moveChildren=function(_100,_101,trim){
var _103=0;
if(trim){
while(_100.hasChildNodes()&&_100.firstChild.nodeType==dojo.dom.TEXT_NODE){
_100.removeChild(_100.firstChild);
}
while(_100.hasChildNodes()&&_100.lastChild.nodeType==dojo.dom.TEXT_NODE){
_100.removeChild(_100.lastChild);
}
}
while(_100.hasChildNodes()){
_101.appendChild(_100.firstChild);
_103++;
}
return _103;
};
dojo.dom.copyChildren=function(_104,_105,trim){
var _107=_104.cloneNode(true);
return this.moveChildren(_107,_105,trim);
};
dojo.dom.replaceChildren=function(node,_109){
var _10a=[];
if(dojo.render.html.ie){
for(var i=0;i<node.childNodes.length;i++){
_10a.push(node.childNodes[i]);
}
}
dojo.dom.removeChildren(node);
node.appendChild(_109);
for(var i=0;i<_10a.length;i++){
dojo.dom.destroyNode(_10a[i]);
}
};
dojo.dom.removeChildren=function(node){
var _10d=node.childNodes.length;
while(node.hasChildNodes()){
dojo.dom.removeNode(node.firstChild);
}
return _10d;
};
dojo.dom.replaceNode=function(node,_10f){
return node.parentNode.replaceChild(_10f,node);
};
dojo.dom.destroyNode=function(node){
if(node.parentNode){
node=dojo.dom.removeNode(node);
}
if(node.nodeType!=3){
if(dojo.evalObjPath("dojo.event.browser.clean",false)){
dojo.event.browser.clean(node);
}
if(dojo.render.html.ie){
node.outerHTML="";
}
}
};
dojo.dom.removeNode=function(node){
if(node&&node.parentNode){
return node.parentNode.removeChild(node);
}
};
dojo.dom.getAncestors=function(node,_113,_114){
var _115=[];
var _116=(_113&&(_113 instanceof Function||typeof _113=="function"));
while(node){
if(!_116||_113(node)){
_115.push(node);
}
if(_114&&_115.length>0){
return _115[0];
}
node=node.parentNode;
}
if(_114){
return null;
}
return _115;
};
dojo.dom.getAncestorsByTag=function(node,tag,_119){
tag=tag.toLowerCase();
return dojo.dom.getAncestors(node,function(el){
return ((el.tagName)&&(el.tagName.toLowerCase()==tag));
},_119);
};
dojo.dom.getFirstAncestorByTag=function(node,tag){
return dojo.dom.getAncestorsByTag(node,tag,true);
};
dojo.dom.isDescendantOf=function(node,_11e,_11f){
if(_11f&&node){
node=node.parentNode;
}
while(node){
if(node==_11e){
return true;
}
node=node.parentNode;
}
return false;
};
dojo.dom.innerXML=function(node){
if(node.innerXML){
return node.innerXML;
}else{
if(node.xml){
return node.xml;
}else{
if(typeof XMLSerializer!="undefined"){
return (new XMLSerializer()).serializeToString(node);
}
}
}
};
dojo.dom.createDocument=function(){
var doc=null;
var _122=dojo.doc();
if(!dj_undef("ActiveXObject")){
var _123=["MSXML2","Microsoft","MSXML","MSXML3"];
for(var i=0;i<_123.length;i++){
try{
doc=new ActiveXObject(_123[i]+".XMLDOM");
}
catch(e){
}
if(doc){
break;
}
}
}else{
if((_122.implementation)&&(_122.implementation.createDocument)){
doc=_122.implementation.createDocument("","",null);
}
}
return doc;
};
dojo.dom.createDocumentFromText=function(str,_126){
if(!_126){
_126="text/xml";
}
if(!dj_undef("DOMParser")){
var _127=new DOMParser();
return _127.parseFromString(str,_126);
}else{
if(!dj_undef("ActiveXObject")){
var _128=dojo.dom.createDocument();
if(_128){
_128.async=false;
_128.loadXML(str);
return _128;
}else{
dojo.debug("toXml didn't work?");
}
}else{
var _129=dojo.doc();
if(_129.createElement){
var tmp=_129.createElement("xml");
tmp.innerHTML=str;
if(_129.implementation&&_129.implementation.createDocument){
var _12b=_129.implementation.createDocument("foo","",null);
for(var i=0;i<tmp.childNodes.length;i++){
_12b.importNode(tmp.childNodes.item(i),true);
}
return _12b;
}
return ((tmp.document)&&(tmp.document.firstChild?tmp.document.firstChild:tmp));
}
}
}
return null;
};
dojo.dom.prependChild=function(node,_12e){
if(_12e.firstChild){
_12e.insertBefore(node,_12e.firstChild);
}else{
_12e.appendChild(node);
}
return true;
};
dojo.dom.insertBefore=function(node,ref,_131){
if((_131!=true)&&(node===ref||node.nextSibling===ref)){
return false;
}
var _132=ref.parentNode;
_132.insertBefore(node,ref);
return true;
};
dojo.dom.insertAfter=function(node,ref,_135){
var pn=ref.parentNode;
if(ref==pn.lastChild){
if((_135!=true)&&(node===ref)){
return false;
}
pn.appendChild(node);
}else{
return this.insertBefore(node,ref.nextSibling,_135);
}
return true;
};
dojo.dom.insertAtPosition=function(node,ref,_139){
if((!node)||(!ref)||(!_139)){
return false;
}
switch(_139.toLowerCase()){
case "before":
return dojo.dom.insertBefore(node,ref);
case "after":
return dojo.dom.insertAfter(node,ref);
case "first":
if(ref.firstChild){
return dojo.dom.insertBefore(node,ref.firstChild);
}else{
ref.appendChild(node);
return true;
}
break;
default:
ref.appendChild(node);
return true;
}
};
dojo.dom.insertAtIndex=function(node,_13b,_13c){
var _13d=_13b.childNodes;
if(!_13d.length||_13d.length==_13c){
_13b.appendChild(node);
return true;
}
if(_13c==0){
return dojo.dom.prependChild(node,_13b);
}
return dojo.dom.insertAfter(node,_13d[_13c-1]);
};
dojo.dom.textContent=function(node,text){
if(arguments.length>1){
var _140=dojo.doc();
dojo.dom.replaceChildren(node,_140.createTextNode(text));
return text;
}else{
if(node.textContent!=undefined){
return node.textContent;
}
var _141="";
if(node==null){
return _141;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
_141+=dojo.dom.textContent(node.childNodes[i]);
break;
case 3:
case 2:
case 4:
_141+=node.childNodes[i].nodeValue;
break;
default:
break;
}
}
return _141;
}
};
dojo.dom.hasParent=function(node){
return Boolean(node&&node.parentNode&&dojo.dom.isNode(node.parentNode));
};
dojo.dom.isTag=function(node){
if(node&&node.tagName){
for(var i=1;i<arguments.length;i++){
if(node.tagName==String(arguments[i])){
return String(arguments[i]);
}
}
}
return "";
};
dojo.dom.setAttributeNS=function(elem,_147,_148,_149){
if(elem==null||((elem==undefined)&&(typeof elem=="undefined"))){
dojo.raise("No element given to dojo.dom.setAttributeNS");
}
if(!((elem.setAttributeNS==undefined)&&(typeof elem.setAttributeNS=="undefined"))){
elem.setAttributeNS(_147,_148,_149);
}else{
var _14a=elem.ownerDocument;
var _14b=_14a.createNode(2,_148,_147);
_14b.nodeValue=_149;
elem.setAttributeNode(_14b);
}
};
dojo.provide("dojo.xml.Parse");
dojo.xml.Parse=function(){
var isIE=((dojo.render.html.capable)&&(dojo.render.html.ie));
function getTagName(node){
try{
return node.tagName.toLowerCase();
}
catch(e){
return "";
}
}
function getDojoTagName(node){
var _14f=getTagName(node);
if(!_14f){
return "";
}
if((dojo.widget)&&(dojo.widget.tags[_14f])){
return _14f;
}
var p=_14f.indexOf(":");
if(p>=0){
return _14f;
}
if(_14f.substr(0,5)=="dojo:"){
return _14f;
}
if(dojo.render.html.capable&&dojo.render.html.ie&&node.scopeName&&node.scopeName!="HTML"){
return node.scopeName.toLowerCase()+":"+_14f;
}
if(_14f.substr(0,4)=="dojo"){
return "dojo:"+_14f.substring(4);
}
var djt=node.getAttribute("dojoType")||node.getAttribute("dojotype");
if(djt){
if(djt.indexOf(":")<0){
djt="dojo:"+djt;
}
return djt.toLowerCase();
}
djt=node.getAttributeNS&&node.getAttributeNS(dojo.dom.dojoml,"type");
if(djt){
return "dojo:"+djt.toLowerCase();
}
try{
djt=node.getAttribute("dojo:type");
}
catch(e){
}
if(djt){
return "dojo:"+djt.toLowerCase();
}
if((dj_global["djConfig"])&&(!djConfig["ignoreClassNames"])){
var _152=node.className||node.getAttribute("class");
if((_152)&&(_152.indexOf)&&(_152.indexOf("dojo-")!=-1)){
var _153=_152.split(" ");
for(var x=0,c=_153.length;x<c;x++){
if(_153[x].slice(0,5)=="dojo-"){
return "dojo:"+_153[x].substr(5).toLowerCase();
}
}
}
}
return "";
}
this.parseElement=function(node,_157,_158,_159){
var _15a=getTagName(node);
if(isIE&&_15a.indexOf("/")==0){
return null;
}
try{
var attr=node.getAttribute("parseWidgets");
if(attr&&attr.toLowerCase()=="false"){
return {};
}
}
catch(e){
}
var _15c=true;
if(_158){
var _15d=getDojoTagName(node);
_15a=_15d||_15a;
_15c=Boolean(_15d);
}
var _15e={};
_15e[_15a]=[];
var pos=_15a.indexOf(":");
if(pos>0){
var ns=_15a.substring(0,pos);
_15e["ns"]=ns;
if((dojo.ns)&&(!dojo.ns.allow(ns))){
_15c=false;
}
}
if(_15c){
var _161=this.parseAttributes(node);
for(var attr in _161){
if((!_15e[_15a][attr])||(typeof _15e[_15a][attr]!="array")){
_15e[_15a][attr]=[];
}
_15e[_15a][attr].push(_161[attr]);
}
_15e[_15a].nodeRef=node;
_15e.tagName=_15a;
_15e.index=_159||0;
}
var _162=0;
for(var i=0;i<node.childNodes.length;i++){
var tcn=node.childNodes.item(i);
switch(tcn.nodeType){
case dojo.dom.ELEMENT_NODE:
var ctn=getDojoTagName(tcn)||getTagName(tcn);
if(!_15e[ctn]){
_15e[ctn]=[];
}
_15e[ctn].push(this.parseElement(tcn,true,_158,_162));
if((tcn.childNodes.length==1)&&(tcn.childNodes.item(0).nodeType==dojo.dom.TEXT_NODE)){
_15e[ctn][_15e[ctn].length-1].value=tcn.childNodes.item(0).nodeValue;
}
_162++;
break;
case dojo.dom.TEXT_NODE:
if(node.childNodes.length==1){
_15e[_15a].push({value:node.childNodes.item(0).nodeValue});
}
break;
default:
break;
}
}
return _15e;
};
this.parseAttributes=function(node){
var _167={};
var atts=node.attributes;
var _169,i=0;
while((_169=atts[i++])){
if(isIE){
if(!_169){
continue;
}
if((typeof _169=="object")&&(typeof _169.nodeValue=="undefined")||(_169.nodeValue==null)||(_169.nodeValue=="")){
continue;
}
}
var nn=_169.nodeName.split(":");
nn=(nn.length==2)?nn[1]:_169.nodeName;
_167[nn]={value:_169.nodeValue};
}
return _167;
};
};
dojo.provide("dojo.lang.common");
dojo.lang.inherits=function(_16c,_16d){
if(!dojo.lang.isFunction(_16d)){
dojo.raise("dojo.inherits: superclass argument ["+_16d+"] must be a function (subclass: ["+_16c+"']");
}
_16c.prototype=new _16d();
_16c.prototype.constructor=_16c;
_16c.superclass=_16d.prototype;
_16c["super"]=_16d.prototype;
};
dojo.lang._mixin=function(obj,_16f){
var tobj={};
for(var x in _16f){
if((typeof tobj[x]=="undefined")||(tobj[x]!=_16f[x])){
obj[x]=_16f[x];
}
}
if(dojo.render.html.ie&&(typeof (_16f["toString"])=="function")&&(_16f["toString"]!=obj["toString"])&&(_16f["toString"]!=tobj["toString"])){
obj.toString=_16f.toString;
}
return obj;
};
dojo.lang.mixin=function(obj,_173){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(obj,arguments[i]);
}
return obj;
};
dojo.lang.extend=function(_176,_177){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(_176.prototype,arguments[i]);
}
return _176;
};
dojo.lang._delegate=function(obj,_17b){
function TMP(){
}
TMP.prototype=obj;
var tmp=new TMP();
if(_17b){
dojo.lang.mixin(tmp,_17b);
}
return tmp;
};
dojo.inherits=dojo.lang.inherits;
dojo.mixin=dojo.lang.mixin;
dojo.extend=dojo.lang.extend;
dojo.lang.find=function(_17d,_17e,_17f,_180){
var _181=dojo.lang.isString(_17d);
if(_181){
_17d=_17d.split("");
}
if(_180){
var step=-1;
var i=_17d.length-1;
var end=-1;
}else{
var step=1;
var i=0;
var end=_17d.length;
}
if(_17f){
while(i!=end){
if(_17d[i]===_17e){
return i;
}
i+=step;
}
}else{
while(i!=end){
if(_17d[i]==_17e){
return i;
}
i+=step;
}
}
return -1;
};
dojo.lang.indexOf=dojo.lang.find;
dojo.lang.findLast=function(_185,_186,_187){
return dojo.lang.find(_185,_186,_187,true);
};
dojo.lang.lastIndexOf=dojo.lang.findLast;
dojo.lang.inArray=function(_188,_189){
return dojo.lang.find(_188,_189)>-1;
};
dojo.lang.isObject=function(it){
if(typeof it=="undefined"){
return false;
}
return (typeof it=="object"||it===null||dojo.lang.isArray(it)||dojo.lang.isFunction(it));
};
dojo.lang.isArray=function(it){
return (it&&it instanceof Array||typeof it=="array");
};
dojo.lang.isArrayLike=function(it){
if((!it)||(dojo.lang.isUndefined(it))){
return false;
}
if(dojo.lang.isString(it)){
return false;
}
if(dojo.lang.isFunction(it)){
return false;
}
if(dojo.lang.isArray(it)){
return true;
}
if((it.tagName)&&(it.tagName.toLowerCase()=="form")){
return false;
}
if(dojo.lang.isNumber(it.length)&&isFinite(it.length)){
return true;
}
return false;
};
dojo.lang.isFunction=function(it){
return (it instanceof Function||typeof it=="function");
};
(function(){
if((dojo.render.html.capable)&&(dojo.render.html["safari"])){
dojo.lang.isFunction=function(it){
if((typeof (it)=="function")&&(it=="[object NodeList]")){
return false;
}
return (it instanceof Function||typeof it=="function");
};
}
})();
dojo.lang.isString=function(it){
return (typeof it=="string"||it instanceof String);
};
dojo.lang.isAlien=function(it){
if(!it){
return false;
}
return !dojo.lang.isFunction(it)&&/\{\s*\[native code\]\s*\}/.test(String(it));
};
dojo.lang.isBoolean=function(it){
return (it instanceof Boolean||typeof it=="boolean");
};
dojo.lang.isNumber=function(it){
return (it instanceof Number||typeof it=="number");
};
dojo.lang.isUndefined=function(it){
return ((typeof (it)=="undefined")&&(it==undefined));
};
dojo.provide("dojo.lang.func");
dojo.lang.hitch=function(_194,_195){
var args=[];
for(var x=2;x<arguments.length;x++){
args.push(arguments[x]);
}
var fcn=(dojo.lang.isString(_195)?_194[_195]:_195)||function(){
};
return function(){
var ta=args.concat([]);
for(var x=0;x<arguments.length;x++){
ta.push(arguments[x]);
}
return fcn.apply(_194,ta);
};
};
dojo.lang.anonCtr=0;
dojo.lang.anon={};
dojo.lang.nameAnonFunc=function(_19b,_19c,_19d){
var nso=(_19c||dojo.lang.anon);
if((_19d)||((dj_global["djConfig"])&&(djConfig["slowAnonFuncLookups"]==true))){
for(var x in nso){
try{
if(nso[x]===_19b){
return x;
}
}
catch(e){
}
}
}
var ret="__"+dojo.lang.anonCtr++;
while(typeof nso[ret]!="undefined"){
ret="__"+dojo.lang.anonCtr++;
}
nso[ret]=_19b;
return ret;
};
dojo.lang.forward=function(_1a1){
return function(){
return this[_1a1].apply(this,arguments);
};
};
dojo.lang.curry=function(_1a2,func){
var _1a4=[];
_1a2=_1a2||dj_global;
if(dojo.lang.isString(func)){
func=_1a2[func];
}
for(var x=2;x<arguments.length;x++){
_1a4.push(arguments[x]);
}
var _1a6=(func["__preJoinArity"]||func.length)-_1a4.length;
function gather(_1a7,_1a8,_1a9){
var _1aa=_1a9;
var _1ab=_1a8.slice(0);
for(var x=0;x<_1a7.length;x++){
_1ab.push(_1a7[x]);
}
_1a9=_1a9-_1a7.length;
if(_1a9<=0){
var res=func.apply(_1a2,_1ab);
_1a9=_1aa;
return res;
}else{
return function(){
return gather(arguments,_1ab,_1a9);
};
}
}
return gather([],_1a4,_1a6);
};
dojo.lang.curryArguments=function(_1ae,func,args,_1b1){
var _1b2=[];
var x=_1b1||0;
for(x=_1b1;x<args.length;x++){
_1b2.push(args[x]);
}
return dojo.lang.curry.apply(dojo.lang,[_1ae,func].concat(_1b2));
};
dojo.lang.tryThese=function(){
for(var x=0;x<arguments.length;x++){
try{
if(typeof arguments[x]=="function"){
var ret=(arguments[x]());
if(ret){
return ret;
}
}
}
catch(e){
dojo.debug(e);
}
}
};
dojo.lang.delayThese=function(farr,cb,_1b8,_1b9){
if(!farr.length){
if(typeof _1b9=="function"){
_1b9();
}
return;
}
if((typeof _1b8=="undefined")&&(typeof cb=="number")){
_1b8=cb;
cb=function(){
};
}else{
if(!cb){
cb=function(){
};
if(!_1b8){
_1b8=0;
}
}
}
setTimeout(function(){
(farr.shift())();
cb();
dojo.lang.delayThese(farr,cb,_1b8,_1b9);
},_1b8);
};
dojo.provide("dojo.lang.array");
dojo.lang.mixin(dojo.lang,{has:function(obj,name){
try{
return typeof obj[name]!="undefined";
}
catch(e){
return false;
}
},isEmpty:function(obj){
if(dojo.lang.isArrayLike(obj)||dojo.lang.isString(obj)){
return obj.length===0;
}else{
if(dojo.lang.isObject(obj)){
var tmp={};
for(var x in obj){
if(obj[x]&&(!tmp[x])){
return false;
}
}
return true;
}
}
},map:function(arr,obj,_1c1){
var _1c2=dojo.lang.isString(arr);
if(_1c2){
arr=arr.split("");
}
if(dojo.lang.isFunction(obj)&&(!_1c1)){
_1c1=obj;
obj=dj_global;
}else{
if(dojo.lang.isFunction(obj)&&_1c1){
var _1c3=obj;
obj=_1c1;
_1c1=_1c3;
}
}
if(Array.map){
var _1c4=Array.map(arr,_1c1,obj);
}else{
var _1c4=[];
for(var i=0;i<arr.length;++i){
_1c4.push(_1c1.call(obj,arr[i]));
}
}
if(_1c2){
return _1c4.join("");
}else{
return _1c4;
}
},reduce:function(arr,_1c7,obj,_1c9){
var _1ca=_1c7;
if(arguments.length==1){
dojo.debug("dojo.lang.reduce called with too few arguments!");
return false;
}else{
if(arguments.length==2){
_1c9=_1c7;
_1ca=arr.shift();
}else{
if(arguments.lenght==3){
if(dojo.lang.isFunction(obj)){
_1c9=obj;
obj=null;
}
}else{
if(dojo.lang.isFunction(obj)){
var tmp=_1c9;
_1c9=obj;
obj=tmp;
}
}
}
}
var ob=obj?obj:dj_global;
dojo.lang.map(arr,function(val){
_1ca=_1c9.call(ob,_1ca,val);
});
return _1ca;
},forEach:function(_1ce,_1cf,_1d0){
if(dojo.lang.isString(_1ce)){
_1ce=_1ce.split("");
}
if(Array.forEach){
Array.forEach(_1ce,_1cf,_1d0);
}else{
if(!_1d0){
_1d0=dj_global;
}
for(var i=0,l=_1ce.length;i<l;i++){
_1cf.call(_1d0,_1ce[i],i,_1ce);
}
}
},_everyOrSome:function(_1d3,arr,_1d5,_1d6){
if(dojo.lang.isString(arr)){
arr=arr.split("");
}
if(Array.every){
return Array[_1d3?"every":"some"](arr,_1d5,_1d6);
}else{
if(!_1d6){
_1d6=dj_global;
}
for(var i=0,l=arr.length;i<l;i++){
var _1d9=_1d5.call(_1d6,arr[i],i,arr);
if(_1d3&&!_1d9){
return false;
}else{
if((!_1d3)&&(_1d9)){
return true;
}
}
}
return Boolean(_1d3);
}
},every:function(arr,_1db,_1dc){
return this._everyOrSome(true,arr,_1db,_1dc);
},some:function(arr,_1de,_1df){
return this._everyOrSome(false,arr,_1de,_1df);
},filter:function(arr,_1e1,_1e2){
var _1e3=dojo.lang.isString(arr);
if(_1e3){
arr=arr.split("");
}
var _1e4;
if(Array.filter){
_1e4=Array.filter(arr,_1e1,_1e2);
}else{
if(!_1e2){
if(arguments.length>=3){
dojo.raise("thisObject doesn't exist!");
}
_1e2=dj_global;
}
_1e4=[];
for(var i=0;i<arr.length;i++){
if(_1e1.call(_1e2,arr[i],i,arr)){
_1e4.push(arr[i]);
}
}
}
if(_1e3){
return _1e4.join("");
}else{
return _1e4;
}
},unnest:function(){
var out=[];
for(var i=0;i<arguments.length;i++){
if(dojo.lang.isArrayLike(arguments[i])){
var add=dojo.lang.unnest.apply(this,arguments[i]);
out=out.concat(add);
}else{
out.push(arguments[i]);
}
}
return out;
},toArray:function(_1e9,_1ea){
var _1eb=[];
for(var i=_1ea||0;i<_1e9.length;i++){
_1eb.push(_1e9[i]);
}
return _1eb;
}});
dojo.provide("dojo.lang.extras");
dojo.lang.setTimeout=function(func,_1ee){
var _1ef=window,_1f0=2;
if(!dojo.lang.isFunction(func)){
_1ef=func;
func=_1ee;
_1ee=arguments[2];
_1f0++;
}
if(dojo.lang.isString(func)){
func=_1ef[func];
}
var args=[];
for(var i=_1f0;i<arguments.length;i++){
args.push(arguments[i]);
}
return dojo.global().setTimeout(function(){
func.apply(_1ef,args);
},_1ee);
};
dojo.lang.clearTimeout=function(_1f3){
dojo.global().clearTimeout(_1f3);
};
dojo.lang.getNameInObj=function(ns,item){
if(!ns){
ns=dj_global;
}
for(var x in ns){
if(ns[x]===item){
return new String(x);
}
}
return null;
};
dojo.lang.shallowCopy=function(obj,deep){
var i,ret;
if(obj===null){
return null;
}
if(dojo.lang.isObject(obj)){
ret=new obj.constructor();
for(i in obj){
if(dojo.lang.isUndefined(ret[i])){
ret[i]=deep?dojo.lang.shallowCopy(obj[i],deep):obj[i];
}
}
}else{
if(dojo.lang.isArray(obj)){
ret=[];
for(i=0;i<obj.length;i++){
ret[i]=deep?dojo.lang.shallowCopy(obj[i],deep):obj[i];
}
}else{
ret=obj;
}
}
return ret;
};
dojo.lang.firstValued=function(){
for(var i=0;i<arguments.length;i++){
if(typeof arguments[i]!="undefined"){
return arguments[i];
}
}
return undefined;
};
dojo.lang.getObjPathValue=function(_1fc,_1fd,_1fe){
with(dojo.parseObjPath(_1fc,_1fd,_1fe)){
return dojo.evalProp(prop,obj,_1fe);
}
};
dojo.lang.setObjPathValue=function(_1ff,_200,_201,_202){
dojo.deprecated("dojo.lang.setObjPathValue","use dojo.parseObjPath and the '=' operator","0.6");
if(arguments.length<4){
_202=true;
}
with(dojo.parseObjPath(_1ff,_201,_202)){
if(obj&&(_202||(prop in obj))){
obj[prop]=_200;
}
}
};
dojo.provide("dojo.lang.declare");
dojo.lang.declare=function(_203,_204,init,_206){
if((dojo.lang.isFunction(_206))||((!_206)&&(!dojo.lang.isFunction(init)))){
if(dojo.lang.isFunction(_206)){
dojo.deprecated("dojo.lang.declare("+_203+"...):","use class, superclass, initializer, properties argument order","0.6");
}
var temp=_206;
_206=init;
init=temp;
}
if(_206&&_206.initializer){
dojo.deprecated("dojo.lang.declare("+_203+"...):","specify initializer as third argument, not as an element in properties","0.6");
}
var _208=[];
if(dojo.lang.isArray(_204)){
_208=_204;
_204=_208.shift();
}
if(!init){
init=dojo.evalObjPath(_203,false);
if((init)&&(!dojo.lang.isFunction(init))){
init=null;
}
}
var ctor=dojo.lang.declare._makeConstructor();
var scp=(_204?_204.prototype:null);
if(scp){
scp.prototyping=true;
ctor.prototype=new _204();
scp.prototyping=false;
}
ctor.superclass=scp;
ctor.mixins=_208;
for(var i=0,l=_208.length;i<l;i++){
dojo.lang.extend(ctor,_208[i].prototype);
}
ctor.prototype.initializer=null;
ctor.prototype.declaredClass=_203;
if(dojo.lang.isArray(_206)){
dojo.lang.extend.apply(dojo.lang,[ctor].concat(_206));
}else{
dojo.lang.extend(ctor,(_206)||{});
}
dojo.lang.extend(ctor,dojo.lang.declare._common);
ctor.prototype.constructor=ctor;
ctor.prototype.initializer=(ctor.prototype.initializer)||(init)||(function(){
});
var _20d=dojo.parseObjPath(_203,null,true);
_20d.obj[_20d.prop]=ctor;
return ctor;
};
dojo.lang.declare._makeConstructor=function(){
return function(){
var self=this._getPropContext();
var s=self.constructor.superclass;
if((s)&&(s.constructor)){
if(s.constructor==arguments.callee){
this._inherited("constructor",arguments);
}else{
this._contextMethod(s,"constructor",arguments);
}
}
var ms=(self.constructor.mixins)||([]);
for(var i=0,m;(m=ms[i]);i++){
(((m.prototype)&&(m.prototype.initializer))||(m)).apply(this,arguments);
}
if((!this.prototyping)&&(self.initializer)){
self.initializer.apply(this,arguments);
}
};
};
dojo.lang.declare._common={_getPropContext:function(){
return (this.___proto||this);
},_contextMethod:function(_213,_214,args){
var _216,_217=this.___proto;
this.___proto=_213;
try{
_216=_213[_214].apply(this,(args||[]));
}
catch(e){
throw e;
}
finally{
this.___proto=_217;
}
return _216;
},_inherited:function(prop,args){
var p=this._getPropContext();
do{
if((!p.constructor)||(!p.constructor.superclass)){
return;
}
p=p.constructor.superclass;
}while(!(prop in p));
return (dojo.lang.isFunction(p[prop])?this._contextMethod(p,prop,args):p[prop]);
}};
dojo.declare=dojo.lang.declare;
dojo.provide("dojo.ns");
dojo.ns={namespaces:{},failed:{},loading:{},loaded:{},register:function(name,_21c,_21d,_21e){
if(!_21e||!this.namespaces[name]){
this.namespaces[name]=new dojo.ns.Ns(name,_21c,_21d);
}
},allow:function(name){
if(this.failed[name]){
return false;
}
if((djConfig.excludeNamespace)&&(dojo.lang.inArray(djConfig.excludeNamespace,name))){
return false;
}
return ((name==this.dojo)||(!djConfig.includeNamespace)||(dojo.lang.inArray(djConfig.includeNamespace,name)));
},get:function(name){
return this.namespaces[name];
},require:function(name){
var ns=this.namespaces[name];
if((ns)&&(this.loaded[name])){
return ns;
}
if(!this.allow(name)){
return false;
}
if(this.loading[name]){
dojo.debug("dojo.namespace.require: re-entrant request to load namespace \""+name+"\" must fail.");
return false;
}
var req=dojo.require;
this.loading[name]=true;
try{
if(name=="dojo"){
req("dojo.namespaces.dojo");
}else{
if(!dojo.hostenv.moduleHasPrefix(name)){
dojo.registerModulePath(name,"../"+name);
}
req([name,"manifest"].join("."),false,true);
}
if(!this.namespaces[name]){
this.failed[name]=true;
}
}
finally{
this.loading[name]=false;
}
return this.namespaces[name];
}};
dojo.ns.Ns=function(name,_225,_226){
this.name=name;
this.module=_225;
this.resolver=_226;
this._loaded=[];
this._failed=[];
};
dojo.ns.Ns.prototype.resolve=function(name,_228,_229){
if(!this.resolver||djConfig["skipAutoRequire"]){
return false;
}
var _22a=this.resolver(name,_228);
if((_22a)&&(!this._loaded[_22a])&&(!this._failed[_22a])){
var req=dojo.require;
req(_22a,false,true);
if(dojo.hostenv.findModule(_22a,false)){
this._loaded[_22a]=true;
}else{
if(!_229){
dojo.raise("dojo.ns.Ns.resolve: module '"+_22a+"' not found after loading via namespace '"+this.name+"'");
}
this._failed[_22a]=true;
}
}
return Boolean(this._loaded[_22a]);
};
dojo.registerNamespace=function(name,_22d,_22e){
dojo.ns.register.apply(dojo.ns,arguments);
};
dojo.registerNamespaceResolver=function(name,_230){
var n=dojo.ns.namespaces[name];
if(n){
n.resolver=_230;
}
};
dojo.registerNamespaceManifest=function(_232,path,name,_235,_236){
dojo.registerModulePath(name,path);
dojo.registerNamespace(name,_235,_236);
};
dojo.registerNamespace("dojo","dojo.widget");
dojo.provide("dojo.event.common");
dojo.event=new function(){
this._canTimeout=dojo.lang.isFunction(dj_global["setTimeout"])||dojo.lang.isAlien(dj_global["setTimeout"]);
function interpolateArgs(args,_238){
var dl=dojo.lang;
var ao={srcObj:dj_global,srcFunc:null,adviceObj:dj_global,adviceFunc:null,aroundObj:null,aroundFunc:null,adviceType:(args.length>2)?args[0]:"after",precedence:"last",once:false,delay:null,rate:0,adviceMsg:false,maxCalls:-1};
switch(args.length){
case 0:
return;
case 1:
return;
case 2:
ao.srcFunc=args[0];
ao.adviceFunc=args[1];
break;
case 3:
if((dl.isObject(args[0]))&&(dl.isString(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
}else{
if((dl.isString(args[1]))&&(dl.isString(args[2]))){
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
}else{
if((dl.isObject(args[0]))&&(dl.isString(args[1]))&&(dl.isFunction(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
var _23b=dl.nameAnonFunc(args[2],ao.adviceObj,_238);
ao.adviceFunc=_23b;
}else{
if((dl.isFunction(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=dj_global;
var _23b=dl.nameAnonFunc(args[0],ao.srcObj,_238);
ao.srcFunc=_23b;
ao.adviceObj=args[1];
ao.adviceFunc=args[2];
}
}
}
}
break;
case 4:
if((dl.isObject(args[0]))&&(dl.isObject(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isString(args[1]))&&(dl.isObject(args[2]))){
ao.adviceType=args[0];
ao.srcObj=dj_global;
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isFunction(args[1]))&&(dl.isObject(args[2]))){
ao.adviceType=args[0];
ao.srcObj=dj_global;
var _23b=dl.nameAnonFunc(args[1],dj_global,_238);
ao.srcFunc=_23b;
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))&&(dl.isFunction(args[3]))){
ao.srcObj=args[1];
ao.srcFunc=args[2];
var _23b=dl.nameAnonFunc(args[3],dj_global,_238);
ao.adviceObj=dj_global;
ao.adviceFunc=_23b;
}else{
if(dl.isObject(args[1])){
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=dj_global;
ao.adviceFunc=args[3];
}else{
if(dl.isObject(args[2])){
ao.srcObj=dj_global;
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
ao.srcObj=ao.adviceObj=ao.aroundObj=dj_global;
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
ao.aroundFunc=args[3];
}
}
}
}
}
}
break;
case 6:
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=args[3];
ao.adviceFunc=args[4];
ao.aroundFunc=args[5];
ao.aroundObj=dj_global;
break;
default:
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=args[3];
ao.adviceFunc=args[4];
ao.aroundObj=args[5];
ao.aroundFunc=args[6];
ao.once=args[7];
ao.delay=args[8];
ao.rate=args[9];
ao.adviceMsg=args[10];
ao.maxCalls=(!isNaN(parseInt(args[11])))?args[11]:-1;
break;
}
if(dl.isFunction(ao.aroundFunc)){
var _23b=dl.nameAnonFunc(ao.aroundFunc,ao.aroundObj,_238);
ao.aroundFunc=_23b;
}
if(dl.isFunction(ao.srcFunc)){
ao.srcFunc=dl.getNameInObj(ao.srcObj,ao.srcFunc);
}
if(dl.isFunction(ao.adviceFunc)){
ao.adviceFunc=dl.getNameInObj(ao.adviceObj,ao.adviceFunc);
}
if((ao.aroundObj)&&(dl.isFunction(ao.aroundFunc))){
ao.aroundFunc=dl.getNameInObj(ao.aroundObj,ao.aroundFunc);
}
if(!ao.srcObj){
dojo.raise("bad srcObj for srcFunc: "+ao.srcFunc);
}
if(!ao.adviceObj){
dojo.raise("bad adviceObj for adviceFunc: "+ao.adviceFunc);
}
if(!ao.adviceFunc){
dojo.debug("bad adviceFunc for srcFunc: "+ao.srcFunc);
dojo.debugShallow(ao);
}
return ao;
}
this.connect=function(){
if(arguments.length==1){
var ao=arguments[0];
}else{
var ao=interpolateArgs(arguments,true);
}
if(dojo.lang.isString(ao.srcFunc)&&(ao.srcFunc.toLowerCase()=="onkey")){
if(dojo.render.html.ie){
ao.srcFunc="onkeydown";
this.connect(ao);
}
ao.srcFunc="onkeypress";
}
if(dojo.lang.isArray(ao.srcObj)&&ao.srcObj!=""){
var _23d={};
for(var x in ao){
_23d[x]=ao[x];
}
var mjps=[];
dojo.lang.forEach(ao.srcObj,function(src){
if((dojo.render.html.capable)&&(dojo.lang.isString(src))){
src=dojo.byId(src);
}
_23d.srcObj=src;
mjps.push(dojo.event.connect.call(dojo.event,_23d));
});
return mjps;
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc);
if(ao.adviceFunc){
var mjp2=dojo.event.MethodJoinPoint.getForMethod(ao.adviceObj,ao.adviceFunc);
}
mjp.kwAddAdvice(ao);
return mjp;
};
this.log=function(a1,a2){
var _245;
if((arguments.length==1)&&(typeof a1=="object")){
_245=a1;
}else{
_245={srcObj:a1,srcFunc:a2};
}
_245.adviceFunc=function(){
var _246=[];
for(var x=0;x<arguments.length;x++){
_246.push(arguments[x]);
}
dojo.debug("("+_245.srcObj+")."+_245.srcFunc,":",_246.join(", "));
};
this.kwConnect(_245);
};
this.connectBefore=function(){
var args=["before"];
for(var i=0;i<arguments.length;i++){
args.push(arguments[i]);
}
return this.connect.apply(this,args);
};
this.connectAround=function(){
var args=["around"];
for(var i=0;i<arguments.length;i++){
args.push(arguments[i]);
}
return this.connect.apply(this,args);
};
this.connectOnce=function(){
var ao=interpolateArgs(arguments,true);
ao.once=true;
return this.connect(ao);
};
this.connectRunOnce=function(){
var ao=interpolateArgs(arguments,true);
ao.maxCalls=1;
return this.connect(ao);
};
this._kwConnectImpl=function(_24e,_24f){
var fn=(_24f)?"disconnect":"connect";
if(typeof _24e["srcFunc"]=="function"){
_24e.srcObj=_24e["srcObj"]||dj_global;
var _251=dojo.lang.nameAnonFunc(_24e.srcFunc,_24e.srcObj,true);
_24e.srcFunc=_251;
}
if(typeof _24e["adviceFunc"]=="function"){
_24e.adviceObj=_24e["adviceObj"]||dj_global;
var _251=dojo.lang.nameAnonFunc(_24e.adviceFunc,_24e.adviceObj,true);
_24e.adviceFunc=_251;
}
_24e.srcObj=_24e["srcObj"]||dj_global;
_24e.adviceObj=_24e["adviceObj"]||_24e["targetObj"]||dj_global;
_24e.adviceFunc=_24e["adviceFunc"]||_24e["targetFunc"];
return dojo.event[fn](_24e);
};
this.kwConnect=function(_252){
return this._kwConnectImpl(_252,false);
};
this.disconnect=function(){
if(arguments.length==1){
var ao=arguments[0];
}else{
var ao=interpolateArgs(arguments,true);
}
if(!ao.adviceFunc){
return;
}
if(dojo.lang.isString(ao.srcFunc)&&(ao.srcFunc.toLowerCase()=="onkey")){
if(dojo.render.html.ie){
ao.srcFunc="onkeydown";
this.disconnect(ao);
}
ao.srcFunc="onkeypress";
}
if(!ao.srcObj[ao.srcFunc]){
return null;
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc,true);
mjp.removeAdvice(ao.adviceObj,ao.adviceFunc,ao.adviceType,ao.once);
return mjp;
};
this.kwDisconnect=function(_255){
return this._kwConnectImpl(_255,true);
};
};
dojo.event.MethodInvocation=function(_256,obj,args){
this.jp_=_256;
this.object=obj;
this.args=[];
for(var x=0;x<args.length;x++){
this.args[x]=args[x];
}
this.around_index=-1;
};
dojo.event.MethodInvocation.prototype.proceed=function(){
this.around_index++;
if(this.around_index>=this.jp_.around.length){
return this.jp_.object[this.jp_.methodname].apply(this.jp_.object,this.args);
}else{
var ti=this.jp_.around[this.around_index];
var mobj=ti[0]||dj_global;
var meth=ti[1];
return mobj[meth].call(mobj,this);
}
};
dojo.event.MethodJoinPoint=function(obj,_25e){
this.object=obj||dj_global;
this.methodname=_25e;
this.methodfunc=this.object[_25e];
};
dojo.event.MethodJoinPoint.getForMethod=function(obj,_260){
if(!obj){
obj=dj_global;
}
var ofn=obj[_260];
if(!ofn){
ofn=obj[_260]=function(){
};
if(!obj[_260]){
dojo.raise("Cannot set do-nothing method on that object "+_260);
}
}else{
if((typeof ofn!="function")&&(!dojo.lang.isFunction(ofn))&&(!dojo.lang.isAlien(ofn))){
return null;
}
}
var _262=_260+"$joinpoint";
var _263=_260+"$joinpoint$method";
var _264=obj[_262];
if(!_264){
var _265=false;
if(dojo.event["browser"]){
if((obj["attachEvent"])||(obj["nodeType"])||(obj["addEventListener"])){
_265=true;
dojo.event.browser.addClobberNodeAttrs(obj,[_262,_263,_260]);
}
}
var _266=ofn.length;
obj[_263]=ofn;
_264=obj[_262]=new dojo.event.MethodJoinPoint(obj,_263);
if(!_265){
obj[_260]=function(){
return _264.run.apply(_264,arguments);
};
}else{
obj[_260]=function(){
var args=[];
if(!arguments.length){
var evt=null;
try{
if(obj.ownerDocument){
evt=obj.ownerDocument.parentWindow.event;
}else{
if(obj.documentElement){
evt=obj.documentElement.ownerDocument.parentWindow.event;
}else{
if(obj.event){
evt=obj.event;
}else{
evt=window.event;
}
}
}
}
catch(e){
evt=window.event;
}
if(evt){
args.push(dojo.event.browser.fixEvent(evt,this));
}
}else{
for(var x=0;x<arguments.length;x++){
if((x==0)&&(dojo.event.browser.isEvent(arguments[x]))){
args.push(dojo.event.browser.fixEvent(arguments[x],this));
}else{
args.push(arguments[x]);
}
}
}
return _264.run.apply(_264,args);
};
}
obj[_260].__preJoinArity=_266;
}
return _264;
};
dojo.lang.extend(dojo.event.MethodJoinPoint,{squelch:false,unintercept:function(){
this.object[this.methodname]=this.methodfunc;
this.before=[];
this.after=[];
this.around=[];
},disconnect:dojo.lang.forward("unintercept"),run:function(){
var obj=this.object||dj_global;
var args=arguments;
var _26c=[];
for(var x=0;x<args.length;x++){
_26c[x]=args[x];
}
var _26e=function(marr){
if(!marr){
dojo.debug("Null argument to unrollAdvice()");
return;
}
var _270=marr[0]||dj_global;
var _271=marr[1];
if(!_270[_271]){
dojo.raise("function \""+_271+"\" does not exist on \""+_270+"\"");
}
var _272=marr[2]||dj_global;
var _273=marr[3];
var msg=marr[6];
var _275=marr[7];
if(_275>-1){
if(_275==0){
return;
}
marr[7]--;
}
var _276;
var to={args:[],jp_:this,object:obj,proceed:function(){
return _270[_271].apply(_270,to.args);
}};
to.args=_26c;
var _278=parseInt(marr[4]);
var _279=((!isNaN(_278))&&(marr[4]!==null)&&(typeof marr[4]!="undefined"));
if(marr[5]){
var rate=parseInt(marr[5]);
var cur=new Date();
var _27c=false;
if((marr["last"])&&((cur-marr.last)<=rate)){
if(dojo.event._canTimeout){
if(marr["delayTimer"]){
clearTimeout(marr.delayTimer);
}
var tod=parseInt(rate*2);
var mcpy=dojo.lang.shallowCopy(marr);
marr.delayTimer=setTimeout(function(){
mcpy[5]=0;
_26e(mcpy);
},tod);
}
return;
}else{
marr.last=cur;
}
}
if(_273){
_272[_273].call(_272,to);
}else{
if((_279)&&((dojo.render.html)||(dojo.render.svg))){
dj_global["setTimeout"](function(){
if(msg){
_270[_271].call(_270,to);
}else{
_270[_271].apply(_270,args);
}
},_278);
}else{
if(msg){
_270[_271].call(_270,to);
}else{
_270[_271].apply(_270,args);
}
}
}
};
var _27f=function(){
if(this.squelch){
try{
return _26e.apply(this,arguments);
}
catch(e){
dojo.debug(e);
}
}else{
return _26e.apply(this,arguments);
}
};
if((this["before"])&&(this.before.length>0)){
dojo.lang.forEach(this.before.concat(new Array()),_27f);
}
var _280;
try{
if((this["around"])&&(this.around.length>0)){
var mi=new dojo.event.MethodInvocation(this,obj,args);
_280=mi.proceed();
}else{
if(this.methodfunc){
_280=this.object[this.methodname].apply(this.object,args);
}
}
}
catch(e){
if(!this.squelch){
dojo.debug(e,"when calling",this.methodname,"on",this.object,"with arguments",args);
dojo.raise(e);
}
}
if((this["after"])&&(this.after.length>0)){
dojo.lang.forEach(this.after.concat(new Array()),_27f);
}
return (this.methodfunc)?_280:null;
},getArr:function(kind){
var type="after";
if((typeof kind=="string")&&(kind.indexOf("before")!=-1)){
type="before";
}else{
if(kind=="around"){
type="around";
}
}
if(!this[type]){
this[type]=[];
}
return this[type];
},kwAddAdvice:function(args){
this.addAdvice(args["adviceObj"],args["adviceFunc"],args["aroundObj"],args["aroundFunc"],args["adviceType"],args["precedence"],args["once"],args["delay"],args["rate"],args["adviceMsg"],args["maxCalls"]);
},addAdvice:function(_285,_286,_287,_288,_289,_28a,once,_28c,rate,_28e,_28f){
var arr=this.getArr(_289);
if(!arr){
dojo.raise("bad this: "+this);
}
var ao=[_285,_286,_287,_288,_28c,rate,_28e,_28f];
if(once){
if(this.hasAdvice(_285,_286,_289,arr)>=0){
return;
}
}
if(_28a=="first"){
arr.unshift(ao);
}else{
arr.push(ao);
}
},hasAdvice:function(_292,_293,_294,arr){
if(!arr){
arr=this.getArr(_294);
}
var ind=-1;
for(var x=0;x<arr.length;x++){
var aao=(typeof _293=="object")?(new String(_293)).toString():_293;
var a1o=(typeof arr[x][1]=="object")?(new String(arr[x][1])).toString():arr[x][1];
if((arr[x][0]==_292)&&(a1o==aao)){
ind=x;
}
}
return ind;
},removeAdvice:function(_29a,_29b,_29c,once){
var arr=this.getArr(_29c);
var ind=this.hasAdvice(_29a,_29b,_29c,arr);
if(ind==-1){
return false;
}
while(ind!=-1){
arr.splice(ind,1);
if(once){
break;
}
ind=this.hasAdvice(_29a,_29b,_29c,arr);
}
return true;
}});
dojo.provide("dojo.event.topic");
dojo.event.topic=new function(){
this.topics={};
this.getTopic=function(_2a0){
if(!this.topics[_2a0]){
this.topics[_2a0]=new this.TopicImpl(_2a0);
}
return this.topics[_2a0];
};
this.registerPublisher=function(_2a1,obj,_2a3){
var _2a1=this.getTopic(_2a1);
_2a1.registerPublisher(obj,_2a3);
};
this.subscribe=function(_2a4,obj,_2a6){
var _2a4=this.getTopic(_2a4);
_2a4.subscribe(obj,_2a6);
};
this.unsubscribe=function(_2a7,obj,_2a9){
var _2a7=this.getTopic(_2a7);
_2a7.unsubscribe(obj,_2a9);
};
this.destroy=function(_2aa){
this.getTopic(_2aa).destroy();
delete this.topics[_2aa];
};
this.publishApply=function(_2ab,args){
var _2ab=this.getTopic(_2ab);
_2ab.sendMessage.apply(_2ab,args);
};
this.publish=function(_2ad,_2ae){
var _2ad=this.getTopic(_2ad);
var args=[];
for(var x=1;x<arguments.length;x++){
args.push(arguments[x]);
}
_2ad.sendMessage.apply(_2ad,args);
};
};
dojo.event.topic.TopicImpl=function(_2b1){
this.topicName=_2b1;
this.subscribe=function(_2b2,_2b3){
var tf=_2b3||_2b2;
var to=(!_2b3)?dj_global:_2b2;
return dojo.event.kwConnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this.unsubscribe=function(_2b6,_2b7){
var tf=(!_2b7)?_2b6:_2b7;
var to=(!_2b7)?null:_2b6;
return dojo.event.kwDisconnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this._getJoinPoint=function(){
return dojo.event.MethodJoinPoint.getForMethod(this,"sendMessage");
};
this.setSquelch=function(_2ba){
this._getJoinPoint().squelch=_2ba;
};
this.destroy=function(){
this._getJoinPoint().disconnect();
};
this.registerPublisher=function(_2bb,_2bc){
dojo.event.connect(_2bb,_2bc,this,"sendMessage");
};
this.sendMessage=function(_2bd){
};
};
dojo.provide("dojo.event.browser");
dojo._ie_clobber=new function(){
this.clobberNodes=[];
function nukeProp(node,prop){
try{
node[prop]=null;
}
catch(e){
}
try{
delete node[prop];
}
catch(e){
}
try{
node.removeAttribute(prop);
}
catch(e){
}
}
this.clobber=function(_2c0){
var na;
var tna;
if(_2c0){
tna=_2c0.all||_2c0.getElementsByTagName("*");
na=[_2c0];
for(var x=0;x<tna.length;x++){
if(tna[x]["__doClobber__"]){
na.push(tna[x]);
}
}
}else{
try{
window.onload=null;
}
catch(e){
}
na=(this.clobberNodes.length)?this.clobberNodes:document.all;
}
tna=null;
var _2c4={};
for(var i=na.length-1;i>=0;i=i-1){
var el=na[i];
try{
if(el&&el["__clobberAttrs__"]){
for(var j=0;j<el.__clobberAttrs__.length;j++){
nukeProp(el,el.__clobberAttrs__[j]);
}
nukeProp(el,"__clobberAttrs__");
nukeProp(el,"__doClobber__");
}
}
catch(e){
}
}
na=null;
};
};
if(dojo.render.html.ie){
dojo.addOnUnload(function(){
dojo._ie_clobber.clobber();
try{
if((dojo["widget"])&&(dojo.widget["manager"])){
dojo.widget.manager.destroyAll();
}
}
catch(e){
}
if(dojo.widget){
for(var name in dojo.widget._templateCache){
if(dojo.widget._templateCache[name].node){
dojo.dom.destroyNode(dojo.widget._templateCache[name].node);
dojo.widget._templateCache[name].node=null;
delete dojo.widget._templateCache[name].node;
}
}
}
try{
window.onload=null;
}
catch(e){
}
try{
window.onunload=null;
}
catch(e){
}
dojo._ie_clobber.clobberNodes=[];
});
}
dojo.event.browser=new function(){
var _2c9=0;
this.normalizedEventName=function(_2ca){
switch(_2ca){
case "CheckboxStateChange":
case "DOMAttrModified":
case "DOMMenuItemActive":
case "DOMMenuItemInactive":
case "DOMMouseScroll":
case "DOMNodeInserted":
case "DOMNodeRemoved":
case "RadioStateChange":
return _2ca;
break;
default:
return _2ca.toLowerCase();
break;
}
};
this.clean=function(node){
if(dojo.render.html.ie){
dojo._ie_clobber.clobber(node);
}
};
this.addClobberNode=function(node){
if(!dojo.render.html.ie){
return;
}
if(!node["__doClobber__"]){
node.__doClobber__=true;
dojo._ie_clobber.clobberNodes.push(node);
node.__clobberAttrs__=[];
}
};
this.addClobberNodeAttrs=function(node,_2ce){
if(!dojo.render.html.ie){
return;
}
this.addClobberNode(node);
for(var x=0;x<_2ce.length;x++){
node.__clobberAttrs__.push(_2ce[x]);
}
};
this.removeListener=function(node,_2d1,fp,_2d3){
if(!_2d3){
var _2d3=false;
}
_2d1=dojo.event.browser.normalizedEventName(_2d1);
if((_2d1=="onkey")||(_2d1=="key")){
if(dojo.render.html.ie){
this.removeListener(node,"onkeydown",fp,_2d3);
}
_2d1="onkeypress";
}
if(_2d1.substr(0,2)=="on"){
_2d1=_2d1.substr(2);
}
if(node.removeEventListener){
node.removeEventListener(_2d1,fp,_2d3);
}
};
this.addListener=function(node,_2d5,fp,_2d7,_2d8){
if(!node){
return;
}
if(!_2d7){
var _2d7=false;
}
_2d5=dojo.event.browser.normalizedEventName(_2d5);
if((_2d5=="onkey")||(_2d5=="key")){
if(dojo.render.html.ie){
this.addListener(node,"onkeydown",fp,_2d7,_2d8);
}
_2d5="onkeypress";
}
if(_2d5.substr(0,2)!="on"){
_2d5="on"+_2d5;
}
if(!_2d8){
var _2d9=function(evt){
if(!evt){
evt=window.event;
}
var ret=fp(dojo.event.browser.fixEvent(evt,this));
if(_2d7){
dojo.event.browser.stopEvent(evt);
}
return ret;
};
}else{
_2d9=fp;
}
if(node.addEventListener){
node.addEventListener(_2d5.substr(2),_2d9,_2d7);
return _2d9;
}else{
if(typeof node[_2d5]=="function"){
var _2dc=node[_2d5];
node[_2d5]=function(e){
_2dc(e);
return _2d9(e);
};
}else{
node[_2d5]=_2d9;
}
if(dojo.render.html.ie){
this.addClobberNodeAttrs(node,[_2d5]);
}
return _2d9;
}
};
this.isEvent=function(obj){
return (typeof obj!="undefined")&&(obj)&&(typeof Event!="undefined")&&(obj.eventPhase);
};
this.currentEvent=null;
this.callListener=function(_2df,_2e0){
if(typeof _2df!="function"){
dojo.raise("listener not a function: "+_2df);
}
dojo.event.browser.currentEvent.currentTarget=_2e0;
return _2df.call(_2e0,dojo.event.browser.currentEvent);
};
this._stopPropagation=function(){
dojo.event.browser.currentEvent.cancelBubble=true;
};
this._preventDefault=function(){
dojo.event.browser.currentEvent.returnValue=false;
};
this.keys={KEY_BACKSPACE:8,KEY_TAB:9,KEY_CLEAR:12,KEY_ENTER:13,KEY_SHIFT:16,KEY_CTRL:17,KEY_ALT:18,KEY_PAUSE:19,KEY_CAPS_LOCK:20,KEY_ESCAPE:27,KEY_SPACE:32,KEY_PAGE_UP:33,KEY_PAGE_DOWN:34,KEY_END:35,KEY_HOME:36,KEY_LEFT_ARROW:37,KEY_UP_ARROW:38,KEY_RIGHT_ARROW:39,KEY_DOWN_ARROW:40,KEY_INSERT:45,KEY_DELETE:46,KEY_HELP:47,KEY_LEFT_WINDOW:91,KEY_RIGHT_WINDOW:92,KEY_SELECT:93,KEY_NUMPAD_0:96,KEY_NUMPAD_1:97,KEY_NUMPAD_2:98,KEY_NUMPAD_3:99,KEY_NUMPAD_4:100,KEY_NUMPAD_5:101,KEY_NUMPAD_6:102,KEY_NUMPAD_7:103,KEY_NUMPAD_8:104,KEY_NUMPAD_9:105,KEY_NUMPAD_MULTIPLY:106,KEY_NUMPAD_PLUS:107,KEY_NUMPAD_ENTER:108,KEY_NUMPAD_MINUS:109,KEY_NUMPAD_PERIOD:110,KEY_NUMPAD_DIVIDE:111,KEY_F1:112,KEY_F2:113,KEY_F3:114,KEY_F4:115,KEY_F5:116,KEY_F6:117,KEY_F7:118,KEY_F8:119,KEY_F9:120,KEY_F10:121,KEY_F11:122,KEY_F12:123,KEY_F13:124,KEY_F14:125,KEY_F15:126,KEY_NUM_LOCK:144,KEY_SCROLL_LOCK:145};
this.revKeys=[];
for(var key in this.keys){
this.revKeys[this.keys[key]]=key;
}
this.fixEvent=function(evt,_2e3){
if(!evt){
if(window["event"]){
evt=window.event;
}
}
if((evt["type"])&&(evt["type"].indexOf("key")==0)){
evt.keys=this.revKeys;
for(var key in this.keys){
evt[key]=this.keys[key];
}
if(evt["type"]=="keydown"&&dojo.render.html.ie){
switch(evt.keyCode){
case evt.KEY_SHIFT:
case evt.KEY_CTRL:
case evt.KEY_ALT:
case evt.KEY_CAPS_LOCK:
case evt.KEY_LEFT_WINDOW:
case evt.KEY_RIGHT_WINDOW:
case evt.KEY_SELECT:
case evt.KEY_NUM_LOCK:
case evt.KEY_SCROLL_LOCK:
case evt.KEY_NUMPAD_0:
case evt.KEY_NUMPAD_1:
case evt.KEY_NUMPAD_2:
case evt.KEY_NUMPAD_3:
case evt.KEY_NUMPAD_4:
case evt.KEY_NUMPAD_5:
case evt.KEY_NUMPAD_6:
case evt.KEY_NUMPAD_7:
case evt.KEY_NUMPAD_8:
case evt.KEY_NUMPAD_9:
case evt.KEY_NUMPAD_PERIOD:
break;
case evt.KEY_NUMPAD_MULTIPLY:
case evt.KEY_NUMPAD_PLUS:
case evt.KEY_NUMPAD_ENTER:
case evt.KEY_NUMPAD_MINUS:
case evt.KEY_NUMPAD_DIVIDE:
break;
case evt.KEY_PAUSE:
case evt.KEY_TAB:
case evt.KEY_BACKSPACE:
case evt.KEY_ENTER:
case evt.KEY_ESCAPE:
case evt.KEY_PAGE_UP:
case evt.KEY_PAGE_DOWN:
case evt.KEY_END:
case evt.KEY_HOME:
case evt.KEY_LEFT_ARROW:
case evt.KEY_UP_ARROW:
case evt.KEY_RIGHT_ARROW:
case evt.KEY_DOWN_ARROW:
case evt.KEY_INSERT:
case evt.KEY_DELETE:
case evt.KEY_F1:
case evt.KEY_F2:
case evt.KEY_F3:
case evt.KEY_F4:
case evt.KEY_F5:
case evt.KEY_F6:
case evt.KEY_F7:
case evt.KEY_F8:
case evt.KEY_F9:
case evt.KEY_F10:
case evt.KEY_F11:
case evt.KEY_F12:
case evt.KEY_F12:
case evt.KEY_F13:
case evt.KEY_F14:
case evt.KEY_F15:
case evt.KEY_CLEAR:
case evt.KEY_HELP:
evt.key=evt.keyCode;
break;
default:
if(evt.ctrlKey||evt.altKey){
var _2e5=evt.keyCode;
if(_2e5>=65&&_2e5<=90&&evt.shiftKey==false){
_2e5+=32;
}
if(_2e5>=1&&_2e5<=26&&evt.ctrlKey){
_2e5+=96;
}
evt.key=String.fromCharCode(_2e5);
}
}
}else{
if(evt["type"]=="keypress"){
if(dojo.render.html.opera){
if(evt.which==0){
evt.key=evt.keyCode;
}else{
if(evt.which>0){
switch(evt.which){
case evt.KEY_SHIFT:
case evt.KEY_CTRL:
case evt.KEY_ALT:
case evt.KEY_CAPS_LOCK:
case evt.KEY_NUM_LOCK:
case evt.KEY_SCROLL_LOCK:
break;
case evt.KEY_PAUSE:
case evt.KEY_TAB:
case evt.KEY_BACKSPACE:
case evt.KEY_ENTER:
case evt.KEY_ESCAPE:
evt.key=evt.which;
break;
default:
var _2e5=evt.which;
if((evt.ctrlKey||evt.altKey||evt.metaKey)&&(evt.which>=65&&evt.which<=90&&evt.shiftKey==false)){
_2e5+=32;
}
evt.key=String.fromCharCode(_2e5);
}
}
}
}else{
if(dojo.render.html.ie){
if(!evt.ctrlKey&&!evt.altKey&&evt.keyCode>=evt.KEY_SPACE){
evt.key=String.fromCharCode(evt.keyCode);
}
}else{
if(dojo.render.html.safari){
switch(evt.keyCode){
case 25:
evt.key=evt.KEY_TAB;
evt.shift=true;
break;
case 63232:
evt.key=evt.KEY_UP_ARROW;
break;
case 63233:
evt.key=evt.KEY_DOWN_ARROW;
break;
case 63234:
evt.key=evt.KEY_LEFT_ARROW;
break;
case 63235:
evt.key=evt.KEY_RIGHT_ARROW;
break;
case 63236:
evt.key=evt.KEY_F1;
break;
case 63237:
evt.key=evt.KEY_F2;
break;
case 63238:
evt.key=evt.KEY_F3;
break;
case 63239:
evt.key=evt.KEY_F4;
break;
case 63240:
evt.key=evt.KEY_F5;
break;
case 63241:
evt.key=evt.KEY_F6;
break;
case 63242:
evt.key=evt.KEY_F7;
break;
case 63243:
evt.key=evt.KEY_F8;
break;
case 63244:
evt.key=evt.KEY_F9;
break;
case 63245:
evt.key=evt.KEY_F10;
break;
case 63246:
evt.key=evt.KEY_F11;
break;
case 63247:
evt.key=evt.KEY_F12;
break;
case 63250:
evt.key=evt.KEY_PAUSE;
break;
case 63272:
evt.key=evt.KEY_DELETE;
break;
case 63273:
evt.key=evt.KEY_HOME;
break;
case 63275:
evt.key=evt.KEY_END;
break;
case 63276:
evt.key=evt.KEY_PAGE_UP;
break;
case 63277:
evt.key=evt.KEY_PAGE_DOWN;
break;
case 63302:
evt.key=evt.KEY_INSERT;
break;
case 63248:
case 63249:
case 63289:
break;
default:
evt.key=evt.charCode>=evt.KEY_SPACE?String.fromCharCode(evt.charCode):evt.keyCode;
}
}else{
evt.key=evt.charCode>0?String.fromCharCode(evt.charCode):evt.keyCode;
}
}
}
}
}
}
if(dojo.render.html.ie){
if(!evt.target){
evt.target=evt.srcElement;
}
if(!evt.currentTarget){
evt.currentTarget=(_2e3?_2e3:evt.srcElement);
}
if(!evt.layerX){
evt.layerX=evt.offsetX;
}
if(!evt.layerY){
evt.layerY=evt.offsetY;
}
var doc=(evt.srcElement&&evt.srcElement.ownerDocument)?evt.srcElement.ownerDocument:document;
var _2e7=((dojo.render.html.ie55)||(doc["compatMode"]=="BackCompat"))?doc.body:doc.documentElement;
if(!evt.pageX){
evt.pageX=evt.clientX+(_2e7.scrollLeft||0);
}
if(!evt.pageY){
evt.pageY=evt.clientY+(_2e7.scrollTop||0);
}
if(evt.type=="mouseover"){
evt.relatedTarget=evt.fromElement;
}
if(evt.type=="mouseout"){
evt.relatedTarget=evt.toElement;
}
this.currentEvent=evt;
evt.callListener=this.callListener;
evt.stopPropagation=this._stopPropagation;
evt.preventDefault=this._preventDefault;
}
return evt;
};
this.stopEvent=function(evt){
if(window.event){
evt.cancelBubble=true;
evt.returnValue=false;
}else{
evt.preventDefault();
evt.stopPropagation();
}
};
};
dojo.provide("dojo.event.*");
dojo.provide("dojo.widget.Manager");
dojo.widget.manager=new function(){
this.widgets=[];
this.widgetIds=[];
this.topWidgets={};
var _2e9={};
var _2ea=[];
this.getUniqueId=function(_2eb){
var _2ec;
do{
_2ec=_2eb+"_"+(_2e9[_2eb]!=undefined?++_2e9[_2eb]:_2e9[_2eb]=0);
}while(this.getWidgetById(_2ec));
return _2ec;
};
this.add=function(_2ed){
this.widgets.push(_2ed);
if(!_2ed.extraArgs["id"]){
_2ed.extraArgs["id"]=_2ed.extraArgs["ID"];
}
if(_2ed.widgetId==""){
if(_2ed["id"]){
_2ed.widgetId=_2ed["id"];
}else{
if(_2ed.extraArgs["id"]){
_2ed.widgetId=_2ed.extraArgs["id"];
}else{
_2ed.widgetId=this.getUniqueId(_2ed.ns+"_"+_2ed.widgetType);
}
}
}
if(this.widgetIds[_2ed.widgetId]){
dojo.debug("widget ID collision on ID: "+_2ed.widgetId);
}
this.widgetIds[_2ed.widgetId]=_2ed;
};
this.destroyAll=function(){
for(var x=this.widgets.length-1;x>=0;x--){
try{
this.widgets[x].destroy(true);
delete this.widgets[x];
}
catch(e){
}
}
};
this.remove=function(_2ef){
if(dojo.lang.isNumber(_2ef)){
var tw=this.widgets[_2ef].widgetId;
delete this.widgetIds[tw];
this.widgets.splice(_2ef,1);
}else{
this.removeById(_2ef);
}
};
this.removeById=function(id){
if(!dojo.lang.isString(id)){
id=id["widgetId"];
if(!id){
dojo.debug("invalid widget or id passed to removeById");
return;
}
}
for(var i=0;i<this.widgets.length;i++){
if(this.widgets[i].widgetId==id){
this.remove(i);
break;
}
}
};
this.getWidgetById=function(id){
if(dojo.lang.isString(id)){
return this.widgetIds[id];
}
return id;
};
this.getWidgetsByType=function(type){
var lt=type.toLowerCase();
var _2f6=(type.indexOf(":")<0?function(x){
return x.widgetType.toLowerCase();
}:function(x){
return x.getNamespacedType();
});
var ret=[];
dojo.lang.forEach(this.widgets,function(x){
if(_2f6(x)==lt){
ret.push(x);
}
});
return ret;
};
this.getWidgetsByFilter=function(_2fb,_2fc){
var ret=[];
dojo.lang.every(this.widgets,function(x){
if(_2fb(x)){
ret.push(x);
if(_2fc){
return false;
}
}
return true;
});
return (_2fc?ret[0]:ret);
};
this.getAllWidgets=function(){
return this.widgets.concat();
};
this.getWidgetByNode=function(node){
var w=this.getAllWidgets();
node=dojo.byId(node);
for(var i=0;i<w.length;i++){
if(w[i].domNode==node){
return w[i];
}
}
return null;
};
this.byId=this.getWidgetById;
this.byType=this.getWidgetsByType;
this.byFilter=this.getWidgetsByFilter;
this.byNode=this.getWidgetByNode;
var _302={};
var _303=["dojo.widget"];
for(var i=0;i<_303.length;i++){
_303[_303[i]]=true;
}
this.registerWidgetPackage=function(_305){
if(!_303[_305]){
_303[_305]=true;
_303.push(_305);
}
};
this.getWidgetPackageList=function(){
return dojo.lang.map(_303,function(elt){
return (elt!==true?elt:undefined);
});
};
this.getImplementation=function(_307,_308,_309,ns){
var impl=this.getImplementationName(_307,ns);
if(impl){
var ret=_308?new impl(_308):new impl();
return ret;
}
};
function buildPrefixCache(){
for(var _30d in dojo.render){
if(dojo.render[_30d]["capable"]===true){
var _30e=dojo.render[_30d].prefixes;
for(var i=0;i<_30e.length;i++){
_2ea.push(_30e[i].toLowerCase());
}
}
}
}
var _310=function(_311,_312){
if(!_312){
return null;
}
for(var i=0,l=_2ea.length,_315;i<=l;i++){
_315=(i<l?_312[_2ea[i]]:_312);
if(!_315){
continue;
}
for(var name in _315){
if(name.toLowerCase()==_311){
return _315[name];
}
}
}
return null;
};
var _317=function(_318,_319){
var _31a=dojo.evalObjPath(_319,false);
return (_31a?_310(_318,_31a):null);
};
this.getImplementationName=function(_31b,ns){
var _31d=_31b.toLowerCase();
ns=ns||"dojo";
var imps=_302[ns]||(_302[ns]={});
var impl=imps[_31d];
if(impl){
return impl;
}
if(!_2ea.length){
buildPrefixCache();
}
var _320=dojo.ns.get(ns);
if(!_320){
dojo.ns.register(ns,ns+".widget");
_320=dojo.ns.get(ns);
}
if(_320){
_320.resolve(_31b);
}
impl=_317(_31d,_320.module);
if(impl){
return (imps[_31d]=impl);
}
_320=dojo.ns.require(ns);
if((_320)&&(_320.resolver)){
_320.resolve(_31b);
impl=_317(_31d,_320.module);
if(impl){
return (imps[_31d]=impl);
}
}
throw new Error("Could not locate widget implementation for \""+_31b+"\" in \""+_320.module+"\" registered to namespace \""+_320.name+"\"");
};
this.resizing=false;
this.onWindowResized=function(){
if(this.resizing){
return;
}
try{
this.resizing=true;
for(var id in this.topWidgets){
var _322=this.topWidgets[id];
if(_322.checkSize){
_322.checkSize();
}
}
}
catch(e){
}
finally{
this.resizing=false;
}
};
if(typeof window!="undefined"){
dojo.addOnLoad(this,"onWindowResized");
dojo.event.connect(window,"onresize",this,"onWindowResized");
}
};
(function(){
var dw=dojo.widget;
var dwm=dw.manager;
var h=dojo.lang.curry(dojo.lang,"hitch",dwm);
var g=function(_327,_328){
dw[(_328||_327)]=h(_327);
};
g("add","addWidget");
g("destroyAll","destroyAllWidgets");
g("remove","removeWidget");
g("removeById","removeWidgetById");
g("getWidgetById");
g("getWidgetById","byId");
g("getWidgetsByType");
g("getWidgetsByFilter");
g("getWidgetsByType","byType");
g("getWidgetsByFilter","byFilter");
g("getWidgetByNode","byNode");
dw.all=function(n){
var _32a=dwm.getAllWidgets.apply(dwm,arguments);
if(arguments.length>0){
return _32a[n];
}
return _32a;
};
g("registerWidgetPackage");
g("getImplementation","getWidgetImplementation");
g("getImplementationName","getWidgetImplementationName");
dw.widgets=dwm.widgets;
dw.widgetIds=dwm.widgetIds;
dw.root=dwm.root;
})();
dojo.provide("dojo.uri.Uri");
dojo.uri=new function(){
this.dojoUri=function(uri){
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri(),uri);
};
this.moduleUri=function(_32c,uri){
var loc=dojo.hostenv.getModuleSymbols(_32c).join("/");
if(!loc){
return null;
}
if(loc.lastIndexOf("/")!=loc.length-1){
loc+="/";
}
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri()+loc,uri);
};
this.Uri=function(){
var uri=arguments[0];
for(var i=1;i<arguments.length;i++){
if(!arguments[i]){
continue;
}
var _331=new dojo.uri.Uri(arguments[i].toString());
var _332=new dojo.uri.Uri(uri.toString());
if((_331.path=="")&&(_331.scheme==null)&&(_331.authority==null)&&(_331.query==null)){
if(_331.fragment!=null){
_332.fragment=_331.fragment;
}
_331=_332;
}else{
if(_331.scheme==null){
_331.scheme=_332.scheme;
if(_331.authority==null){
_331.authority=_332.authority;
if(_331.path.charAt(0)!="/"){
var path=_332.path.substring(0,_332.path.lastIndexOf("/")+1)+_331.path;
var segs=path.split("/");
for(var j=0;j<segs.length;j++){
if(segs[j]=="."){
if(j==segs.length-1){
segs[j]="";
}else{
segs.splice(j,1);
j--;
}
}else{
if(j>0&&!(j==1&&segs[0]=="")&&segs[j]==".."&&segs[j-1]!=".."){
if(j==segs.length-1){
segs.splice(j,1);
segs[j-1]="";
}else{
segs.splice(j-1,2);
j-=2;
}
}
}
}
_331.path=segs.join("/");
}
}
}
}
uri="";
if(_331.scheme!=null){
uri+=_331.scheme+":";
}
if(_331.authority!=null){
uri+="//"+_331.authority;
}
uri+=_331.path;
if(_331.query!=null){
uri+="?"+_331.query;
}
if(_331.fragment!=null){
uri+="#"+_331.fragment;
}
}
this.uri=uri.toString();
var _336="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?$";
var r=this.uri.match(new RegExp(_336));
this.scheme=r[2]||(r[1]?"":null);
this.authority=r[4]||(r[3]?"":null);
this.path=r[5];
this.query=r[7]||(r[6]?"":null);
this.fragment=r[9]||(r[8]?"":null);
if(this.authority!=null){
_336="^((([^:]+:)?([^@]+))@)?([^:]*)(:([0-9]+))?$";
r=this.authority.match(new RegExp(_336));
this.user=r[3]||null;
this.password=r[4]||null;
this.host=r[5];
this.port=r[7]||null;
}
this.toString=function(){
return this.uri;
};
};
};
dojo.provide("dojo.uri.*");
dojo.provide("dojo.html.common");
dojo.lang.mixin(dojo.html,dojo.dom);
dojo.html.getEventTarget=function(evt){
if(!evt){
evt=dojo.global().event||{};
}
var t=(evt.srcElement?evt.srcElement:(evt.target?evt.target:null));
while((t)&&(t.nodeType!=1)){
t=t.parentNode;
}
return t;
};
dojo.html.getViewport=function(){
var _33a=dojo.global();
var _33b=dojo.doc();
var w=0;
var h=0;
if(dojo.render.html.mozilla){
w=_33b.documentElement.clientWidth;
h=_33a.innerHeight;
}else{
if(!dojo.render.html.opera&&_33a.innerWidth){
w=_33a.innerWidth;
h=_33a.innerHeight;
}else{
if(!dojo.render.html.opera&&dojo.exists(_33b,"documentElement.clientWidth")){
var w2=_33b.documentElement.clientWidth;
if(!w||w2&&w2<w){
w=w2;
}
h=_33b.documentElement.clientHeight;
}else{
if(dojo.body().clientWidth){
w=dojo.body().clientWidth;
h=dojo.body().clientHeight;
}
}
}
}
return {width:w,height:h};
};
dojo.html.getScroll=function(){
var _33f=dojo.global();
var _340=dojo.doc();
var top=_33f.pageYOffset||_340.documentElement.scrollTop||dojo.body().scrollTop||0;
var left=_33f.pageXOffset||_340.documentElement.scrollLeft||dojo.body().scrollLeft||0;
return {top:top,left:left,offset:{x:left,y:top}};
};
dojo.html.getParentByType=function(node,type){
var _345=dojo.doc();
var _346=dojo.byId(node);
type=type.toLowerCase();
while((_346)&&(_346.nodeName.toLowerCase()!=type)){
if(_346==(_345["body"]||_345["documentElement"])){
return null;
}
_346=_346.parentNode;
}
return _346;
};
dojo.html.getAttribute=function(node,attr){
node=dojo.byId(node);
if((!node)||(!node.getAttribute)){
return null;
}
var ta=typeof attr=="string"?attr:new String(attr);
var v=node.getAttribute(ta.toUpperCase());
if((v)&&(typeof v=="string")&&(v!="")){
return v;
}
if(v&&v.value){
return v.value;
}
if((node.getAttributeNode)&&(node.getAttributeNode(ta))){
return (node.getAttributeNode(ta)).value;
}else{
if(node.getAttribute(ta)){
return node.getAttribute(ta);
}else{
if(node.getAttribute(ta.toLowerCase())){
return node.getAttribute(ta.toLowerCase());
}
}
}
return null;
};
dojo.html.hasAttribute=function(node,attr){
return dojo.html.getAttribute(dojo.byId(node),attr)?true:false;
};
dojo.html.getCursorPosition=function(e){
e=e||dojo.global().event;
var _34e={x:0,y:0};
if(e.pageX||e.pageY){
_34e.x=e.pageX;
_34e.y=e.pageY;
}else{
var de=dojo.doc().documentElement;
var db=dojo.body();
_34e.x=e.clientX+((de||db)["scrollLeft"])-((de||db)["clientLeft"]);
_34e.y=e.clientY+((de||db)["scrollTop"])-((de||db)["clientTop"]);
}
return _34e;
};
dojo.html.isTag=function(node){
node=dojo.byId(node);
if(node&&node.tagName){
for(var i=1;i<arguments.length;i++){
if(node.tagName.toLowerCase()==String(arguments[i]).toLowerCase()){
return String(arguments[i]).toLowerCase();
}
}
}
return "";
};
if(dojo.render.html.ie&&!dojo.render.html.ie70){
if(window.location.href.substr(0,6).toLowerCase()!="https:"){
(function(){
var _353=dojo.doc().createElement("script");
_353.src="javascript:'dojo.html.createExternalElement=function(doc, tag){ return doc.createElement(tag); }'";
dojo.doc().getElementsByTagName("head")[0].appendChild(_353);
})();
}
}else{
dojo.html.createExternalElement=function(doc,tag){
return doc.createElement(tag);
};
}
dojo.provide("dojo.a11y");
dojo.a11y={imgPath:dojo.uri.dojoUri("src/widget/templates/images"),doAccessibleCheck:true,accessible:null,checkAccessible:function(){
if(this.accessible===null){
this.accessible=false;
if(this.doAccessibleCheck==true){
this.accessible=this.testAccessible();
}
}
return this.accessible;
},testAccessible:function(){
this.accessible=false;
if(dojo.render.html.ie||dojo.render.html.mozilla){
var div=document.createElement("div");
div.style.backgroundImage="url(\""+this.imgPath+"/tab_close.gif\")";
dojo.body().appendChild(div);
var _357=null;
if(window.getComputedStyle){
var _358=getComputedStyle(div,"");
_357=_358.getPropertyValue("background-image");
}else{
_357=div.currentStyle.backgroundImage;
}
var _359=false;
if(_357!=null&&(_357=="none"||_357=="url(invalid-url:)")){
this.accessible=true;
}
dojo.body().removeChild(div);
}
return this.accessible;
},setAccessible:function(_35a){
this.accessible=_35a;
},setCheckAccessible:function(_35b){
this.doAccessibleCheck=_35b;
},setAccessibleMode:function(){
if(this.accessible===null){
if(this.checkAccessible()){
dojo.render.html.prefixes.unshift("a11y");
}
}
return this.accessible;
}};
dojo.provide("dojo.widget.Widget");
dojo.declare("dojo.widget.Widget",null,function(){
this.children=[];
this.extraArgs={};
},{parent:null,isTopLevel:false,disabled:false,isContainer:false,widgetId:"",widgetType:"Widget",ns:"dojo",getNamespacedType:function(){
return (this.ns?this.ns+":"+this.widgetType:this.widgetType).toLowerCase();
},toString:function(){
return "[Widget "+this.getNamespacedType()+", "+(this.widgetId||"NO ID")+"]";
},repr:function(){
return this.toString();
},enable:function(){
this.disabled=false;
},disable:function(){
this.disabled=true;
},onResized:function(){
this.notifyChildrenOfResize();
},notifyChildrenOfResize:function(){
for(var i=0;i<this.children.length;i++){
var _35d=this.children[i];
if(_35d.onResized){
_35d.onResized();
}
}
},create:function(args,_35f,_360,ns){
if(ns){
this.ns=ns;
}
this.satisfyPropertySets(args,_35f,_360);
this.mixInProperties(args,_35f,_360);
this.postMixInProperties(args,_35f,_360);
dojo.widget.manager.add(this);
this.buildRendering(args,_35f,_360);
this.initialize(args,_35f,_360);
this.postInitialize(args,_35f,_360);
this.postCreate(args,_35f,_360);
return this;
},destroy:function(_362){
if(this.parent){
this.parent.removeChild(this);
}
this.destroyChildren();
this.uninitialize();
this.destroyRendering(_362);
dojo.widget.manager.removeById(this.widgetId);
},destroyChildren:function(){
var _363;
var i=0;
while(this.children.length>i){
_363=this.children[i];
if(_363 instanceof dojo.widget.Widget){
this.removeChild(_363);
_363.destroy();
continue;
}
i++;
}
},getChildrenOfType:function(type,_366){
var ret=[];
var _368=dojo.lang.isFunction(type);
if(!_368){
type=type.toLowerCase();
}
for(var x=0;x<this.children.length;x++){
if(_368){
if(this.children[x] instanceof type){
ret.push(this.children[x]);
}
}else{
if(this.children[x].widgetType.toLowerCase()==type){
ret.push(this.children[x]);
}
}
if(_366){
ret=ret.concat(this.children[x].getChildrenOfType(type,_366));
}
}
return ret;
},getDescendants:function(){
var _36a=[];
var _36b=[this];
var elem;
while((elem=_36b.pop())){
_36a.push(elem);
if(elem.children){
dojo.lang.forEach(elem.children,function(elem){
_36b.push(elem);
});
}
}
return _36a;
},isFirstChild:function(){
return this===this.parent.children[0];
},isLastChild:function(){
return this===this.parent.children[this.parent.children.length-1];
},satisfyPropertySets:function(args){
return args;
},mixInProperties:function(args,frag){
if((args["fastMixIn"])||(frag["fastMixIn"])){
for(var x in args){
this[x]=args[x];
}
return;
}
var _372;
var _373=dojo.widget.lcArgsCache[this.widgetType];
if(_373==null){
_373={};
for(var y in this){
_373[((new String(y)).toLowerCase())]=y;
}
dojo.widget.lcArgsCache[this.widgetType]=_373;
}
var _375={};
for(var x in args){
if(!this[x]){
var y=_373[(new String(x)).toLowerCase()];
if(y){
args[y]=args[x];
x=y;
}
}
if(_375[x]){
continue;
}
_375[x]=true;
if((typeof this[x])!=(typeof _372)){
if(typeof args[x]!="string"){
this[x]=args[x];
}else{
if(dojo.lang.isString(this[x])){
this[x]=args[x];
}else{
if(dojo.lang.isNumber(this[x])){
this[x]=new Number(args[x]);
}else{
if(dojo.lang.isBoolean(this[x])){
this[x]=(args[x].toLowerCase()=="false")?false:true;
}else{
if(dojo.lang.isFunction(this[x])){
if(args[x].search(/[^\w\.]+/i)==-1){
this[x]=dojo.evalObjPath(args[x],false);
}else{
var tn=dojo.lang.nameAnonFunc(new Function(args[x]),this);
dojo.event.kwConnect({srcObj:this,srcFunc:x,adviceObj:this,adviceFunc:tn});
}
}else{
if(dojo.lang.isArray(this[x])){
this[x]=args[x].split(";");
}else{
if(this[x] instanceof Date){
this[x]=new Date(Number(args[x]));
}else{
if(typeof this[x]=="object"){
if(this[x] instanceof dojo.uri.Uri){
this[x]=dojo.uri.dojoUri(args[x]);
}else{
var _377=args[x].split(";");
for(var y=0;y<_377.length;y++){
var si=_377[y].indexOf(":");
if((si!=-1)&&(_377[y].length>si)){
this[x][_377[y].substr(0,si).replace(/^\s+|\s+$/g,"")]=_377[y].substr(si+1);
}
}
}
}else{
this[x]=args[x];
}
}
}
}
}
}
}
}
}else{
this.extraArgs[x.toLowerCase()]=args[x];
}
}
},postMixInProperties:function(args,frag,_37b){
},initialize:function(args,frag,_37e){
return false;
},postInitialize:function(args,frag,_381){
return false;
},postCreate:function(args,frag,_384){
return false;
},uninitialize:function(){
return false;
},buildRendering:function(args,frag,_387){
dojo.unimplemented("dojo.widget.Widget.buildRendering, on "+this.toString()+", ");
return false;
},destroyRendering:function(){
dojo.unimplemented("dojo.widget.Widget.destroyRendering");
return false;
},addedTo:function(_388){
},addChild:function(_389){
dojo.unimplemented("dojo.widget.Widget.addChild");
return false;
},removeChild:function(_38a){
for(var x=0;x<this.children.length;x++){
if(this.children[x]===_38a){
this.children.splice(x,1);
_38a.parent=null;
break;
}
}
return _38a;
},getPreviousSibling:function(){
var idx=this.getParentIndex();
if(idx<=0){
return null;
}
return this.parent.children[idx-1];
},getSiblings:function(){
return this.parent.children;
},getParentIndex:function(){
return dojo.lang.indexOf(this.parent.children,this,true);
},getNextSibling:function(){
var idx=this.getParentIndex();
if(idx==this.parent.children.length-1){
return null;
}
if(idx<0){
return null;
}
return this.parent.children[idx+1];
}});
dojo.widget.lcArgsCache={};
dojo.widget.tags={};
dojo.widget.tags["dojo:propertyset"]=function(_38e,_38f,_390){
var _391=_38f.parseProperties(_38e["dojo:propertyset"]);
};
dojo.widget.tags["dojo:connect"]=function(_392,_393,_394){
var _395=_393.parseProperties(_392["dojo:connect"]);
};
dojo.widget.buildWidgetFromParseTree=function(type,frag,_398,_399,_39a,_39b){
dojo.a11y.setAccessibleMode();
var _39c=type.split(":");
_39c=(_39c.length==2)?_39c[1]:type;
var _39d=_39b||_398.parseProperties(frag[frag["ns"]+":"+_39c]);
var _39e=dojo.widget.manager.getImplementation(_39c,null,null,frag["ns"]);
if(!_39e){
throw new Error("cannot find \""+type+"\" widget");
}else{
if(!_39e.create){
throw new Error("\""+type+"\" widget object has no \"create\" method and does not appear to implement *Widget");
}
}
_39d["dojoinsertionindex"]=_39a;
var ret=_39e.create(_39d,frag,_399,frag["ns"]);
return ret;
};
dojo.widget.defineWidget=function(_3a0,_3a1,_3a2,init,_3a4){
if(dojo.lang.isString(arguments[3])){
dojo.widget._defineWidget(arguments[0],arguments[3],arguments[1],arguments[4],arguments[2]);
}else{
var args=[arguments[0]],p=3;
if(dojo.lang.isString(arguments[1])){
args.push(arguments[1],arguments[2]);
}else{
args.push("",arguments[1]);
p=2;
}
if(dojo.lang.isFunction(arguments[p])){
args.push(arguments[p],arguments[p+1]);
}else{
args.push(null,arguments[p]);
}
dojo.widget._defineWidget.apply(this,args);
}
};
dojo.widget.defineWidget.renderers="html|svg|vml";
dojo.widget._defineWidget=function(_3a7,_3a8,_3a9,init,_3ab){
var _3ac=_3a7.split(".");
var type=_3ac.pop();
var regx="\\.("+(_3a8?_3a8+"|":"")+dojo.widget.defineWidget.renderers+")\\.";
var r=_3a7.search(new RegExp(regx));
_3ac=(r<0?_3ac.join("."):_3a7.substr(0,r));
dojo.widget.manager.registerWidgetPackage(_3ac);
var pos=_3ac.indexOf(".");
var _3b1=(pos>-1)?_3ac.substring(0,pos):_3ac;
_3ab=(_3ab)||{};
_3ab.widgetType=type;
if((!init)&&(_3ab["classConstructor"])){
init=_3ab.classConstructor;
delete _3ab.classConstructor;
}
dojo.declare(_3a7,_3a9,init,_3ab);
};
dojo.provide("dojo.widget.Parse");
dojo.widget.Parse=function(_3b2){
this.propertySetsList=[];
this.fragment=_3b2;
this.createComponents=function(frag,_3b4){
var _3b5=[];
var _3b6=false;
try{
if(frag&&frag.tagName&&(frag!=frag.nodeRef)){
var _3b7=dojo.widget.tags;
var tna=String(frag.tagName).split(";");
for(var x=0;x<tna.length;x++){
var ltn=tna[x].replace(/^\s+|\s+$/g,"").toLowerCase();
frag.tagName=ltn;
var ret;
if(_3b7[ltn]){
_3b6=true;
ret=_3b7[ltn](frag,this,_3b4,frag.index);
_3b5.push(ret);
}else{
if(ltn.indexOf(":")==-1){
ltn="dojo:"+ltn;
}
ret=dojo.widget.buildWidgetFromParseTree(ltn,frag,this,_3b4,frag.index);
if(ret){
_3b6=true;
_3b5.push(ret);
}
}
}
}
}
catch(e){
dojo.debug("dojo.widget.Parse: error:",e);
}
if(!_3b6){
_3b5=_3b5.concat(this.createSubComponents(frag,_3b4));
}
return _3b5;
};
this.createSubComponents=function(_3bc,_3bd){
var frag,_3bf=[];
for(var item in _3bc){
frag=_3bc[item];
if(frag&&typeof frag=="object"&&(frag!=_3bc.nodeRef)&&(frag!=_3bc.tagName)&&(item.indexOf("$")==-1)){
_3bf=_3bf.concat(this.createComponents(frag,_3bd));
}
}
return _3bf;
};
this.parsePropertySets=function(_3c1){
return [];
};
this.parseProperties=function(_3c2){
var _3c3={};
for(var item in _3c2){
if((_3c2[item]==_3c2.tagName)||(_3c2[item]==_3c2.nodeRef)){
}else{
var frag=_3c2[item];
if(frag.tagName&&dojo.widget.tags[frag.tagName.toLowerCase()]){
}else{
if(frag[0]&&frag[0].value!=""&&frag[0].value!=null){
try{
if(item.toLowerCase()=="dataprovider"){
var _3c6=this;
this.getDataProvider(_3c6,frag[0].value);
_3c3.dataProvider=this.dataProvider;
}
_3c3[item]=frag[0].value;
var _3c7=this.parseProperties(frag);
for(var _3c8 in _3c7){
_3c3[_3c8]=_3c7[_3c8];
}
}
catch(e){
dojo.debug(e);
}
}
}
switch(item.toLowerCase()){
case "checked":
case "disabled":
if(typeof _3c3[item]!="boolean"){
_3c3[item]=true;
}
break;
}
}
}
return _3c3;
};
this.getDataProvider=function(_3c9,_3ca){
dojo.io.bind({url:_3ca,load:function(type,_3cc){
if(type=="load"){
_3c9.dataProvider=_3cc;
}
},mimetype:"text/javascript",sync:true});
};
this.getPropertySetById=function(_3cd){
for(var x=0;x<this.propertySetsList.length;x++){
if(_3cd==this.propertySetsList[x]["id"][0].value){
return this.propertySetsList[x];
}
}
return "";
};
this.getPropertySetsByType=function(_3cf){
var _3d0=[];
for(var x=0;x<this.propertySetsList.length;x++){
var cpl=this.propertySetsList[x];
var cpcc=cpl.componentClass||cpl.componentType||null;
var _3d4=this.propertySetsList[x]["id"][0].value;
if(cpcc&&(_3d4==cpcc[0].value)){
_3d0.push(cpl);
}
}
return _3d0;
};
this.getPropertySets=function(_3d5){
var ppl="dojo:propertyproviderlist";
var _3d7=[];
var _3d8=_3d5.tagName;
if(_3d5[ppl]){
var _3d9=_3d5[ppl].value.split(" ");
for(var _3da in _3d9){
if((_3da.indexOf("..")==-1)&&(_3da.indexOf("://")==-1)){
var _3db=this.getPropertySetById(_3da);
if(_3db!=""){
_3d7.push(_3db);
}
}else{
}
}
}
return this.getPropertySetsByType(_3d8).concat(_3d7);
};
this.createComponentFromScript=function(_3dc,_3dd,_3de,ns){
_3de.fastMixIn=true;
var ltn=(ns||"dojo")+":"+_3dd.toLowerCase();
if(dojo.widget.tags[ltn]){
return [dojo.widget.tags[ltn](_3de,this,null,null,_3de)];
}
return [dojo.widget.buildWidgetFromParseTree(ltn,_3de,this,null,null,_3de)];
};
};
dojo.widget._parser_collection={"dojo":new dojo.widget.Parse()};
dojo.widget.getParser=function(name){
if(!name){
name="dojo";
}
if(!this._parser_collection[name]){
this._parser_collection[name]=new dojo.widget.Parse();
}
return this._parser_collection[name];
};
dojo.widget.createWidget=function(name,_3e3,_3e4,_3e5){
var _3e6=false;
var _3e7=(typeof name=="string");
if(_3e7){
var pos=name.indexOf(":");
var ns=(pos>-1)?name.substring(0,pos):"dojo";
if(pos>-1){
name=name.substring(pos+1);
}
var _3ea=name.toLowerCase();
var _3eb=ns+":"+_3ea;
_3e6=(dojo.byId(name)&&!dojo.widget.tags[_3eb]);
}
if((arguments.length==1)&&(_3e6||!_3e7)){
var xp=new dojo.xml.Parse();
var tn=_3e6?dojo.byId(name):name;
return dojo.widget.getParser().createComponents(xp.parseElement(tn,null,true))[0];
}
function fromScript(_3ee,name,_3f0,ns){
_3f0[_3eb]={dojotype:[{value:_3ea}],nodeRef:_3ee,fastMixIn:true};
_3f0.ns=ns;
return dojo.widget.getParser().createComponentFromScript(_3ee,name,_3f0,ns);
}
_3e3=_3e3||{};
var _3f2=false;
var tn=null;
var h=dojo.render.html.capable;
if(h){
tn=document.createElement("span");
}
if(!_3e4){
_3f2=true;
_3e4=tn;
if(h){
dojo.body().appendChild(_3e4);
}
}else{
if(_3e5){
dojo.dom.insertAtPosition(tn,_3e4,_3e5);
}else{
tn=_3e4;
}
}
var _3f4=fromScript(tn,name.toLowerCase(),_3e3,ns);
if((!_3f4)||(!_3f4[0])||(typeof _3f4[0].widgetType=="undefined")){
throw new Error("createWidget: Creation of \""+name+"\" widget failed.");
}
try{
if(_3f2&&_3f4[0].domNode.parentNode){
_3f4[0].domNode.parentNode.removeChild(_3f4[0].domNode);
}
}
catch(e){
dojo.debug(e);
}
return _3f4[0];
};
dojo.provide("dojo.html.style");
dojo.html.getClass=function(node){
node=dojo.byId(node);
if(!node){
return "";
}
var cs="";
if(node.className){
cs=node.className;
}else{
if(dojo.html.hasAttribute(node,"class")){
cs=dojo.html.getAttribute(node,"class");
}
}
return cs.replace(/^\s+|\s+$/g,"");
};
dojo.html.getClasses=function(node){
var c=dojo.html.getClass(node);
return (c=="")?[]:c.split(/\s+/g);
};
dojo.html.hasClass=function(node,_3fa){
return (new RegExp("(^|\\s+)"+_3fa+"(\\s+|$)")).test(dojo.html.getClass(node));
};
dojo.html.prependClass=function(node,_3fc){
_3fc+=" "+dojo.html.getClass(node);
return dojo.html.setClass(node,_3fc);
};
dojo.html.addClass=function(node,_3fe){
if(dojo.html.hasClass(node,_3fe)){
return false;
}
_3fe=(dojo.html.getClass(node)+" "+_3fe).replace(/^\s+|\s+$/g,"");
return dojo.html.setClass(node,_3fe);
};
dojo.html.setClass=function(node,_400){
node=dojo.byId(node);
var cs=new String(_400);
try{
if(typeof node.className=="string"){
node.className=cs;
}else{
if(node.setAttribute){
node.setAttribute("class",_400);
node.className=cs;
}else{
return false;
}
}
}
catch(e){
dojo.debug("dojo.html.setClass() failed",e);
}
return true;
};
dojo.html.removeClass=function(node,_403,_404){
try{
if(!_404){
var _405=dojo.html.getClass(node).replace(new RegExp("(^|\\s+)"+_403+"(\\s+|$)"),"$1$2");
}else{
var _405=dojo.html.getClass(node).replace(_403,"");
}
dojo.html.setClass(node,_405);
}
catch(e){
dojo.debug("dojo.html.removeClass() failed",e);
}
return true;
};
dojo.html.replaceClass=function(node,_407,_408){
dojo.html.removeClass(node,_408);
dojo.html.addClass(node,_407);
};
dojo.html.classMatchType={ContainsAll:0,ContainsAny:1,IsOnly:2};
dojo.html.getElementsByClass=function(_409,_40a,_40b,_40c,_40d){
_40d=false;
var _40e=dojo.doc();
_40a=dojo.byId(_40a)||_40e;
var _40f=_409.split(/\s+/g);
var _410=[];
if(_40c!=1&&_40c!=2){
_40c=0;
}
var _411=new RegExp("(\\s|^)(("+_40f.join(")|(")+"))(\\s|$)");
var _412=_40f.join(" ").length;
var _413=[];
if(!_40d&&_40e.evaluate){
var _414=".//"+(_40b||"*")+"[contains(";
if(_40c!=dojo.html.classMatchType.ContainsAny){
_414+="concat(' ',@class,' '), ' "+_40f.join(" ') and contains(concat(' ',@class,' '), ' ")+" ')";
if(_40c==2){
_414+=" and string-length(@class)="+_412+"]";
}else{
_414+="]";
}
}else{
_414+="concat(' ',@class,' '), ' "+_40f.join(" ') or contains(concat(' ',@class,' '), ' ")+" ')]";
}
var _415=_40e.evaluate(_414,_40a,null,XPathResult.ANY_TYPE,null);
var _416=_415.iterateNext();
while(_416){
try{
_413.push(_416);
_416=_415.iterateNext();
}
catch(e){
break;
}
}
return _413;
}else{
if(!_40b){
_40b="*";
}
_413=_40a.getElementsByTagName(_40b);
var node,i=0;
outer:
while(node=_413[i++]){
var _419=dojo.html.getClasses(node);
if(_419.length==0){
continue outer;
}
var _41a=0;
for(var j=0;j<_419.length;j++){
if(_411.test(_419[j])){
if(_40c==dojo.html.classMatchType.ContainsAny){
_410.push(node);
continue outer;
}else{
_41a++;
}
}else{
if(_40c==dojo.html.classMatchType.IsOnly){
continue outer;
}
}
}
if(_41a==_40f.length){
if((_40c==dojo.html.classMatchType.IsOnly)&&(_41a==_419.length)){
_410.push(node);
}else{
if(_40c==dojo.html.classMatchType.ContainsAll){
_410.push(node);
}
}
}
}
return _410;
}
};
dojo.html.getElementsByClassName=dojo.html.getElementsByClass;
dojo.html.toCamelCase=function(_41c){
var arr=_41c.split("-"),cc=arr[0];
for(var i=1;i<arr.length;i++){
cc+=arr[i].charAt(0).toUpperCase()+arr[i].substring(1);
}
return cc;
};
dojo.html.toSelectorCase=function(_420){
return _420.replace(/([A-Z])/g,"-$1").toLowerCase();
};
dojo.html.getComputedStyle=function(node,_422,_423){
node=dojo.byId(node);
var _422=dojo.html.toSelectorCase(_422);
var _424=dojo.html.toCamelCase(_422);
if(!node||!node.style){
return _423;
}else{
if(document.defaultView&&dojo.html.isDescendantOf(node,node.ownerDocument)){
try{
var cs=document.defaultView.getComputedStyle(node,"");
if(cs){
return cs.getPropertyValue(_422);
}
}
catch(e){
if(node.style.getPropertyValue){
return node.style.getPropertyValue(_422);
}else{
return _423;
}
}
}else{
if(node.currentStyle){
return node.currentStyle[_424];
}
}
}
if(node.style.getPropertyValue){
return node.style.getPropertyValue(_422);
}else{
return _423;
}
};
dojo.html.getStyleProperty=function(node,_427){
node=dojo.byId(node);
return (node&&node.style?node.style[dojo.html.toCamelCase(_427)]:undefined);
};
dojo.html.getStyle=function(node,_429){
var _42a=dojo.html.getStyleProperty(node,_429);
return (_42a?_42a:dojo.html.getComputedStyle(node,_429));
};
dojo.html.setStyle=function(node,_42c,_42d){
node=dojo.byId(node);
if(node&&node.style){
var _42e=dojo.html.toCamelCase(_42c);
node.style[_42e]=_42d;
}
};
dojo.html.setStyleText=function(_42f,text){
try{
_42f.style.cssText=text;
}
catch(e){
_42f.setAttribute("style",text);
}
};
dojo.html.copyStyle=function(_431,_432){
if(!_432.style.cssText){
_431.setAttribute("style",_432.getAttribute("style"));
}else{
_431.style.cssText=_432.style.cssText;
}
dojo.html.addClass(_431,dojo.html.getClass(_432));
};
dojo.html.getUnitValue=function(node,_434,_435){
var s=dojo.html.getComputedStyle(node,_434);
if((!s)||((s=="auto")&&(_435))){
return {value:0,units:"px"};
}
var _437=s.match(/(\-?[\d.]+)([a-z%]*)/i);
if(!_437){
return dojo.html.getUnitValue.bad;
}
return {value:Number(_437[1]),units:_437[2].toLowerCase()};
};
dojo.html.getUnitValue.bad={value:NaN,units:""};
dojo.html.getPixelValue=function(node,_439,_43a){
var _43b=dojo.html.getUnitValue(node,_439,_43a);
if(isNaN(_43b.value)){
return 0;
}
if((_43b.value)&&(_43b.units!="px")){
return NaN;
}
return _43b.value;
};
dojo.html.setPositivePixelValue=function(node,_43d,_43e){
if(isNaN(_43e)){
return false;
}
node.style[_43d]=Math.max(0,_43e)+"px";
return true;
};
dojo.html.styleSheet=null;
dojo.html.insertCssRule=function(_43f,_440,_441){
if(!dojo.html.styleSheet){
if(document.createStyleSheet){
dojo.html.styleSheet=document.createStyleSheet();
}else{
if(document.styleSheets[0]){
dojo.html.styleSheet=document.styleSheets[0];
}else{
return null;
}
}
}
if(arguments.length<3){
if(dojo.html.styleSheet.cssRules){
_441=dojo.html.styleSheet.cssRules.length;
}else{
if(dojo.html.styleSheet.rules){
_441=dojo.html.styleSheet.rules.length;
}else{
return null;
}
}
}
if(dojo.html.styleSheet.insertRule){
var rule=_43f+" { "+_440+" }";
return dojo.html.styleSheet.insertRule(rule,_441);
}else{
if(dojo.html.styleSheet.addRule){
return dojo.html.styleSheet.addRule(_43f,_440,_441);
}else{
return null;
}
}
};
dojo.html.removeCssRule=function(_443){
if(!dojo.html.styleSheet){
dojo.debug("no stylesheet defined for removing rules");
return false;
}
if(dojo.render.html.ie){
if(!_443){
_443=dojo.html.styleSheet.rules.length;
dojo.html.styleSheet.removeRule(_443);
}
}else{
if(document.styleSheets[0]){
if(!_443){
_443=dojo.html.styleSheet.cssRules.length;
}
dojo.html.styleSheet.deleteRule(_443);
}
}
return true;
};
dojo.html._insertedCssFiles=[];
dojo.html.insertCssFile=function(URI,doc,_446,_447){
if(!URI){
return;
}
if(!doc){
doc=document;
}
var _448=dojo.hostenv.getText(URI,false,_447);
if(_448===null){
return;
}
_448=dojo.html.fixPathsInCssText(_448,URI);
if(_446){
var idx=-1,node,ent=dojo.html._insertedCssFiles;
for(var i=0;i<ent.length;i++){
if((ent[i].doc==doc)&&(ent[i].cssText==_448)){
idx=i;
node=ent[i].nodeRef;
break;
}
}
if(node){
var _44d=doc.getElementsByTagName("style");
for(var i=0;i<_44d.length;i++){
if(_44d[i]==node){
return;
}
}
dojo.html._insertedCssFiles.shift(idx,1);
}
}
var _44e=dojo.html.insertCssText(_448,doc);
dojo.html._insertedCssFiles.push({"doc":doc,"cssText":_448,"nodeRef":_44e});
if(_44e&&djConfig.isDebug){
_44e.setAttribute("dbgHref",URI);
}
return _44e;
};
dojo.html.insertCssText=function(_44f,doc,URI){
if(!_44f){
return;
}
if(!doc){
doc=document;
}
if(URI){
_44f=dojo.html.fixPathsInCssText(_44f,URI);
}
var _452=doc.createElement("style");
_452.setAttribute("type","text/css");
var head=doc.getElementsByTagName("head")[0];
if(!head){
dojo.debug("No head tag in document, aborting styles");
return;
}else{
head.appendChild(_452);
}
if(_452.styleSheet){
var _454=function(){
try{
_452.styleSheet.cssText=_44f;
}
catch(e){
dojo.debug(e);
}
};
if(_452.styleSheet.disabled){
setTimeout(_454,10);
}else{
_454();
}
}else{
var _455=doc.createTextNode(_44f);
_452.appendChild(_455);
}
return _452;
};
dojo.html.fixPathsInCssText=function(_456,URI){
if(!_456||!URI){
return;
}
var _458,str="",url="",_45b="[\\t\\s\\w\\(\\)\\/\\.\\\\'\"-:#=&?~]+";
var _45c=new RegExp("url\\(\\s*("+_45b+")\\s*\\)");
var _45d=/(file|https?|ftps?):\/\//;
regexTrim=new RegExp("^[\\s]*(['\"]?)("+_45b+")\\1[\\s]*?$");
if(dojo.render.html.ie55||dojo.render.html.ie60){
var _45e=new RegExp("AlphaImageLoader\\((.*)src=['\"]("+_45b+")['\"]");
while(_458=_45e.exec(_456)){
url=_458[2].replace(regexTrim,"$2");
if(!_45d.exec(url)){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=_456.substring(0,_458.index)+"AlphaImageLoader("+_458[1]+"src='"+url+"'";
_456=_456.substr(_458.index+_458[0].length);
}
_456=str+_456;
str="";
}
while(_458=_45c.exec(_456)){
url=_458[1].replace(regexTrim,"$2");
if(!_45d.exec(url)){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=_456.substring(0,_458.index)+"url("+url+")";
_456=_456.substr(_458.index+_458[0].length);
}
return str+_456;
};
dojo.html.setActiveStyleSheet=function(_45f){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")){
a.disabled=true;
if(a.getAttribute("title")==_45f){
a.disabled=false;
}
}
}
};
dojo.html.getActiveStyleSheet=function(){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")&&!a.disabled){
return a.getAttribute("title");
}
}
return null;
};
dojo.html.getPreferredStyleSheet=function(){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("rel").indexOf("alt")==-1&&a.getAttribute("title")){
return a.getAttribute("title");
}
}
return null;
};
dojo.html.applyBrowserClass=function(node){
var drh=dojo.render.html;
var _46b={dj_ie:drh.ie,dj_ie55:drh.ie55,dj_ie6:drh.ie60,dj_ie7:drh.ie70,dj_iequirks:drh.ie&&drh.quirks,dj_opera:drh.opera,dj_opera8:drh.opera&&(Math.floor(dojo.render.version)==8),dj_opera9:drh.opera&&(Math.floor(dojo.render.version)==9),dj_khtml:drh.khtml,dj_safari:drh.safari,dj_gecko:drh.mozilla};
for(var p in _46b){
if(_46b[p]){
dojo.html.addClass(node,p);
}
}
};
dojo.provide("dojo.widget.DomWidget");
dojo.widget._cssFiles={};
dojo.widget._cssStrings={};
dojo.widget._templateCache={};
dojo.widget.defaultStrings={dojoRoot:dojo.hostenv.getBaseScriptUri(),baseScriptUri:dojo.hostenv.getBaseScriptUri()};
dojo.widget.fillFromTemplateCache=function(obj,_46e,_46f,_470){
var _471=_46e||obj.templatePath;
var _472=dojo.widget._templateCache;
if(!_471&&!obj["widgetType"]){
do{
var _473="__dummyTemplate__"+dojo.widget._templateCache.dummyCount++;
}while(_472[_473]);
obj.widgetType=_473;
}
var wt=_471?_471.toString():obj.widgetType;
var ts=_472[wt];
if(!ts){
_472[wt]={"string":null,"node":null};
if(_470){
ts={};
}else{
ts=_472[wt];
}
}
if((!obj.templateString)&&(!_470)){
obj.templateString=_46f||ts["string"];
}
if(obj.templateString){
obj.templateString=this._sanitizeTemplateString(obj.templateString);
}
if((!obj.templateNode)&&(!_470)){
obj.templateNode=ts["node"];
}
if((!obj.templateNode)&&(!obj.templateString)&&(_471)){
var _476=this._sanitizeTemplateString(dojo.hostenv.getText(_471));
obj.templateString=_476;
if(!_470){
_472[wt]["string"]=_476;
}
}
if((!ts["string"])&&(!_470)){
ts.string=obj.templateString;
}
};
dojo.widget._sanitizeTemplateString=function(_477){
if(_477){
_477=_477.replace(/^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im,"");
var _478=_477.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(_478){
_477=_478[1];
}
}else{
_477="";
}
return _477;
};
dojo.widget._templateCache.dummyCount=0;
dojo.widget.attachProperties=["dojoAttachPoint","id"];
dojo.widget.eventAttachProperty="dojoAttachEvent";
dojo.widget.onBuildProperty="dojoOnBuild";
dojo.widget.waiNames=["waiRole","waiState"];
dojo.widget.wai={waiRole:{name:"waiRole","namespace":"http://www.w3.org/TR/xhtml2",alias:"x2",prefix:"wairole:"},waiState:{name:"waiState","namespace":"http://www.w3.org/2005/07/aaa",alias:"aaa",prefix:""},setAttr:function(node,ns,attr,_47c){
if(dojo.render.html.ie){
node.setAttribute(this[ns].alias+":"+attr,this[ns].prefix+_47c);
}else{
node.setAttributeNS(this[ns]["namespace"],attr,this[ns].prefix+_47c);
}
},getAttr:function(node,ns,attr){
if(dojo.render.html.ie){
return node.getAttribute(this[ns].alias+":"+attr);
}else{
return node.getAttributeNS(this[ns]["namespace"],attr);
}
},removeAttr:function(node,ns,attr){
var _483=true;
if(dojo.render.html.ie){
_483=node.removeAttribute(this[ns].alias+":"+attr);
}else{
node.removeAttributeNS(this[ns]["namespace"],attr);
}
return _483;
}};
dojo.widget.attachTemplateNodes=function(_484,_485,_486){
var _487=dojo.dom.ELEMENT_NODE;
function trim(str){
return str.replace(/^\s+|\s+$/g,"");
}
if(!_484){
_484=_485.domNode;
}
if(_484.nodeType!=_487){
return;
}
var _489=_484.all||_484.getElementsByTagName("*");
var _48a=_485;
for(var x=-1;x<_489.length;x++){
var _48c=(x==-1)?_484:_489[x];
var _48d=[];
if(!_485.widgetsInTemplate||!_48c.getAttribute("dojoType")){
for(var y=0;y<this.attachProperties.length;y++){
var _48f=_48c.getAttribute(this.attachProperties[y]);
if(_48f){
_48d=_48f.split(";");
for(var z=0;z<_48d.length;z++){
if(dojo.lang.isArray(_485[_48d[z]])){
_485[_48d[z]].push(_48c);
}else{
_485[_48d[z]]=_48c;
}
}
break;
}
}
var _491=_48c.getAttribute(this.eventAttachProperty);
if(_491){
var evts=_491.split(";");
for(var y=0;y<evts.length;y++){
if((!evts[y])||(!evts[y].length)){
continue;
}
var _493=null;
var tevt=trim(evts[y]);
if(evts[y].indexOf(":")>=0){
var _495=tevt.split(":");
tevt=trim(_495[0]);
_493=trim(_495[1]);
}
if(!_493){
_493=tevt;
}
var tf=function(){
var ntf=new String(_493);
return function(evt){
if(_48a[ntf]){
_48a[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_48c,tevt,tf,false,true);
}
}
for(var y=0;y<_486.length;y++){
var _499=_48c.getAttribute(_486[y]);
if((_499)&&(_499.length)){
var _493=null;
var _49a=_486[y].substr(4);
_493=trim(_499);
var _49b=[_493];
if(_493.indexOf(";")>=0){
_49b=dojo.lang.map(_493.split(";"),trim);
}
for(var z=0;z<_49b.length;z++){
if(!_49b[z].length){
continue;
}
var tf=function(){
var ntf=new String(_49b[z]);
return function(evt){
if(_48a[ntf]){
_48a[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_48c,_49a,tf,false,true);
}
}
}
}
var _49e=_48c.getAttribute(this.templateProperty);
if(_49e){
_485[_49e]=_48c;
}
dojo.lang.forEach(dojo.widget.waiNames,function(name){
var wai=dojo.widget.wai[name];
var val=_48c.getAttribute(wai.name);
if(val){
if(val.indexOf("-")==-1){
dojo.widget.wai.setAttr(_48c,wai.name,"role",val);
}else{
var _4a2=val.split("-");
dojo.widget.wai.setAttr(_48c,wai.name,_4a2[0],_4a2[1]);
}
}
},this);
var _4a3=_48c.getAttribute(this.onBuildProperty);
if(_4a3){
eval("var node = baseNode; var widget = targetObj; "+_4a3);
}
}
};
dojo.widget.getDojoEventsFromStr=function(str){
var re=/(dojoOn([a-z]+)(\s?))=/gi;
var evts=str?str.match(re)||[]:[];
var ret=[];
var lem={};
for(var x=0;x<evts.length;x++){
if(evts[x].length<1){
continue;
}
var cm=evts[x].replace(/\s/,"");
cm=(cm.slice(0,cm.length-1));
if(!lem[cm]){
lem[cm]=true;
ret.push(cm);
}
}
return ret;
};
dojo.declare("dojo.widget.DomWidget",dojo.widget.Widget,function(){
if((arguments.length>0)&&(typeof arguments[0]=="object")){
this.create(arguments[0]);
}
},{templateNode:null,templateString:null,templateCssString:null,preventClobber:false,domNode:null,containerNode:null,widgetsInTemplate:false,addChild:function(_4ab,_4ac,pos,ref,_4af){
if(!this.isContainer){
dojo.debug("dojo.widget.DomWidget.addChild() attempted on non-container widget");
return null;
}else{
if(_4af==undefined){
_4af=this.children.length;
}
this.addWidgetAsDirectChild(_4ab,_4ac,pos,ref,_4af);
this.registerChild(_4ab,_4af);
}
return _4ab;
},addWidgetAsDirectChild:function(_4b0,_4b1,pos,ref,_4b4){
if((!this.containerNode)&&(!_4b1)){
this.containerNode=this.domNode;
}
var cn=(_4b1)?_4b1:this.containerNode;
if(!pos){
pos="after";
}
if(!ref){
if(!cn){
cn=dojo.body();
}
ref=cn.lastChild;
}
if(!_4b4){
_4b4=0;
}
_4b0.domNode.setAttribute("dojoinsertionindex",_4b4);
if(!ref){
cn.appendChild(_4b0.domNode);
}else{
if(pos=="insertAtIndex"){
dojo.dom.insertAtIndex(_4b0.domNode,ref.parentNode,_4b4);
}else{
if((pos=="after")&&(ref===cn.lastChild)){
cn.appendChild(_4b0.domNode);
}else{
dojo.dom.insertAtPosition(_4b0.domNode,cn,pos);
}
}
}
},registerChild:function(_4b6,_4b7){
_4b6.dojoInsertionIndex=_4b7;
var idx=-1;
for(var i=0;i<this.children.length;i++){
if(this.children[i].dojoInsertionIndex<=_4b7){
idx=i;
}
}
this.children.splice(idx+1,0,_4b6);
_4b6.parent=this;
_4b6.addedTo(this,idx+1);
delete dojo.widget.manager.topWidgets[_4b6.widgetId];
},removeChild:function(_4ba){
dojo.dom.removeNode(_4ba.domNode);
return dojo.widget.DomWidget.superclass.removeChild.call(this,_4ba);
},getFragNodeRef:function(frag){
if(!frag){
return null;
}
if(!frag[this.getNamespacedType()]){
dojo.raise("Error: no frag for widget type "+this.getNamespacedType()+", id "+this.widgetId+" (maybe a widget has set it's type incorrectly)");
}
return frag[this.getNamespacedType()]["nodeRef"];
},postInitialize:function(args,frag,_4be){
var _4bf=this.getFragNodeRef(frag);
if(_4be&&(_4be.snarfChildDomOutput||!_4bf)){
_4be.addWidgetAsDirectChild(this,"","insertAtIndex","",args["dojoinsertionindex"],_4bf);
}else{
if(_4bf){
if(this.domNode&&(this.domNode!==_4bf)){
this._sourceNodeRef=dojo.dom.replaceNode(_4bf,this.domNode);
}
}
}
if(_4be){
_4be.registerChild(this,args.dojoinsertionindex);
}else{
dojo.widget.manager.topWidgets[this.widgetId]=this;
}
if(this.widgetsInTemplate){
var _4c0=new dojo.xml.Parse();
var _4c1;
var _4c2=this.domNode.getElementsByTagName("*");
for(var i=0;i<_4c2.length;i++){
if(_4c2[i].getAttribute("dojoAttachPoint")=="subContainerWidget"){
_4c1=_4c2[i];
}
if(_4c2[i].getAttribute("dojoType")){
_4c2[i].setAttribute("isSubWidget",true);
}
}
if(this.isContainer&&!this.containerNode){
if(_4c1){
var src=this.getFragNodeRef(frag);
if(src){
dojo.dom.moveChildren(src,_4c1);
frag["dojoDontFollow"]=true;
}
}else{
dojo.debug("No subContainerWidget node can be found in template file for widget "+this);
}
}
var _4c5=_4c0.parseElement(this.domNode,null,true);
dojo.widget.getParser().createSubComponents(_4c5,this);
var _4c6=[];
var _4c7=[this];
var w;
while((w=_4c7.pop())){
for(var i=0;i<w.children.length;i++){
var _4c9=w.children[i];
if(_4c9._processedSubWidgets||!_4c9.extraArgs["issubwidget"]){
continue;
}
_4c6.push(_4c9);
if(_4c9.isContainer){
_4c7.push(_4c9);
}
}
}
for(var i=0;i<_4c6.length;i++){
var _4ca=_4c6[i];
if(_4ca._processedSubWidgets){
dojo.debug("This should not happen: widget._processedSubWidgets is already true!");
return;
}
_4ca._processedSubWidgets=true;
if(_4ca.extraArgs["dojoattachevent"]){
var evts=_4ca.extraArgs["dojoattachevent"].split(";");
for(var j=0;j<evts.length;j++){
var _4cd=null;
var tevt=dojo.string.trim(evts[j]);
if(tevt.indexOf(":")>=0){
var _4cf=tevt.split(":");
tevt=dojo.string.trim(_4cf[0]);
_4cd=dojo.string.trim(_4cf[1]);
}
if(!_4cd){
_4cd=tevt;
}
if(dojo.lang.isFunction(_4ca[tevt])){
dojo.event.kwConnect({srcObj:_4ca,srcFunc:tevt,targetObj:this,targetFunc:_4cd});
}else{
alert(tevt+" is not a function in widget "+_4ca);
}
}
}
if(_4ca.extraArgs["dojoattachpoint"]){
this[_4ca.extraArgs["dojoattachpoint"]]=_4ca;
}
}
}
if(this.isContainer&&!frag["dojoDontFollow"]){
dojo.widget.getParser().createSubComponents(frag,this);
}
},buildRendering:function(args,frag){
var ts=dojo.widget._templateCache[this.widgetType];
if(args["templatecsspath"]){
args["templateCssPath"]=args["templatecsspath"];
}
var _4d3=args["templateCssPath"]||this.templateCssPath;
if(_4d3&&!dojo.widget._cssFiles[_4d3.toString()]){
if((!this.templateCssString)&&(_4d3)){
this.templateCssString=dojo.hostenv.getText(_4d3);
this.templateCssPath=null;
}
dojo.widget._cssFiles[_4d3.toString()]=true;
}
if((this["templateCssString"])&&(!dojo.widget._cssStrings[this.templateCssString])){
dojo.html.insertCssText(this.templateCssString,null,_4d3);
dojo.widget._cssStrings[this.templateCssString]=true;
}
if((!this.preventClobber)&&((this.templatePath)||(this.templateNode)||((this["templateString"])&&(this.templateString.length))||((typeof ts!="undefined")&&((ts["string"])||(ts["node"]))))){
this.buildFromTemplate(args,frag);
}else{
this.domNode=this.getFragNodeRef(frag);
}
this.fillInTemplate(args,frag);
},buildFromTemplate:function(args,frag){
var _4d6=false;
if(args["templatepath"]){
args["templatePath"]=args["templatepath"];
}
dojo.widget.fillFromTemplateCache(this,args["templatePath"],null,_4d6);
var ts=dojo.widget._templateCache[this.templatePath?this.templatePath.toString():this.widgetType];
if((ts)&&(!_4d6)){
if(!this.templateString.length){
this.templateString=ts["string"];
}
if(!this.templateNode){
this.templateNode=ts["node"];
}
}
var _4d8=false;
var node=null;
var tstr=this.templateString;
if((!this.templateNode)&&(this.templateString)){
_4d8=this.templateString.match(/\$\{([^\}]+)\}/g);
if(_4d8){
var hash=this.strings||{};
for(var key in dojo.widget.defaultStrings){
if(dojo.lang.isUndefined(hash[key])){
hash[key]=dojo.widget.defaultStrings[key];
}
}
for(var i=0;i<_4d8.length;i++){
var key=_4d8[i];
key=key.substring(2,key.length-1);
var kval=(key.substring(0,5)=="this.")?dojo.lang.getObjPathValue(key.substring(5),this):hash[key];
var _4df;
if((kval)||(dojo.lang.isString(kval))){
_4df=new String((dojo.lang.isFunction(kval))?kval.call(this,key,this.templateString):kval);
while(_4df.indexOf("\"")>-1){
_4df=_4df.replace("\"","&quot;");
}
tstr=tstr.replace(_4d8[i],_4df);
}
}
}else{
this.templateNode=this.createNodesFromText(this.templateString,true)[0];
if(!_4d6){
ts.node=this.templateNode;
}
}
}
if((!this.templateNode)&&(!_4d8)){
dojo.debug("DomWidget.buildFromTemplate: could not create template");
return false;
}else{
if(!_4d8){
node=this.templateNode.cloneNode(true);
if(!node){
return false;
}
}else{
node=this.createNodesFromText(tstr,true)[0];
}
}
this.domNode=node;
this.attachTemplateNodes();
if(this.isContainer&&this.containerNode){
var src=this.getFragNodeRef(frag);
if(src){
dojo.dom.moveChildren(src,this.containerNode);
}
}
},attachTemplateNodes:function(_4e1,_4e2){
if(!_4e1){
_4e1=this.domNode;
}
if(!_4e2){
_4e2=this;
}
return dojo.widget.attachTemplateNodes(_4e1,_4e2,dojo.widget.getDojoEventsFromStr(this.templateString));
},fillInTemplate:function(){
},destroyRendering:function(){
try{
dojo.dom.destroyNode(this.domNode);
delete this.domNode;
}
catch(e){
}
if(this._sourceNodeRef){
try{
dojo.dom.destroyNode(this._sourceNodeRef);
}
catch(e){
}
}
},createNodesFromText:function(){
dojo.unimplemented("dojo.widget.DomWidget.createNodesFromText");
}});
dojo.provide("dojo.html.display");
dojo.html._toggle=function(node,_4e4,_4e5){
node=dojo.byId(node);
_4e5(node,!_4e4(node));
return _4e4(node);
};
dojo.html.show=function(node){
node=dojo.byId(node);
if(dojo.html.getStyleProperty(node,"display")=="none"){
var _4e7=dojo.html.getAttribute("djDisplayCache");
dojo.html.setStyle(node,"display",(_4e7||""));
node.removeAttribute("djDisplayCache");
}
};
dojo.html.hide=function(node){
node=dojo.byId(node);
var _4e9=dojo.html.getAttribute("djDisplayCache");
if(_4e9==null){
var d=dojo.html.getStyleProperty(node,"display");
if(d!="none"){
node.setAttribute("djDisplayCache",d);
}
}
dojo.html.setStyle(node,"display","none");
};
dojo.html.setShowing=function(node,_4ec){
dojo.html[(_4ec?"show":"hide")](node);
};
dojo.html.isShowing=function(node){
return (dojo.html.getStyleProperty(node,"display")!="none");
};
dojo.html.toggleShowing=function(node){
return dojo.html._toggle(node,dojo.html.isShowing,dojo.html.setShowing);
};
dojo.html.displayMap={tr:"",td:"",th:"",img:"inline",span:"inline",input:"inline",button:"inline"};
dojo.html.suggestDisplayByTagName=function(node){
node=dojo.byId(node);
if(node&&node.tagName){
var tag=node.tagName.toLowerCase();
return (tag in dojo.html.displayMap?dojo.html.displayMap[tag]:"block");
}
};
dojo.html.setDisplay=function(node,_4f2){
dojo.html.setStyle(node,"display",((_4f2 instanceof String||typeof _4f2=="string")?_4f2:(_4f2?dojo.html.suggestDisplayByTagName(node):"none")));
};
dojo.html.isDisplayed=function(node){
return (dojo.html.getComputedStyle(node,"display")!="none");
};
dojo.html.toggleDisplay=function(node){
return dojo.html._toggle(node,dojo.html.isDisplayed,dojo.html.setDisplay);
};
dojo.html.setVisibility=function(node,_4f6){
dojo.html.setStyle(node,"visibility",((_4f6 instanceof String||typeof _4f6=="string")?_4f6:(_4f6?"visible":"hidden")));
};
dojo.html.isVisible=function(node){
return (dojo.html.getComputedStyle(node,"visibility")!="hidden");
};
dojo.html.toggleVisibility=function(node){
return dojo.html._toggle(node,dojo.html.isVisible,dojo.html.setVisibility);
};
dojo.html.setOpacity=function(node,_4fa,_4fb){
node=dojo.byId(node);
var h=dojo.render.html;
if(!_4fb){
if(_4fa>=1){
if(h.ie){
dojo.html.clearOpacity(node);
return;
}else{
_4fa=0.999999;
}
}else{
if(_4fa<0){
_4fa=0;
}
}
}
if(h.ie){
if(node.nodeName.toLowerCase()=="tr"){
var tds=node.getElementsByTagName("td");
for(var x=0;x<tds.length;x++){
tds[x].style.filter="Alpha(Opacity="+_4fa*100+")";
}
}
node.style.filter="Alpha(Opacity="+_4fa*100+")";
}else{
if(h.moz){
node.style.opacity=_4fa;
node.style.MozOpacity=_4fa;
}else{
if(h.safari){
node.style.opacity=_4fa;
node.style.KhtmlOpacity=_4fa;
}else{
node.style.opacity=_4fa;
}
}
}
};
dojo.html.clearOpacity=function(node){
node=dojo.byId(node);
var ns=node.style;
var h=dojo.render.html;
if(h.ie){
try{
if(node.filters&&node.filters.alpha){
ns.filter="";
}
}
catch(e){
}
}else{
if(h.moz){
ns.opacity=1;
ns.MozOpacity=1;
}else{
if(h.safari){
ns.opacity=1;
ns.KhtmlOpacity=1;
}else{
ns.opacity=1;
}
}
}
};
dojo.html.getOpacity=function(node){
node=dojo.byId(node);
var h=dojo.render.html;
if(h.ie){
var opac=(node.filters&&node.filters.alpha&&typeof node.filters.alpha.opacity=="number"?node.filters.alpha.opacity:100)/100;
}else{
var opac=node.style.opacity||node.style.MozOpacity||node.style.KhtmlOpacity||1;
}
return opac>=0.999999?1:Number(opac);
};
dojo.provide("dojo.html.layout");
dojo.html.sumAncestorProperties=function(node,prop){
node=dojo.byId(node);
if(!node){
return 0;
}
var _507=0;
while(node){
if(dojo.html.getComputedStyle(node,"position")=="fixed"){
return 0;
}
var val=node[prop];
if(val){
_507+=val-0;
if(node==dojo.body()){
break;
}
}
node=node.parentNode;
}
return _507;
};
dojo.html.setStyleAttributes=function(node,_50a){
node=dojo.byId(node);
var _50b=_50a.replace(/(;)?\s*$/,"").split(";");
for(var i=0;i<_50b.length;i++){
var _50d=_50b[i].split(":");
var name=_50d[0].replace(/\s*$/,"").replace(/^\s*/,"").toLowerCase();
var _50f=_50d[1].replace(/\s*$/,"").replace(/^\s*/,"");
switch(name){
case "opacity":
dojo.html.setOpacity(node,_50f);
break;
case "content-height":
dojo.html.setContentBox(node,{height:_50f});
break;
case "content-width":
dojo.html.setContentBox(node,{width:_50f});
break;
case "outer-height":
dojo.html.setMarginBox(node,{height:_50f});
break;
case "outer-width":
dojo.html.setMarginBox(node,{width:_50f});
break;
default:
node.style[dojo.html.toCamelCase(name)]=_50f;
}
}
};
dojo.html.boxSizing={MARGIN_BOX:"margin-box",BORDER_BOX:"border-box",PADDING_BOX:"padding-box",CONTENT_BOX:"content-box"};
dojo.html.getAbsolutePosition=dojo.html.abs=function(node,_511,_512){
node=dojo.byId(node);
var _513=dojo.doc();
var ret={x:0,y:0};
var bs=dojo.html.boxSizing;
if(!_512){
_512=bs.CONTENT_BOX;
}
var _516=2;
var _517;
switch(_512){
case bs.MARGIN_BOX:
_517=3;
break;
case bs.BORDER_BOX:
_517=2;
break;
case bs.PADDING_BOX:
default:
_517=1;
break;
case bs.CONTENT_BOX:
_517=0;
break;
}
var h=dojo.render.html;
var db=_513["body"]||_513["documentElement"];
if(h.ie){
with(node.getBoundingClientRect()){
ret.x=left-2;
ret.y=top-2;
}
}else{
if(_513["getBoxObjectFor"]){
_516=1;
try{
var bo=_513.getBoxObjectFor(node);
ret.x=bo.x-dojo.html.sumAncestorProperties(node,"scrollLeft");
ret.y=bo.y-dojo.html.sumAncestorProperties(node,"scrollTop");
}
catch(e){
}
}else{
if(node["offsetParent"]){
var _51b;
if((h.safari)&&(node.style.getPropertyValue("position")=="absolute")&&(node.parentNode==db)){
_51b=db;
}else{
_51b=db.parentNode;
}
if(node.parentNode!=db){
var nd=node;
if(dojo.render.html.opera){
nd=db;
}
ret.x-=dojo.html.sumAncestorProperties(nd,"scrollLeft");
ret.y-=dojo.html.sumAncestorProperties(nd,"scrollTop");
}
var _51d=node;
do{
var n=_51d["offsetLeft"];
if(!h.opera||n>0){
ret.x+=isNaN(n)?0:n;
}
var m=_51d["offsetTop"];
ret.y+=isNaN(m)?0:m;
_51d=_51d.offsetParent;
}while((_51d!=_51b)&&(_51d!=null));
}else{
if(node["x"]&&node["y"]){
ret.x+=isNaN(node.x)?0:node.x;
ret.y+=isNaN(node.y)?0:node.y;
}
}
}
}
if(_511){
var _520=dojo.html.getScroll();
ret.y+=_520.top;
ret.x+=_520.left;
}
var _521=[dojo.html.getPaddingExtent,dojo.html.getBorderExtent,dojo.html.getMarginExtent];
if(_516>_517){
for(var i=_517;i<_516;++i){
ret.y+=_521[i](node,"top");
ret.x+=_521[i](node,"left");
}
}else{
if(_516<_517){
for(var i=_517;i>_516;--i){
ret.y-=_521[i-1](node,"top");
ret.x-=_521[i-1](node,"left");
}
}
}
ret.top=ret.y;
ret.left=ret.x;
return ret;
};
dojo.html.isPositionAbsolute=function(node){
return (dojo.html.getComputedStyle(node,"position")=="absolute");
};
dojo.html._getComponentPixelValues=function(node,_525,_526,_527){
var _528=["top","bottom","left","right"];
var obj={};
for(var i in _528){
side=_528[i];
obj[side]=_526(node,_525+side,_527);
}
obj.width=obj.left+obj.right;
obj.height=obj.top+obj.bottom;
return obj;
};
dojo.html.getMargin=function(node){
return dojo.html._getComponentPixelValues(node,"margin-",dojo.html.getPixelValue,dojo.html.isPositionAbsolute(node));
};
dojo.html.getBorder=function(node){
return dojo.html._getComponentPixelValues(node,"",dojo.html.getBorderExtent);
};
dojo.html.getBorderExtent=function(node,side){
return (dojo.html.getStyle(node,"border-"+side+"-style")=="none"?0:dojo.html.getPixelValue(node,"border-"+side+"-width"));
};
dojo.html.getMarginExtent=function(node,side){
return dojo.html.getPixelValue(node,"margin-"+side,dojo.html.isPositionAbsolute(node));
};
dojo.html.getPaddingExtent=function(node,side){
return dojo.html.getPixelValue(node,"padding-"+side,true);
};
dojo.html.getPadding=function(node){
return dojo.html._getComponentPixelValues(node,"padding-",dojo.html.getPixelValue,true);
};
dojo.html.getPadBorder=function(node){
var pad=dojo.html.getPadding(node);
var _536=dojo.html.getBorder(node);
return {width:pad.width+_536.width,height:pad.height+_536.height};
};
dojo.html.getBoxSizing=function(node){
var h=dojo.render.html;
var bs=dojo.html.boxSizing;
if(((h.ie)||(h.opera))&&node.nodeName!="IMG"){
var cm=document["compatMode"];
if((cm=="BackCompat")||(cm=="QuirksMode")){
return bs.BORDER_BOX;
}else{
return bs.CONTENT_BOX;
}
}else{
if(arguments.length==0){
node=document.documentElement;
}
var _53b=dojo.html.getStyle(node,"-moz-box-sizing");
if(!_53b){
_53b=dojo.html.getStyle(node,"box-sizing");
}
return (_53b?_53b:bs.CONTENT_BOX);
}
};
dojo.html.isBorderBox=function(node){
return (dojo.html.getBoxSizing(node)==dojo.html.boxSizing.BORDER_BOX);
};
dojo.html.getBorderBox=function(node){
node=dojo.byId(node);
return {width:node.offsetWidth,height:node.offsetHeight};
};
dojo.html.getPaddingBox=function(node){
var box=dojo.html.getBorderBox(node);
var _540=dojo.html.getBorder(node);
return {width:box.width-_540.width,height:box.height-_540.height};
};
dojo.html.getContentBox=function(node){
node=dojo.byId(node);
var _542=dojo.html.getPadBorder(node);
return {width:node.offsetWidth-_542.width,height:node.offsetHeight-_542.height};
};
dojo.html.setContentBox=function(node,args){
node=dojo.byId(node);
var _545=0;
var _546=0;
var isbb=dojo.html.isBorderBox(node);
var _548=(isbb?dojo.html.getPadBorder(node):{width:0,height:0});
var ret={};
if(typeof args.width!="undefined"){
_545=args.width+_548.width;
ret.width=dojo.html.setPositivePixelValue(node,"width",_545);
}
if(typeof args.height!="undefined"){
_546=args.height+_548.height;
ret.height=dojo.html.setPositivePixelValue(node,"height",_546);
}
return ret;
};
dojo.html.getMarginBox=function(node){
var _54b=dojo.html.getBorderBox(node);
var _54c=dojo.html.getMargin(node);
return {width:_54b.width+_54c.width,height:_54b.height+_54c.height};
};
dojo.html.setMarginBox=function(node,args){
node=dojo.byId(node);
var _54f=0;
var _550=0;
var isbb=dojo.html.isBorderBox(node);
var _552=(!isbb?dojo.html.getPadBorder(node):{width:0,height:0});
var _553=dojo.html.getMargin(node);
var ret={};
if(typeof args.width!="undefined"){
_54f=args.width-_552.width;
_54f-=_553.width;
ret.width=dojo.html.setPositivePixelValue(node,"width",_54f);
}
if(typeof args.height!="undefined"){
_550=args.height-_552.height;
_550-=_553.height;
ret.height=dojo.html.setPositivePixelValue(node,"height",_550);
}
return ret;
};
dojo.html.getElementBox=function(node,type){
var bs=dojo.html.boxSizing;
switch(type){
case bs.MARGIN_BOX:
return dojo.html.getMarginBox(node);
case bs.BORDER_BOX:
return dojo.html.getBorderBox(node);
case bs.PADDING_BOX:
return dojo.html.getPaddingBox(node);
case bs.CONTENT_BOX:
default:
return dojo.html.getContentBox(node);
}
};
dojo.html.toCoordinateObject=dojo.html.toCoordinateArray=function(_558,_559,_55a){
if(!_558.nodeType&&!(_558 instanceof String||typeof _558=="string")&&("width" in _558||"height" in _558||"left" in _558||"x" in _558||"top" in _558||"y" in _558)){
var ret={left:_558.left||_558.x||0,top:_558.top||_558.y||0,width:_558.width||0,height:_558.height||0};
}else{
var node=dojo.byId(_558);
var pos=dojo.html.abs(node,_559,_55a);
var _55e=dojo.html.getMarginBox(node);
var ret={left:pos.left,top:pos.top,width:_55e.width,height:_55e.height};
}
ret.x=ret.left;
ret.y=ret.top;
return ret;
};
dojo.html.setMarginBoxWidth=dojo.html.setOuterWidth=function(node,_560){
return dojo.html._callDeprecated("setMarginBoxWidth","setMarginBox",arguments,"width");
};
dojo.html.setMarginBoxHeight=dojo.html.setOuterHeight=function(){
return dojo.html._callDeprecated("setMarginBoxHeight","setMarginBox",arguments,"height");
};
dojo.html.getMarginBoxWidth=dojo.html.getOuterWidth=function(){
return dojo.html._callDeprecated("getMarginBoxWidth","getMarginBox",arguments,null,"width");
};
dojo.html.getMarginBoxHeight=dojo.html.getOuterHeight=function(){
return dojo.html._callDeprecated("getMarginBoxHeight","getMarginBox",arguments,null,"height");
};
dojo.html.getTotalOffset=function(node,type,_563){
return dojo.html._callDeprecated("getTotalOffset","getAbsolutePosition",arguments,null,type);
};
dojo.html.getAbsoluteX=function(node,_565){
return dojo.html._callDeprecated("getAbsoluteX","getAbsolutePosition",arguments,null,"x");
};
dojo.html.getAbsoluteY=function(node,_567){
return dojo.html._callDeprecated("getAbsoluteY","getAbsolutePosition",arguments,null,"y");
};
dojo.html.totalOffsetLeft=function(node,_569){
return dojo.html._callDeprecated("totalOffsetLeft","getAbsolutePosition",arguments,null,"left");
};
dojo.html.totalOffsetTop=function(node,_56b){
return dojo.html._callDeprecated("totalOffsetTop","getAbsolutePosition",arguments,null,"top");
};
dojo.html.getMarginWidth=function(node){
return dojo.html._callDeprecated("getMarginWidth","getMargin",arguments,null,"width");
};
dojo.html.getMarginHeight=function(node){
return dojo.html._callDeprecated("getMarginHeight","getMargin",arguments,null,"height");
};
dojo.html.getBorderWidth=function(node){
return dojo.html._callDeprecated("getBorderWidth","getBorder",arguments,null,"width");
};
dojo.html.getBorderHeight=function(node){
return dojo.html._callDeprecated("getBorderHeight","getBorder",arguments,null,"height");
};
dojo.html.getPaddingWidth=function(node){
return dojo.html._callDeprecated("getPaddingWidth","getPadding",arguments,null,"width");
};
dojo.html.getPaddingHeight=function(node){
return dojo.html._callDeprecated("getPaddingHeight","getPadding",arguments,null,"height");
};
dojo.html.getPadBorderWidth=function(node){
return dojo.html._callDeprecated("getPadBorderWidth","getPadBorder",arguments,null,"width");
};
dojo.html.getPadBorderHeight=function(node){
return dojo.html._callDeprecated("getPadBorderHeight","getPadBorder",arguments,null,"height");
};
dojo.html.getBorderBoxWidth=dojo.html.getInnerWidth=function(){
return dojo.html._callDeprecated("getBorderBoxWidth","getBorderBox",arguments,null,"width");
};
dojo.html.getBorderBoxHeight=dojo.html.getInnerHeight=function(){
return dojo.html._callDeprecated("getBorderBoxHeight","getBorderBox",arguments,null,"height");
};
dojo.html.getContentBoxWidth=dojo.html.getContentWidth=function(){
return dojo.html._callDeprecated("getContentBoxWidth","getContentBox",arguments,null,"width");
};
dojo.html.getContentBoxHeight=dojo.html.getContentHeight=function(){
return dojo.html._callDeprecated("getContentBoxHeight","getContentBox",arguments,null,"height");
};
dojo.html.setContentBoxWidth=dojo.html.setContentWidth=function(node,_575){
return dojo.html._callDeprecated("setContentBoxWidth","setContentBox",arguments,"width");
};
dojo.html.setContentBoxHeight=dojo.html.setContentHeight=function(node,_577){
return dojo.html._callDeprecated("setContentBoxHeight","setContentBox",arguments,"height");
};
dojo.provide("dojo.html.util");
dojo.html.getElementWindow=function(_578){
return dojo.html.getDocumentWindow(_578.ownerDocument);
};
dojo.html.getDocumentWindow=function(doc){
if(dojo.render.html.safari&&!doc._parentWindow){
var fix=function(win){
win.document._parentWindow=win;
for(var i=0;i<win.frames.length;i++){
fix(win.frames[i]);
}
};
fix(window.top);
}
if(dojo.render.html.ie&&window!==document.parentWindow&&!doc._parentWindow){
doc.parentWindow.execScript("document._parentWindow = window;","Javascript");
var win=doc._parentWindow;
doc._parentWindow=null;
return win;
}
return doc._parentWindow||doc.parentWindow||doc.defaultView;
};
dojo.html.getAbsolutePositionExt=function(node,_57f,_580,_581){
var _582=dojo.html.getElementWindow(node);
var ret=dojo.withGlobal(_582,"getAbsolutePosition",dojo.html,arguments);
var win=dojo.html.getElementWindow(node);
if(_581!=win&&win.frameElement){
var ext=dojo.html.getAbsolutePositionExt(win.frameElement,_57f,_580,_581);
ret.x+=ext.x;
ret.y+=ext.y;
}
ret.top=ret.y;
ret.left=ret.x;
return ret;
};
dojo.html.gravity=function(node,e){
node=dojo.byId(node);
var _588=dojo.html.getCursorPosition(e);
with(dojo.html){
var _589=getAbsolutePosition(node,true);
var bb=getBorderBox(node);
var _58b=_589.x+(bb.width/2);
var _58c=_589.y+(bb.height/2);
}
with(dojo.html.gravity){
return ((_588.x<_58b?WEST:EAST)|(_588.y<_58c?NORTH:SOUTH));
}
};
dojo.html.gravity.NORTH=1;
dojo.html.gravity.SOUTH=1<<1;
dojo.html.gravity.EAST=1<<2;
dojo.html.gravity.WEST=1<<3;
dojo.html.overElement=function(_58d,e){
_58d=dojo.byId(_58d);
var _58f=dojo.html.getCursorPosition(e);
var bb=dojo.html.getBorderBox(_58d);
var _591=dojo.html.getAbsolutePosition(_58d,true,dojo.html.boxSizing.BORDER_BOX);
var top=_591.y;
var _593=top+bb.height;
var left=_591.x;
var _595=left+bb.width;
return (_58f.x>=left&&_58f.x<=_595&&_58f.y>=top&&_58f.y<=_593);
};
dojo.html.renderedTextContent=function(node){
node=dojo.byId(node);
var _597="";
if(node==null){
return _597;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
var _599="unknown";
try{
_599=dojo.html.getStyle(node.childNodes[i],"display");
}
catch(E){
}
switch(_599){
case "block":
case "list-item":
case "run-in":
case "table":
case "table-row-group":
case "table-header-group":
case "table-footer-group":
case "table-row":
case "table-column-group":
case "table-column":
case "table-cell":
case "table-caption":
_597+="\n";
_597+=dojo.html.renderedTextContent(node.childNodes[i]);
_597+="\n";
break;
case "none":
break;
default:
if(node.childNodes[i].tagName&&node.childNodes[i].tagName.toLowerCase()=="br"){
_597+="\n";
}else{
_597+=dojo.html.renderedTextContent(node.childNodes[i]);
}
break;
}
break;
case 3:
case 2:
case 4:
var text=node.childNodes[i].nodeValue;
var _59b="unknown";
try{
_59b=dojo.html.getStyle(node,"text-transform");
}
catch(E){
}
switch(_59b){
case "capitalize":
var _59c=text.split(" ");
for(var i=0;i<_59c.length;i++){
_59c[i]=_59c[i].charAt(0).toUpperCase()+_59c[i].substring(1);
}
text=_59c.join(" ");
break;
case "uppercase":
text=text.toUpperCase();
break;
case "lowercase":
text=text.toLowerCase();
break;
default:
break;
}
switch(_59b){
case "nowrap":
break;
case "pre-wrap":
break;
case "pre-line":
break;
case "pre":
break;
default:
text=text.replace(/\s+/," ");
if(/\s$/.test(_597)){
text.replace(/^\s/,"");
}
break;
}
_597+=text;
break;
default:
break;
}
}
return _597;
};
dojo.html.createNodesFromText=function(txt,trim){
if(trim){
txt=txt.replace(/^\s+|\s+$/g,"");
}
var tn=dojo.doc().createElement("div");
tn.style.visibility="hidden";
dojo.body().appendChild(tn);
var _5a0="none";
if((/^<t[dh][\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table><tbody><tr>"+txt+"</tr></tbody></table>";
_5a0="cell";
}else{
if((/^<tr[\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table><tbody>"+txt+"</tbody></table>";
_5a0="row";
}else{
if((/^<(thead|tbody|tfoot)[\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table>"+txt+"</table>";
_5a0="section";
}
}
}
tn.innerHTML=txt;
if(tn["normalize"]){
tn.normalize();
}
var _5a1=null;
switch(_5a0){
case "cell":
_5a1=tn.getElementsByTagName("tr")[0];
break;
case "row":
_5a1=tn.getElementsByTagName("tbody")[0];
break;
case "section":
_5a1=tn.getElementsByTagName("table")[0];
break;
default:
_5a1=tn;
break;
}
var _5a2=[];
for(var x=0;x<_5a1.childNodes.length;x++){
_5a2.push(_5a1.childNodes[x].cloneNode(true));
}
tn.style.display="none";
dojo.html.destroyNode(tn);
return _5a2;
};
dojo.html.placeOnScreen=function(node,_5a5,_5a6,_5a7,_5a8,_5a9,_5aa){
if(_5a5 instanceof Array||typeof _5a5=="array"){
_5aa=_5a9;
_5a9=_5a8;
_5a8=_5a7;
_5a7=_5a6;
_5a6=_5a5[1];
_5a5=_5a5[0];
}
if(_5a9 instanceof String||typeof _5a9=="string"){
_5a9=_5a9.split(",");
}
if(!isNaN(_5a7)){
_5a7=[Number(_5a7),Number(_5a7)];
}else{
if(!(_5a7 instanceof Array||typeof _5a7=="array")){
_5a7=[0,0];
}
}
var _5ab=dojo.html.getScroll().offset;
var view=dojo.html.getViewport();
node=dojo.byId(node);
var _5ad=node.style.display;
node.style.display="";
var bb=dojo.html.getBorderBox(node);
var w=bb.width;
var h=bb.height;
node.style.display=_5ad;
if(!(_5a9 instanceof Array||typeof _5a9=="array")){
_5a9=["TL"];
}
var _5b1,_5b2,_5b3=Infinity,_5b4;
for(var _5b5=0;_5b5<_5a9.length;++_5b5){
var _5b6=_5a9[_5b5];
var _5b7=true;
var tryX=_5a5-(_5b6.charAt(1)=="L"?0:w)+_5a7[0]*(_5b6.charAt(1)=="L"?1:-1);
var tryY=_5a6-(_5b6.charAt(0)=="T"?0:h)+_5a7[1]*(_5b6.charAt(0)=="T"?1:-1);
if(_5a8){
tryX-=_5ab.x;
tryY-=_5ab.y;
}
if(tryX<0){
tryX=0;
_5b7=false;
}
if(tryY<0){
tryY=0;
_5b7=false;
}
var x=tryX+w;
if(x>view.width){
x=view.width-w;
_5b7=false;
}else{
x=tryX;
}
x=Math.max(_5a7[0],x)+_5ab.x;
var y=tryY+h;
if(y>view.height){
y=view.height-h;
_5b7=false;
}else{
y=tryY;
}
y=Math.max(_5a7[1],y)+_5ab.y;
if(_5b7){
_5b1=x;
_5b2=y;
_5b3=0;
_5b4=_5b6;
break;
}else{
var dist=Math.pow(x-tryX-_5ab.x,2)+Math.pow(y-tryY-_5ab.y,2);
if(_5b3>dist){
_5b3=dist;
_5b1=x;
_5b2=y;
_5b4=_5b6;
}
}
}
if(!_5aa){
node.style.left=_5b1+"px";
node.style.top=_5b2+"px";
}
return {left:_5b1,top:_5b2,x:_5b1,y:_5b2,dist:_5b3,corner:_5b4};
};
dojo.html.placeOnScreenAroundElement=function(node,_5be,_5bf,_5c0,_5c1,_5c2){
var best,_5c4=Infinity;
_5be=dojo.byId(_5be);
var _5c5=_5be.style.display;
_5be.style.display="";
var mb=dojo.html.getElementBox(_5be,_5c0);
var _5c7=mb.width;
var _5c8=mb.height;
var _5c9=dojo.html.getAbsolutePosition(_5be,true,_5c0);
_5be.style.display=_5c5;
for(var _5ca in _5c1){
var pos,_5cc,_5cd;
var _5ce=_5c1[_5ca];
_5cc=_5c9.x+(_5ca.charAt(1)=="L"?0:_5c7);
_5cd=_5c9.y+(_5ca.charAt(0)=="T"?0:_5c8);
pos=dojo.html.placeOnScreen(node,_5cc,_5cd,_5bf,true,_5ce,true);
if(pos.dist==0){
best=pos;
break;
}else{
if(_5c4>pos.dist){
_5c4=pos.dist;
best=pos;
}
}
}
if(!_5c2){
node.style.left=best.left+"px";
node.style.top=best.top+"px";
}
return best;
};
dojo.html.scrollIntoView=function(node){
if(!node){
return;
}
if(dojo.render.html.ie){
if(dojo.html.getBorderBox(node.parentNode).height<=node.parentNode.scrollHeight){
node.scrollIntoView(false);
}
}else{
if(dojo.render.html.mozilla){
node.scrollIntoView(false);
}else{
var _5d0=node.parentNode;
var _5d1=_5d0.scrollTop+dojo.html.getBorderBox(_5d0).height;
var _5d2=node.offsetTop+dojo.html.getMarginBox(node).height;
if(_5d1<_5d2){
_5d0.scrollTop+=(_5d2-_5d1);
}else{
if(_5d0.scrollTop>node.offsetTop){
_5d0.scrollTop-=(_5d0.scrollTop-node.offsetTop);
}
}
}
}
};
dojo.provide("dojo.gfx.color");
dojo.gfx.color.Color=function(r,g,b,a){
if(dojo.lang.isArray(r)){
this.r=r[0];
this.g=r[1];
this.b=r[2];
this.a=r[3]||1;
}else{
if(dojo.lang.isString(r)){
var rgb=dojo.gfx.color.extractRGB(r);
this.r=rgb[0];
this.g=rgb[1];
this.b=rgb[2];
this.a=g||1;
}else{
if(r instanceof dojo.gfx.color.Color){
this.r=r.r;
this.b=r.b;
this.g=r.g;
this.a=r.a;
}else{
this.r=r;
this.g=g;
this.b=b;
this.a=a;
}
}
}
};
dojo.gfx.color.Color.fromArray=function(arr){
return new dojo.gfx.color.Color(arr[0],arr[1],arr[2],arr[3]);
};
dojo.extend(dojo.gfx.color.Color,{toRgb:function(_5d9){
if(_5d9){
return this.toRgba();
}else{
return [this.r,this.g,this.b];
}
},toRgba:function(){
return [this.r,this.g,this.b,this.a];
},toHex:function(){
return dojo.gfx.color.rgb2hex(this.toRgb());
},toCss:function(){
return "rgb("+this.toRgb().join()+")";
},toString:function(){
return this.toHex();
},blend:function(_5da,_5db){
var rgb=null;
if(dojo.lang.isArray(_5da)){
rgb=_5da;
}else{
if(_5da instanceof dojo.gfx.color.Color){
rgb=_5da.toRgb();
}else{
rgb=new dojo.gfx.color.Color(_5da).toRgb();
}
}
return dojo.gfx.color.blend(this.toRgb(),rgb,_5db);
}});
dojo.gfx.color.named={white:[255,255,255],black:[0,0,0],red:[255,0,0],green:[0,255,0],lime:[0,255,0],blue:[0,0,255],navy:[0,0,128],gray:[128,128,128],silver:[192,192,192]};
dojo.gfx.color.blend=function(a,b,_5df){
if(typeof a=="string"){
return dojo.gfx.color.blendHex(a,b,_5df);
}
if(!_5df){
_5df=0;
}
_5df=Math.min(Math.max(-1,_5df),1);
_5df=((_5df+1)/2);
var c=[];
for(var x=0;x<3;x++){
c[x]=parseInt(b[x]+((a[x]-b[x])*_5df));
}
return c;
};
dojo.gfx.color.blendHex=function(a,b,_5e4){
return dojo.gfx.color.rgb2hex(dojo.gfx.color.blend(dojo.gfx.color.hex2rgb(a),dojo.gfx.color.hex2rgb(b),_5e4));
};
dojo.gfx.color.extractRGB=function(_5e5){
_5e5=_5e5.toLowerCase();
if(_5e5.indexOf("rgb")==0){
var _5e6=_5e5.match(/rgba*\((\d+), *(\d+), *(\d+)/i);
var ret=_5e6.splice(1,3);
return ret;
}else{
var _5e8=dojo.gfx.color.hex2rgb(_5e5);
if(_5e8){
return _5e8;
}else{
return dojo.gfx.color.named[_5e5]||[255,255,255];
}
}
};
dojo.gfx.color.hex2rgb=function(hex){
var _5ea="0123456789ABCDEF";
var rgb=new Array(3);
if(hex.indexOf("#")==0){
hex=hex.substring(1);
}
hex=hex.toUpperCase();
if(hex.replace(new RegExp("["+_5ea+"]","g"),"")!=""){
return null;
}
if(hex.length==3){
rgb[0]=hex.charAt(0)+hex.charAt(0);
rgb[1]=hex.charAt(1)+hex.charAt(1);
rgb[2]=hex.charAt(2)+hex.charAt(2);
}else{
rgb[0]=hex.substring(0,2);
rgb[1]=hex.substring(2,4);
rgb[2]=hex.substring(4);
}
for(var i=0;i<rgb.length;i++){
rgb[i]=_5ea.indexOf(rgb[i].charAt(0))*16+_5ea.indexOf(rgb[i].charAt(1));
}
return rgb;
};
dojo.gfx.color.rgb2hex=function(r,g,b){
if(dojo.lang.isArray(r)){
g=r[1]||0;
b=r[2]||0;
r=r[0]||0;
}
var ret=dojo.lang.map([r,g,b],function(x){
x=new Number(x);
var s=x.toString(16);
while(s.length<2){
s="0"+s;
}
return s;
});
ret.unshift("#");
return ret.join("");
};
dojo.provide("dojo.lfx.Animation");
dojo.lfx.Line=function(_5f3,end){
this.start=_5f3;
this.end=end;
if(dojo.lang.isArray(_5f3)){
var diff=[];
dojo.lang.forEach(this.start,function(s,i){
diff[i]=this.end[i]-s;
},this);
this.getValue=function(n){
var res=[];
dojo.lang.forEach(this.start,function(s,i){
res[i]=(diff[i]*n)+s;
},this);
return res;
};
}else{
var diff=end-_5f3;
this.getValue=function(n){
return (diff*n)+this.start;
};
}
};
dojo.lfx.easeDefault=function(n){
if(dojo.render.html.khtml){
return (parseFloat("0.5")+((Math.sin((n+parseFloat("1.5"))*Math.PI))/2));
}else{
return (0.5+((Math.sin((n+1.5)*Math.PI))/2));
}
};
dojo.lfx.easeIn=function(n){
return Math.pow(n,3);
};
dojo.lfx.easeOut=function(n){
return (1-Math.pow(1-n,3));
};
dojo.lfx.easeInOut=function(n){
return ((3*Math.pow(n,2))-(2*Math.pow(n,3)));
};
dojo.lfx.IAnimation=function(){
};
dojo.lang.extend(dojo.lfx.IAnimation,{curve:null,duration:1000,easing:null,repeatCount:0,rate:25,handler:null,beforeBegin:null,onBegin:null,onAnimate:null,onEnd:null,onPlay:null,onPause:null,onStop:null,play:null,pause:null,stop:null,connect:function(evt,_602,_603){
if(!_603){
_603=_602;
_602=this;
}
_603=dojo.lang.hitch(_602,_603);
var _604=this[evt]||function(){
};
this[evt]=function(){
var ret=_604.apply(this,arguments);
_603.apply(this,arguments);
return ret;
};
return this;
},fire:function(evt,args){
if(this[evt]){
this[evt].apply(this,(args||[]));
}
return this;
},repeat:function(_608){
this.repeatCount=_608;
return this;
},_active:false,_paused:false});
dojo.lfx.Animation=function(_609,_60a,_60b,_60c,_60d,rate){
dojo.lfx.IAnimation.call(this);
if(dojo.lang.isNumber(_609)||(!_609&&_60a.getValue)){
rate=_60d;
_60d=_60c;
_60c=_60b;
_60b=_60a;
_60a=_609;
_609=null;
}else{
if(_609.getValue||dojo.lang.isArray(_609)){
rate=_60c;
_60d=_60b;
_60c=_60a;
_60b=_609;
_60a=null;
_609=null;
}
}
if(dojo.lang.isArray(_60b)){
this.curve=new dojo.lfx.Line(_60b[0],_60b[1]);
}else{
this.curve=_60b;
}
if(_60a!=null&&_60a>0){
this.duration=_60a;
}
if(_60d){
this.repeatCount=_60d;
}
if(rate){
this.rate=rate;
}
if(_609){
dojo.lang.forEach(["handler","beforeBegin","onBegin","onEnd","onPlay","onStop","onAnimate"],function(item){
if(_609[item]){
this.connect(item,_609[item]);
}
},this);
}
if(_60c&&dojo.lang.isFunction(_60c)){
this.easing=_60c;
}
};
dojo.inherits(dojo.lfx.Animation,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Animation,{_startTime:null,_endTime:null,_timer:null,_percent:0,_startRepeatCount:0,play:function(_610,_611){
if(_611){
clearTimeout(this._timer);
this._active=false;
this._paused=false;
this._percent=0;
}else{
if(this._active&&!this._paused){
return this;
}
}
this.fire("handler",["beforeBegin"]);
this.fire("beforeBegin");
if(_610>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_611);
}),_610);
return this;
}
this._startTime=new Date().valueOf();
if(this._paused){
this._startTime-=(this.duration*this._percent/100);
}
this._endTime=this._startTime+this.duration;
this._active=true;
this._paused=false;
var step=this._percent/100;
var _613=this.curve.getValue(step);
if(this._percent==0){
if(!this._startRepeatCount){
this._startRepeatCount=this.repeatCount;
}
this.fire("handler",["begin",_613]);
this.fire("onBegin",[_613]);
}
this.fire("handler",["play",_613]);
this.fire("onPlay",[_613]);
this._cycle();
return this;
},pause:function(){
clearTimeout(this._timer);
if(!this._active){
return this;
}
this._paused=true;
var _614=this.curve.getValue(this._percent/100);
this.fire("handler",["pause",_614]);
this.fire("onPause",[_614]);
return this;
},gotoPercent:function(pct,_616){
clearTimeout(this._timer);
this._active=true;
this._paused=true;
this._percent=pct;
if(_616){
this.play();
}
return this;
},stop:function(_617){
clearTimeout(this._timer);
var step=this._percent/100;
if(_617){
step=1;
}
var _619=this.curve.getValue(step);
this.fire("handler",["stop",_619]);
this.fire("onStop",[_619]);
this._active=false;
this._paused=false;
return this;
},status:function(){
if(this._active){
return this._paused?"paused":"playing";
}else{
return "stopped";
}
return this;
},_cycle:function(){
clearTimeout(this._timer);
if(this._active){
var curr=new Date().valueOf();
var step=(curr-this._startTime)/(this._endTime-this._startTime);
if(step>=1){
step=1;
this._percent=100;
}else{
this._percent=step*100;
}
if((this.easing)&&(dojo.lang.isFunction(this.easing))){
step=this.easing(step);
}
var _61c=this.curve.getValue(step);
this.fire("handler",["animate",_61c]);
this.fire("onAnimate",[_61c]);
if(step<1){
this._timer=setTimeout(dojo.lang.hitch(this,"_cycle"),this.rate);
}else{
this._active=false;
this.fire("handler",["end"]);
this.fire("onEnd");
if(this.repeatCount>0){
this.repeatCount--;
this.play(null,true);
}else{
if(this.repeatCount==-1){
this.play(null,true);
}else{
if(this._startRepeatCount){
this.repeatCount=this._startRepeatCount;
this._startRepeatCount=0;
}
}
}
}
}
return this;
}});
dojo.lfx.Combine=function(_61d){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._animsEnded=0;
var _61e=arguments;
if(_61e.length==1&&(dojo.lang.isArray(_61e[0])||dojo.lang.isArrayLike(_61e[0]))){
_61e=_61e[0];
}
dojo.lang.forEach(_61e,function(anim){
this._anims.push(anim);
anim.connect("onEnd",dojo.lang.hitch(this,"_onAnimsEnded"));
},this);
};
dojo.inherits(dojo.lfx.Combine,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Combine,{_animsEnded:0,play:function(_620,_621){
if(!this._anims.length){
return this;
}
this.fire("beforeBegin");
if(_620>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_621);
}),_620);
return this;
}
if(_621||this._anims[0].percent==0){
this.fire("onBegin");
}
this.fire("onPlay");
this._animsCall("play",null,_621);
return this;
},pause:function(){
this.fire("onPause");
this._animsCall("pause");
return this;
},stop:function(_622){
this.fire("onStop");
this._animsCall("stop",_622);
return this;
},_onAnimsEnded:function(){
this._animsEnded++;
if(this._animsEnded>=this._anims.length){
this.fire("onEnd");
}
return this;
},_animsCall:function(_623){
var args=[];
if(arguments.length>1){
for(var i=1;i<arguments.length;i++){
args.push(arguments[i]);
}
}
var _626=this;
dojo.lang.forEach(this._anims,function(anim){
anim[_623](args);
},_626);
return this;
}});
dojo.lfx.Chain=function(_628){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._currAnim=-1;
var _629=arguments;
if(_629.length==1&&(dojo.lang.isArray(_629[0])||dojo.lang.isArrayLike(_629[0]))){
_629=_629[0];
}
var _62a=this;
dojo.lang.forEach(_629,function(anim,i,_62d){
this._anims.push(anim);
if(i<_62d.length-1){
anim.connect("onEnd",dojo.lang.hitch(this,"_playNext"));
}else{
anim.connect("onEnd",dojo.lang.hitch(this,function(){
this.fire("onEnd");
}));
}
},this);
};
dojo.inherits(dojo.lfx.Chain,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Chain,{_currAnim:-1,play:function(_62e,_62f){
if(!this._anims.length){
return this;
}
if(_62f||!this._anims[this._currAnim]){
this._currAnim=0;
}
var _630=this._anims[this._currAnim];
this.fire("beforeBegin");
if(_62e>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_62f);
}),_62e);
return this;
}
if(_630){
if(this._currAnim==0){
this.fire("handler",["begin",this._currAnim]);
this.fire("onBegin",[this._currAnim]);
}
this.fire("onPlay",[this._currAnim]);
_630.play(null,_62f);
}
return this;
},pause:function(){
if(this._anims[this._currAnim]){
this._anims[this._currAnim].pause();
this.fire("onPause",[this._currAnim]);
}
return this;
},playPause:function(){
if(this._anims.length==0){
return this;
}
if(this._currAnim==-1){
this._currAnim=0;
}
var _631=this._anims[this._currAnim];
if(_631){
if(!_631._active||_631._paused){
this.play();
}else{
this.pause();
}
}
return this;
},stop:function(){
var _632=this._anims[this._currAnim];
if(_632){
_632.stop();
this.fire("onStop",[this._currAnim]);
}
return _632;
},_playNext:function(){
if(this._currAnim==-1||this._anims.length==0){
return this;
}
this._currAnim++;
if(this._anims[this._currAnim]){
this._anims[this._currAnim].play(null,true);
}
return this;
}});
dojo.lfx.combine=function(_633){
var _634=arguments;
if(dojo.lang.isArray(arguments[0])){
_634=arguments[0];
}
if(_634.length==1){
return _634[0];
}
return new dojo.lfx.Combine(_634);
};
dojo.lfx.chain=function(_635){
var _636=arguments;
if(dojo.lang.isArray(arguments[0])){
_636=arguments[0];
}
if(_636.length==1){
return _636[0];
}
return new dojo.lfx.Chain(_636);
};
dojo.provide("dojo.html.color");
dojo.html.getBackgroundColor=function(node){
node=dojo.byId(node);
var _638;
do{
_638=dojo.html.getStyle(node,"background-color");
if(_638.toLowerCase()=="rgba(0, 0, 0, 0)"){
_638="transparent";
}
if(node==document.getElementsByTagName("body")[0]){
node=null;
break;
}
node=node.parentNode;
}while(node&&dojo.lang.inArray(["transparent",""],_638));
if(_638=="transparent"){
_638=[255,255,255,0];
}else{
_638=dojo.gfx.color.extractRGB(_638);
}
return _638;
};
dojo.provide("dojo.lfx.html");
dojo.lfx.html._byId=function(_639){
if(!_639){
return [];
}
if(dojo.lang.isArrayLike(_639)){
if(!_639.alreadyChecked){
var n=[];
dojo.lang.forEach(_639,function(node){
n.push(dojo.byId(node));
});
n.alreadyChecked=true;
return n;
}else{
return _639;
}
}else{
var n=[];
n.push(dojo.byId(_639));
n.alreadyChecked=true;
return n;
}
};
dojo.lfx.html.propertyAnimation=function(_63c,_63d,_63e,_63f,_640){
_63c=dojo.lfx.html._byId(_63c);
var _641={"propertyMap":_63d,"nodes":_63c,"duration":_63e,"easing":_63f||dojo.lfx.easeDefault};
var _642=function(args){
if(args.nodes.length==1){
var pm=args.propertyMap;
if(!dojo.lang.isArray(args.propertyMap)){
var parr=[];
for(var _646 in pm){
pm[_646].property=_646;
parr.push(pm[_646]);
}
pm=args.propertyMap=parr;
}
dojo.lang.forEach(pm,function(prop){
if(dj_undef("start",prop)){
if(prop.property!="opacity"){
prop.start=parseInt(dojo.html.getComputedStyle(args.nodes[0],prop.property));
}else{
prop.start=dojo.html.getOpacity(args.nodes[0]);
}
}
});
}
};
var _648=function(_649){
var _64a=[];
dojo.lang.forEach(_649,function(c){
_64a.push(Math.round(c));
});
return _64a;
};
var _64c=function(n,_64e){
n=dojo.byId(n);
if(!n||!n.style){
return;
}
for(var s in _64e){
try{
if(s=="opacity"){
dojo.html.setOpacity(n,_64e[s]);
}else{
n.style[s]=_64e[s];
}
}
catch(e){
dojo.debug(e);
}
}
};
var _650=function(_651){
this._properties=_651;
this.diffs=new Array(_651.length);
dojo.lang.forEach(_651,function(prop,i){
if(dojo.lang.isFunction(prop.start)){
prop.start=prop.start(prop,i);
}
if(dojo.lang.isFunction(prop.end)){
prop.end=prop.end(prop,i);
}
if(dojo.lang.isArray(prop.start)){
this.diffs[i]=null;
}else{
if(prop.start instanceof dojo.gfx.color.Color){
prop.startRgb=prop.start.toRgb();
prop.endRgb=prop.end.toRgb();
}else{
this.diffs[i]=prop.end-prop.start;
}
}
},this);
this.getValue=function(n){
var ret={};
dojo.lang.forEach(this._properties,function(prop,i){
var _658=null;
if(dojo.lang.isArray(prop.start)){
}else{
if(prop.start instanceof dojo.gfx.color.Color){
_658=(prop.units||"rgb")+"(";
for(var j=0;j<prop.startRgb.length;j++){
_658+=Math.round(((prop.endRgb[j]-prop.startRgb[j])*n)+prop.startRgb[j])+(j<prop.startRgb.length-1?",":"");
}
_658+=")";
}else{
_658=((this.diffs[i])*n)+prop.start+(prop.property!="opacity"?prop.units||"px":"");
}
}
ret[dojo.html.toCamelCase(prop.property)]=_658;
},this);
return ret;
};
};
var anim=new dojo.lfx.Animation({beforeBegin:function(){
_642(_641);
anim.curve=new _650(_641.propertyMap);
},onAnimate:function(_65b){
dojo.lang.forEach(_641.nodes,function(node){
_64c(node,_65b);
});
}},_641.duration,null,_641.easing);
if(_640){
for(var x in _640){
if(dojo.lang.isFunction(_640[x])){
anim.connect(x,anim,_640[x]);
}
}
}
return anim;
};
dojo.lfx.html._makeFadeable=function(_65e){
var _65f=function(node){
if(dojo.render.html.ie){
if((node.style.zoom.length==0)&&(dojo.html.getStyle(node,"zoom")=="normal")){
node.style.zoom="1";
}
if((node.style.width.length==0)&&(dojo.html.getStyle(node,"width")=="auto")){
node.style.width="auto";
}
}
};
if(dojo.lang.isArrayLike(_65e)){
dojo.lang.forEach(_65e,_65f);
}else{
_65f(_65e);
}
};
dojo.lfx.html.fade=function(_661,_662,_663,_664,_665){
_661=dojo.lfx.html._byId(_661);
var _666={property:"opacity"};
if(!dj_undef("start",_662)){
_666.start=_662.start;
}else{
_666.start=function(){
return dojo.html.getOpacity(_661[0]);
};
}
if(!dj_undef("end",_662)){
_666.end=_662.end;
}else{
dojo.raise("dojo.lfx.html.fade needs an end value");
}
var anim=dojo.lfx.propertyAnimation(_661,[_666],_663,_664);
anim.connect("beforeBegin",function(){
dojo.lfx.html._makeFadeable(_661);
});
if(_665){
anim.connect("onEnd",function(){
_665(_661,anim);
});
}
return anim;
};
dojo.lfx.html.fadeIn=function(_668,_669,_66a,_66b){
return dojo.lfx.html.fade(_668,{end:1},_669,_66a,_66b);
};
dojo.lfx.html.fadeOut=function(_66c,_66d,_66e,_66f){
return dojo.lfx.html.fade(_66c,{end:0},_66d,_66e,_66f);
};
dojo.lfx.html.fadeShow=function(_670,_671,_672,_673){
_670=dojo.lfx.html._byId(_670);
dojo.lang.forEach(_670,function(node){
dojo.html.setOpacity(node,0);
});
var anim=dojo.lfx.html.fadeIn(_670,_671,_672,_673);
anim.connect("beforeBegin",function(){
if(dojo.lang.isArrayLike(_670)){
dojo.lang.forEach(_670,dojo.html.show);
}else{
dojo.html.show(_670);
}
});
return anim;
};
dojo.lfx.html.fadeHide=function(_676,_677,_678,_679){
var anim=dojo.lfx.html.fadeOut(_676,_677,_678,function(){
if(dojo.lang.isArrayLike(_676)){
dojo.lang.forEach(_676,dojo.html.hide);
}else{
dojo.html.hide(_676);
}
if(_679){
_679(_676,anim);
}
});
return anim;
};
dojo.lfx.html.wipeIn=function(_67b,_67c,_67d,_67e){
_67b=dojo.lfx.html._byId(_67b);
var _67f=[];
dojo.lang.forEach(_67b,function(node){
var _681={};
var _682,_683,_684;
with(node.style){
_682=top;
_683=left;
_684=position;
top="-9999px";
left="-9999px";
position="absolute";
display="";
}
var _685=dojo.html.getBorderBox(node).height;
with(node.style){
top=_682;
left=_683;
position=_684;
display="none";
}
var anim=dojo.lfx.propertyAnimation(node,{"height":{start:1,end:function(){
return _685;
}}},_67c,_67d);
anim.connect("beforeBegin",function(){
_681.overflow=node.style.overflow;
_681.height=node.style.height;
with(node.style){
overflow="hidden";
_685="1px";
}
dojo.html.show(node);
});
anim.connect("onEnd",function(){
with(node.style){
overflow=_681.overflow;
_685=_681.height;
}
if(_67e){
_67e(node,anim);
}
});
_67f.push(anim);
});
return dojo.lfx.combine(_67f);
};
dojo.lfx.html.wipeOut=function(_687,_688,_689,_68a){
_687=dojo.lfx.html._byId(_687);
var _68b=[];
dojo.lang.forEach(_687,function(node){
var _68d={};
var anim=dojo.lfx.propertyAnimation(node,{"height":{start:function(){
return dojo.html.getContentBox(node).height;
},end:1}},_688,_689,{"beforeBegin":function(){
_68d.overflow=node.style.overflow;
_68d.height=node.style.height;
with(node.style){
overflow="hidden";
}
dojo.html.show(node);
},"onEnd":function(){
dojo.html.hide(node);
with(node.style){
overflow=_68d.overflow;
height=_68d.height;
}
if(_68a){
_68a(node,anim);
}
}});
_68b.push(anim);
});
return dojo.lfx.combine(_68b);
};
dojo.lfx.html.slideTo=function(_68f,_690,_691,_692,_693){
_68f=dojo.lfx.html._byId(_68f);
var _694=[];
var _695=dojo.html.getComputedStyle;
dojo.lang.forEach(_68f,function(node){
var top=null;
var left=null;
var init=(function(){
var _69a=node;
return function(){
var pos=_695(_69a,"position");
top=(pos=="absolute"?node.offsetTop:parseInt(_695(node,"top"))||0);
left=(pos=="absolute"?node.offsetLeft:parseInt(_695(node,"left"))||0);
if(!dojo.lang.inArray(["absolute","relative"],pos)){
var ret=dojo.html.abs(_69a,true);
dojo.html.setStyleAttributes(_69a,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,{"top":{start:top,end:(_690.top||0)},"left":{start:left,end:(_690.left||0)}},_691,_692,{"beforeBegin":init});
if(_693){
anim.connect("onEnd",function(){
_693(_68f,anim);
});
}
_694.push(anim);
});
return dojo.lfx.combine(_694);
};
dojo.lfx.html.slideBy=function(_69e,_69f,_6a0,_6a1,_6a2){
_69e=dojo.lfx.html._byId(_69e);
var _6a3=[];
var _6a4=dojo.html.getComputedStyle;
dojo.lang.forEach(_69e,function(node){
var top=null;
var left=null;
var init=(function(){
var _6a9=node;
return function(){
var pos=_6a4(_6a9,"position");
top=(pos=="absolute"?node.offsetTop:parseInt(_6a4(node,"top"))||0);
left=(pos=="absolute"?node.offsetLeft:parseInt(_6a4(node,"left"))||0);
if(!dojo.lang.inArray(["absolute","relative"],pos)){
var ret=dojo.html.abs(_6a9,true);
dojo.html.setStyleAttributes(_6a9,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,{"top":{start:top,end:top+(_69f.top||0)},"left":{start:left,end:left+(_69f.left||0)}},_6a0,_6a1).connect("beforeBegin",init);
if(_6a2){
anim.connect("onEnd",function(){
_6a2(_69e,anim);
});
}
_6a3.push(anim);
});
return dojo.lfx.combine(_6a3);
};
dojo.lfx.html.explode=function(_6ad,_6ae,_6af,_6b0,_6b1){
var h=dojo.html;
_6ad=dojo.byId(_6ad);
_6ae=dojo.byId(_6ae);
var _6b3=h.toCoordinateObject(_6ad,true);
var _6b4=document.createElement("div");
h.copyStyle(_6b4,_6ae);
if(_6ae.explodeClassName){
_6b4.className=_6ae.explodeClassName;
}
with(_6b4.style){
position="absolute";
display="none";
var _6b5=h.getStyle(_6ad,"background-color");
backgroundColor=_6b5?_6b5.toLowerCase():"transparent";
backgroundColor=(backgroundColor=="transparent")?"rgb(221, 221, 221)":backgroundColor;
}
dojo.body().appendChild(_6b4);
with(_6ae.style){
visibility="hidden";
display="block";
}
var _6b6=h.toCoordinateObject(_6ae,true);
with(_6ae.style){
display="none";
visibility="visible";
}
var _6b7={opacity:{start:0.5,end:1}};
dojo.lang.forEach(["height","width","top","left"],function(type){
_6b7[type]={start:_6b3[type],end:_6b6[type]};
});
var anim=new dojo.lfx.propertyAnimation(_6b4,_6b7,_6af,_6b0,{"beforeBegin":function(){
h.setDisplay(_6b4,"block");
},"onEnd":function(){
h.setDisplay(_6ae,"block");
_6b4.parentNode.removeChild(_6b4);
}});
if(_6b1){
anim.connect("onEnd",function(){
_6b1(_6ae,anim);
});
}
return anim;
};
dojo.lfx.html.implode=function(_6ba,end,_6bc,_6bd,_6be){
var h=dojo.html;
_6ba=dojo.byId(_6ba);
end=dojo.byId(end);
var _6c0=dojo.html.toCoordinateObject(_6ba,true);
var _6c1=dojo.html.toCoordinateObject(end,true);
var _6c2=document.createElement("div");
dojo.html.copyStyle(_6c2,_6ba);
if(_6ba.explodeClassName){
_6c2.className=_6ba.explodeClassName;
}
dojo.html.setOpacity(_6c2,0.3);
with(_6c2.style){
position="absolute";
display="none";
backgroundColor=h.getStyle(_6ba,"background-color").toLowerCase();
}
dojo.body().appendChild(_6c2);
var _6c3={opacity:{start:1,end:0.5}};
dojo.lang.forEach(["height","width","top","left"],function(type){
_6c3[type]={start:_6c0[type],end:_6c1[type]};
});
var anim=new dojo.lfx.propertyAnimation(_6c2,_6c3,_6bc,_6bd,{"beforeBegin":function(){
dojo.html.hide(_6ba);
dojo.html.show(_6c2);
},"onEnd":function(){
_6c2.parentNode.removeChild(_6c2);
}});
if(_6be){
anim.connect("onEnd",function(){
_6be(_6ba,anim);
});
}
return anim;
};
dojo.lfx.html.highlight=function(_6c6,_6c7,_6c8,_6c9,_6ca){
_6c6=dojo.lfx.html._byId(_6c6);
var _6cb=[];
dojo.lang.forEach(_6c6,function(node){
var _6cd=dojo.html.getBackgroundColor(node);
var bg=dojo.html.getStyle(node,"background-color").toLowerCase();
var _6cf=dojo.html.getStyle(node,"background-image");
var _6d0=(bg=="transparent"||bg=="rgba(0, 0, 0, 0)");
while(_6cd.length>3){
_6cd.pop();
}
var rgb=new dojo.gfx.color.Color(_6c7);
var _6d2=new dojo.gfx.color.Color(_6cd);
var anim=dojo.lfx.propertyAnimation(node,{"background-color":{start:rgb,end:_6d2}},_6c8,_6c9,{"beforeBegin":function(){
if(_6cf){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+rgb.toRgb().join(",")+")";
},"onEnd":function(){
if(_6cf){
node.style.backgroundImage=_6cf;
}
if(_6d0){
node.style.backgroundColor="transparent";
}
if(_6ca){
_6ca(node,anim);
}
}});
_6cb.push(anim);
});
return dojo.lfx.combine(_6cb);
};
dojo.lfx.html.unhighlight=function(_6d4,_6d5,_6d6,_6d7,_6d8){
_6d4=dojo.lfx.html._byId(_6d4);
var _6d9=[];
dojo.lang.forEach(_6d4,function(node){
var _6db=new dojo.gfx.color.Color(dojo.html.getBackgroundColor(node));
var rgb=new dojo.gfx.color.Color(_6d5);
var _6dd=dojo.html.getStyle(node,"background-image");
var anim=dojo.lfx.propertyAnimation(node,{"background-color":{start:_6db,end:rgb}},_6d6,_6d7,{"beforeBegin":function(){
if(_6dd){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+_6db.toRgb().join(",")+")";
},"onEnd":function(){
if(_6d8){
_6d8(node,anim);
}
}});
_6d9.push(anim);
});
return dojo.lfx.combine(_6d9);
};
dojo.lang.mixin(dojo.lfx,dojo.lfx.html);
dojo.provide("dojo.lfx.*");
dojo.provide("dojo.lfx.toggler");
dojo.lfx.toggler.plain=function(){
this.stop=function(){
};
this.show=function(node,_6e0,_6e1,_6e2){
dojo.html.show(node);
if(dojo.lang.isFunction(_6e2)){
_6e2();
}
};
this.hide=function(node,_6e4,_6e5,_6e6){
dojo.html.hide(node);
if(dojo.lang.isFunction(_6e6)){
_6e6();
}
};
};
dojo.lfx.toggler.common={stop:function(){
if(this.anim&&this.anim.status()!="stopped"){
this.anim.stop();
}
},_act:function(_6e7,node,_6e9,_6ea,_6eb,_6ec){
this.stop();
this.anim=dojo.lfx[_6e7](node,_6e9,_6ea,_6eb).play();
},show:function(node,_6ee,_6ef,_6f0,_6f1){
this._act(this.show_action,node,_6ee,_6ef,_6f0,_6f1);
},hide:function(node,_6f3,_6f4,_6f5,_6f6){
this._act(this.hide_action,node,_6f3,_6f4,_6f5,_6f6);
}};
dojo.lfx.toggler.fade=function(){
this.anim=null;
this.show_action="fadeShow";
this.hide_action="fadeHide";
};
dojo.extend(dojo.lfx.toggler.fade,dojo.lfx.toggler.common);
dojo.lfx.toggler.wipe=function(){
this.anim=null;
this.show_action="wipeIn";
this.hide_action="wipeOut";
};
dojo.extend(dojo.lfx.toggler.wipe,dojo.lfx.toggler.common);
dojo.lfx.toggler.explode=function(){
this.anim=null;
this.show_action="explode";
this.hide_action="implode";
this.show=function(node,_6f8,_6f9,_6fa,_6fb){
this.stop();
this.anim=dojo.lfx.explode(_6fb||{x:0,y:0,width:0,height:0},node,_6f8,_6f9,_6fa).play();
};
this.hide=function(node,_6fd,_6fe,_6ff,_700){
this.stop();
this.anim=dojo.lfx.implode(node,_700||{x:0,y:0,width:0,height:0},_6fd,_6fe,_6ff).play();
};
};
dojo.extend(dojo.lfx.toggler.explode,dojo.lfx.toggler.common);
dojo.provide("dojo.widget.HtmlWidget");
dojo.declare("dojo.widget.HtmlWidget",dojo.widget.DomWidget,{templateCssPath:null,templatePath:null,lang:"",toggle:"plain",toggleDuration:150,initialize:function(args,frag){
},postMixInProperties:function(args,frag){
if(this.lang===""){
this.lang=null;
}
this.toggleObj=new (dojo.lfx.toggler[this.toggle.toLowerCase()]||dojo.lfx.toggler.plain);
},createNodesFromText:function(txt,wrap){
return dojo.html.createNodesFromText(txt,wrap);
},destroyRendering:function(_707){
try{
if(this.bgIframe){
this.bgIframe.remove();
delete this.bgIframe;
}
if(!_707&&this.domNode){
dojo.event.browser.clean(this.domNode);
}
dojo.widget.HtmlWidget.superclass.destroyRendering.call(this);
}
catch(e){
}
},isShowing:function(){
return dojo.html.isShowing(this.domNode);
},toggleShowing:function(){
if(this.isShowing()){
this.hide();
}else{
this.show();
}
},show:function(){
if(this.isShowing()){
return;
}
this.animationInProgress=true;
this.toggleObj.show(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onShow),this.explodeSrc);
},onShow:function(){
this.animationInProgress=false;
this.checkSize();
},hide:function(){
if(!this.isShowing()){
return;
}
this.animationInProgress=true;
this.toggleObj.hide(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onHide),this.explodeSrc);
},onHide:function(){
this.animationInProgress=false;
},_isResized:function(w,h){
if(!this.isShowing()){
return false;
}
var wh=dojo.html.getMarginBox(this.domNode);
var _70b=w||wh.width;
var _70c=h||wh.height;
if(this.width==_70b&&this.height==_70c){
return false;
}
this.width=_70b;
this.height=_70c;
return true;
},checkSize:function(){
if(!this._isResized()){
return;
}
this.onResized();
},resizeTo:function(w,h){
dojo.html.setMarginBox(this.domNode,{width:w,height:h});
if(this.isShowing()){
this.onResized();
}
},resizeSoon:function(){
if(this.isShowing()){
dojo.lang.setTimeout(this,this.onResized,0);
}
},onResized:function(){
dojo.lang.forEach(this.children,function(_70f){
if(_70f.checkSize){
_70f.checkSize();
}
});
}});
dojo.provide("dojo.widget.*");
dojo.provide("dojo.string.common");
dojo.string.trim=function(str,wh){
if(!str.replace){
return str;
}
if(!str.length){
return str;
}
var re=(wh>0)?(/^\s+/):(wh<0)?(/\s+$/):(/^\s+|\s+$/g);
return str.replace(re,"");
};
dojo.string.trimStart=function(str){
return dojo.string.trim(str,1);
};
dojo.string.trimEnd=function(str){
return dojo.string.trim(str,-1);
};
dojo.string.repeat=function(str,_716,_717){
var out="";
for(var i=0;i<_716;i++){
out+=str;
if(_717&&i<_716-1){
out+=_717;
}
}
return out;
};
dojo.string.pad=function(str,len,c,dir){
var out=String(str);
if(!c){
c="0";
}
if(!dir){
dir=1;
}
while(out.length<len){
if(dir>0){
out=c+out;
}else{
out+=c;
}
}
return out;
};
dojo.string.padLeft=function(str,len,c){
return dojo.string.pad(str,len,c,1);
};
dojo.string.padRight=function(str,len,c){
return dojo.string.pad(str,len,c,-1);
};
dojo.provide("dojo.string");
dojo.provide("dojo.io.common");
dojo.io.transports=[];
dojo.io.hdlrFuncNames=["load","error","timeout"];
dojo.io.Request=function(url,_726,_727,_728){
if((arguments.length==1)&&(arguments[0].constructor==Object)){
this.fromKwArgs(arguments[0]);
}else{
this.url=url;
if(_726){
this.mimetype=_726;
}
if(_727){
this.transport=_727;
}
if(arguments.length>=4){
this.changeUrl=_728;
}
}
};
dojo.lang.extend(dojo.io.Request,{url:"",mimetype:"text/plain",method:"GET",content:undefined,transport:undefined,changeUrl:undefined,formNode:undefined,sync:false,bindSuccess:false,useCache:false,preventCache:false,load:function(type,data,_72b,_72c){
},error:function(type,_72e,_72f,_730){
},timeout:function(type,_732,_733,_734){
},handle:function(type,data,_737,_738){
},timeoutSeconds:0,abort:function(){
},fromKwArgs:function(_739){
if(_739["url"]){
_739.url=_739.url.toString();
}
if(_739["formNode"]){
_739.formNode=dojo.byId(_739.formNode);
}
if(!_739["method"]&&_739["formNode"]&&_739["formNode"].method){
_739.method=_739["formNode"].method;
}
if(!_739["handle"]&&_739["handler"]){
_739.handle=_739.handler;
}
if(!_739["load"]&&_739["loaded"]){
_739.load=_739.loaded;
}
if(!_739["changeUrl"]&&_739["changeURL"]){
_739.changeUrl=_739.changeURL;
}
_739.encoding=dojo.lang.firstValued(_739["encoding"],djConfig["bindEncoding"],"");
_739.sendTransport=dojo.lang.firstValued(_739["sendTransport"],djConfig["ioSendTransport"],false);
var _73a=dojo.lang.isFunction;
for(var x=0;x<dojo.io.hdlrFuncNames.length;x++){
var fn=dojo.io.hdlrFuncNames[x];
if(_739[fn]&&_73a(_739[fn])){
continue;
}
if(_739["handle"]&&_73a(_739["handle"])){
_739[fn]=_739.handle;
}
}
dojo.lang.mixin(this,_739);
}});
dojo.io.Error=function(msg,type,num){
this.message=msg;
this.type=type||"unknown";
this.number=num||0;
};
dojo.io.transports.addTransport=function(name){
this.push(name);
this[name]=dojo.io[name];
};
dojo.io.bind=function(_741){
if(!(_741 instanceof dojo.io.Request)){
try{
_741=new dojo.io.Request(_741);
}
catch(e){
dojo.debug(e);
}
}
var _742="";
if(_741["transport"]){
_742=_741["transport"];
if(!this[_742]){
dojo.io.sendBindError(_741,"No dojo.io.bind() transport with name '"+_741["transport"]+"'.");
return _741;
}
if(!this[_742].canHandle(_741)){
dojo.io.sendBindError(_741,"dojo.io.bind() transport with name '"+_741["transport"]+"' cannot handle this type of request.");
return _741;
}
}else{
for(var x=0;x<dojo.io.transports.length;x++){
var tmp=dojo.io.transports[x];
if((this[tmp])&&(this[tmp].canHandle(_741))){
_742=tmp;
break;
}
}
if(_742==""){
dojo.io.sendBindError(_741,"None of the loaded transports for dojo.io.bind()"+" can handle the request.");
return _741;
}
}
this[_742].bind(_741);
_741.bindSuccess=true;
return _741;
};
dojo.io.sendBindError=function(_745,_746){
if((typeof _745.error=="function"||typeof _745.handle=="function")&&(typeof setTimeout=="function"||typeof setTimeout=="object")){
var _747=new dojo.io.Error(_746);
setTimeout(function(){
_745[(typeof _745.error=="function")?"error":"handle"]("error",_747,null,_745);
},50);
}else{
dojo.raise(_746);
}
};
dojo.io.queueBind=function(_748){
if(!(_748 instanceof dojo.io.Request)){
try{
_748=new dojo.io.Request(_748);
}
catch(e){
dojo.debug(e);
}
}
var _749=_748.load;
_748.load=function(){
dojo.io._queueBindInFlight=false;
var ret=_749.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
var _74b=_748.error;
_748.error=function(){
dojo.io._queueBindInFlight=false;
var ret=_74b.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
dojo.io._bindQueue.push(_748);
dojo.io._dispatchNextQueueBind();
return _748;
};
dojo.io._dispatchNextQueueBind=function(){
if(!dojo.io._queueBindInFlight){
dojo.io._queueBindInFlight=true;
if(dojo.io._bindQueue.length>0){
dojo.io.bind(dojo.io._bindQueue.shift());
}else{
dojo.io._queueBindInFlight=false;
}
}
};
dojo.io._bindQueue=[];
dojo.io._queueBindInFlight=false;
dojo.io.argsFromMap=function(map,_74e,last){
var enc=/utf/i.test(_74e||"")?encodeURIComponent:dojo.string.encodeAscii;
var _751=[];
var _752=new Object();
for(var name in map){
var _754=function(elt){
var val=enc(name)+"="+enc(elt);
_751[(last==name)?"push":"unshift"](val);
};
if(!_752[name]){
var _757=map[name];
if(dojo.lang.isArray(_757)){
dojo.lang.forEach(_757,_754);
}else{
_754(_757);
}
}
}
return _751.join("&");
};
dojo.io.setIFrameSrc=function(_758,src,_75a){
try{
var r=dojo.render.html;
if(!_75a){
if(r.safari){
_758.location=src;
}else{
frames[_758.name].location=src;
}
}else{
var idoc;
if(r.ie){
idoc=_758.contentWindow.document;
}else{
if(r.safari){
idoc=_758.document;
}else{
idoc=_758.contentWindow;
}
}
if(!idoc){
_758.location=src;
return;
}else{
idoc.location.replace(src);
}
}
}
catch(e){
dojo.debug(e);
dojo.debug("setIFrameSrc: "+e);
}
};
dojo.provide("dojo.string.extras");
dojo.string.substituteParams=function(_75d,hash){
var map=(typeof hash=="object")?hash:dojo.lang.toArray(arguments,1);
return _75d.replace(/\%\{(\w+)\}/g,function(_760,key){
if(typeof (map[key])!="undefined"&&map[key]!=null){
return map[key];
}
dojo.raise("Substitution not found: "+key);
});
};
dojo.string.capitalize=function(str){
if(!dojo.lang.isString(str)){
return "";
}
return str.replace(/[^\s]+/g,function(word){
return word.substring(0,1).toUpperCase()+word.substring(1);
});
};
dojo.string.isBlank=function(str){
if(!dojo.lang.isString(str)){
return true;
}
return (dojo.string.trim(str).length==0);
};
dojo.string.encodeAscii=function(str){
if(!dojo.lang.isString(str)){
return str;
}
var ret="";
var _767=escape(str);
var _768,re=/%u([0-9A-F]{4})/i;
while((_768=_767.match(re))){
var num=Number("0x"+_768[1]);
var _76b=escape("&#"+num+";");
ret+=_767.substring(0,_768.index)+_76b;
_767=_767.substring(_768.index+_768[0].length);
}
ret+=_767.replace(/\+/g,"%2B");
return ret;
};
dojo.string.escape=function(type,str){
var args=dojo.lang.toArray(arguments,1);
switch(type.toLowerCase()){
case "xml":
case "html":
case "xhtml":
return dojo.string.escapeXml.apply(this,args);
case "sql":
return dojo.string.escapeSql.apply(this,args);
case "regexp":
case "regex":
return dojo.string.escapeRegExp.apply(this,args);
case "javascript":
case "jscript":
case "js":
return dojo.string.escapeJavaScript.apply(this,args);
case "ascii":
return dojo.string.encodeAscii.apply(this,args);
default:
return str;
}
};
dojo.string.escapeXml=function(str,_770){
str=str.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;");
if(!_770){
str=str.replace(/'/gm,"&#39;");
}
return str;
};
dojo.string.escapeSql=function(str){
return str.replace(/'/gm,"''");
};
dojo.string.escapeRegExp=function(str,_773){
return str.replace(/([\.$?*!=:|{}\(\)\[\]\\\/^])/g,function(ch){
if(_773&&_773.indexOf(ch)!=-1){
return ch;
}
return "\\"+ch;
});
};
dojo.string.escapeJavaScript=function(str){
return str.replace(/(["'\f\b\n\t\r])/gm,"\\$1");
};
dojo.string.escapeString=function(str){
return ("\""+str.replace(/(["\\])/g,"\\$1")+"\"").replace(/[\f]/g,"\\f").replace(/[\b]/g,"\\b").replace(/[\n]/g,"\\n").replace(/[\t]/g,"\\t").replace(/[\r]/g,"\\r");
};
dojo.string.summary=function(str,len){
if(!len||str.length<=len){
return str;
}
return str.substring(0,len).replace(/\.+$/,"")+"...";
};
dojo.string.endsWith=function(str,end,_77b){
if(_77b){
str=str.toLowerCase();
end=end.toLowerCase();
}
if((str.length-end.length)<0){
return false;
}
return str.lastIndexOf(end)==str.length-end.length;
};
dojo.string.endsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.endsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.startsWith=function(str,_77f,_780){
if(_780){
str=str.toLowerCase();
_77f=_77f.toLowerCase();
}
return str.indexOf(_77f)==0;
};
dojo.string.startsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.startsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.has=function(str){
for(var i=1;i<arguments.length;i++){
if(str.indexOf(arguments[i])>-1){
return true;
}
}
return false;
};
dojo.string.normalizeNewlines=function(text,_786){
if(_786=="\n"){
text=text.replace(/\r\n/g,"\n");
text=text.replace(/\r/g,"\n");
}else{
if(_786=="\r"){
text=text.replace(/\r\n/g,"\r");
text=text.replace(/\n/g,"\r");
}else{
text=text.replace(/([^\r])\n/g,"$1\r\n").replace(/\r([^\n])/g,"\r\n$1");
}
}
return text;
};
dojo.string.splitEscaped=function(str,_788){
var _789=[];
for(var i=0,_78b=0;i<str.length;i++){
if(str.charAt(i)=="\\"){
i++;
continue;
}
if(str.charAt(i)==_788){
_789.push(str.substring(_78b,i));
_78b=i+1;
}
}
_789.push(str.substr(_78b));
return _789;
};
dojo.provide("dojo.undo.browser");
try{
if((!djConfig["preventBackButtonFix"])&&(!dojo.hostenv.post_load_)){
document.write("<iframe style='border: 0px; width: 1px; height: 1px; position: absolute; bottom: 0px; right: 0px; visibility: visible;' name='djhistory' id='djhistory' src='"+(dojo.hostenv.getBaseScriptUri()+"iframe_history.html")+"'></iframe>");
}
}
catch(e){
}
if(dojo.render.html.opera){
dojo.debug("Opera is not supported with dojo.undo.browser, so back/forward detection will not work.");
}
dojo.undo.browser={initialHref:(!dj_undef("window"))?window.location.href:"",initialHash:(!dj_undef("window"))?window.location.hash:"",moveForward:false,historyStack:[],forwardStack:[],historyIframe:null,bookmarkAnchor:null,locationTimer:null,setInitialState:function(args){
this.initialState=this._createState(this.initialHref,args,this.initialHash);
},addToHistory:function(args){
this.forwardStack=[];
var hash=null;
var url=null;
if(!this.historyIframe){
this.historyIframe=window.frames["djhistory"];
}
if(!this.bookmarkAnchor){
this.bookmarkAnchor=document.createElement("a");
dojo.body().appendChild(this.bookmarkAnchor);
this.bookmarkAnchor.style.display="none";
}
if(args["changeUrl"]){
hash="#"+((args["changeUrl"]!==true)?args["changeUrl"]:(new Date()).getTime());
if(this.historyStack.length==0&&this.initialState.urlHash==hash){
this.initialState=this._createState(url,args,hash);
return;
}else{
if(this.historyStack.length>0&&this.historyStack[this.historyStack.length-1].urlHash==hash){
this.historyStack[this.historyStack.length-1]=this._createState(url,args,hash);
return;
}
}
this.changingUrl=true;
setTimeout("window.location.href = '"+hash+"'; dojo.undo.browser.changingUrl = false;",1);
this.bookmarkAnchor.href=hash;
if(dojo.render.html.ie){
url=this._loadIframeHistory();
var _790=args["back"]||args["backButton"]||args["handle"];
var tcb=function(_792){
if(window.location.hash!=""){
setTimeout("window.location.href = '"+hash+"';",1);
}
_790.apply(this,[_792]);
};
if(args["back"]){
args.back=tcb;
}else{
if(args["backButton"]){
args.backButton=tcb;
}else{
if(args["handle"]){
args.handle=tcb;
}
}
}
var _793=args["forward"]||args["forwardButton"]||args["handle"];
var tfw=function(_795){
if(window.location.hash!=""){
window.location.href=hash;
}
if(_793){
_793.apply(this,[_795]);
}
};
if(args["forward"]){
args.forward=tfw;
}else{
if(args["forwardButton"]){
args.forwardButton=tfw;
}else{
if(args["handle"]){
args.handle=tfw;
}
}
}
}else{
if(dojo.render.html.moz){
if(!this.locationTimer){
this.locationTimer=setInterval("dojo.undo.browser.checkLocation();",200);
}
}
}
}else{
url=this._loadIframeHistory();
}
this.historyStack.push(this._createState(url,args,hash));
},checkLocation:function(){
if(!this.changingUrl){
var hsl=this.historyStack.length;
if((window.location.hash==this.initialHash||window.location.href==this.initialHref)&&(hsl==1)){
this.handleBackButton();
return;
}
if(this.forwardStack.length>0){
if(this.forwardStack[this.forwardStack.length-1].urlHash==window.location.hash){
this.handleForwardButton();
return;
}
}
if((hsl>=2)&&(this.historyStack[hsl-2])){
if(this.historyStack[hsl-2].urlHash==window.location.hash){
this.handleBackButton();
return;
}
}
}
},iframeLoaded:function(evt,_798){
if(!dojo.render.html.opera){
var _799=this._getUrlQuery(_798.href);
if(_799==null){
if(this.historyStack.length==1){
this.handleBackButton();
}
return;
}
if(this.moveForward){
this.moveForward=false;
return;
}
if(this.historyStack.length>=2&&_799==this._getUrlQuery(this.historyStack[this.historyStack.length-2].url)){
this.handleBackButton();
}else{
if(this.forwardStack.length>0&&_799==this._getUrlQuery(this.forwardStack[this.forwardStack.length-1].url)){
this.handleForwardButton();
}
}
}
},handleBackButton:function(){
var _79a=this.historyStack.pop();
if(!_79a){
return;
}
var last=this.historyStack[this.historyStack.length-1];
if(!last&&this.historyStack.length==0){
last=this.initialState;
}
if(last){
if(last.kwArgs["back"]){
last.kwArgs["back"]();
}else{
if(last.kwArgs["backButton"]){
last.kwArgs["backButton"]();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("back");
}
}
}
}
this.forwardStack.push(_79a);
},handleForwardButton:function(){
var last=this.forwardStack.pop();
if(!last){
return;
}
if(last.kwArgs["forward"]){
last.kwArgs.forward();
}else{
if(last.kwArgs["forwardButton"]){
last.kwArgs.forwardButton();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("forward");
}
}
}
this.historyStack.push(last);
},_createState:function(url,args,hash){
return {"url":url,"kwArgs":args,"urlHash":hash};
},_getUrlQuery:function(url){
var _7a1=url.split("?");
if(_7a1.length<2){
return null;
}else{
return _7a1[1];
}
},_loadIframeHistory:function(){
var url=dojo.hostenv.getBaseScriptUri()+"iframe_history.html?"+(new Date()).getTime();
this.moveForward=true;
dojo.io.setIFrameSrc(this.historyIframe,url,false);
return url;
}};
dojo.provide("dojo.io.BrowserIO");
if(!dj_undef("window")){
dojo.io.checkChildrenForFile=function(node){
var _7a4=false;
var _7a5=node.getElementsByTagName("input");
dojo.lang.forEach(_7a5,function(_7a6){
if(_7a4){
return;
}
if(_7a6.getAttribute("type")=="file"){
_7a4=true;
}
});
return _7a4;
};
dojo.io.formHasFile=function(_7a7){
return dojo.io.checkChildrenForFile(_7a7);
};
dojo.io.updateNode=function(node,_7a9){
node=dojo.byId(node);
var args=_7a9;
if(dojo.lang.isString(_7a9)){
args={url:_7a9};
}
args.mimetype="text/html";
args.load=function(t,d,e){
while(node.firstChild){
dojo.dom.destroyNode(node.firstChild);
}
node.innerHTML=d;
};
dojo.io.bind(args);
};
dojo.io.formFilter=function(node){
var type=(node.type||"").toLowerCase();
return !node.disabled&&node.name&&!dojo.lang.inArray(["file","submit","image","reset","button"],type);
};
dojo.io.encodeForm=function(_7b0,_7b1,_7b2){
if((!_7b0)||(!_7b0.tagName)||(!_7b0.tagName.toLowerCase()=="form")){
dojo.raise("Attempted to encode a non-form element.");
}
if(!_7b2){
_7b2=dojo.io.formFilter;
}
var enc=/utf/i.test(_7b1||"")?encodeURIComponent:dojo.string.encodeAscii;
var _7b4=[];
for(var i=0;i<_7b0.elements.length;i++){
var elm=_7b0.elements[i];
if(!elm||elm.tagName.toLowerCase()=="fieldset"||!_7b2(elm)){
continue;
}
var name=enc(elm.name);
var type=elm.type.toLowerCase();
if(type=="select-multiple"){
for(var j=0;j<elm.options.length;j++){
if(elm.options[j].selected){
_7b4.push(name+"="+enc(elm.options[j].value));
}
}
}else{
if(dojo.lang.inArray(["radio","checkbox"],type)){
if(elm.checked){
_7b4.push(name+"="+enc(elm.value));
}
}else{
_7b4.push(name+"="+enc(elm.value));
}
}
}
var _7ba=_7b0.getElementsByTagName("input");
for(var i=0;i<_7ba.length;i++){
var _7bb=_7ba[i];
if(_7bb.type.toLowerCase()=="image"&&_7bb.form==_7b0&&_7b2(_7bb)){
var name=enc(_7bb.name);
_7b4.push(name+"="+enc(_7bb.value));
_7b4.push(name+".x=0");
_7b4.push(name+".y=0");
}
}
return _7b4.join("&")+"&";
};
dojo.io.FormBind=function(args){
this.bindArgs={};
if(args&&args.formNode){
this.init(args);
}else{
if(args){
this.init({formNode:args});
}
}
};
dojo.lang.extend(dojo.io.FormBind,{form:null,bindArgs:null,clickedButton:null,init:function(args){
var form=dojo.byId(args.formNode);
if(!form||!form.tagName||form.tagName.toLowerCase()!="form"){
throw new Error("FormBind: Couldn't apply, invalid form");
}else{
if(this.form==form){
return;
}else{
if(this.form){
throw new Error("FormBind: Already applied to a form");
}
}
}
dojo.lang.mixin(this.bindArgs,args);
this.form=form;
this.connect(form,"onsubmit","submit");
for(var i=0;i<form.elements.length;i++){
var node=form.elements[i];
if(node&&node.type&&dojo.lang.inArray(["submit","button"],node.type.toLowerCase())){
this.connect(node,"onclick","click");
}
}
var _7c1=form.getElementsByTagName("input");
for(var i=0;i<_7c1.length;i++){
var _7c2=_7c1[i];
if(_7c2.type.toLowerCase()=="image"&&_7c2.form==form){
this.connect(_7c2,"onclick","click");
}
}
},onSubmit:function(form){
return true;
},submit:function(e){
e.preventDefault();
if(this.onSubmit(this.form)){
dojo.io.bind(dojo.lang.mixin(this.bindArgs,{formFilter:dojo.lang.hitch(this,"formFilter")}));
}
},click:function(e){
var node=e.currentTarget;
if(node.disabled){
return;
}
this.clickedButton=node;
},formFilter:function(node){
var type=(node.type||"").toLowerCase();
var _7c9=false;
if(node.disabled||!node.name){
_7c9=false;
}else{
if(dojo.lang.inArray(["submit","button","image"],type)){
if(!this.clickedButton){
this.clickedButton=node;
}
_7c9=node==this.clickedButton;
}else{
_7c9=!dojo.lang.inArray(["file","submit","reset","button"],type);
}
}
return _7c9;
},connect:function(_7ca,_7cb,_7cc){
if(dojo.evalObjPath("dojo.event.connect")){
dojo.event.connect(_7ca,_7cb,this,_7cc);
}else{
var fcn=dojo.lang.hitch(this,_7cc);
_7ca[_7cb]=function(e){
if(!e){
e=window.event;
}
if(!e.currentTarget){
e.currentTarget=e.srcElement;
}
if(!e.preventDefault){
e.preventDefault=function(){
window.event.returnValue=false;
};
}
fcn(e);
};
}
}});
dojo.io.XMLHTTPTransport=new function(){
var _7cf=this;
var _7d0={};
this.useCache=false;
this.preventCache=false;
function getCacheKey(url,_7d2,_7d3){
return url+"|"+_7d2+"|"+_7d3.toLowerCase();
}
function addToCache(url,_7d5,_7d6,http){
_7d0[getCacheKey(url,_7d5,_7d6)]=http;
}
function getFromCache(url,_7d9,_7da){
return _7d0[getCacheKey(url,_7d9,_7da)];
}
this.clearCache=function(){
_7d0={};
};
function doLoad(_7db,http,url,_7de,_7df){
if(((http.status>=200)&&(http.status<300))||(http.status==304)||(location.protocol=="file:"&&(http.status==0||http.status==undefined))||(location.protocol=="chrome:"&&(http.status==0||http.status==undefined))){
var ret;
if(_7db.method.toLowerCase()=="head"){
var _7e1=http.getAllResponseHeaders();
ret={};
ret.toString=function(){
return _7e1;
};
var _7e2=_7e1.split(/[\r\n]+/g);
for(var i=0;i<_7e2.length;i++){
var pair=_7e2[i].match(/^([^:]+)\s*:\s*(.+)$/i);
if(pair){
ret[pair[1]]=pair[2];
}
}
}else{
if(_7db.mimetype=="text/javascript"){
try{
ret=dj_eval(http.responseText);
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=null;
}
}else{
if(_7db.mimetype=="text/json"||_7db.mimetype=="application/json"){
try{
ret=dj_eval("("+http.responseText+")");
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=false;
}
}else{
if((_7db.mimetype=="application/xml")||(_7db.mimetype=="text/xml")){
ret=http.responseXML;
if(!ret||typeof ret=="string"||!http.getResponseHeader("Content-Type")){
ret=dojo.dom.createDocumentFromText(http.responseText);
}
}else{
ret=http.responseText;
}
}
}
}
if(_7df){
addToCache(url,_7de,_7db.method,http);
}
_7db[(typeof _7db.load=="function")?"load":"handle"]("load",ret,http,_7db);
}else{
var _7e5=new dojo.io.Error("XMLHttpTransport Error: "+http.status+" "+http.statusText);
_7db[(typeof _7db.error=="function")?"error":"handle"]("error",_7e5,http,_7db);
}
}
function setHeaders(http,_7e7){
if(_7e7["headers"]){
for(var _7e8 in _7e7["headers"]){
if(_7e8.toLowerCase()=="content-type"&&!_7e7["contentType"]){
_7e7["contentType"]=_7e7["headers"][_7e8];
}else{
http.setRequestHeader(_7e8,_7e7["headers"][_7e8]);
}
}
}
}
this.inFlight=[];
this.inFlightTimer=null;
this.startWatchingInFlight=function(){
if(!this.inFlightTimer){
this.inFlightTimer=setTimeout("dojo.io.XMLHTTPTransport.watchInFlight();",10);
}
};
this.watchInFlight=function(){
var now=null;
if(!dojo.hostenv._blockAsync&&!_7cf._blockAsync){
for(var x=this.inFlight.length-1;x>=0;x--){
try{
var tif=this.inFlight[x];
if(!tif||tif.http._aborted||!tif.http.readyState){
this.inFlight.splice(x,1);
continue;
}
if(4==tif.http.readyState){
this.inFlight.splice(x,1);
doLoad(tif.req,tif.http,tif.url,tif.query,tif.useCache);
}else{
if(tif.startTime){
if(!now){
now=(new Date()).getTime();
}
if(tif.startTime+(tif.req.timeoutSeconds*1000)<now){
if(typeof tif.http.abort=="function"){
tif.http.abort();
}
this.inFlight.splice(x,1);
tif.req[(typeof tif.req.timeout=="function")?"timeout":"handle"]("timeout",null,tif.http,tif.req);
}
}
}
}
catch(e){
try{
var _7ec=new dojo.io.Error("XMLHttpTransport.watchInFlight Error: "+e);
tif.req[(typeof tif.req.error=="function")?"error":"handle"]("error",_7ec,tif.http,tif.req);
}
catch(e2){
dojo.debug("XMLHttpTransport error callback failed: "+e2);
}
}
}
}
clearTimeout(this.inFlightTimer);
if(this.inFlight.length==0){
this.inFlightTimer=null;
return;
}
this.inFlightTimer=setTimeout("dojo.io.XMLHTTPTransport.watchInFlight();",10);
};
var _7ed=dojo.hostenv.getXmlhttpObject()?true:false;
this.canHandle=function(_7ee){
return _7ed&&dojo.lang.inArray(["text/plain","text/html","application/xml","text/xml","text/javascript","text/json","application/json"],(_7ee["mimetype"].toLowerCase()||""))&&!(_7ee["formNode"]&&dojo.io.formHasFile(_7ee["formNode"]));
};
this.multipartBoundary="45309FFF-BD65-4d50-99C9-36986896A96F";
this.bind=function(_7ef){
var url=_7ef.url;
var _7f1="";
if(_7ef["formNode"]){
var ta=_7ef.formNode.getAttribute("action");
if((ta)&&(!_7ef["url"])){
url=ta;
}
var tp=_7ef.formNode.getAttribute("method");
if((tp)&&(!_7ef["method"])){
_7ef.method=tp;
}
_7f1+=dojo.io.encodeForm(_7ef.formNode,_7ef.encoding,_7ef["formFilter"]);
}
if(url.indexOf("#")>-1){
dojo.debug("Warning: dojo.io.bind: stripping hash values from url:",url);
url=url.split("#")[0];
}
if(_7ef["file"]){
_7ef.method="post";
}
if(!_7ef["method"]){
_7ef.method="get";
}
if(_7ef.method.toLowerCase()=="get"){
_7ef.multipart=false;
}else{
if(_7ef["file"]){
_7ef.multipart=true;
}else{
if(!_7ef["multipart"]){
_7ef.multipart=false;
}
}
}
if(_7ef["backButton"]||_7ef["back"]||_7ef["changeUrl"]){
dojo.undo.browser.addToHistory(_7ef);
}
var _7f4=_7ef["content"]||{};
if(_7ef.sendTransport){
_7f4["dojo.transport"]="xmlhttp";
}
do{
if(_7ef.postContent){
_7f1=_7ef.postContent;
break;
}
if(_7f4){
_7f1+=dojo.io.argsFromMap(_7f4,_7ef.encoding);
}
if(_7ef.method.toLowerCase()=="get"||!_7ef.multipart){
break;
}
var t=[];
if(_7f1.length){
var q=_7f1.split("&");
for(var i=0;i<q.length;++i){
if(q[i].length){
var p=q[i].split("=");
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+p[0]+"\"","",p[1]);
}
}
}
if(_7ef.file){
if(dojo.lang.isArray(_7ef.file)){
for(var i=0;i<_7ef.file.length;++i){
var o=_7ef.file[i];
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}else{
var o=_7ef.file;
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}
if(t.length){
t.push("--"+this.multipartBoundary+"--","");
_7f1=t.join("\r\n");
}
}while(false);
var _7fa=_7ef["sync"]?false:true;
var _7fb=_7ef["preventCache"]||(this.preventCache==true&&_7ef["preventCache"]!=false);
var _7fc=_7ef["useCache"]==true||(this.useCache==true&&_7ef["useCache"]!=false);
if(!_7fb&&_7fc){
var _7fd=getFromCache(url,_7f1,_7ef.method);
if(_7fd){
doLoad(_7ef,_7fd,url,_7f1,false);
return;
}
}
var http=dojo.hostenv.getXmlhttpObject(_7ef);
var _7ff=false;
if(_7fa){
var _800=this.inFlight.push({"req":_7ef,"http":http,"url":url,"query":_7f1,"useCache":_7fc,"startTime":_7ef.timeoutSeconds?(new Date()).getTime():0});
this.startWatchingInFlight();
}else{
_7cf._blockAsync=true;
}
if(_7ef.method.toLowerCase()=="post"){
if(!_7ef.user){
http.open("POST",url,_7fa);
}else{
http.open("POST",url,_7fa,_7ef.user,_7ef.password);
}
setHeaders(http,_7ef);
http.setRequestHeader("Content-Type",_7ef.multipart?("multipart/form-data; boundary="+this.multipartBoundary):(_7ef.contentType||"application/x-www-form-urlencoded"));
try{
http.send(_7f1);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_7ef,{status:404},url,_7f1,_7fc);
}
}else{
var _801=url;
if(_7f1!=""){
_801+=(_801.indexOf("?")>-1?"&":"?")+_7f1;
}
if(_7fb){
_801+=(dojo.string.endsWithAny(_801,"?","&")?"":(_801.indexOf("?")>-1?"&":"?"))+"dojo.preventCache="+new Date().valueOf();
}
if(!_7ef.user){
http.open(_7ef.method.toUpperCase(),_801,_7fa);
}else{
http.open(_7ef.method.toUpperCase(),_801,_7fa,_7ef.user,_7ef.password);
}
setHeaders(http,_7ef);
try{
http.send(null);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_7ef,{status:404},url,_7f1,_7fc);
}
}
if(!_7fa){
doLoad(_7ef,http,url,_7f1,_7fc);
_7cf._blockAsync=false;
}
_7ef.abort=function(){
try{
http._aborted=true;
}
catch(e){
}
return http.abort();
};
return;
};
dojo.io.transports.addTransport("XMLHTTPTransport");
};
}
dojo.provide("dojo.io.cookie");
dojo.io.cookie.setCookie=function(name,_803,days,path,_806,_807){
var _808=-1;
if((typeof days=="number")&&(days>=0)){
var d=new Date();
d.setTime(d.getTime()+(days*24*60*60*1000));
_808=d.toGMTString();
}
_803=escape(_803);
document.cookie=name+"="+_803+";"+(_808!=-1?" expires="+_808+";":"")+(path?"path="+path:"")+(_806?"; domain="+_806:"")+(_807?"; secure":"");
};
dojo.io.cookie.set=dojo.io.cookie.setCookie;
dojo.io.cookie.getCookie=function(name){
var idx=document.cookie.lastIndexOf(name+"=");
if(idx==-1){
return null;
}
var _80c=document.cookie.substring(idx+name.length+1);
var end=_80c.indexOf(";");
if(end==-1){
end=_80c.length;
}
_80c=_80c.substring(0,end);
_80c=unescape(_80c);
return _80c;
};
dojo.io.cookie.get=dojo.io.cookie.getCookie;
dojo.io.cookie.deleteCookie=function(name){
dojo.io.cookie.setCookie(name,"-",0);
};
dojo.io.cookie.setObjectCookie=function(name,obj,days,path,_813,_814,_815){
if(arguments.length==5){
_815=_813;
_813=null;
_814=null;
}
var _816=[],_817,_818="";
if(!_815){
_817=dojo.io.cookie.getObjectCookie(name);
}
if(days>=0){
if(!_817){
_817={};
}
for(var prop in obj){
if(obj[prop]==null){
delete _817[prop];
}else{
if((typeof obj[prop]=="string")||(typeof obj[prop]=="number")){
_817[prop]=obj[prop];
}
}
}
prop=null;
for(var prop in _817){
_816.push(escape(prop)+"="+escape(_817[prop]));
}
_818=_816.join("&");
}
dojo.io.cookie.setCookie(name,_818,days,path,_813,_814);
};
dojo.io.cookie.getObjectCookie=function(name){
var _81b=null,_81c=dojo.io.cookie.getCookie(name);
if(_81c){
_81b={};
var _81d=_81c.split("&");
for(var i=0;i<_81d.length;i++){
var pair=_81d[i].split("=");
var _820=pair[1];
if(isNaN(_820)){
_820=unescape(pair[1]);
}
_81b[unescape(pair[0])]=_820;
}
}
return _81b;
};
dojo.io.cookie.isSupported=function(){
if(typeof navigator.cookieEnabled!="boolean"){
dojo.io.cookie.setCookie("__TestingYourBrowserForCookieSupport__","CookiesAllowed",90,null);
var _821=dojo.io.cookie.getCookie("__TestingYourBrowserForCookieSupport__");
navigator.cookieEnabled=(_821=="CookiesAllowed");
if(navigator.cookieEnabled){
this.deleteCookie("__TestingYourBrowserForCookieSupport__");
}
}
return navigator.cookieEnabled;
};
if(!dojo.io.cookies){
dojo.io.cookies=dojo.io.cookie;
}
dojo.provide("dojo.io.*");
dojo.provide("dojo.widget.Toolbar");
dojo.widget.defineWidget("dojo.widget.ToolbarContainer",dojo.widget.HtmlWidget,{isContainer:true,templateString:"<div class=\"toolbarContainer\" dojoAttachPoint=\"containerNode\"></div>",templateCssPath:dojo.uri.dojoUri("src/widget/templates/Toolbar.css"),getItem:function(name){
if(name instanceof dojo.widget.ToolbarItem){
return name;
}
for(var i=0;i<this.children.length;i++){
var _824=this.children[i];
if(_824 instanceof dojo.widget.Toolbar){
var item=_824.getItem(name);
if(item){
return item;
}
}
}
return null;
},getItems:function(){
var _826=[];
for(var i=0;i<this.children.length;i++){
var _828=this.children[i];
if(_828 instanceof dojo.widget.Toolbar){
_826=_826.concat(_828.getItems());
}
}
return _826;
},enable:function(){
for(var i=0;i<this.children.length;i++){
var _82a=this.children[i];
if(_82a instanceof dojo.widget.Toolbar){
_82a.enable.apply(_82a,arguments);
}
}
},disable:function(){
for(var i=0;i<this.children.length;i++){
var _82c=this.children[i];
if(_82c instanceof dojo.widget.Toolbar){
_82c.disable.apply(_82c,arguments);
}
}
},select:function(name){
for(var i=0;i<this.children.length;i++){
var _82f=this.children[i];
if(_82f instanceof dojo.widget.Toolbar){
_82f.select(arguments);
}
}
},deselect:function(name){
for(var i=0;i<this.children.length;i++){
var _832=this.children[i];
if(_832 instanceof dojo.widget.Toolbar){
_832.deselect(arguments);
}
}
},getItemsState:function(){
var _833={};
for(var i=0;i<this.children.length;i++){
var _835=this.children[i];
if(_835 instanceof dojo.widget.Toolbar){
dojo.lang.mixin(_833,_835.getItemsState());
}
}
return _833;
},getItemsActiveState:function(){
var _836={};
for(var i=0;i<this.children.length;i++){
var _838=this.children[i];
if(_838 instanceof dojo.widget.Toolbar){
dojo.lang.mixin(_836,_838.getItemsActiveState());
}
}
return _836;
},getItemsSelectedState:function(){
var _839={};
for(var i=0;i<this.children.length;i++){
var _83b=this.children[i];
if(_83b instanceof dojo.widget.Toolbar){
dojo.lang.mixin(_839,_83b.getItemsSelectedState());
}
}
return _839;
}});
dojo.widget.defineWidget("dojo.widget.Toolbar",dojo.widget.HtmlWidget,{isContainer:true,templateString:"<div class=\"toolbar\" dojoAttachPoint=\"containerNode\" unselectable=\"on\" dojoOnMouseover=\"_onmouseover\" dojoOnMouseout=\"_onmouseout\" dojoOnClick=\"_onclick\" dojoOnMousedown=\"_onmousedown\" dojoOnMouseup=\"_onmouseup\"></div>",_getItem:function(node){
var _83d=new Date();
var _83e=null;
while(node&&node!=this.domNode){
if(dojo.html.hasClass(node,"toolbarItem")){
var _83f=dojo.widget.manager.getWidgetsByFilter(function(w){
return w.domNode==node;
});
if(_83f.length==1){
_83e=_83f[0];
break;
}else{
if(_83f.length>1){
dojo.raise("Toolbar._getItem: More than one widget matches the node");
}
}
}
node=node.parentNode;
}
return _83e;
},_onmouseover:function(e){
var _842=this._getItem(e.target);
if(_842&&_842._onmouseover){
_842._onmouseover(e);
}
},_onmouseout:function(e){
var _844=this._getItem(e.target);
if(_844&&_844._onmouseout){
_844._onmouseout(e);
}
},_onclick:function(e){
var _846=this._getItem(e.target);
if(_846&&_846._onclick){
_846._onclick(e);
}
},_onmousedown:function(e){
var _848=this._getItem(e.target);
if(_848&&_848._onmousedown){
_848._onmousedown(e);
}
},_onmouseup:function(e){
var _84a=this._getItem(e.target);
if(_84a&&_84a._onmouseup){
_84a._onmouseup(e);
}
},addChild:function(item,pos,_84d){
var _84e=dojo.widget.ToolbarItem.make(item,null,_84d);
var ret=dojo.widget.Toolbar.superclass.addChild.call(this,_84e,null,pos,null);
return ret;
},push:function(){
for(var i=0;i<arguments.length;i++){
this.addChild(arguments[i]);
}
},getItem:function(name){
if(name instanceof dojo.widget.ToolbarItem){
return name;
}
for(var i=0;i<this.children.length;i++){
var _853=this.children[i];
if(_853 instanceof dojo.widget.ToolbarItem&&_853._name==name){
return _853;
}
}
return null;
},getItems:function(){
var _854=[];
for(var i=0;i<this.children.length;i++){
var _856=this.children[i];
if(_856 instanceof dojo.widget.ToolbarItem){
_854.push(_856);
}
}
return _854;
},getItemsState:function(){
var _857={};
for(var i=0;i<this.children.length;i++){
var _859=this.children[i];
if(_859 instanceof dojo.widget.ToolbarItem){
_857[_859._name]={selected:_859._selected,enabled:!_859.disabled};
}
}
return _857;
},getItemsActiveState:function(){
var _85a=this.getItemsState();
for(var item in _85a){
_85a[item]=_85a[item].enabled;
}
return _85a;
},getItemsSelectedState:function(){
var _85c=this.getItemsState();
for(var item in _85c){
_85c[item]=_85c[item].selected;
}
return _85c;
},enable:function(){
var _85e=arguments.length?arguments:this.children;
for(var i=0;i<_85e.length;i++){
var _860=this.getItem(_85e[i]);
if(_860 instanceof dojo.widget.ToolbarItem){
_860.enable(false,true);
}
}
},disable:function(){
var _861=arguments.length?arguments:this.children;
for(var i=0;i<_861.length;i++){
var _863=this.getItem(_861[i]);
if(_863 instanceof dojo.widget.ToolbarItem){
_863.disable();
}
}
},select:function(){
for(var i=0;i<arguments.length;i++){
var name=arguments[i];
var item=this.getItem(name);
if(item){
item.select();
}
}
},deselect:function(){
for(var i=0;i<arguments.length;i++){
var name=arguments[i];
var item=this.getItem(name);
if(item){
item.disable();
}
}
},setValue:function(){
for(var i=0;i<arguments.length;i+=2){
var name=arguments[i],_86c=arguments[i+1];
var item=this.getItem(name);
if(item){
if(item instanceof dojo.widget.ToolbarItem){
item.setValue(_86c);
}
}
}
}});
dojo.widget.defineWidget("dojo.widget.ToolbarItem",dojo.widget.HtmlWidget,{templateString:"<span unselectable=\"on\" class=\"toolbarItem\"></span>",_name:null,getName:function(){
return this._name;
},setName:function(_86e){
return (this._name=_86e);
},getValue:function(){
return this.getName();
},setValue:function(_86f){
return this.setName(_86f);
},_selected:false,isSelected:function(){
return this._selected;
},setSelected:function(is,_871,_872){
if(!this._toggleItem&&!_871){
return;
}
is=Boolean(is);
if(_871||!this.disabled&&this._selected!=is){
this._selected=is;
this.update();
if(!_872){
this._fireEvent(is?"onSelect":"onDeselect");
this._fireEvent("onChangeSelect");
}
}
},select:function(_873,_874){
return this.setSelected(true,_873,_874);
},deselect:function(_875,_876){
return this.setSelected(false,_875,_876);
},_toggleItem:false,isToggleItem:function(){
return this._toggleItem;
},setToggleItem:function(_877){
this._toggleItem=Boolean(_877);
},toggleSelected:function(_878){
return this.setSelected(!this._selected,_878);
},isEnabled:function(){
return !this.disabled;
},setEnabled:function(is,_87a,_87b){
is=Boolean(is);
if(_87a||this.disabled==is){
this.disabled=!is;
this.update();
if(!_87b){
this._fireEvent(this.disabled?"onDisable":"onEnable");
this._fireEvent("onChangeEnabled");
}
}
return !this.disabled;
},enable:function(_87c,_87d){
return this.setEnabled(true,_87c,_87d);
},disable:function(_87e,_87f){
return this.setEnabled(false,_87e,_87f);
},toggleEnabled:function(_880,_881){
return this.setEnabled(this.disabled,_880,_881);
},_icon:null,getIcon:function(){
return this._icon;
},setIcon:function(_882){
var icon=dojo.widget.Icon.make(_882);
if(this._icon){
this._icon.setIcon(icon);
}else{
this._icon=icon;
}
var _884=this._icon.getNode();
if(_884.parentNode!=this.domNode){
if(this.domNode.hasChildNodes()){
this.domNode.insertBefore(_884,this.domNode.firstChild);
}else{
this.domNode.appendChild(_884);
}
}
return this._icon;
},_label:"",getLabel:function(){
return this._label;
},setLabel:function(_885){
var ret=(this._label=_885);
if(!this.labelNode){
this.labelNode=document.createElement("span");
this.domNode.appendChild(this.labelNode);
}
this.labelNode.innerHTML="";
this.labelNode.appendChild(document.createTextNode(this._label));
this.update();
return ret;
},update:function(){
if(this.disabled){
this._selected=false;
dojo.html.addClass(this.domNode,"disabled");
dojo.html.removeClass(this.domNode,"down");
dojo.html.removeClass(this.domNode,"hover");
}else{
dojo.html.removeClass(this.domNode,"disabled");
if(this._selected){
dojo.html.addClass(this.domNode,"selected");
}else{
dojo.html.removeClass(this.domNode,"selected");
}
}
this._updateIcon();
},_updateIcon:function(){
if(this._icon){
if(this.disabled){
this._icon.disable();
}else{
if(this._cssHover){
this._icon.hover();
}else{
if(this._selected){
this._icon.select();
}else{
this._icon.enable();
}
}
}
}
},_fireEvent:function(evt){
if(typeof this[evt]=="function"){
var args=[this];
for(var i=1;i<arguments.length;i++){
args.push(arguments[i]);
}
this[evt].apply(this,args);
}
},_onmouseover:function(e){
if(this.disabled){
return;
}
dojo.html.addClass(this.domNode,"hover");
this._fireEvent("onMouseOver");
},_onmouseout:function(e){
dojo.html.removeClass(this.domNode,"hover");
dojo.html.removeClass(this.domNode,"down");
if(!this._selected){
dojo.html.removeClass(this.domNode,"selected");
}
this._fireEvent("onMouseOut");
},_onclick:function(e){
if(!this.disabled&&!this._toggleItem){
this._fireEvent("onClick");
}
},_onmousedown:function(e){
if(e.preventDefault){
e.preventDefault();
}
if(this.disabled){
return;
}
dojo.html.addClass(this.domNode,"down");
if(this._toggleItem){
if(this.parent.preventDeselect&&this._selected){
return;
}
this.toggleSelected();
}
this._fireEvent("onMouseDown");
},_onmouseup:function(e){
dojo.html.removeClass(this.domNode,"down");
this._fireEvent("onMouseUp");
},onClick:function(){
},onMouseOver:function(){
},onMouseOut:function(){
},onMouseDown:function(){
},onMouseUp:function(){
},fillInTemplate:function(args,frag){
if(args.name){
this._name=args.name;
}
if(args.selected){
this.select();
}
if(args.disabled){
this.disable();
}
if(args.label){
this.setLabel(args.label);
}
if(args.icon){
this.setIcon(args.icon);
}
if(args.toggleitem||args.toggleItem){
this.setToggleItem(true);
}
}});
dojo.widget.ToolbarItem.make=function(wh,_892,_893){
var item=null;
if(wh instanceof Array){
item=dojo.widget.createWidget("ToolbarButtonGroup",_893);
item.setName(wh[0]);
for(var i=1;i<wh.length;i++){
item.addChild(wh[i]);
}
}else{
if(wh instanceof dojo.widget.ToolbarItem){
item=wh;
}else{
if(wh instanceof dojo.uri.Uri){
item=dojo.widget.createWidget("ToolbarButton",dojo.lang.mixin(_893||{},{icon:new dojo.widget.Icon(wh.toString())}));
}else{
if(_892){
item=dojo.widget.createWidget(wh,_893);
}else{
if(typeof wh=="string"||wh instanceof String){
switch(wh.charAt(0)){
case "|":
case "-":
case "/":
item=dojo.widget.createWidget("ToolbarSeparator",_893);
break;
case " ":
if(wh.length==1){
item=dojo.widget.createWidget("ToolbarSpace",_893);
}else{
item=dojo.widget.createWidget("ToolbarFlexibleSpace",_893);
}
break;
default:
if(/\.(gif|jpg|jpeg|png)$/i.test(wh)){
item=dojo.widget.createWidget("ToolbarButton",dojo.lang.mixin(_893||{},{icon:new dojo.widget.Icon(wh.toString())}));
}else{
item=dojo.widget.createWidget("ToolbarButton",dojo.lang.mixin(_893||{},{label:wh.toString()}));
}
}
}else{
if(wh&&wh.tagName&&/^img$/i.test(wh.tagName)){
item=dojo.widget.createWidget("ToolbarButton",dojo.lang.mixin(_893||{},{icon:wh}));
}else{
item=dojo.widget.createWidget("ToolbarButton",dojo.lang.mixin(_893||{},{label:wh.toString()}));
}
}
}
}
}
}
return item;
};
dojo.widget.defineWidget("dojo.widget.ToolbarButtonGroup",dojo.widget.ToolbarItem,{isContainer:true,templateString:"<span unselectable=\"on\" class=\"toolbarButtonGroup\" dojoAttachPoint=\"containerNode\"></span>",defaultButton:"",postCreate:function(){
for(var i=0;i<this.children.length;i++){
this._injectChild(this.children[i]);
}
},addChild:function(item,pos,_899){
var _89a=dojo.widget.ToolbarItem.make(item,null,dojo.lang.mixin(_899||{},{toggleItem:true}));
var ret=dojo.widget.ToolbarButtonGroup.superclass.addChild.call(this,_89a,null,pos,null);
this._injectChild(_89a);
return ret;
},_injectChild:function(_89c){
dojo.event.connect(_89c,"onSelect",this,"onChildSelected");
dojo.event.connect(_89c,"onDeselect",this,"onChildDeSelected");
if(_89c._name==this.defaultButton||(typeof this.defaultButton=="number"&&this.children.length-1==this.defaultButton)){
_89c.select(false,true);
}
},getItem:function(name){
if(name instanceof dojo.widget.ToolbarItem){
return name;
}
for(var i=0;i<this.children.length;i++){
var _89f=this.children[i];
if(_89f instanceof dojo.widget.ToolbarItem&&_89f._name==name){
return _89f;
}
}
return null;
},getItems:function(){
var _8a0=[];
for(var i=0;i<this.children.length;i++){
var _8a2=this.children[i];
if(_8a2 instanceof dojo.widget.ToolbarItem){
_8a0.push(_8a2);
}
}
return _8a0;
},onChildSelected:function(e){
this.select(e._name);
},onChildDeSelected:function(e){
this._fireEvent("onChangeSelect",this._value);
},enable:function(_8a5,_8a6){
for(var i=0;i<this.children.length;i++){
var _8a8=this.children[i];
if(_8a8 instanceof dojo.widget.ToolbarItem){
_8a8.enable(_8a5,_8a6);
if(_8a8._name==this._value){
_8a8.select(_8a5,_8a6);
}
}
}
},disable:function(_8a9,_8aa){
for(var i=0;i<this.children.length;i++){
var _8ac=this.children[i];
if(_8ac instanceof dojo.widget.ToolbarItem){
_8ac.disable(_8a9,_8aa);
}
}
},_value:"",getValue:function(){
return this._value;
},select:function(name,_8ae,_8af){
for(var i=0;i<this.children.length;i++){
var _8b1=this.children[i];
if(_8b1 instanceof dojo.widget.ToolbarItem){
if(_8b1._name==name){
_8b1.select(_8ae,_8af);
this._value=name;
}else{
_8b1.deselect(true,true);
}
}
}
if(!_8af){
this._fireEvent("onSelect",this._value);
this._fireEvent("onChangeSelect",this._value);
}
},setValue:this.select,preventDeselect:false});
dojo.widget.defineWidget("dojo.widget.ToolbarButton",dojo.widget.ToolbarItem,{fillInTemplate:function(args,frag){
dojo.widget.ToolbarButton.superclass.fillInTemplate.call(this,args,frag);
dojo.html.addClass(this.domNode,"toolbarButton");
if(this._icon){
this.setIcon(this._icon);
}
if(this._label){
this.setLabel(this._label);
}
if(!this._name){
if(this._label){
this.setName(this._label);
}else{
if(this._icon){
var src=this._icon.getSrc("enabled").match(/[\/^]([^\.\/]+)\.(gif|jpg|jpeg|png)$/i);
if(src){
this.setName(src[1]);
}
}else{
this._name=this._widgetId;
}
}
}
}});
dojo.widget.defineWidget("dojo.widget.ToolbarDialog",dojo.widget.ToolbarButton,{fillInTemplate:function(args,frag){
dojo.widget.ToolbarDialog.superclass.fillInTemplate.call(this,args,frag);
dojo.event.connect(this,"onSelect",this,"showDialog");
dojo.event.connect(this,"onDeselect",this,"hideDialog");
},showDialog:function(e){
dojo.lang.setTimeout(dojo.event.connect,1,document,"onmousedown",this,"deselect");
},hideDialog:function(e){
dojo.event.disconnect(document,"onmousedown",this,"deselect");
}});
dojo.widget.defineWidget("dojo.widget.ToolbarMenu",dojo.widget.ToolbarDialog,{});
dojo.widget.ToolbarMenuItem=function(){
};
dojo.widget.defineWidget("dojo.widget.ToolbarSeparator",dojo.widget.ToolbarItem,{templateString:"<span unselectable=\"on\" class=\"toolbarItem toolbarSeparator\"></span>",defaultIconPath:new dojo.uri.dojoUri("src/widget/templates/buttons/sep.gif"),fillInTemplate:function(args,frag,skip){
dojo.widget.ToolbarSeparator.superclass.fillInTemplate.call(this,args,frag);
this._name=this.widgetId;
if(!skip){
if(!this._icon){
this.setIcon(this.defaultIconPath);
}
this.domNode.appendChild(this._icon.getNode());
}
},_onmouseover:null,_onmouseout:null,_onclick:null,_onmousedown:null,_onmouseup:null});
dojo.widget.defineWidget("dojo.widget.ToolbarSpace",dojo.widget.ToolbarSeparator,{fillInTemplate:function(args,frag,skip){
dojo.widget.ToolbarSpace.superclass.fillInTemplate.call(this,args,frag,true);
if(!skip){
dojo.html.addClass(this.domNode,"toolbarSpace");
}
}});
dojo.widget.defineWidget("dojo.widget.ToolbarSelect",dojo.widget.ToolbarItem,{templateString:"<span class=\"toolbarItem toolbarSelect\" unselectable=\"on\"><select dojoAttachPoint=\"selectBox\" dojoOnChange=\"changed\"></select></span>",fillInTemplate:function(args,frag){
dojo.widget.ToolbarSelect.superclass.fillInTemplate.call(this,args,frag,true);
var keys=args.values;
var i=0;
for(var val in keys){
var opt=document.createElement("option");
opt.setAttribute("value",keys[val]);
opt.innerHTML=val;
this.selectBox.appendChild(opt);
}
},changed:function(e){
this._fireEvent("onSetValue",this.selectBox.value);
},setEnabled:function(is,_8c7,_8c8){
var ret=dojo.widget.ToolbarSelect.superclass.setEnabled.call(this,is,_8c7,_8c8);
this.selectBox.disabled=this.disabled;
return ret;
},_onmouseover:null,_onmouseout:null,_onclick:null,_onmousedown:null,_onmouseup:null});
dojo.widget.Icon=function(_8ca,_8cb,_8cc,_8cd){
if(!arguments.length){
throw new Error("Icon must have at least an enabled state");
}
var _8ce=["enabled","disabled","hovered","selected"];
var _8cf="enabled";
var _8d0=document.createElement("img");
this.getState=function(){
return _8cf;
};
this.setState=function(_8d1){
if(dojo.lang.inArray(_8ce,_8d1)){
if(this[_8d1]){
_8cf=_8d1;
var img=this[_8cf];
if((dojo.render.html.ie55||dojo.render.html.ie60)&&img.src&&img.src.match(/[.]png$/i)){
_8d0.width=img.width||img.offsetWidth;
_8d0.height=img.height||img.offsetHeight;
_8d0.setAttribute("src",dojo.uri.dojoUri("src/widget/templates/images/blank.gif").uri);
_8d0.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+img.src+"',sizingMethod='image')";
}else{
_8d0.setAttribute("src",img.src);
}
}
}else{
throw new Error("Invalid state set on Icon (state: "+_8d1+")");
}
};
this.setSrc=function(_8d3,_8d4){
if(/^img$/i.test(_8d4.tagName)){
this[_8d3]=_8d4;
}else{
if(typeof _8d4=="string"||_8d4 instanceof String||_8d4 instanceof dojo.uri.Uri){
this[_8d3]=new Image();
this[_8d3].src=_8d4.toString();
}
}
return this[_8d3];
};
this.setIcon=function(icon){
for(var i=0;i<_8ce.length;i++){
if(icon[_8ce[i]]){
this.setSrc(_8ce[i],icon[_8ce[i]]);
}
}
this.update();
};
this.enable=function(){
this.setState("enabled");
};
this.disable=function(){
this.setState("disabled");
};
this.hover=function(){
this.setState("hovered");
};
this.select=function(){
this.setState("selected");
};
this.getSize=function(){
return {width:_8d0.width||_8d0.offsetWidth,height:_8d0.height||_8d0.offsetHeight};
};
this.setSize=function(w,h){
_8d0.width=w;
_8d0.height=h;
return {width:w,height:h};
};
this.getNode=function(){
return _8d0;
};
this.getSrc=function(_8d9){
if(_8d9){
return this[_8d9].src;
}
return _8d0.src||"";
};
this.update=function(){
this.setState(_8cf);
};
for(var i=0;i<_8ce.length;i++){
var arg=arguments[i];
var _8dc=_8ce[i];
this[_8dc]=null;
if(!arg){
continue;
}
this.setSrc(_8dc,arg);
}
this.enable();
};
dojo.widget.Icon.make=function(a,b,c,d){
for(var i=0;i<arguments.length;i++){
if(arguments[i] instanceof dojo.widget.Icon){
return arguments[i];
}
}
return new dojo.widget.Icon(a,b,c,d);
};
dojo.widget.defineWidget("dojo.widget.ToolbarColorDialog",dojo.widget.ToolbarDialog,{palette:"7x10",fillInTemplate:function(args,frag){
dojo.widget.ToolbarColorDialog.superclass.fillInTemplate.call(this,args,frag);
this.dialog=dojo.widget.createWidget("ColorPalette",{palette:this.palette});
this.dialog.domNode.style.position="absolute";
dojo.event.connect(this.dialog,"onColorSelect",this,"_setValue");
},_setValue:function(_8e4){
this._value=_8e4;
this._fireEvent("onSetValue",_8e4);
},showDialog:function(e){
dojo.widget.ToolbarColorDialog.superclass.showDialog.call(this,e);
var abs=dojo.html.getAbsolutePosition(this.domNode,true);
var y=abs.y+dojo.html.getBorderBox(this.domNode).height;
this.dialog.showAt(abs.x,y);
},hideDialog:function(e){
dojo.widget.ToolbarColorDialog.superclass.hideDialog.call(this,e);
this.dialog.hide();
}});
dojo.provide("dojo.html.*");
dojo.provide("dojo.html.selection");
dojo.html.selectionType={NONE:0,TEXT:1,CONTROL:2};
dojo.html.clearSelection=function(){
var _8e9=dojo.global();
var _8ea=dojo.doc();
try{
if(_8e9["getSelection"]){
if(dojo.render.html.safari){
_8e9.getSelection().collapse();
}else{
_8e9.getSelection().removeAllRanges();
}
}else{
if(_8ea.selection){
if(_8ea.selection.empty){
_8ea.selection.empty();
}else{
if(_8ea.selection.clear){
_8ea.selection.clear();
}
}
}
}
return true;
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.html.disableSelection=function(_8eb){
_8eb=dojo.byId(_8eb)||dojo.body();
var h=dojo.render.html;
if(h.mozilla){
_8eb.style.MozUserSelect="none";
}else{
if(h.safari){
_8eb.style.KhtmlUserSelect="none";
}else{
if(h.ie){
_8eb.unselectable="on";
}else{
return false;
}
}
}
return true;
};
dojo.html.enableSelection=function(_8ed){
_8ed=dojo.byId(_8ed)||dojo.body();
var h=dojo.render.html;
if(h.mozilla){
_8ed.style.MozUserSelect="";
}else{
if(h.safari){
_8ed.style.KhtmlUserSelect="";
}else{
if(h.ie){
_8ed.unselectable="off";
}else{
return false;
}
}
}
return true;
};
dojo.html.selectInputText=function(_8ef){
var _8f0=dojo.global();
var _8f1=dojo.doc();
_8ef=dojo.byId(_8ef);
if(_8f1["selection"]&&dojo.body()["createTextRange"]){
var _8f2=_8ef.createTextRange();
_8f2.moveStart("character",0);
_8f2.moveEnd("character",_8ef.value.length);
_8f2.select();
}else{
if(_8f0["getSelection"]){
var _8f3=_8f0.getSelection();
_8ef.setSelectionRange(0,_8ef.value.length);
}
}
_8ef.focus();
};
dojo.lang.mixin(dojo.html.selection,{getType:function(){
if(dojo.doc()["selection"]){
return dojo.html.selectionType[dojo.doc().selection.type.toUpperCase()];
}else{
var _8f4=dojo.html.selectionType.TEXT;
var oSel;
try{
oSel=dojo.global().getSelection();
}
catch(e){
}
if(oSel&&oSel.rangeCount==1){
var _8f6=oSel.getRangeAt(0);
if(_8f6.startContainer==_8f6.endContainer&&(_8f6.endOffset-_8f6.startOffset)==1&&_8f6.startContainer.nodeType!=dojo.dom.TEXT_NODE){
_8f4=dojo.html.selectionType.CONTROL;
}
}
return _8f4;
}
},isCollapsed:function(){
var _8f7=dojo.global();
var _8f8=dojo.doc();
if(_8f8["selection"]){
return _8f8.selection.createRange().text=="";
}else{
if(_8f7["getSelection"]){
var _8f9=_8f7.getSelection();
if(dojo.lang.isString(_8f9)){
return _8f9=="";
}else{
return _8f9.isCollapsed||_8f9.toString()=="";
}
}
}
},getSelectedElement:function(){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
if(dojo.doc()["selection"]){
var _8fa=dojo.doc().selection.createRange();
if(_8fa&&_8fa.item){
return dojo.doc().selection.createRange().item(0);
}
}else{
var _8fb=dojo.global().getSelection();
return _8fb.anchorNode.childNodes[_8fb.anchorOffset];
}
}
},getParentElement:function(){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
var p=dojo.html.selection.getSelectedElement();
if(p){
return p.parentNode;
}
}else{
if(dojo.doc()["selection"]){
return dojo.doc().selection.createRange().parentElement();
}else{
var _8fd=dojo.global().getSelection();
if(_8fd){
var node=_8fd.anchorNode;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.parentNode;
}
return node;
}
}
}
},getSelectedText:function(){
if(dojo.doc()["selection"]){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
return null;
}
return dojo.doc().selection.createRange().text;
}else{
var _8ff=dojo.global().getSelection();
if(_8ff){
return _8ff.toString();
}
}
},getSelectedHtml:function(){
if(dojo.doc()["selection"]){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
return null;
}
return dojo.doc().selection.createRange().htmlText;
}else{
var _900=dojo.global().getSelection();
if(_900&&_900.rangeCount){
var frag=_900.getRangeAt(0).cloneContents();
var div=document.createElement("div");
div.appendChild(frag);
return div.innerHTML;
}
return null;
}
},hasAncestorElement:function(_903){
return (dojo.html.selection.getAncestorElement.apply(this,arguments)!=null);
},getAncestorElement:function(_904){
var node=dojo.html.selection.getSelectedElement()||dojo.html.selection.getParentElement();
while(node){
if(dojo.html.selection.isTag(node,arguments).length>0){
return node;
}
node=node.parentNode;
}
return null;
},isTag:function(node,tags){
if(node&&node.tagName){
for(var i=0;i<tags.length;i++){
if(node.tagName.toLowerCase()==String(tags[i]).toLowerCase()){
return String(tags[i]).toLowerCase();
}
}
}
return "";
},selectElement:function(_909){
var _90a=dojo.global();
var _90b=dojo.doc();
_909=dojo.byId(_909);
if(_90b.selection&&dojo.body().createTextRange){
try{
var _90c=dojo.body().createControlRange();
_90c.addElement(_909);
_90c.select();
}
catch(e){
dojo.html.selection.selectElementChildren(_909);
}
}else{
if(_90a["getSelection"]){
var _90d=_90a.getSelection();
if(_90d["removeAllRanges"]){
var _90c=_90b.createRange();
_90c.selectNode(_909);
_90d.removeAllRanges();
_90d.addRange(_90c);
}
}
}
},selectElementChildren:function(_90e){
var _90f=dojo.global();
var _910=dojo.doc();
_90e=dojo.byId(_90e);
if(_910.selection&&dojo.body().createTextRange){
var _911=dojo.body().createTextRange();
_911.moveToElementText(_90e);
_911.select();
}else{
if(_90f["getSelection"]){
var _912=_90f.getSelection();
if(_912["setBaseAndExtent"]){
_912.setBaseAndExtent(_90e,0,_90e,_90e.innerText.length-1);
}else{
if(_912["selectAllChildren"]){
_912.selectAllChildren(_90e);
}
}
}
}
},getBookmark:function(){
var _913;
var _914=dojo.doc();
if(_914["selection"]){
var _915=_914.selection.createRange();
_913=_915.getBookmark();
}else{
var _916;
try{
_916=dojo.global().getSelection();
}
catch(e){
}
if(_916){
var _915=_916.getRangeAt(0);
_913=_915.cloneRange();
}else{
dojo.debug("No idea how to store the current selection for this browser!");
}
}
return _913;
},moveToBookmark:function(_917){
var _918=dojo.doc();
if(_918["selection"]){
var _919=_918.selection.createRange();
_919.moveToBookmark(_917);
_919.select();
}else{
var _91a;
try{
_91a=dojo.global().getSelection();
}
catch(e){
}
if(_91a&&_91a["removeAllRanges"]){
_91a.removeAllRanges();
_91a.addRange(_917);
}else{
dojo.debug("No idea how to restore selection for this browser!");
}
}
},collapse:function(_91b){
if(dojo.global()["getSelection"]){
var _91c=dojo.global().getSelection();
if(_91c.removeAllRanges){
if(_91b){
_91c.collapseToStart();
}else{
_91c.collapseToEnd();
}
}else{
dojo.global().getSelection().collapse(_91b);
}
}else{
if(dojo.doc().selection){
var _91d=dojo.doc().selection.createRange();
_91d.collapse(_91b);
_91d.select();
}
}
},remove:function(){
if(dojo.doc().selection){
var _91e=dojo.doc().selection;
if(_91e.type.toUpperCase()!="NONE"){
_91e.clear();
}
return _91e;
}else{
var _91e=dojo.global().getSelection();
for(var i=0;i<_91e.rangeCount;i++){
_91e.getRangeAt(i).deleteContents();
}
return _91e;
}
}});
dojo.provide("dojo.Deferred");
dojo.Deferred=function(_920){
this.chain=[];
this.id=this._nextId();
this.fired=-1;
this.paused=0;
this.results=[null,null];
this.canceller=_920;
this.silentlyCancelled=false;
};
dojo.lang.extend(dojo.Deferred,{getFunctionFromArgs:function(){
var a=arguments;
if((a[0])&&(!a[1])){
if(dojo.lang.isFunction(a[0])){
return a[0];
}else{
if(dojo.lang.isString(a[0])){
return dj_global[a[0]];
}
}
}else{
if((a[0])&&(a[1])){
return dojo.lang.hitch(a[0],a[1]);
}
}
return null;
},makeCalled:function(){
var _922=new dojo.Deferred();
_922.callback();
return _922;
},repr:function(){
var _923;
if(this.fired==-1){
_923="unfired";
}else{
if(this.fired==0){
_923="success";
}else{
_923="error";
}
}
return "Deferred("+this.id+", "+_923+")";
},toString:dojo.lang.forward("repr"),_nextId:(function(){
var n=1;
return function(){
return n++;
};
})(),cancel:function(){
if(this.fired==-1){
if(this.canceller){
this.canceller(this);
}else{
this.silentlyCancelled=true;
}
if(this.fired==-1){
this.errback(new Error(this.repr()));
}
}else{
if((this.fired==0)&&(this.results[0] instanceof dojo.Deferred)){
this.results[0].cancel();
}
}
},_pause:function(){
this.paused++;
},_unpause:function(){
this.paused--;
if((this.paused==0)&&(this.fired>=0)){
this._fire();
}
},_continue:function(res){
this._resback(res);
this._unpause();
},_resback:function(res){
this.fired=((res instanceof Error)?1:0);
this.results[this.fired]=res;
this._fire();
},_check:function(){
if(this.fired!=-1){
if(!this.silentlyCancelled){
dojo.raise("already called!");
}
this.silentlyCancelled=false;
return;
}
},callback:function(res){
this._check();
this._resback(res);
},errback:function(res){
this._check();
if(!(res instanceof Error)){
res=new Error(res);
}
this._resback(res);
},addBoth:function(cb,cbfn){
var _92b=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_92b=dojo.lang.curryArguments(null,_92b,arguments,2);
}
return this.addCallbacks(_92b,_92b);
},addCallback:function(cb,cbfn){
var _92e=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_92e=dojo.lang.curryArguments(null,_92e,arguments,2);
}
return this.addCallbacks(_92e,null);
},addErrback:function(cb,cbfn){
var _931=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_931=dojo.lang.curryArguments(null,_931,arguments,2);
}
return this.addCallbacks(null,_931);
return this.addCallbacks(null,cbfn);
},addCallbacks:function(cb,eb){
this.chain.push([cb,eb]);
if(this.fired>=0){
this._fire();
}
return this;
},_fire:function(){
var _934=this.chain;
var _935=this.fired;
var res=this.results[_935];
var self=this;
var cb=null;
while(_934.length>0&&this.paused==0){
var pair=_934.shift();
var f=pair[_935];
if(f==null){
continue;
}
try{
res=f(res);
_935=((res instanceof Error)?1:0);
if(res instanceof dojo.Deferred){
cb=function(res){
self._continue(res);
};
this._pause();
}
}
catch(err){
_935=1;
res=err;
}
}
this.fired=_935;
this.results[_935]=res;
if((cb)&&(this.paused)){
res.addBoth(cb);
}
}});
dojo.provide("dojo.widget.RichText");
if(dojo.hostenv.post_load_){
(function(){
var _93c=dojo.doc().createElement("textarea");
_93c.id="dojo.widget.RichText.savedContent";
_93c.style="display:none;position:absolute;top:-100px;left:-100px;height:3px;width:3px;overflow:hidden;";
dojo.body().appendChild(_93c);
})();
}else{
try{
dojo.doc().write("<textarea id=\"dojo.widget.RichText.savedContent\" "+"style=\"display:none;position:absolute;top:-100px;left:-100px;height:3px;width:3px;overflow:hidden;\"></textarea>");
}
catch(e){
}
}
dojo.widget.defineWidget("dojo.widget.RichText",dojo.widget.HtmlWidget,function(){
this.contentPreFilters=[];
this.contentPostFilters=[];
this.contentDomPreFilters=[];
this.contentDomPostFilters=[];
this.editingAreaStyleSheets=[];
if(dojo.render.html.moz){
this.contentPreFilters.push(this._fixContentForMoz);
}
this._keyHandlers={};
if(dojo.Deferred){
this.onLoadDeferred=new dojo.Deferred();
}
},{inheritWidth:false,focusOnLoad:false,saveName:"",styleSheets:"",_content:"",height:"",minHeight:"1em",isClosed:true,isLoaded:false,useActiveX:false,relativeImageUrls:false,_SEPARATOR:"@@**%%__RICHTEXTBOUNDRY__%%**@@",onLoadDeferred:null,fillInTemplate:function(){
dojo.event.topic.publish("dojo.widget.RichText::init",this);
this.open();
dojo.event.connect(this,"onKeyPressed",this,"afterKeyPress");
dojo.event.connect(this,"onKeyPress",this,"keyPress");
dojo.event.connect(this,"onKeyDown",this,"keyDown");
dojo.event.connect(this,"onKeyUp",this,"keyUp");
this.setupDefaultShortcuts();
},setupDefaultShortcuts:function(){
var ctrl=this.KEY_CTRL;
var exec=function(cmd,arg){
return arguments.length==1?function(){
this.execCommand(cmd);
}:function(){
this.execCommand(cmd,arg);
};
};
this.addKeyHandler("b",ctrl,exec("bold"));
this.addKeyHandler("i",ctrl,exec("italic"));
this.addKeyHandler("u",ctrl,exec("underline"));
this.addKeyHandler("a",ctrl,exec("selectall"));
this.addKeyHandler("s",ctrl,function(){
this.save(true);
});
this.addKeyHandler("1",ctrl,exec("formatblock","h1"));
this.addKeyHandler("2",ctrl,exec("formatblock","h2"));
this.addKeyHandler("3",ctrl,exec("formatblock","h3"));
this.addKeyHandler("4",ctrl,exec("formatblock","h4"));
this.addKeyHandler("\\",ctrl,exec("insertunorderedlist"));
if(!dojo.render.html.ie){
this.addKeyHandler("Z",ctrl,exec("redo"));
}
},events:["onBlur","onFocus","onKeyPress","onKeyDown","onKeyUp","onClick"],open:function(_941){
if(this.onLoadDeferred.fired>=0){
this.onLoadDeferred=new dojo.Deferred();
}
var h=dojo.render.html;
if(!this.isClosed){
this.close();
}
dojo.event.topic.publish("dojo.widget.RichText::open",this);
this._content="";
if((arguments.length==1)&&(_941["nodeName"])){
this.domNode=_941;
}
if((this.domNode["nodeName"])&&(this.domNode.nodeName.toLowerCase()=="textarea")){
this.textarea=this.domNode;
var html=dojo.string.trim(this.textarea.value);
this.domNode=dojo.doc().createElement("div");
dojo.html.copyStyle(this.domNode,this.textarea);
var _944=dojo.lang.hitch(this,function(){
with(this.textarea.style){
display="block";
position="absolute";
left=top="-1000px";
if(h.ie){
this.__overflow=overflow;
overflow="hidden";
}
}
});
if(h.ie){
setTimeout(_944,10);
}else{
_944();
}
if(!h.safari){
dojo.html.insertBefore(this.domNode,this.textarea);
}
if(this.textarea.form){
dojo.event.connect("before",this.textarea.form,"onsubmit",dojo.lang.hitch(this,function(){
this.textarea.value=this.getEditorContent();
}));
}
var _945=this;
dojo.event.connect(this,"postCreate",function(){
dojo.html.insertAfter(_945.textarea,_945.domNode);
});
}else{
var html=this._preFilterContent(dojo.string.trim(this.domNode.innerHTML));
}
if(html==""){
html="&nbsp;";
}
var _946=dojo.html.getContentBox(this.domNode);
this._oldHeight=_946.height;
this._oldWidth=_946.width;
this._firstChildContributingMargin=this._getContributingMargin(this.domNode,"top");
this._lastChildContributingMargin=this._getContributingMargin(this.domNode,"bottom");
this.savedContent=this.domNode.innerHTML;
this.domNode.innerHTML="";
if((this.domNode["nodeName"])&&(this.domNode.nodeName=="LI")){
this.domNode.innerHTML=" <br>";
}
this.editingArea=dojo.doc().createElement("div");
this.domNode.appendChild(this.editingArea);
if(this.saveName!=""){
var _947=dojo.doc().getElementById("dojo.widget.RichText.savedContent");
if(_947.value!=""){
var _948=_947.value.split(this._SEPARATOR);
for(var i=0;i<_948.length;i++){
var data=_948[i].split(":");
if(data[0]==this.saveName){
html=data[1];
_948.splice(i,1);
break;
}
}
}
dojo.event.connect("before",window,"onunload",this,"_saveContent");
}
if(h.ie70&&this.useActiveX){
dojo.debug("activeX in ie70 is not currently supported, useActiveX is ignored for now.");
this.useActiveX=false;
}
if(this.useActiveX&&h.ie){
var self=this;
setTimeout(function(){
self._drawObject(html);
},0);
}else{
if(h.ie||this._safariIsLeopard()||h.opera){
this.iframe=dojo.doc().createElement("iframe");
this.iframe.src="javascript:void(0)";
this.editorObject=this.iframe;
with(this.iframe.style){
border="0";
width="100%";
}
this.iframe.frameBorder=0;
this.editingArea.appendChild(this.iframe);
this.window=this.iframe.contentWindow;
this.document=this.window.document;
this.document.open();
this.document.write("<html><head><style>body{margin:0;padding:0;border:0;overflow:hidden;}</style></head><body><div></div></body></html>");
this.document.close();
this.editNode=this.document.body.firstChild;
this.editNode.contentEditable=true;
with(this.iframe.style){
if(h.ie70){
if(this.height){
height=this.height;
}
if(this.minHeight){
minHeight=this.minHeight;
}
}else{
height=this.height?this.height:this.minHeight;
}
}
var _94c=["p","pre","address","h1","h2","h3","h4","h5","h6","ol","div","ul"];
var _94d="";
for(var i in _94c){
if(_94c[i].charAt(1)!="l"){
_94d+="<"+_94c[i]+"><span>content</span></"+_94c[i]+">";
}else{
_94d+="<"+_94c[i]+"><li>content</li></"+_94c[i]+">";
}
}
with(this.editNode.style){
position="absolute";
left="-2000px";
top="-2000px";
}
this.editNode.innerHTML=_94d;
var node=this.editNode.firstChild;
while(node){
dojo.withGlobal(this.window,"selectElement",dojo.html.selection,[node.firstChild]);
var _94f=node.tagName.toLowerCase();
this._local2NativeFormatNames[_94f]=this.queryCommandValue("formatblock");
this._native2LocalFormatNames[this._local2NativeFormatNames[_94f]]=_94f;
node=node.nextSibling;
}
with(this.editNode.style){
position="";
left="";
top="";
}
this.editNode.innerHTML=html;
if(this.height){
this.document.body.style.overflowY="scroll";
}
dojo.lang.forEach(this.events,function(e){
dojo.event.connect(this.editNode,e.toLowerCase(),this,e);
},this);
this.onLoad();
}else{
this._drawIframe(html);
this.editorObject=this.iframe;
}
}
if(this.domNode.nodeName=="LI"){
this.domNode.lastChild.style.marginTop="-1.2em";
}
dojo.html.addClass(this.domNode,"RichTextEditable");
this.isClosed=false;
},_hasCollapseableMargin:function(_951,side){
if(dojo.html.getPixelValue(_951,"border-"+side+"-width",false)){
return false;
}else{
if(dojo.html.getPixelValue(_951,"padding-"+side,false)){
return false;
}else{
return true;
}
}
},_getContributingMargin:function(_953,_954){
if(_954=="top"){
var _955="previousSibling";
var _956="nextSibling";
var _957="firstChild";
var _958="margin-top";
var _959="margin-bottom";
}else{
var _955="nextSibling";
var _956="previousSibling";
var _957="lastChild";
var _958="margin-bottom";
var _959="margin-top";
}
var _95a=dojo.html.getPixelValue(_953,_958,false);
function isSignificantNode(_95b){
return !(_95b.nodeType==3&&dojo.string.isBlank(_95b.data))&&dojo.html.getStyle(_95b,"display")!="none"&&!dojo.html.isPositionAbsolute(_95b);
}
var _95c=0;
var _95d=_953[_957];
while(_95d){
while((!isSignificantNode(_95d))&&_95d[_956]){
_95d=_95d[_956];
}
_95c=Math.max(_95c,dojo.html.getPixelValue(_95d,_958,false));
if(!this._hasCollapseableMargin(_95d,_954)){
break;
}
_95d=_95d[_957];
}
if(!this._hasCollapseableMargin(_953,_954)){
return parseInt(_95c);
}
var _95e=0;
var _95f=_953[_955];
while(_95f){
if(isSignificantNode(_95f)){
_95e=dojo.html.getPixelValue(_95f,_959,false);
break;
}
_95f=_95f[_955];
}
if(!_95f){
_95e=dojo.html.getPixelValue(_953.parentNode,_958,false);
}
if(_95c>_95a){
return parseInt(Math.max((_95c-_95a)-_95e,0));
}else{
return 0;
}
},_drawIframe:function(html){
var _961=Boolean(dojo.render.html.moz&&(typeof window.XML=="undefined"));
if(!this.iframe){
var _962=(new dojo.uri.Uri(dojo.doc().location)).host;
this.iframe=dojo.doc().createElement("iframe");
with(this.iframe){
style.border="none";
style.lineHeight="0";
style.verticalAlign="bottom";
scrolling=this.height?"auto":"no";
}
}
this.iframe.src=dojo.uri.dojoUri("src/widget/templates/richtextframe.html")+((dojo.doc().domain!=_962)?("#"+dojo.doc().domain):"");
this.iframe.width=this.inheritWidth?this._oldWidth:"100%";
if(this.height){
this.iframe.style.height=this.height;
}else{
var _963=this._oldHeight;
if(this._hasCollapseableMargin(this.domNode,"top")){
_963+=this._firstChildContributingMargin;
}
if(this._hasCollapseableMargin(this.domNode,"bottom")){
_963+=this._lastChildContributingMargin;
}
this.iframe.height=_963;
}
var _964=dojo.doc().createElement("div");
_964.innerHTML=html;
this.editingArea.appendChild(_964);
if(this.relativeImageUrls){
var imgs=_964.getElementsByTagName("img");
for(var i=0;i<imgs.length;i++){
imgs[i].src=(new dojo.uri.Uri(dojo.global().location,imgs[i].src)).toString();
}
html=_964.innerHTML;
}
var _967=dojo.html.firstElement(_964);
var _968=dojo.html.lastElement(_964);
if(_967){
_967.style.marginTop=this._firstChildContributingMargin+"px";
}
if(_968){
_968.style.marginBottom=this._lastChildContributingMargin+"px";
}
this.editingArea.appendChild(this.iframe);
if(dojo.render.html.safari){
this.iframe.src=this.iframe.src;
}
var _969=false;
var _96a=dojo.lang.hitch(this,function(){
if(!_969){
_969=true;
}else{
return;
}
if(!this.editNode){
if(this.iframe.contentWindow){
this.window=this.iframe.contentWindow;
this.document=this.iframe.contentWindow.document;
}else{
if(this.iframe.contentDocument){
this.window=this.iframe.contentDocument.window;
this.document=this.iframe.contentDocument;
}
}
var _96b=(function(_96c){
return function(_96d){
return dojo.html.getStyle(_96c,_96d);
};
})(this.domNode);
var font=_96b("font-weight")+" "+_96b("font-size")+" "+_96b("font-family");
var _96f="1.0";
var _970=dojo.html.getUnitValue(this.domNode,"line-height");
if(_970.value&&_970.units==""){
_96f=_970.value;
}
dojo.html.insertCssText("body,html{background:transparent;padding:0;margin:0;}"+"body{top:0;left:0;right:0;"+(((this.height)||(dojo.render.html.opera))?"":"position:fixed;")+"font:"+font+";"+"min-height:"+this.minHeight+";"+"line-height:"+_96f+"}"+"p{margin: 1em 0 !important;}"+"body > *:first-child{padding-top:0 !important;margin-top:"+this._firstChildContributingMargin+"px !important;}"+"body > *:last-child{padding-bottom:0 !important;margin-bottom:"+this._lastChildContributingMargin+"px !important;}"+"li > ul:-moz-first-node, li > ol:-moz-first-node{padding-top:1.2em;}\n"+"li{min-height:1.2em;}"+"",this.document);
dojo.html.removeNode(_964);
this.document.body.innerHTML=html;
if(_961||dojo.render.html.safari){
this.document.designMode="on";
}
this.onLoad();
}else{
dojo.html.removeNode(_964);
this.editNode.innerHTML=html;
this.onDisplayChanged();
}
});
if(this.editNode){
_96a();
}else{
if(dojo.render.html.moz){
this.iframe.onload=function(){
setTimeout(_96a,250);
};
}else{
this.iframe.onload=_96a;
}
}
},_applyEditingAreaStyleSheets:function(){
var _971=[];
if(this.styleSheets){
_971=this.styleSheets.split(";");
this.styleSheets="";
}
_971=_971.concat(this.editingAreaStyleSheets);
this.editingAreaStyleSheets=[];
if(_971.length>0){
for(var i=0;i<_971.length;i++){
var url=_971[i];
if(url){
this.addStyleSheet(dojo.uri.dojoUri(url));
}
}
}
},addStyleSheet:function(uri){
var url=uri.toString();
if(dojo.lang.find(this.editingAreaStyleSheets,url)>-1){
dojo.debug("dojo.widget.RichText.addStyleSheet: Style sheet "+url+" is already applied to the editing area!");
return;
}
if(url.charAt(0)=="."||(url.charAt(0)!="/"&&!uri.host)){
url=(new dojo.uri.Uri(dojo.global().location,url)).toString();
}
this.editingAreaStyleSheets.push(url);
if(this.document.createStyleSheet){
this.document.createStyleSheet(url);
}else{
var head=this.document.getElementsByTagName("head")[0];
var _977=this.document.createElement("link");
with(_977){
rel="stylesheet";
type="text/css";
href=url;
}
head.appendChild(_977);
}
},removeStyleSheet:function(uri){
var url=uri.toString();
if(url.charAt(0)=="."||(url.charAt(0)!="/"&&!uri.host)){
url=(new dojo.uri.Uri(dojo.global().location,url)).toString();
}
var _97a=dojo.lang.find(this.editingAreaStyleSheets,url);
if(_97a==-1){
dojo.debug("dojo.widget.RichText.removeStyleSheet: Style sheet "+url+" is not applied to the editing area so it can not be removed!");
return;
}
delete this.editingAreaStyleSheets[_97a];
var _97b=this.document.getElementsByTagName("link");
for(var i=0;i<_97b.length;i++){
if(_97b[i].href==url){
if(dojo.render.html.ie){
_97b[i].href="";
}
dojo.html.removeNode(_97b[i]);
break;
}
}
},_drawObject:function(html){
this.object=dojo.html.createExternalElement(dojo.doc(),"object");
with(this.object){
classid="clsid:2D360201-FFF5-11D1-8D03-00A0C959BC0A";
width=this.inheritWidth?this._oldWidth:"100%";
style.height=this.height?this.height:(this._oldHeight+"px");
Scrollbars=this.height?true:false;
Appearance=this._activeX.appearance.flat;
}
this.editorObject=this.object;
this.editingArea.appendChild(this.object);
this.object.attachEvent("DocumentComplete",dojo.lang.hitch(this,"onLoad"));
dojo.lang.forEach(this.events,function(e){
this.object.attachEvent(e.toLowerCase(),dojo.lang.hitch(this,e));
},this);
this.object.DocumentHTML="<!doctype HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">"+"<html><title></title>"+"<style type=\"text/css\">"+"    body,html { padding: 0; margin: 0; }"+(this.height?"":"    body,  { overflow: hidden; }")+"</style>"+"<body><div>"+html+"<div></body></html>";
this._cacheLocalBlockFormatNames();
},_local2NativeFormatNames:{},_native2LocalFormatNames:{},_cacheLocalBlockFormatNames:function(){
if(!this._native2LocalFormatNames["p"]){
var obj=this.object;
var _980=false;
if(!obj){
try{
obj=dojo.html.createExternalElement(dojo.doc(),"object");
obj.classid="clsid:2D360201-FFF5-11D1-8D03-00A0C959BC0A";
dojo.body().appendChild(obj);
obj.DocumentHTML="<html><head></head><body></body></html>";
}
catch(e){
_980=true;
}
}
try{
var _981=new ActiveXObject("DEGetBlockFmtNamesParam.DEGetBlockFmtNamesParam");
obj.ExecCommand(this._activeX.command["getblockformatnames"],0,_981);
var _982=new VBArray(_981.Names);
var _983=_982.toArray();
var _984=["p","pre","address","h1","h2","h3","h4","h5","h6","ol","ul","","","","","div"];
for(var i=0;i<_984.length;++i){
if(_984[i].length>0){
this._local2NativeFormatNames[_983[i]]=_984[i];
this._native2LocalFormatNames[_984[i]]=_983[i];
}
}
}
catch(e){
_980=true;
}
if(obj&&!this.object){
dojo.body().removeChild(obj);
}
}
return !_980;
},_isResized:function(){
return false;
},onLoad:function(e){
this.isLoaded=true;
if(this.object){
this.document=this.object.DOM;
this.window=this.document.parentWindow;
this.editNode=this.document.body.firstChild;
this.editingArea.style.height=this.height?this.height:this.minHeight;
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
this.window._frameElement=this.object;
}else{
if(this.iframe&&!dojo.render.html.ie){
this.editNode=this.document.body;
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
try{
this.document.execCommand("useCSS",false,true);
this.document.execCommand("styleWithCSS",false,false);
}
catch(e2){
}
if(dojo.render.html.safari){
this.connect(this.editNode,"onblur","onBlur");
this.connect(this.editNode,"onfocus","onFocus");
this.connect(this.editNode,"onclick","onFocus");
this.interval=setInterval(dojo.lang.hitch(this,"onDisplayChanged"),750);
}else{
if(dojo.render.html.mozilla||dojo.render.html.opera){
var doc=this.document;
var _988=dojo.event.browser.addListener;
var self=this;
dojo.lang.forEach(this.events,function(e){
var l=_988(self.document,e.substr(2).toLowerCase(),dojo.lang.hitch(self,e));
if(e=="onBlur"){
var _98c={unBlur:function(e){
dojo.event.browser.removeListener(doc,"blur",l);
}};
dojo.event.connect("before",self,"close",_98c,"unBlur");
}
});
}
}
}else{
if(dojo.render.html.ie){
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
this.editNode.style.zoom=1;
}
}
}
this._applyEditingAreaStyleSheets();
if(this.focusOnLoad){
this.focus();
}
this.onDisplayChanged(e);
if(this.onLoadDeferred){
this.onLoadDeferred.callback(true);
}
},onKeyDown:function(e){
if((!e)&&(this.object)){
e=dojo.event.browser.fixEvent(this.window.event);
}
if((dojo.render.html.ie)&&(e.keyCode==e.KEY_TAB)){
e.preventDefault();
e.stopPropagation();
this.execCommand((e.shiftKey?"outdent":"indent"));
}else{
if(dojo.render.html.ie){
if((65<=e.keyCode)&&(e.keyCode<=90)){
e.charCode=e.keyCode;
this.onKeyPress(e);
}
}
}
},onKeyUp:function(e){
return;
},KEY_CTRL:1,onKeyPress:function(e){
if((!e)&&(this.object)){
e=dojo.event.browser.fixEvent(this.window.event);
}
var _991=e.ctrlKey?this.KEY_CTRL:0;
if(this._keyHandlers[e.key]){
var _992=this._keyHandlers[e.key],i=0,_994;
while(_994=_992[i++]){
if(_991==_994.modifiers){
e.preventDefault();
_994.handler.call(this);
break;
}
}
}
dojo.lang.setTimeout(this,this.onKeyPressed,1,e);
},addKeyHandler:function(key,_996,_997){
if(!(this._keyHandlers[key] instanceof Array)){
this._keyHandlers[key]=[];
}
this._keyHandlers[key].push({modifiers:_996||0,handler:_997});
},onKeyPressed:function(e){
this.onDisplayChanged();
},onClick:function(e){
this.onDisplayChanged(e);
},onBlur:function(e){
},_initialFocus:true,onFocus:function(e){
if((dojo.render.html.mozilla)&&(this._initialFocus)){
this._initialFocus=false;
if(dojo.string.trim(this.editNode.innerHTML)=="&nbsp;"){
this.placeCursorAtStart();
}
}
},blur:function(){
if(this.iframe){
this.window.blur();
}else{
if(this.object){
this.document.body.blur();
}else{
if(this.editNode){
this.editNode.blur();
}
}
}
},focus:function(){
if(this.iframe&&!dojo.render.html.ie){
this.window.focus();
}else{
if(this.object){
this.document.focus();
}else{
if(this.editNode&&this.editNode.focus){
this.editNode.focus();
}else{
dojo.debug("Have no idea how to focus into the editor!");
}
}
}
},onDisplayChanged:function(e){
},_activeX:{command:{bold:5000,italic:5023,underline:5048,justifycenter:5024,justifyleft:5025,justifyright:5026,cut:5003,copy:5002,paste:5032,"delete":5004,undo:5049,redo:5033,removeformat:5034,selectall:5035,unlink:5050,indent:5018,outdent:5031,insertorderedlist:5030,insertunorderedlist:5051,inserttable:5022,insertcell:5019,insertcol:5020,insertrow:5021,deletecells:5005,deletecols:5006,deleterows:5007,mergecells:5029,splitcell:5047,setblockformat:5043,getblockformat:5011,getblockformatnames:5012,setfontname:5044,getfontname:5013,setfontsize:5045,getfontsize:5014,setbackcolor:5042,getbackcolor:5010,setforecolor:5046,getforecolor:5015,findtext:5008,font:5009,hyperlink:5016,image:5017,lockelement:5027,makeabsolute:5028,sendbackward:5036,bringforward:5037,sendbelowtext:5038,bringabovetext:5039,sendtoback:5040,bringtofront:5041,properties:5052},ui:{"default":0,prompt:1,noprompt:2},status:{notsupported:0,disabled:1,enabled:3,latched:7,ninched:11},appearance:{flat:0,inset:1},state:{unchecked:0,checked:1,gray:2}},_normalizeCommand:function(cmd){
var drh=dojo.render.html;
var _99f=cmd.toLowerCase();
if(_99f=="formatblock"){
if(drh.safari){
_99f="heading";
}
}else{
if(this.object){
switch(_99f){
case "createlink":
_99f="hyperlink";
break;
case "insertimage":
_99f="image";
break;
}
}else{
if(_99f=="hilitecolor"&&!drh.mozilla){
_99f="backcolor";
}
}
}
return _99f;
},_safariIsLeopard:function(){
var _9a0=false;
if(dojo.render.html.safari){
var tmp=dojo.render.html.UA.split("AppleWebKit/")[1];
var ver=parseFloat(tmp.split(" ")[0]);
if(ver>=420){
_9a0=true;
}
}
return _9a0;
},queryCommandAvailable:function(_9a3){
var ie=1;
var _9a5=1<<1;
var _9a6=1<<2;
var _9a7=1<<3;
var _9a8=1<<4;
var _9a9=this._safariIsLeopard();
function isSupportedBy(_9aa){
return {ie:Boolean(_9aa&ie),mozilla:Boolean(_9aa&_9a5),safari:Boolean(_9aa&_9a6),safari420:Boolean(_9aa&_9a8),opera:Boolean(_9aa&_9a7)};
}
var _9ab=null;
switch(_9a3.toLowerCase()){
case "bold":
case "italic":
case "underline":
case "subscript":
case "superscript":
case "fontname":
case "fontsize":
case "forecolor":
case "hilitecolor":
case "justifycenter":
case "justifyfull":
case "justifyleft":
case "justifyright":
case "delete":
case "selectall":
_9ab=isSupportedBy(_9a5|ie|_9a6|_9a7);
break;
case "createlink":
case "unlink":
case "removeformat":
case "inserthorizontalrule":
case "insertimage":
case "insertorderedlist":
case "insertunorderedlist":
case "indent":
case "outdent":
case "formatblock":
case "inserthtml":
case "undo":
case "redo":
case "strikethrough":
_9ab=isSupportedBy(_9a5|ie|_9a7|_9a8);
break;
case "blockdirltr":
case "blockdirrtl":
case "dirltr":
case "dirrtl":
case "inlinedirltr":
case "inlinedirrtl":
_9ab=isSupportedBy(ie);
break;
case "cut":
case "copy":
case "paste":
_9ab=isSupportedBy(ie|_9a5|_9a8);
break;
case "inserttable":
_9ab=isSupportedBy(_9a5|(this.object?ie:0));
break;
case "insertcell":
case "insertcol":
case "insertrow":
case "deletecells":
case "deletecols":
case "deleterows":
case "mergecells":
case "splitcell":
_9ab=isSupportedBy(this.object?ie:0);
break;
default:
return false;
}
return (dojo.render.html.ie&&_9ab.ie)||(dojo.render.html.mozilla&&_9ab.mozilla)||(dojo.render.html.safari&&_9ab.safari)||(_9a9&&_9ab.safari420)||(dojo.render.html.opera&&_9ab.opera);
},execCommand:function(_9ac,_9ad){
var _9ae;
this.focus();
_9ac=this._normalizeCommand(_9ac);
if(_9ad!=undefined){
if(_9ac=="heading"){
throw new Error("unimplemented");
}else{
if(_9ac=="formatblock"){
if(this.object){
_9ad=this._native2LocalFormatNames[_9ad];
}else{
if(dojo.render.html.ie){
_9ad="<"+_9ad+">";
}
}
}
}
}
if(this.object){
switch(_9ac){
case "hilitecolor":
_9ac="setbackcolor";
break;
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_9ac="set"+_9ac;
break;
case "formatblock":
_9ac="setblockformat";
}
if(_9ac=="strikethrough"){
_9ac="inserthtml";
var _9af=this.document.selection.createRange();
if(!_9af.htmlText){
return;
}
_9ad=_9af.htmlText.strike();
}else{
if(_9ac=="inserthorizontalrule"){
_9ac="inserthtml";
_9ad="<hr>";
}
}
if(_9ac=="inserthtml"){
var _9af=this.document.selection.createRange();
if(this.document.selection.type.toUpperCase()=="CONTROL"){
for(var i=0;i<_9af.length;i++){
_9af.item(i).outerHTML=_9ad;
}
}else{
_9af.pasteHTML(_9ad);
_9af.select();
}
_9ae=true;
}else{
if(arguments.length==1){
_9ae=this.object.ExecCommand(this._activeX.command[_9ac],this._activeX.ui.noprompt);
}else{
_9ae=this.object.ExecCommand(this._activeX.command[_9ac],this._activeX.ui.noprompt,_9ad);
}
}
}else{
if(_9ac=="inserthtml"){
if(dojo.render.html.ie){
var _9b1=this.document.selection.createRange();
_9b1.pasteHTML(_9ad);
_9b1.select();
return true;
}else{
return this.document.execCommand(_9ac,false,_9ad);
}
}else{
if((_9ac=="unlink")&&(this.queryCommandEnabled("unlink"))&&(dojo.render.html.mozilla)){
var _9b2=this.window.getSelection();
var _9b3=_9b2.getRangeAt(0);
var _9b4=_9b3.startContainer;
var _9b5=_9b3.startOffset;
var _9b6=_9b3.endContainer;
var _9b7=_9b3.endOffset;
var a=dojo.withGlobal(this.window,"getAncestorElement",dojo.html.selection,["a"]);
dojo.withGlobal(this.window,"selectElement",dojo.html.selection,[a]);
_9ae=this.document.execCommand("unlink",false,null);
var _9b3=this.document.createRange();
_9b3.setStart(_9b4,_9b5);
_9b3.setEnd(_9b6,_9b7);
_9b2.removeAllRanges();
_9b2.addRange(_9b3);
return _9ae;
}else{
if((_9ac=="hilitecolor")&&(dojo.render.html.mozilla)){
this.document.execCommand("useCSS",false,false);
_9ae=this.document.execCommand(_9ac,false,_9ad);
this.document.execCommand("useCSS",false,true);
}else{
if((dojo.render.html.ie)&&((_9ac=="backcolor")||(_9ac=="forecolor"))){
_9ad=arguments.length>1?_9ad:null;
_9ae=this.document.execCommand(_9ac,false,_9ad);
}else{
_9ad=arguments.length>1?_9ad:null;
if(_9ad||_9ac!="createlink"){
_9ae=this.document.execCommand(_9ac,false,_9ad);
}
}
}
}
}
}
this.onDisplayChanged();
return _9ae;
},queryCommandEnabled:function(_9b9){
_9b9=this._normalizeCommand(_9b9);
if(this.object){
switch(_9b9){
case "hilitecolor":
_9b9="setbackcolor";
break;
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_9b9="set"+_9b9;
break;
case "formatblock":
_9b9="setblockformat";
break;
case "strikethrough":
_9b9="bold";
break;
case "inserthorizontalrule":
return true;
}
if(typeof this._activeX.command[_9b9]=="undefined"){
return false;
}
var _9ba=this.object.QueryStatus(this._activeX.command[_9b9]);
return ((_9ba!=this._activeX.status.notsupported)&&(_9ba!=this._activeX.status.disabled));
}else{
if(dojo.render.html.mozilla){
if(_9b9=="unlink"){
return dojo.withGlobal(this.window,"hasAncestorElement",dojo.html.selection,["a"]);
}else{
if(_9b9=="inserttable"){
return true;
}
}
}
var elem=(dojo.render.html.ie)?this.document.selection.createRange():this.document;
return elem.queryCommandEnabled(_9b9);
}
},queryCommandState:function(_9bc){
_9bc=this._normalizeCommand(_9bc);
if(this.object){
if(_9bc=="forecolor"){
_9bc="setforecolor";
}else{
if(_9bc=="backcolor"){
_9bc="setbackcolor";
}else{
if(_9bc=="strikethrough"){
return dojo.withGlobal(this.window,"hasAncestorElement",dojo.html.selection,["strike"]);
}else{
if(_9bc=="inserthorizontalrule"){
return false;
}
}
}
}
if(typeof this._activeX.command[_9bc]=="undefined"){
return null;
}
var _9bd=this.object.QueryStatus(this._activeX.command[_9bc]);
return ((_9bd==this._activeX.status.latched)||(_9bd==this._activeX.status.ninched));
}else{
return this.document.queryCommandState(_9bc);
}
},queryCommandValue:function(_9be){
_9be=this._normalizeCommand(_9be);
if(this.object){
switch(_9be){
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_9be="get"+_9be;
return this.object.execCommand(this._activeX.command[_9be],this._activeX.ui.noprompt);
case "formatblock":
var _9bf=this.object.execCommand(this._activeX.command["getblockformat"],this._activeX.ui.noprompt);
if(_9bf){
return this._local2NativeFormatNames[_9bf];
}
}
}else{
if(dojo.render.html.ie&&_9be=="formatblock"){
return this._local2NativeFormatNames[this.document.queryCommandValue(_9be)]||this.document.queryCommandValue(_9be);
}
return this.document.queryCommandValue(_9be);
}
},placeCursorAtStart:function(){
this.focus();
if(dojo.render.html.moz&&this.editNode.firstChild&&this.editNode.firstChild.nodeType!=dojo.dom.TEXT_NODE){
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode.firstChild]);
}else{
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode]);
}
dojo.withGlobal(this.window,"collapse",dojo.html.selection,[true]);
},placeCursorAtEnd:function(){
this.focus();
if(dojo.render.html.moz&&this.editNode.lastChild&&this.editNode.lastChild.nodeType!=dojo.dom.TEXT_NODE){
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode.lastChild]);
}else{
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode]);
}
dojo.withGlobal(this.window,"collapse",dojo.html.selection,[false]);
},replaceEditorContent:function(html){
html=this._preFilterContent(html);
if(this.isClosed){
this.domNode.innerHTML=html;
}else{
if(this.window&&this.window.getSelection&&!dojo.render.html.moz){
this.editNode.innerHTML=html;
}else{
if((this.window&&this.window.getSelection)||(this.document&&this.document.selection)){
this.execCommand("selectall");
if(dojo.render.html.moz&&!html){
html="&nbsp;";
}
this.execCommand("inserthtml",html);
}
}
}
},_preFilterContent:function(html){
var ec=html;
dojo.lang.forEach(this.contentPreFilters,function(ef){
ec=ef(ec);
});
if(this.contentDomPreFilters.length>0){
var dom=dojo.doc().createElement("div");
dom.style.display="none";
dojo.body().appendChild(dom);
dom.innerHTML=ec;
dojo.lang.forEach(this.contentDomPreFilters,function(ef){
dom=ef(dom);
});
ec=dom.innerHTML;
dojo.body().removeChild(dom);
}
return ec;
},_postFilterContent:function(html){
var ec=html;
if(this.contentDomPostFilters.length>0){
var dom=this.document.createElement("div");
dom.innerHTML=ec;
dojo.lang.forEach(this.contentDomPostFilters,function(ef){
dom=ef(dom);
});
ec=dom.innerHTML;
}
dojo.lang.forEach(this.contentPostFilters,function(ef){
ec=ef(ec);
});
return ec;
},_lastHeight:0,_updateHeight:function(){
if(!this.isLoaded){
return;
}
if(this.height){
return;
}
var _9cb=dojo.html.getBorderBox(this.editNode).height;
if(!_9cb){
_9cb=dojo.html.getBorderBox(this.document.body).height;
}
if(_9cb==0){
dojo.debug("Can not figure out the height of the editing area!");
return;
}
this._lastHeight=_9cb;
this.editorObject.style.height=this._lastHeight+"px";
this.window.scrollTo(0,0);
},_saveContent:function(e){
var _9cd=dojo.doc().getElementById("dojo.widget.RichText.savedContent");
_9cd.value+=this._SEPARATOR+this.saveName+":"+this.getEditorContent();
},getEditorContent:function(){
var ec="";
try{
ec=(this._content.length>0)?this._content:this.editNode.innerHTML;
if(dojo.string.trim(ec)=="&nbsp;"){
ec="";
}
}
catch(e){
}
if(dojo.render.html.ie&&!this.object){
var re=new RegExp("(?:<p>&nbsp;</p>[\n\r]*)+$","i");
ec=ec.replace(re,"");
}
ec=this._postFilterContent(ec);
if(this.relativeImageUrls){
var _9d0=dojo.global().location.protocol+"//"+dojo.global().location.host;
var _9d1=dojo.global().location.pathname;
if(_9d1.match(/\/$/)){
}else{
var _9d2=_9d1.split("/");
if(_9d2.length){
_9d2.pop();
}
_9d1=_9d2.join("/")+"/";
}
var _9d3=new RegExp("(<img[^>]* src=[\"'])("+_9d0+"("+_9d1+")?)","ig");
ec=ec.replace(_9d3,"$1");
}
return ec;
},close:function(save,_9d5){
if(this.isClosed){
return false;
}
if(arguments.length==0){
save=true;
}
this._content=this._postFilterContent(this.editNode.innerHTML);
var _9d6=(this.savedContent!=this._content);
if(this.interval){
clearInterval(this.interval);
}
if(dojo.render.html.ie&&!this.object){
dojo.event.browser.clean(this.editNode);
}
if(this.iframe){
dojo.html.destroyNode(this.iframe);
delete this.iframe;
}
if(this.textarea){
with(this.textarea.style){
position="";
left=top="";
if(dojo.render.html.ie){
overflow=this.__overflow;
this.__overflow=null;
}
}
if(save){
this.textarea.value=this._content;
}else{
this.textarea.value=this.savedContent;
}
dojo.html.removeNode(this.domNode);
this.domNode=this.textarea;
}else{
if(save){
if(dojo.render.html.moz){
var nc=dojo.doc().createElement("span");
this.domNode.appendChild(nc);
nc.innerHTML=this.editNode.innerHTML;
}else{
this.domNode.innerHTML=this._content;
}
}else{
this.domNode.innerHTML=this.savedContent;
}
}
dojo.html.removeClass(this.domNode,"RichTextEditable");
this.isClosed=true;
this.isLoaded=false;
delete this.editNode;
if(this.window._frameElement){
this.window._frameElement=null;
}
this.window=null;
this.document=null;
this.object=null;
this.editingArea=null;
this.editorObject=null;
return _9d6;
},destroyRendering:function(){
},destroy:function(){
this.destroyRendering();
if(!this.isClosed){
this.close(false);
}
dojo.widget.RichText.superclass.destroy.call(this);
},connect:function(_9d8,_9d9,_9da){
dojo.event.connect(_9d8,_9d9,this,_9da);
},disconnect:function(_9db,_9dc,_9dd){
dojo.event.disconnect(_9db,_9dc,this,_9dd);
},_fixContentForMoz:function(html){
html=html.replace(/<strong([ \>])/gi,"<b$1");
html=html.replace(/<\/strong>/gi,"</b>");
html=html.replace(/<em([ \>])/gi,"<i$1");
html=html.replace(/<\/em>/gi,"</i>");
return html;
}});
dojo.provide("dojo.widget.ColorPalette");
dojo.widget.defineWidget("dojo.widget.ColorPalette",dojo.widget.HtmlWidget,{palette:"7x10",_palettes:{"7x10":[["fff","fcc","fc9","ff9","ffc","9f9","9ff","cff","ccf","fcf"],["ccc","f66","f96","ff6","ff3","6f9","3ff","6ff","99f","f9f"],["c0c0c0","f00","f90","fc6","ff0","3f3","6cc","3cf","66c","c6c"],["999","c00","f60","fc3","fc0","3c0","0cc","36f","63f","c3c"],["666","900","c60","c93","990","090","399","33f","60c","939"],["333","600","930","963","660","060","366","009","339","636"],["000","300","630","633","330","030","033","006","309","303"]],"3x4":[["ffffff","00ff00","008000","0000ff"],["c0c0c0","ffff00","ff00ff","000080"],["808080","ff0000","800080","000000"]]},buildRendering:function(){
this.domNode=document.createElement("table");
dojo.html.disableSelection(this.domNode);
dojo.event.connect(this.domNode,"onmousedown",function(e){
e.preventDefault();
});
with(this.domNode){
cellPadding="0";
cellSpacing="1";
border="1";
style.backgroundColor="white";
}
var _9e0=this._palettes[this.palette];
for(var i=0;i<_9e0.length;i++){
var tr=this.domNode.insertRow(-1);
for(var j=0;j<_9e0[i].length;j++){
if(_9e0[i][j].length==3){
_9e0[i][j]=_9e0[i][j].replace(/(.)(.)(.)/,"$1$1$2$2$3$3");
}
var td=tr.insertCell(-1);
with(td.style){
backgroundColor="#"+_9e0[i][j];
border="1px solid gray";
width=height="15px";
fontSize="1px";
}
td.color="#"+_9e0[i][j];
td.onmouseover=function(e){
this.style.borderColor="white";
};
td.onmouseout=function(e){
this.style.borderColor="gray";
};
dojo.event.connect(td,"onmousedown",this,"onClick");
td.innerHTML="&nbsp;";
}
}
},onClick:function(e){
this.onColorSelect(e.currentTarget.color);
e.currentTarget.style.borderColor="gray";
},onColorSelect:function(_9e8){
}});
dojo.provide("dojo.widget.Editor");
dojo.deprecated("dojo.widget.Editor","is replaced by dojo.widget.Editor2","0.5");
// dojo.widget.tags.addParseTreeHandler("dojo:Editor");
dojo.widget.Editor=function(){
dojo.widget.HtmlWidget.call(this);
this.contentFilters=[];
this._toolbars=[];
};
dojo.inherits(dojo.widget.Editor,dojo.widget.HtmlWidget);
dojo.widget.Editor.itemGroups={textGroup:["bold","italic","underline","strikethrough"],blockGroup:["formatBlock","fontName","fontSize"],justifyGroup:["justifyleft","justifycenter","justifyright"],commandGroup:["save","cancel"],colorGroup:["forecolor","hilitecolor"],listGroup:["insertorderedlist","insertunorderedlist"],indentGroup:["outdent","indent"],linkGroup:["createlink","insertimage","inserthorizontalrule"]};
dojo.widget.Editor.formatBlockValues={"Normal":"p","Main heading":"h2","Sub heading":"h3","Sub sub heading":"h4","Preformatted":"pre"};
dojo.widget.Editor.fontNameValues={"Arial":"Arial, Helvetica, sans-serif","Verdana":"Verdana, sans-serif","Times New Roman":"Times New Roman, serif","Courier":"Courier New, monospace"};
dojo.widget.Editor.fontSizeValues={"1 (8 pt)":"1","2 (10 pt)":"2","3 (12 pt)":"3","4 (14 pt)":"4","5 (18 pt)":"5","6 (24 pt)":"6","7 (36 pt)":"7"};
dojo.widget.Editor.defaultItems=["commandGroup","|","blockGroup","|","textGroup","|","colorGroup","|","justifyGroup","|","listGroup","indentGroup","|","linkGroup"];
dojo.widget.Editor.supportedCommands=["save","cancel","|","-","/"," "];
dojo.lang.extend(dojo.widget.Editor,{widgetType:"Editor",saveUrl:"",saveMethod:"post",saveArgName:"editorContent",closeOnSave:false,items:dojo.widget.Editor.defaultItems,formatBlockItems:dojo.lang.shallowCopy(dojo.widget.Editor.formatBlockValues),fontNameItems:dojo.lang.shallowCopy(dojo.widget.Editor.fontNameValues),fontSizeItems:dojo.lang.shallowCopy(dojo.widget.Editor.fontSizeValues),getItemProperties:function(name){
var _9ea={};
switch(name.toLowerCase()){
case "bold":
case "italic":
case "underline":
case "strikethrough":
_9ea.toggleItem=true;
break;
case "justifygroup":
_9ea.defaultButton="justifyleft";
_9ea.preventDeselect=true;
_9ea.buttonGroup=true;
break;
case "listgroup":
_9ea.buttonGroup=true;
break;
case "save":
case "cancel":
_9ea.label=dojo.string.capitalize(name);
break;
case "forecolor":
case "hilitecolor":
_9ea.name=name;
_9ea.toggleItem=true;
_9ea.icon=this.getCommandImage(name);
break;
case "formatblock":
_9ea.name="formatBlock";
_9ea.values=this.formatBlockItems;
break;
case "fontname":
_9ea.name="fontName";
_9ea.values=this.fontNameItems;
case "fontsize":
_9ea.name="fontSize";
_9ea.values=this.fontSizeItems;
}
return _9ea;
},validateItems:true,focusOnLoad:true,minHeight:"1em",_richText:null,_richTextType:"RichText",_toolbarContainer:null,_toolbarContainerType:"ToolbarContainer",_toolbars:[],_toolbarType:"Toolbar",_toolbarItemType:"ToolbarItem",buildRendering:function(args,frag){
var node=frag["dojo:"+this.widgetType.toLowerCase()]["nodeRef"];
var trt=dojo.widget.createWidget(this._richTextType,{focusOnLoad:this.focusOnLoad,minHeight:this.minHeight},node);
var _9ef=this;
setTimeout(function(){
_9ef.setRichText(trt);
_9ef.initToolbar();
_9ef.fillInTemplate(args,frag);
},0);
},setRichText:function(_9f0){
if(this._richText&&this._richText==_9f0){
dojo.debug("Already set the richText to this richText!");
return;
}
if(this._richText&&!this._richText.isClosed){
dojo.debug("You are switching richTexts yet you haven't closed the current one. Losing reference!");
}
this._richText=_9f0;
dojo.event.connect(this._richText,"close",this,"onClose");
dojo.event.connect(this._richText,"onLoad",this,"onLoad");
dojo.event.connect(this._richText,"onDisplayChanged",this,"updateToolbar");
if(this._toolbarContainer){
this._toolbarContainer.enable();
this.updateToolbar(true);
}
},initToolbar:function(){
if(this._toolbarContainer){
return;
}
this._toolbarContainer=dojo.widget.createWidget(this._toolbarContainerType);
var tb=this.addToolbar();
var last=true;
for(var i=0;i<this.items.length;i++){
if(this.items[i]=="\n"){
tb=this.addToolbar();
}else{
if((this.items[i]=="|")&&(!last)){
last=true;
}else{
last=this.addItem(this.items[i],tb);
}
}
}
this.insertToolbar(this._toolbarContainer.domNode,this._richText.domNode);
},insertToolbar:function(_9f4,_9f5){
dojo.html.insertBefore(_9f4,_9f5);
},addToolbar:function(_9f6){
this.initToolbar();
if(!(_9f6 instanceof dojo.widget.Toolbar)){
_9f6=dojo.widget.createWidget(this._toolbarType);
}
this._toolbarContainer.addChild(_9f6);
this._toolbars.push(_9f6);
return _9f6;
},addItem:function(item,tb,_9f9){
if(!tb){
tb=this._toolbars[0];
}
var cmd=((item)&&(!dojo.lang.isUndefined(item["getValue"])))?cmd=item["getValue"]():item;
var _9fb=dojo.widget.Editor.itemGroups;
if(item instanceof dojo.widget.ToolbarItem){
tb.addChild(item);
}else{
if(_9fb[cmd]){
var _9fc=_9fb[cmd];
var _9fd=true;
if(cmd=="justifyGroup"||cmd=="listGroup"){
var _9fe=[cmd];
for(var i=0;i<_9fc.length;i++){
if(_9f9||this.isSupportedCommand(_9fc[i])){
_9fe.push(this.getCommandImage(_9fc[i]));
}else{
_9fd=false;
}
}
if(_9fe.length){
var btn=tb.addChild(_9fe,null,this.getItemProperties(cmd));
dojo.event.connect(btn,"onClick",this,"_action");
dojo.event.connect(btn,"onChangeSelect",this,"_action");
}
return _9fd;
}else{
for(var i=0;i<_9fc.length;i++){
if(!this.addItem(_9fc[i],tb)){
_9fd=false;
}
}
return _9fd;
}
}else{
if((!_9f9)&&(!this.isSupportedCommand(cmd))){
return false;
}
if(_9f9||this.isSupportedCommand(cmd)){
cmd=cmd.toLowerCase();
if(cmd=="formatblock"){
var _a01=dojo.widget.createWidget("ToolbarSelect",{name:"formatBlock",values:this.formatBlockItems});
tb.addChild(_a01);
var _a02=this;
dojo.event.connect(_a01,"onSetValue",function(item,_a04){
_a02.onAction("formatBlock",_a04);
});
}else{
if(cmd=="fontname"){
var _a01=dojo.widget.createWidget("ToolbarSelect",{name:"fontName",values:this.fontNameItems});
tb.addChild(_a01);
dojo.event.connect(_a01,"onSetValue",dojo.lang.hitch(this,function(item,_a06){
this.onAction("fontName",_a06);
}));
}else{
if(cmd=="fontsize"){
var _a01=dojo.widget.createWidget("ToolbarSelect",{name:"fontSize",values:this.fontSizeItems});
tb.addChild(_a01);
dojo.event.connect(_a01,"onSetValue",dojo.lang.hitch(this,function(item,_a08){
this.onAction("fontSize",_a08);
}));
}else{
if(dojo.lang.inArray(cmd,["forecolor","hilitecolor"])){
var btn=tb.addChild(dojo.widget.createWidget("ToolbarColorDialog",this.getItemProperties(cmd)));
dojo.event.connect(btn,"onSetValue",this,"_setValue");
}else{
var btn=tb.addChild(this.getCommandImage(cmd),null,this.getItemProperties(cmd));
if(cmd=="save"){
dojo.event.connect(btn,"onClick",this,"_save");
}else{
if(cmd=="cancel"){
dojo.event.connect(btn,"onClick",this,"_close");
}else{
dojo.event.connect(btn,"onClick",this,"_action");
dojo.event.connect(btn,"onChangeSelect",this,"_action");
}
}
}
}
}
}
}
}
}
return true;
},enableToolbar:function(){
if(this._toolbarContainer){
this._toolbarContainer.domNode.style.display="";
this._toolbarContainer.enable();
}
},disableToolbar:function(hide){
if(hide){
if(this._toolbarContainer){
this._toolbarContainer.domNode.style.display="none";
}
}else{
if(this._toolbarContainer){
this._toolbarContainer.disable();
}
}
},_updateToolbarLastRan:null,_updateToolbarTimer:null,_updateToolbarFrequency:500,updateToolbar:function(_a0a){
if(!this._toolbarContainer){
return;
}
var diff=new Date()-this._updateToolbarLastRan;
if(!_a0a&&this._updateToolbarLastRan&&(diff<this._updateToolbarFrequency)){
clearTimeout(this._updateToolbarTimer);
var _a0c=this;
this._updateToolbarTimer=setTimeout(function(){
_a0c.updateToolbar();
},this._updateToolbarFrequency/2);
return;
}else{
this._updateToolbarLastRan=new Date();
}
var _a0d=this._toolbarContainer.getItems();
for(var i=0;i<_a0d.length;i++){
var item=_a0d[i];
if(item instanceof dojo.widget.ToolbarSeparator){
continue;
}
var cmd=item._name;
if(cmd=="save"||cmd=="cancel"){
continue;
}else{
if(cmd=="justifyGroup"){
try{
if(!this._richText.queryCommandEnabled("justifyleft")){
item.disable(false,true);
}else{
item.enable(false,true);
var _a11=item.getItems();
for(var j=0;j<_a11.length;j++){
var name=_a11[j]._name;
var _a14=this._richText.queryCommandValue(name);
if(typeof _a14=="boolean"&&_a14){
_a14=name;
break;
}else{
if(typeof _a14=="string"){
_a14="justify"+_a14;
}else{
_a14=null;
}
}
}
if(!_a14){
_a14="justifyleft";
}
item.setValue(_a14,false,true);
}
}
catch(err){
}
}else{
if(cmd=="listGroup"){
var _a15=item.getItems();
for(var j=0;j<_a15.length;j++){
this.updateItem(_a15[j]);
}
}else{
this.updateItem(item);
}
}
}
}
},updateItem:function(item){
try{
var cmd=item._name;
var _a18=this._richText.queryCommandEnabled(cmd);
item.setEnabled(_a18,false,true);
var _a19=this._richText.queryCommandState(cmd);
if(_a19&&cmd=="underline"){
_a19=!this._richText.queryCommandEnabled("unlink");
}
item.setSelected(_a19,false,true);
return true;
}
catch(err){
return false;
}
},supportedCommands:dojo.widget.Editor.supportedCommands.concat(),isSupportedCommand:function(cmd){
var yes=dojo.lang.inArray(cmd,this.supportedCommands);
if(!yes){
try{
var _a1c=this._richText||dojo.widget.HtmlRichText.prototype;
yes=_a1c.queryCommandAvailable(cmd);
}
catch(E){
}
}
return yes;
},getCommandImage:function(cmd){
if(cmd=="|"){
return cmd;
}else{
return dojo.uri.dojoUri("src/widget/templates/buttons/"+cmd+".gif");
}
},_action:function(e){
this._fire("onAction",e.getValue());
},_setValue:function(a,b){
this._fire("onAction",a.getValue(),b);
},_save:function(e){
if(!this._richText.isClosed){
if(this.saveUrl.length){
var _a22={};
_a22[this.saveArgName]=this.getHtml();
dojo.io.bind({method:this.saveMethod,url:this.saveUrl,content:_a22});
}else{
dojo.debug("please set a saveUrl for the editor");
}
if(this.closeOnSave){
this._richText.close(e.getName().toLowerCase()=="save");
}
}
},_close:function(e){
if(!this._richText.isClosed){
this._richText.close(e.getName().toLowerCase()=="save");
}
},onAction:function(cmd,_a25){
switch(cmd){
case "createlink":
if(!(_a25=prompt("Please enter the URL of the link:","http://"))){
return;
}
break;
case "insertimage":
if(!(_a25=prompt("Please enter the URL of the image:","http://"))){
return;
}
break;
}
this._richText.execCommand(cmd,_a25);
},fillInTemplate:function(args,frag){
},_fire:function(_a28){
if(dojo.lang.isFunction(this[_a28])){
var args=[];
if(arguments.length==1){
args.push(this);
}else{
for(var i=1;i<arguments.length;i++){
args.push(arguments[i]);
}
}
this[_a28].apply(this,args);
}
},getHtml:function(){
this._richText.contentFilters=this._richText.contentFilters.concat(this.contentFilters);
return this._richText.getEditorContent();
},getEditorContent:function(){
return this.getHtml();
},onClose:function(save,hide){
this.disableToolbar(hide);
if(save){
this._fire("onSave");
}else{
this._fire("onCancel");
}
},onLoad:function(){
},onSave:function(){
},onCancel:function(){
}});
dojo.provide("dojo.lang.type");
dojo.lang.whatAmI={};
dojo.lang.whatAmI.custom={};
dojo.lang.getType=function(_a2d){
try{
if(dojo.lang.isArray(_a2d)){
return "array";
}
if(dojo.lang.isFunction(_a2d)){
return "function";
}
if(dojo.lang.isString(_a2d)){
return "string";
}
if(dojo.lang.isNumber(_a2d)){
return "number";
}
if(dojo.lang.isBoolean(_a2d)){
return "boolean";
}
if(dojo.lang.isAlien(_a2d)){
return "alien";
}
if(dojo.lang.isUndefined(_a2d)){
return "undefined";
}
for(var name in dojo.lang.whatAmI.custom){
if(dojo.lang.whatAmI.custom[name](_a2d)){
return name;
}
}
if(dojo.lang.isObject(_a2d)){
return "object";
}
}
catch(e){
}
return "unknown";
};
dojo.lang.isNumeric=function(_a2f){
return (!isNaN(_a2f)&&isFinite(_a2f)&&(_a2f!=null)&&!dojo.lang.isBoolean(_a2f)&&!dojo.lang.isArray(_a2f)&&!/^\s*$/.test(_a2f));
};
dojo.lang.isBuiltIn=function(_a30){
return (dojo.lang.isArray(_a30)||dojo.lang.isFunction(_a30)||dojo.lang.isString(_a30)||dojo.lang.isNumber(_a30)||dojo.lang.isBoolean(_a30)||(_a30==null)||(_a30 instanceof Error)||(typeof _a30=="error"));
};
dojo.lang.isPureObject=function(_a31){
return ((_a31!=null)&&dojo.lang.isObject(_a31)&&_a31.constructor==Object);
};
dojo.lang.isOfType=function(_a32,type,_a34){
var _a35=false;
if(_a34){
_a35=_a34["optional"];
}
if(_a35&&((_a32===null)||dojo.lang.isUndefined(_a32))){
return true;
}
if(dojo.lang.isArray(type)){
var _a36=type;
for(var i in _a36){
var _a38=_a36[i];
if(dojo.lang.isOfType(_a32,_a38)){
return true;
}
}
return false;
}else{
if(dojo.lang.isString(type)){
type=type.toLowerCase();
}
switch(type){
case Array:
case "array":
return dojo.lang.isArray(_a32);
case Function:
case "function":
return dojo.lang.isFunction(_a32);
case String:
case "string":
return dojo.lang.isString(_a32);
case Number:
case "number":
return dojo.lang.isNumber(_a32);
case "numeric":
return dojo.lang.isNumeric(_a32);
case Boolean:
case "boolean":
return dojo.lang.isBoolean(_a32);
case Object:
case "object":
return dojo.lang.isObject(_a32);
case "pureobject":
return dojo.lang.isPureObject(_a32);
case "builtin":
return dojo.lang.isBuiltIn(_a32);
case "alien":
return dojo.lang.isAlien(_a32);
case "undefined":
return dojo.lang.isUndefined(_a32);
case null:
case "null":
return (_a32===null);
default:
if(dojo.lang.isFunction(type)){
return (_a32 instanceof type);
}else{
dojo.raise("dojo.lang.isOfType() was passed an invalid type");
}
}
}
dojo.raise("If we get here, it means a bug was introduced above.");
};
dojo.lang.getObject=function(str){
var _a3a=str.split("."),i=0,obj=dj_global;
do{
obj=obj[_a3a[i++]];
}while(i<_a3a.length&&obj);
return (obj!=dj_global)?obj:null;
};
dojo.lang.doesObjectExist=function(str){
var _a3e=str.split("."),i=0,obj=dj_global;
do{
obj=obj[_a3e[i++]];
}while(i<_a3e.length&&obj);
return (obj&&obj!=dj_global);
};
dojo.provide("dojo.lang.assert");
dojo.lang.assert=function(_a41,_a42){
if(!_a41){
var _a43="An assert statement failed.\n"+"The method dojo.lang.assert() was called with a 'false' value.\n";
if(_a42){
_a43+="Here's the assert message:\n"+_a42+"\n";
}
throw new Error(_a43);
}
};
dojo.lang.assertType=function(_a44,type,_a46){
if(!dojo.lang.isOfType(_a44,type,_a46)){
if(!dojo.lang.assertType._errorMessage){
dojo.lang.assertType._errorMessage="Type mismatch: dojo.lang.assertType() failed.";
}
dojo.lang.assert(false,dojo.lang.assertType._errorMessage);
}
};
dojo.lang.assertValidKeywords=function(_a47,_a48,_a49){
var key;
if(!_a49){
if(!dojo.lang.assertValidKeywords._errorMessage){
dojo.lang.assertValidKeywords._errorMessage="In dojo.lang.assertValidKeywords(), found invalid keyword:";
}
_a49=dojo.lang.assertValidKeywords._errorMessage;
}
if(dojo.lang.isArray(_a48)){
for(key in _a47){
if(!dojo.lang.inArray(_a48,key)){
dojo.lang.assert(false,_a49+" "+key);
}
}
}else{
for(key in _a47){
if(!(key in _a48)){
dojo.lang.assert(false,_a49+" "+key);
}
}
}
};
dojo.provide("dojo.AdapterRegistry");
dojo.AdapterRegistry=function(_a4b){
this.pairs=[];
this.returnWrappers=_a4b||false;
};
dojo.lang.extend(dojo.AdapterRegistry,{register:function(name,_a4d,wrap,_a4f,_a50){
var type=(_a50)?"unshift":"push";
this.pairs[type]([name,_a4d,wrap,_a4f]);
},match:function(){
for(var i=0;i<this.pairs.length;i++){
var pair=this.pairs[i];
if(pair[1].apply(this,arguments)){
if((pair[3])||(this.returnWrappers)){
return pair[2];
}else{
return pair[2].apply(this,arguments);
}
}
}
throw new Error("No match found");
},unregister:function(name){
for(var i=0;i<this.pairs.length;i++){
var pair=this.pairs[i];
if(pair[0]==name){
this.pairs.splice(i,1);
return true;
}
}
return false;
}});
dojo.provide("dojo.lang.repr");
dojo.lang.reprRegistry=new dojo.AdapterRegistry();
dojo.lang.registerRepr=function(name,_a58,wrap,_a5a){
dojo.lang.reprRegistry.register(name,_a58,wrap,_a5a);
};
dojo.lang.repr=function(obj){
if(typeof (obj)=="undefined"){
return "undefined";
}else{
if(obj===null){
return "null";
}
}
try{
if(typeof (obj["__repr__"])=="function"){
return obj["__repr__"]();
}else{
if((typeof (obj["repr"])=="function")&&(obj.repr!=arguments.callee)){
return obj["repr"]();
}
}
return dojo.lang.reprRegistry.match(obj);
}
catch(e){
if(typeof (obj.NAME)=="string"&&(obj.toString==Function.prototype.toString||obj.toString==Object.prototype.toString)){
return obj.NAME;
}
}
if(typeof (obj)=="function"){
obj=(obj+"").replace(/^\s+/,"");
var idx=obj.indexOf("{");
if(idx!=-1){
obj=obj.substr(0,idx)+"{...}";
}
}
return obj+"";
};
dojo.lang.reprArrayLike=function(arr){
try{
var na=dojo.lang.map(arr,dojo.lang.repr);
return "["+na.join(", ")+"]";
}
catch(e){
}
};
(function(){
var m=dojo.lang;
m.registerRepr("arrayLike",m.isArrayLike,m.reprArrayLike);
m.registerRepr("string",m.isString,m.reprString);
m.registerRepr("numbers",m.isNumber,m.reprNumber);
m.registerRepr("boolean",m.isBoolean,m.reprNumber);
})();
dojo.provide("dojo.lang.*");
dojo.provide("dojo.widget.Editor2Toolbar");
dojo.lang.declare("dojo.widget.HandlerManager",null,function(){
this._registeredHandlers=[];
},{registerHandler:function(obj,func){
if(arguments.length==2){
this._registeredHandlers.push(function(){
return obj[func].apply(obj,arguments);
});
}else{
this._registeredHandlers.push(obj);
}
},removeHandler:function(func){
for(var i=0;i<this._registeredHandlers.length;i++){
if(func===this._registeredHandlers[i]){
delete this._registeredHandlers[i];
return;
}
}
dojo.debug("HandlerManager handler "+func+" is not registered, can not remove.");
},destroy:function(){
for(var i=0;i<this._registeredHandlers.length;i++){
delete this._registeredHandlers[i];
}
}});
dojo.widget.Editor2ToolbarItemManager=new dojo.widget.HandlerManager;
dojo.lang.mixin(dojo.widget.Editor2ToolbarItemManager,{getToolbarItem:function(name){
var item;
name=name.toLowerCase();
for(var i=0;i<this._registeredHandlers.length;i++){
item=this._registeredHandlers[i](name);
if(item){
return item;
}
}
_deprecated=function(cmd,_a69){
if(!dojo.widget["Editor2Plugin"]||!dojo.widget.Editor2Plugin[_a69]){
dojo.deprecated("Toolbar item "+name+" is now defined in plugin dojo.widget.Editor2Plugin."+_a69+". It shall be required explicitly","0.6");
dojo["require"]("dojo.widget.Editor2Plugin."+_a69);
}
};
if(name=="forecolor"||name=="hilitecolor"){
_deprecated(name,"ColorPicker");
}else{
if(name=="formatblock"||name=="fontsize"||name=="fontname"){
_deprecated(name,"DropDownList");
}
}
switch(name){
case "bold":
case "copy":
case "cut":
case "delete":
case "indent":
case "inserthorizontalrule":
case "insertorderedlist":
case "insertunorderedlist":
case "italic":
case "justifycenter":
case "justifyfull":
case "justifyleft":
case "justifyright":
case "outdent":
case "paste":
case "redo":
case "removeformat":
case "selectall":
case "strikethrough":
case "subscript":
case "superscript":
case "underline":
case "undo":
case "unlink":
case "createlink":
case "insertimage":
case "htmltoggle":
item=new dojo.widget.Editor2ToolbarButton(name);
break;
case "forecolor":
case "hilitecolor":
item=new dojo.widget.Editor2ToolbarColorPaletteButton(name);
break;
case "plainformatblock":
item=new dojo.widget.Editor2ToolbarFormatBlockPlainSelect("formatblock");
break;
case "formatblock":
item=new dojo.widget.Editor2ToolbarFormatBlockSelect("formatblock");
break;
case "fontsize":
item=new dojo.widget.Editor2ToolbarFontSizeSelect("fontsize");
break;
case "fontname":
item=new dojo.widget.Editor2ToolbarFontNameSelect("fontname");
break;
case "inserthtml":
case "blockdirltr":
case "blockdirrtl":
case "dirltr":
case "dirrtl":
case "inlinedirltr":
case "inlinedirrtl":
dojo.debug("Not yet implemented toolbar item: "+name);
break;
default:
dojo.debug("dojo.widget.Editor2ToolbarItemManager.getToolbarItem: Unknown toolbar item: "+name);
}
return item;
}});
dojo.addOnUnload(dojo.widget.Editor2ToolbarItemManager,"destroy");
dojo.declare("dojo.widget.Editor2ToolbarButton",null,function(name){
this._name=name;
},{create:function(node,_a6c,_a6d){
this._domNode=node;
var cmd=_a6c.parent.getCommand(this._name);
if(cmd){
this._domNode.title=cmd.getText();
}
this.disableSelection(this._domNode);
this._parentToolbar=_a6c;
dojo.event.connect(this._domNode,"onclick",this,"onClick");
if(!_a6d){
dojo.event.connect(this._domNode,"onmouseover",this,"onMouseOver");
dojo.event.connect(this._domNode,"onmouseout",this,"onMouseOut");
}
},disableSelection:function(_a6f){
dojo.html.disableSelection(_a6f);
var _a70=_a6f.all||_a6f.getElementsByTagName("*");
for(var x=0;x<_a70.length;x++){
dojo.html.disableSelection(_a70[x]);
}
},onMouseOver:function(){
var _a72=dojo.widget.Editor2Manager.getCurrentInstance();
if(_a72){
var _a73=_a72.getCommand(this._name);
if(_a73&&_a73.getState()!=dojo.widget.Editor2Manager.commandState.Disabled){
this.highlightToolbarItem();
}
}
},onMouseOut:function(){
this.unhighlightToolbarItem();
},destroy:function(){
this._domNode=null;
this._parentToolbar=null;
},onClick:function(e){
if(this._domNode&&!this._domNode.disabled&&this._parentToolbar.checkAvailability()){
e.preventDefault();
e.stopPropagation();
var _a75=dojo.widget.Editor2Manager.getCurrentInstance();
if(_a75){
var _a76=_a75.getCommand(this._name);
if(_a76){
_a76.execute();
}
}
}
},refreshState:function(){
var _a77=dojo.widget.Editor2Manager.getCurrentInstance();
var em=dojo.widget.Editor2Manager;
if(_a77){
var _a79=_a77.getCommand(this._name);
if(_a79){
var _a7a=_a79.getState();
if(_a7a!=this._lastState){
switch(_a7a){
case em.commandState.Latched:
this.latchToolbarItem();
break;
case em.commandState.Enabled:
this.enableToolbarItem();
break;
case em.commandState.Disabled:
default:
this.disableToolbarItem();
}
this._lastState=_a7a;
}
}
}
return em.commandState.Enabled;
},latchToolbarItem:function(){
this._domNode.disabled=false;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarLatchedItemStyle);
},enableToolbarItem:function(){
this._domNode.disabled=false;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarEnabledItemStyle);
},disableToolbarItem:function(){
this._domNode.disabled=true;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarDisabledItemStyle);
},highlightToolbarItem:function(){
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarHighlightedItemStyle);
},unhighlightToolbarItem:function(){
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarHighlightedItemStyle);
},removeToolbarItemStyle:function(){
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarEnabledItemStyle);
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarLatchedItemStyle);
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarDisabledItemStyle);
this.unhighlightToolbarItem();
}});
dojo.declare("dojo.widget.Editor2ToolbarFormatBlockPlainSelect",dojo.widget.Editor2ToolbarButton,{create:function(node,_a7c){
this._domNode=node;
this._parentToolbar=_a7c;
this._domNode=node;
this.disableSelection(this._domNode);
dojo.event.connect(this._domNode,"onchange",this,"onChange");
},destroy:function(){
this._domNode=null;
},onChange:function(){
if(this._parentToolbar.checkAvailability()){
var sv=this._domNode.value.toLowerCase();
var _a7e=dojo.widget.Editor2Manager.getCurrentInstance();
if(_a7e){
var _a7f=_a7e.getCommand(this._name);
if(_a7f){
_a7f.execute(sv);
}
}
}
},refreshState:function(){
if(this._domNode){
dojo.widget.Editor2ToolbarFormatBlockPlainSelect.superclass.refreshState.call(this);
var _a80=dojo.widget.Editor2Manager.getCurrentInstance();
if(_a80){
var _a81=_a80.getCommand(this._name);
if(_a81){
var _a82=_a81.getValue();
if(!_a82){
_a82="";
}
dojo.lang.forEach(this._domNode.options,function(item){
if(item.value.toLowerCase()==_a82.toLowerCase()){
item.selected=true;
}
});
}
}
}
}});
dojo.widget.defineWidget("dojo.widget.Editor2Toolbar",dojo.widget.HtmlWidget,function(){
dojo.event.connect(this,"fillInTemplate",dojo.lang.hitch(this,function(){
if(dojo.render.html.ie){
this.domNode.style.zoom=1;
}
}));
},{templatePath:dojo.uri.dojoUri("src/widget/templates/EditorToolbar.html"),templateCssPath:dojo.uri.dojoUri("src/widget/templates/EditorToolbar.css"),ToolbarLatchedItemStyle:"ToolbarButtonLatched",ToolbarEnabledItemStyle:"ToolbarButtonEnabled",ToolbarDisabledItemStyle:"ToolbarButtonDisabled",ToolbarHighlightedItemStyle:"ToolbarButtonHighlighted",ToolbarHighlightedSelectStyle:"ToolbarSelectHighlighted",ToolbarHighlightedSelectItemStyle:"ToolbarSelectHighlightedItem",postCreate:function(){
var _a84=dojo.html.getElementsByClass("dojoEditorToolbarItem",this.domNode);
this.items={};
for(var x=0;x<_a84.length;x++){
var node=_a84[x];
var _a87=node.getAttribute("dojoETItemName");
if(_a87){
var item=dojo.widget.Editor2ToolbarItemManager.getToolbarItem(_a87);
if(item){
item.create(node,this);
this.items[_a87.toLowerCase()]=item;
}else{
node.style.display="none";
}
}
}
},update:function(){
for(var cmd in this.items){
this.items[cmd].refreshState();
}
},shareGroup:"",checkAvailability:function(){
if(!this.shareGroup){
this.parent.focus();
return true;
}
var _a8a=dojo.widget.Editor2Manager.getCurrentInstance();
if(this.shareGroup==_a8a.toolbarGroup){
return true;
}
return false;
},destroy:function(){
for(var it in this.items){
this.items[it].destroy();
delete this.items[it];
}
dojo.widget.Editor2Toolbar.superclass.destroy.call(this);
}});
dojo.provide("dojo.i18n.common");
dojo.i18n.getLocalization=function(_a8c,_a8d,_a8e){
dojo.hostenv.preloadLocalizations();
_a8e=dojo.hostenv.normalizeLocale(_a8e);
var _a8f=_a8e.split("-");
var _a90=[_a8c,"nls",_a8d].join(".");
var _a91=dojo.hostenv.findModule(_a90,true);
var _a92;
for(var i=_a8f.length;i>0;i--){
var loc=_a8f.slice(0,i).join("_");
if(_a91[loc]){
_a92=_a91[loc];
break;
}
}
if(!_a92){
_a92=_a91.ROOT;
}
if(_a92){
var _a95=function(){
};
_a95.prototype=_a92;
return new _a95();
}
dojo.raise("Bundle not found: "+_a8d+" in "+_a8c+" , locale="+_a8e);
};
dojo.i18n.isLTR=function(_a96){
var lang=dojo.hostenv.normalizeLocale(_a96).split("-")[0];
var RTL={ar:true,fa:true,he:true,ur:true,yi:true};
return !RTL[lang];
};
dojo.provide("dojo.uri.cache");
dojo.uri.cache={_cache:{},set:function(uri,_a9a){
this._cache[uri.toString()]=_a9a;
return uri;
},remove:function(uri){
delete this._cache[uri.toString()];
},get:function(uri){
var key=uri.toString();
var _a9e=this._cache[key];
if(!_a9e){
_a9e=dojo.hostenv.getText(key);
if(_a9e){
this._cache[key]=_a9e;
}
}
return _a9e;
},allow:function(uri){
return uri;
}};
dojo.provide("dojo.widget.Editor2Plugin.AlwaysShowToolbar");
dojo.event.topic.subscribe("dojo.widget.Editor2::onLoad",function(_aa0){
if(_aa0.toolbarAlwaysVisible){
var p=new dojo.widget.Editor2Plugin.AlwaysShowToolbar(_aa0);
}
});
dojo.declare("dojo.widget.Editor2Plugin.AlwaysShowToolbar",null,function(_aa2){
this.editor=_aa2;
this.editor.registerLoadedPlugin(this);
this.setup();
},{_scrollSetUp:false,_fixEnabled:false,_scrollThreshold:false,_handleScroll:true,setup:function(){
var tdn=this.editor.toolbarWidget;
if(!tdn.tbBgIframe){
tdn.tbBgIframe=new dojo.html.BackgroundIframe(tdn.domNode);
tdn.tbBgIframe.onResized();
}
this.scrollInterval=setInterval(dojo.lang.hitch(this,"globalOnScrollHandler"),100);
dojo.event.connect("before",this.editor.toolbarWidget,"destroy",this,"destroy");
},globalOnScrollHandler:function(){
var isIE=dojo.render.html.ie;
if(!this._handleScroll){
return;
}
var dh=dojo.html;
var tdn=this.editor.toolbarWidget.domNode;
var db=dojo.body();
if(!this._scrollSetUp){
this._scrollSetUp=true;
var _aa8=dh.getMarginBox(this.editor.domNode).width;
this._scrollThreshold=dh.abs(tdn,true).y;
if((isIE)&&(db)&&(dh.getStyle(db,"background-image")=="none")){
with(db.style){
backgroundImage="url("+dojo.uri.dojoUri("src/widget/templates/images/blank.gif")+")";
backgroundAttachment="fixed";
}
}
}
var _aa9=(window["pageYOffset"])?window["pageYOffset"]:(document["documentElement"]||document["body"]).scrollTop;
if(_aa9>this._scrollThreshold){
if(!this._fixEnabled){
var _aaa=dojo.html.getMarginBox(tdn);
this.editor.editorObject.style.marginTop=_aaa.height+"px";
if(isIE){
tdn.style.left=dojo.html.abs(tdn,dojo.html.boxSizing.MARGIN_BOX).x;
if(tdn.previousSibling){
this._IEOriginalPos=["after",tdn.previousSibling];
}else{
if(tdn.nextSibling){
this._IEOriginalPos=["before",tdn.nextSibling];
}else{
this._IEOriginalPos=["",tdn.parentNode];
}
}
dojo.body().appendChild(tdn);
dojo.html.addClass(tdn,"IEFixedToolbar");
}else{
with(tdn.style){
position="fixed";
top="0px";
}
}
tdn.style.width=_aaa.width+"px";
tdn.style.zIndex=1000;
this._fixEnabled=true;
}
if(!dojo.render.html.safari){
var _aab=(this.height)?parseInt(this.editor.height):this.editor._lastHeight;
if(_aa9>(this._scrollThreshold+_aab)){
tdn.style.display="none";
}else{
tdn.style.display="";
}
}
}else{
if(this._fixEnabled){
(this.editor.object||this.editor.iframe).style.marginTop=null;
with(tdn.style){
position="";
top="";
zIndex="";
display="";
}
if(isIE){
tdn.style.left="";
dojo.html.removeClass(tdn,"IEFixedToolbar");
if(this._IEOriginalPos){
dojo.html.insertAtPosition(tdn,this._IEOriginalPos[1],this._IEOriginalPos[0]);
this._IEOriginalPos=null;
}else{
dojo.html.insertBefore(tdn,this.editor.object||this.editor.iframe);
}
}
tdn.style.width="";
this._fixEnabled=false;
}
}
},destroy:function(){
this._IEOriginalPos=null;
this._handleScroll=false;
clearInterval(this.scrollInterval);
this.editor.unregisterLoadedPlugin(this);
if(dojo.render.html.ie){
dojo.html.removeClass(this.editor.toolbarWidget.domNode,"IEFixedToolbar");
}
}});
dojo.provide("dojo.widget.Editor2");
dojo.widget.Editor2Manager=new dojo.widget.HandlerManager();
dojo.lang.mixin(dojo.widget.Editor2Manager,{_currentInstance:null,commandState:{Disabled:0,Latched:1,Enabled:2},getCurrentInstance:function(){
return this._currentInstance;
},setCurrentInstance:function(inst){
this._currentInstance=inst;
},getCommand:function(_aad,name){
var _aaf;
name=name.toLowerCase();
for(var i=0;i<this._registeredHandlers.length;i++){
_aaf=this._registeredHandlers[i](_aad,name);
if(_aaf){
return _aaf;
}
}
if(name=="createlink"||name=="insertimage"){
if(!dojo.widget["Editor2Plugin"]||!dojo.widget.Editor2Plugin["DialogCommands"]){
dojo.deprecated("Command "+name+" is now defined in plugin dojo.widget.Editor2Plugin.DialogCommands. It shall be required explicitly","0.6");
dojo["require"]("dojo.widget.Editor2Plugin.DialogCommands");
}
}
var _ab1=dojo.i18n.getLocalization("dojo.widget","Editor2",this.lang);
switch(name){
case "htmltoggle":
_aaf=new dojo.widget.Editor2BrowserCommand(_aad,name);
break;
case "anchor":
_aaf=new dojo.widget.Editor2Command(_aad,name);
break;
case "createlink":
_aaf=new dojo.widget.Editor2DialogCommand(_aad,name,{contentFile:"dojo.widget.Editor2Plugin.CreateLinkDialog",contentClass:"Editor2CreateLinkDialog",title:_ab1.createLinkDialogTitle,width:"300px",height:"200px",lang:this.lang});
break;
case "insertimage":
_aaf=new dojo.widget.Editor2DialogCommand(_aad,name,{contentFile:"dojo.widget.Editor2Plugin.InsertImageDialog",contentClass:"Editor2InsertImageDialog",title:_ab1.insertImageDialogTitle,width:"400px",height:"270px",lang:this.lang});
break;
default:
var _ab2=this.getCurrentInstance();
if((_ab2&&_ab2.queryCommandAvailable(name))||(!_ab2&&dojo.widget.Editor2.prototype.queryCommandAvailable(name))){
_aaf=new dojo.widget.Editor2BrowserCommand(_aad,name);
}else{
dojo.debug("dojo.widget.Editor2Manager.getCommand: Unknown command "+name);
return;
}
}
return _aaf;
},destroy:function(){
this._currentInstance=null;
dojo.widget.HandlerManager.prototype.destroy.call(this);
}});
dojo.addOnUnload(dojo.widget.Editor2Manager,"destroy");
dojo.lang.declare("dojo.widget.Editor2Command",null,function(_ab3,name){
this._editor=_ab3;
this._updateTime=0;
this._name=name;
},{_text:"Unknown",execute:function(para){
dojo.unimplemented("dojo.widget.Editor2Command.execute");
},getText:function(){
return this._text;
},getState:function(){
return dojo.widget.Editor2Manager.commandState.Enabled;
},destroy:function(){
}});
dojo.lang.declare("dojo.widget.Editor2BrowserCommand",dojo.widget.Editor2Command,function(_ab6,name){
var _ab8=dojo.i18n.getLocalization("dojo.widget","Editor2BrowserCommand",_ab6.lang);
var text=_ab8[name.toLowerCase()];
if(text){
this._text=text;
}
},{execute:function(para){
this._editor.execCommand(this._name,para);
},getState:function(){
if(this._editor._lastStateTimestamp>this._updateTime||this._state==undefined){
this._updateTime=this._editor._lastStateTimestamp;
try{
if(this._editor.queryCommandEnabled(this._name)){
if(this._editor.queryCommandState(this._name)){
this._state=dojo.widget.Editor2Manager.commandState.Latched;
}else{
this._state=dojo.widget.Editor2Manager.commandState.Enabled;
}
}else{
this._state=dojo.widget.Editor2Manager.commandState.Disabled;
}
}
catch(e){
this._state=dojo.widget.Editor2Manager.commandState.Enabled;
}
}
return this._state;
},getValue:function(){
try{
return this._editor.queryCommandValue(this._name);
}
catch(e){
}
}});
dojo.widget.Editor2ToolbarGroups={};
dojo.widget.defineWidget("dojo.widget.Editor2",dojo.widget.RichText,function(){
this._loadedCommands={};
},{toolbarAlwaysVisible:false,toolbarWidget:null,scrollInterval:null,toolbarTemplatePath:dojo.uri.cache.allow(dojo.uri.dojoUri("src/widget/templates/EditorToolbarOneline.html")),toolbarTemplateCssPath:dojo.uri.cache.allow(dojo.uri.dojoUri("src/widget/templates/EditorToolbar.css")),toolbarPlaceHolder:"",_inSourceMode:false,_htmlEditNode:null,toolbarGroup:"",shareToolbar:false,contextMenuGroupSet:"",editorOnLoad:function(){
dojo.event.topic.publish("dojo.widget.Editor2::preLoadingToolbar",this);
if(this.toolbarAlwaysVisible){
dojo.require("dojo.widget.Editor2Plugin.AlwaysShowToolbar");
}
if(this.toolbarWidget){
this.toolbarWidget.show();
dojo.html.insertBefore(this.toolbarWidget.domNode,this.domNode.firstChild);
}else{
if(this.shareToolbar){
dojo.deprecated("Editor2:shareToolbar is deprecated in favor of toolbarGroup","0.5");
this.toolbarGroup="defaultDojoToolbarGroup";
}
if(this.toolbarGroup){
if(dojo.widget.Editor2ToolbarGroups[this.toolbarGroup]){
this.toolbarWidget=dojo.widget.Editor2ToolbarGroups[this.toolbarGroup];
}
}
if(!this.toolbarWidget){
var _abb={shareGroup:this.toolbarGroup,parent:this,lang:this.lang};
_abb.templateString=dojo.uri.cache.get(this.toolbarTemplatePath);
if(this.toolbarTemplateCssPath){
_abb.templateCssPath=this.toolbarTemplateCssPath;
_abb.templateCssString=dojo.uri.cache.get(this.toolbarTemplateCssPath);
}
if(this.toolbarPlaceHolder){
this.toolbarWidget=dojo.widget.createWidget("Editor2Toolbar",_abb,dojo.byId(this.toolbarPlaceHolder),"after");
}else{
this.toolbarWidget=dojo.widget.createWidget("Editor2Toolbar",_abb,this.domNode.firstChild,"before");
}
if(this.toolbarGroup){
dojo.widget.Editor2ToolbarGroups[this.toolbarGroup]=this.toolbarWidget;
}
dojo.event.connect(this,"close",this.toolbarWidget,"hide");
this.toolbarLoaded();
}
}
dojo.event.topic.registerPublisher("Editor2.clobberFocus",this,"clobberFocus");
dojo.event.topic.subscribe("Editor2.clobberFocus",this,"setBlur");
dojo.event.topic.publish("dojo.widget.Editor2::onLoad",this);
},toolbarLoaded:function(){
},registerLoadedPlugin:function(obj){
if(!this.loadedPlugins){
this.loadedPlugins=[];
}
this.loadedPlugins.push(obj);
},unregisterLoadedPlugin:function(obj){
for(var i in this.loadedPlugins){
if(this.loadedPlugins[i]===obj){
delete this.loadedPlugins[i];
return;
}
}
dojo.debug("dojo.widget.Editor2.unregisterLoadedPlugin: unknown plugin object: "+obj);
},execCommand:function(_abf,_ac0){
switch(_abf.toLowerCase()){
case "htmltoggle":
this.toggleHtmlEditing();
break;
default:
dojo.widget.Editor2.superclass.execCommand.apply(this,arguments);
}
},queryCommandEnabled:function(_ac1,_ac2){
switch(_ac1.toLowerCase()){
case "htmltoggle":
return true;
default:
if(this._inSourceMode){
return false;
}
return dojo.widget.Editor2.superclass.queryCommandEnabled.apply(this,arguments);
}
},queryCommandState:function(_ac3,_ac4){
switch(_ac3.toLowerCase()){
case "htmltoggle":
return this._inSourceMode;
default:
return dojo.widget.Editor2.superclass.queryCommandState.apply(this,arguments);
}
},onClick:function(e){
dojo.widget.Editor2.superclass.onClick.call(this,e);
if(dojo.widget.PopupManager){
if(!e){
e=this.window.event;
}
dojo.widget.PopupManager.onClick(e);
}
},clobberFocus:function(){
},toggleHtmlEditing:function(){
if(this===dojo.widget.Editor2Manager.getCurrentInstance()){
if(!this._inSourceMode){
var html=this.getEditorContent();
this._inSourceMode=true;
if(!this._htmlEditNode){
this._htmlEditNode=dojo.doc().createElement("textarea");
dojo.html.insertAfter(this._htmlEditNode,this.editorObject);
}
this._htmlEditNode.style.display="";
this._htmlEditNode.style.width="100%";
this._htmlEditNode.style.height=dojo.html.getBorderBox(this.editNode).height+"px";
this._htmlEditNode.value=html;
with(this.editorObject.style){
position="absolute";
left="-2000px";
top="-2000px";
}
}else{
this._inSourceMode=false;
this._htmlEditNode.blur();
with(this.editorObject.style){
position="";
left="";
top="";
}
var html=this._htmlEditNode.value;
dojo.lang.setTimeout(this,"replaceEditorContent",1,html);
this._htmlEditNode.style.display="none";
this.focus();
}
this.onDisplayChanged(null,true);
}
},setFocus:function(){
if(dojo.widget.Editor2Manager.getCurrentInstance()===this){
return;
}
this.clobberFocus();
dojo.widget.Editor2Manager.setCurrentInstance(this);
},setBlur:function(){
},saveSelection:function(){
this._bookmark=null;
this._bookmark=dojo.withGlobal(this.window,dojo.html.selection.getBookmark);
},restoreSelection:function(){
if(this._bookmark){
this.focus();
dojo.withGlobal(this.window,"moveToBookmark",dojo.html.selection,[this._bookmark]);
this._bookmark=null;
}else{
dojo.debug("restoreSelection: no saved selection is found!");
}
},_updateToolbarLastRan:null,_updateToolbarTimer:null,_updateToolbarFrequency:500,updateToolbar:function(_ac7){
if((!this.isLoaded)||(!this.toolbarWidget)){
return;
}
var diff=new Date()-this._updateToolbarLastRan;
if((!_ac7)&&(this._updateToolbarLastRan)&&((diff<this._updateToolbarFrequency))){
clearTimeout(this._updateToolbarTimer);
var _ac9=this;
this._updateToolbarTimer=setTimeout(function(){
_ac9.updateToolbar();
},this._updateToolbarFrequency/2);
return;
}else{
this._updateToolbarLastRan=new Date();
}
if(dojo.widget.Editor2Manager.getCurrentInstance()!==this){
return;
}
this.toolbarWidget.update();
},destroy:function(_aca){
this._htmlEditNode=null;
dojo.event.disconnect(this,"close",this.toolbarWidget,"hide");
if(!_aca){
this.toolbarWidget.destroy();
}
dojo.widget.Editor2.superclass.destroy.call(this);
},_lastStateTimestamp:0,onDisplayChanged:function(e,_acc){
this._lastStateTimestamp=(new Date()).getTime();
dojo.widget.Editor2.superclass.onDisplayChanged.call(this,e);
this.updateToolbar(_acc);
},onLoad:function(){
try{
dojo.widget.Editor2.superclass.onLoad.call(this);
}
catch(e){
dojo.debug(e);
}
this.editorOnLoad();
},onFocus:function(){
dojo.widget.Editor2.superclass.onFocus.call(this);
this.setFocus();
},getEditorContent:function(){
if(this._inSourceMode){
return this._htmlEditNode.value;
}
return dojo.widget.Editor2.superclass.getEditorContent.call(this);
},replaceEditorContent:function(html){
if(this._inSourceMode){
this._htmlEditNode.value=html;
return;
}
dojo.widget.Editor2.superclass.replaceEditorContent.apply(this,arguments);
},getCommand:function(name){
if(this._loadedCommands[name]){
return this._loadedCommands[name];
}
var cmd=dojo.widget.Editor2Manager.getCommand(this,name);
this._loadedCommands[name]=cmd;
return cmd;
},shortcuts:[["bold"],["italic"],["underline"],["selectall","a"],["insertunorderedlist","\\"]],setupDefaultShortcuts:function(){
var exec=function(cmd){
return function(){
cmd.execute();
};
};
var self=this;
dojo.lang.forEach(this.shortcuts,function(item){
var cmd=self.getCommand(item[0]);
if(cmd){
self.addKeyHandler(item[1]?item[1]:item[0].charAt(0),item[2]==undefined?self.KEY_CTRL:item[2],exec(cmd));
}
});
}});

