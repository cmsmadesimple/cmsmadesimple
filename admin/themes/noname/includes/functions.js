$(document).ready(function(){
	// Variables
	var objMain = $('#content');
	// Sidebar toggle
	var objToggle = $('.toggle-button');
	// Show sidebar
	function showSidebar(){
		objMain.addClass('sidebar-on');
		objMain.removeClass('sidebar-off');
		$('.footer-left').removeClass('sidebar-off');		
		$('.toggle-button').removeClass('open');
		$.cookie('sidebar-pref', 'sidebar-on', { expires: 60 });
	}
	// Hide sidebar
	function hideSidebar(){
		objMain.removeClass('sidebar-on');
		objMain.addClass('sidebar-off');
		$('.footer-left').addClass('sidebar-off');
		$('.toggle-button').addClass('open');		
		$.cookie('sidebar-pref', 'sidebar-off', { expires: 60 });
	}

	objToggle.click(function(e){
		e.preventDefault();
		if ( objMain.hasClass('sidebar-on') ){
			hideSidebar();
		}
		else {
			showSidebar();
		}
	});
	// Load preference
	if ($.cookie('sidebar-pref') == 'sidebar-off'){
		objMain.addClass('sidebar-off');
		objMain.removeClass('sidebar-on');
		$('.toggle-button').addClass('open');
	}
});