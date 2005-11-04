function IEHoverPseudo() {
	if (document.getElementById("primary-nav-vert") != null) {
		var navItems = document.getElementById("primary-nav-vert").getElementsByTagName("li");
		
		for (var i=0; i<navItems.length; i++) {
			if(navItems[i].className == "menuparent") {
				navItems[i].onmouseover=function() { this.className += " over"; }
				navItems[i].onmouseout=function() { this.className = "menuparent"; }
			}
		}
	}
	if (document.getElementById("primary-nav-horiz") != null) {
		var navItems = document.getElementById("primary-nav-horiz").getElementsByTagName("li");
		
		for (var i=0; i<navItems.length; i++) {
			if(navItems[i].className == "menuparent") {
				navItems[i].onmouseover=function() { this.className += " over"; }
				navItems[i].onmouseout=function() { this.className = "menuparent"; }
			}
		}
	}
}
window.onload = IEHoverPseudo;
