// Plugin for htmlArea to realtime validate code by a ruleset,
// This ensures semantically clean content.
// By Troels Knak-Nielsen
//
// Distributed under the terms of LGPL
// This notice MUST stay intact for use (see license.txt).

function Indite(editor) {
	this.editor = editor;

	var cfg = editor.config;
	
	cfg.pageStyle = "body {font-family : verdana; font-size : 85%;} p { border : 1px dotted #008000; } ol, ul { border : 1px dotted #287ed1; } h2 { border : 1px dotted #ffa500; } pre { border : 1px dotted #888888; background-color : #eeeeee; } ul, ol, h2, p, pre { margin-top : .5em; margin-bottom : .5em; }";
	var self = this;
};

Indite._pluginInfo = {
	name          : "Indite",
	version       : "RC1",
	developer     : "Troels Knak-Nielsen",
	developer_url : "http://www.kyberfabrikken.dk/opensource/indite/",
	sponsor       : "Kyberfabrikken",
	sponsor_url   : "http://www.kyberfabrikken.dk/",
	license       : "LGPL"
};

/**
  * test if value is present in array
  *
  * @param value the value to be found
  * @param a the array to traverse
  * @returns true or false
  */
Indite.inArray = function(value, a)
{
	for (var i = 0; i < a.length; i++)
		if (a[i] == value) return true;
	return false;
}

Indite.prototype.onGenerate = function()
{
	var self = this;
	var doc = this.editor._iframe.contentWindow.document;
	this.runtimeTransformer = new RuntimeTransformer(doc, this.editor);
	this.finalTransformer = new FinalTransformer(doc, this.editor);

	// here we hook the runtime transformer up to some events, that may modify our document.
	// most notably dragdrop and paste
	var realDoc = HTMLArea.is_ie ? doc.body : doc;
	HTMLArea._addEvents(realDoc, ["paste","dragdrop","drop","resizeend"],
		 function (e) {
		 	return self.invokeTransform(HTMLArea.is_ie ? self.editor._iframe.contentWindow.event : event);
		 });


	// since htmlarea provides no means to trigger an event on submit,
	// we hook it manually here. it should work, but it's rather un-elegant
	var editor = self.editor;
	if (self.editor._textArea.form) {
		// we have a form, on submit get the HTMLArea content and
		// update original textarea.
		var f = self.editor._textArea.form;
		if (typeof f.onsubmit == "function") {
			var funcref = f.onsubmit;
			if (typeof f.__msh_prevOnSubmit == "undefined") {
				f.__msh_prevOnSubmit = [];
			}
			f.__msh_prevOnSubmit.push(funcref);
		}
		f.onsubmit = function() {
			editor.plugins["Indite"].instance.finalTransformer.transformDoc();
			var a = this.__msh_prevOnSubmit;
			// call previous submit methods if they were there.
			if (typeof a != "undefined") {
				for (var i in a) {
					a[i]();
				}
			}
		};
	}
	// fire the initial transform at loadtime
	this.invokeTransform(null);
};

Indite.prototype.onUpdateToolbar = function(ev)
{
	this.invokeTransform(ev);
}

/*
Indite.prototype.onKeyPress = function(ev) {
	var keyCode = HTMLArea.is_ie ? ev.keyCode : ev.charCodeM
	if (Indite.inArray(keyCode, [38,40,33,34,35,36,37,39,16,17,18,20]))
		return true;
	this.invokeTransform(ev);
};
*/

/**
  * This is the main engine of the plug
  * Here we perform runtime transformation of the document to suit our needs
  */
Indite.prototype.invokeTransform = function(ev) {
	this.editor._undoTakeSnapshot();
	this.runtimeTransformer.transformDoc();
};


