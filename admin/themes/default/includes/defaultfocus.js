function defaultFocus() {
    if (!document.getElementsByTagName) {
        return;
    }
    var anchors = document.getElementsByTagName("input");
    for (var i=0; i<anchors.length; i++) {
        var anchor = anchors[i];
        var classvalue = anchor.getAttribute("class");

	if (classvalue!=null) {
	        var defaultfocuslocation = classvalue.indexOf("defaultfocus");
        	if (defaultfocuslocation != -1) {
            	// anchor.target = "_blank";
            	anchor.focus();
		anchor.select();
        	}
	}
    }
}
window.onload = defaultFocus;