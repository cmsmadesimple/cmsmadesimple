if (window.attachEvent) window.attachEvent("onload", IEhover);

window.onload = function() {
	linksExternal(); 
	defaultFocus();
 	if (document.getElementById('navt_tabs')) {
		var el = document.getElementById('navt_tabs');
		_add_show_handlers(el);
	}
 	if (document.getElementById('page_tabs')) {
		var el = document.getElementById('page_tabs');
		_add_show_handlers(el);
	}
}

function IEhover() {
		if (document.getElementById('nav')) {
			cssHover('nav','LI');	
		}
	 	if (document.getElementById('navt_tabs')) {
			cssHover('navt_tabs','DIV');
		}
	 	if (document.getElementById('page_tabs')) {
			cssHover('page_tabs','DIV');
		}
}

function cssHover(tagid,tagname) {
	var sfEls = document.getElementById(tagid).getElementsByTagName(tagname);
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" cssHover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" cssHover\\b"), "");
		}
	}
}

function change(id, newClass, oldClass) {
	identity=document.getElementById(id);
	if (identity.className == oldClass) {
		identity.className=newClass;
	} else {
		identity.className=oldClass;
	}
}

function _add_show_handlers(navbar) {
    var tabs = navbar.getElementsByTagName('div');
    for (var i = 0; i < tabs.length; i += 1) {
	tabs[i].onmousedown = function() {
	    for (var j = 0; j < tabs.length; j += 1) {
		tabs[j].className = '';
		document.getElementById(tabs[j].id + "_c").style.display = 'none';
	    }
	    this.className = 'active';
	    document.getElementById(this.id + "_c").style.display = 'block';
	    return true;
	};
    }
    var activefound=0;
    for (var i = 0; i < tabs.length; i += 1) {
    	if (tabs[i].className=='active') activefound=i;
    }
    tabs[activefound].onmousedown();
}

function activatetab(index) {
	var el=0;
	if (document.getElementById('navt_tabs')) {
		el = document.getElementById('navt_tabs');
		
	} else {
 	  if (document.getElementById('page_tabs')) {
		  el = document.getElementById('page_tabs');
	  }
	}
	if (el==0) return;
	var tabs = navbar.getElementsByTagName('div');
	tabs[index].onmousedown();
}

function linksExternal()	{
	if (document.getElementsByTagName)	{
		var anchors = document.getElementsByTagName("a");
		for (var i=0; i<anchors.length; i++)	{
			var anchor = anchors[i];
			if (anchor.getAttribute("rel") == "external")	{
				anchor.target = "_blank";
			}
		}
	}
}

//use <input class="defaultfocus" ...>
function defaultFocus() {

   if (!document.getElementsByTagName) {
        return;
   }

   var anchors = document.getElementsByTagName("input");
   for (var i=0; i<anchors.length; i++) {
      var anchor = anchors[i];
      var classvalue;

      //IE is broken! 
      if(navigator.appName == 'Microsoft Internet Explorer') {
            classvalue = anchor.getAttribute('className');
      } else {
            classvalue = anchor.getAttribute('class');
      }

      if (classvalue!=null) {
                var defaultfocuslocation = classvalue.indexOf("defaultfocus");
                if (defaultfocuslocation != -1) {
                	anchor.focus();
			var defaultfocusselect = classvalue.indexOf("selectall");
			if (defaultfocusselect != -1) {
				anchor.select();
			}
                }
        }
   }
}

function togglecollapse(cid)
{
  document.getElementById(cid).style.display=(document.getElementById(cid).style.display!="block")? "block" : "none";
}