//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
// Here comes the magic
function DOMTransformer(doc, x)
{
	this.doc = doc;
	this.x = x;

	this.validNodes = Array();
	this.validNodeAttributes = Array();
	this.allowEmptyNodes = Array();
	this.allowInRoot = Array();
	this.pureTextNodes = Array();
	
	this.rootEncapsulate = true;

	/**
	  * transforms document
	  *
	  * @returns void
	  */
	this.transformDoc = function()
	{
		// the main transform
		this._transformDoc(this.doc.body);
		
		if (this.rootEncapsulate) {
			// encapsulate rootnodes in <P>
			for (var i=0; i < this.doc.body.childNodes.length; i++) {
				var c = this.doc.body.childNodes[i];

				var bIsAllWhite = false;
				if (c.nodeType == 3) {
					var s = c.nodeValue;
					var re = new RegExp(/^[\s]*$/);
					bIsAllWhite = (s.match(re));	// no pun intended
				}

				if (!bIsAllWhite && (c.nodeType != 1 || !Indite.inArray(c.nodeName.toLowerCase(), this.allowInRoot))) {
					var p = this.doc.createElement("P");
					p.appendChild(c.cloneNode(true));
					if (document.replaceNode)  c.replaceNode(p); // ie
					else {
						// allow style in root for moz
						if (c.nodeName.toLowerCase() != "style")
							c.parentNode.replaceChild(p, c); // moz
					}

					///////////////////////////////////////////////////////////////////
					if (document.getSelection) {
						// moz
						var sel = this.x._getSelection();
						if (sel) {
							var range = sel.getRangeAt(0);
						} else {
							var range = this.doc.createRange();
						}
					} else if (document.selection) {
						// ie
						var sel = this.doc.selection;
						var range = sel.createRange();
						range.move("character", -1);
						range.move("character", 1);
						range.select();
					}
					//
					///////////////////////////////////////////////////////////////////
				}
			}
		}
	} // end function transformDoc
	
	this._transformSpanNode = function(n)
	{
		return null;
	}

	this._transformImgNode = function(n)
	{
		return null;
	}
	
	this._transformDoc = function(doc)
	{
		var _stack = Array();
		for (var i=0; i < doc.childNodes.length; i++) {
			_stack.push(doc.childNodes[i]);
		}

		var overflow=0;
		while (_stack.length > 0) {
			overflow++;
			if (overflow++ > 8000) {
				alert("Stack overflow error");
				return false;
			}
			c = _stack.pop();
			for (var i=0; i < c.childNodes.length; i++) {
				_stack.push(c.childNodes[i]);
			}
			if (c.nodeType == 1) {
				nameOfNode = c.nodeName.toLowerCase();

				if (nameOfNode == "span") {
					var newNode = this._transformSpanNode(c);
					if (newNode != null) {
						for (var k=0; k < c.childNodes.length; k++) {
//							newNode.appendChild(c.childNodes[k].cloneNode(true));
							tmpNode = c.childNodes[k].cloneNode(false);
							for (var l=0; l < c.childNodes[k].childNodes.length; l++) {
								tmpNode.appendChild(c.childNodes[k].childNodes[l]);
							}

						}
						c.parentNode.insertBefore(newNode, c);
						c.parentNode.removeChild(c);
					}
				} else if (typeof(this.validNodes[nameOfNode]) == "undefined") {
					for (var k=0; k < c.childNodes.length; k++) {
//						newNode = c.childNodes[k].cloneNode(true);
						newNode = c.childNodes[k].cloneNode(false);
						for (var l=0; l < c.childNodes[k].childNodes.length; l++) {
							newNode.appendChild(c.childNodes[k].childNodes[l]);
						}
						c.parentNode.insertBefore(newNode, c);
						_stack.push(newNode);
					}
					c.parentNode.removeChild(c);
				} else if (Indite.inArray(c.parentNode.nodeName.toLowerCase(), this.pureTextNodes)) {
					for (var k=0; k < c.childNodes.length; k++) {
						newNode = c.childNodes[k].cloneNode(true);
						c.parentNode.insertBefore(newNode, c);
						_stack.push(newNode);
					}
					c.parentNode.removeChild(c);
				} else {

					if (nameOfNode == "img") {
						this._transformImgNode(c);
					}
					
					breakout = false;
					if (this.validNodes[nameOfNode] != nameOfNode) {
						// replace node with other
						var newNode = this.doc.createElement(this.validNodes[nameOfNode]);
						for (var k=0; k < c.childNodes.length; k++)
							newNode.appendChild(c.childNodes[k].cloneNode(true));
						c.parentNode.insertBefore(newNode, c);
						c.parentNode.removeChild(c);
						_stack.push(newNode);
						breakout = true;
					}
					if (!breakout) {
						if (!c.hasChildNodes() && !Indite.inArray(nameOfNode, this.allowEmptyNodes)) {
							var sel = this.x._getSelection();
							var range = this.x._createRange(sel);
							if (range.parentNode != c) {
								c.parentNode.removeChild(c);
								breakout = true;
							}
						}
					}

					if (!breakout) {
						for (var j=0; j < c.attributes.length; j++) {
							if (c.attributes[j].specified) {
								if ( typeof(this.validNodeAttributes[nameOfNode]) == "undefined"
								|| !Indite.inArray(c.attributes[j].name.toLowerCase(), this.validNodeAttributes[nameOfNode]) ) {
									if (c.attributes[j].name == "class")
										c.removeAttribute("className");
									c.removeAttribute(c.attributes[j].name);
								}
							}
						}
					}
				}
			}
		}
	} // end function 
}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

/**
  * class RuntimeTransformer extends DOMTransformer
  *
  * The RuntimeTransformer object is used to transform the document in
  * realtime - eg. while the user types. It maintains the working document.
  */
function RuntimeTransformer(doc, x)
{
	// call parent constructor
	this.base = DOMTransformer;
	this.base(doc, x);

	this.validNodes["h2"] = "h2";
	this.validNodes["p"] = "p";
	this.validNodes["b"] = "b";
	this.validNodes["i"] = "i";
	this.validNodes["strike"] = "strike";
	this.validNodes["br"] = "br";
	this.validNodes["a"] = "a";
	
	this.validNodes["ul"] = "ul";
	this.validNodes["ol"] = "ol";
	this.validNodes["li"] = "li";

	this.validNodes["strong"] = "b";
	this.validNodes["em"] = "i";
	this.validNodes["del"] = "strike";
	this.validNodes["s"] = "strike";

	this.validNodes["h1"] = "h2";
	this.validNodes["h3"] = "h2";
	this.validNodes["h4"] = "h2";
	this.validNodes["h5"] = "h2";

	this.validNodes["td"] = "p";
	
	this.validNodes["sup"] = "sup";
	this.validNodes["sub"] = "sub";
	this.validNodes["pre"] = "pre";
	
	this.validNodes["img"] = "img";
	
	this.validNodeAttributes["a"] = Array("href", "target");
	this.validNodeAttributes["img"] = Array("src", "height", "width", "align", "alt");
	this.validNodeAttributes["p"] = Array("align");
	
	this.allowEmptyNodes = Array("br");
	this.allowEmptyNodes = Array("br", "img");
	this.allowInRoot = Array("h2", "p", "ol", "ul", "pre");
	
	this.pureTextNodes = Array("h2", "a");
	
	this.rootEncapsulate = true;

	this._transformSpanNode = function(n)
	{
		if (n.style.fontWeight.toLowerCase() == "bold") {
			return this.doc.createElement("b");
		} else if (n.style.fontStyle.toLowerCase() == "italic") {
			return this.doc.createElement("i");
		} else {
			// neither - kill!
			if (document.removeNode) n.removeNode(); // ie
			else n.parentNode.removeChild(n); // moz
			return null;
		}
	}

	this._transformImgNode = function(n)
	{
		// converting style float > align
		if (n.style.styleFloat) {
			var styleFloat = n.style.styleFloat;
		} else if (n.style.cssFloat) {
			var styleFloat = n.style.cssFloat;
		} else {
			var styleFloat = "";
		}
		if (styleFloat.toLowerCase() == "right") {
			n.align = "right";
		} else if (styleFloat.toLowerCase() == "left") {
			n.align = "left";
		}
		n.removeAttribute("style");
	}
}
RuntimeTransformer.prototype = new DOMTransformer;

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

/**
  * class FinalTransformer extends DOMTransformer
  *
  * the finaltransformer is called just before submitting content from the form.
  * the source document will already be transformed by the runtimetransformer
  * before this happens.
  */
function FinalTransformer(doc, x)
{
	// call parent constructor
	this.base = DOMTransformer;
	this.base(doc, x);

	this.validNodes = Array();
	this.validNodes["h2"] = "h2";
	this.validNodes["p"] = "p";
	this.validNodes["strong"] = "strong";
	this.validNodes["em"] = "em";
	this.validNodes["br"] = "br";
	this.validNodes["a"] = "a";
	
	this.validNodes["ul"] = "ul";
	this.validNodes["ol"] = "ol";
	this.validNodes["li"] = "li";

	this.validNodes["b"] = "strong";
	this.validNodes["i"] = "em";
	
	this.validNodes["img"] = "img";

	this.validNodeAttributes = Array();
	this.validNodeAttributes["a"] = Array("href", "target");
	this.validNodeAttributes["img"] = Array("src", "height", "width", "style", "alt");
	
	this.allowEmptyNodes = Array("br", "img");
	this.allowInRoot = Array("h2", "p", "ol", "ul");
	
	this.pureTextNodes = Array("h2", "a");
	
	this.rootEncapsulate = true;

	this._transformSpanNode = function(n)
	{
		if (n.style.fontWeight.toLowerCase() == "bold") {
			return this.doc.createElement("strong");
		} else if (n.style.fontStyle.toLowerCase() == "italic") {
			return this.doc.createElement("em");
		} else {
			// neither - kill!
			if (document.removeNode) n.removeNode(); // ie
			else n.parentNode.removeChild(n); // moz
			return null;
		}
	}

	this._transformImgNode = function(n)
	{
		// converting align > style float
		n.removeAttribute("style");
		if (n.align.toLowerCase() == "right") {
			n.setAttribute("style", "float:right");
		} else if (n.align.toLowerCase() == "left") {
			n.setAttribute("style", "float:left");
		}
		if (typeof(n.alt) == "undefined") {
			n.alt = "";
		}
	}
}
FinalTransformer.prototype = new DOMTransformer;