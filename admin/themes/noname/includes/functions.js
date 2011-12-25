$(document).ready(function() {
	// TOGGLE SIDEBAR
	// Variables
	var objMain = $('#container');
	var objToggle = $('.toggle-button');
	// Show sidebar
	function showSidebar() {
		objMain.addClass('sidebar-on');
		objMain.removeClass('sidebar-off');
		$('.toggle-button').removeClass('open');
		$.cookie('sidebar-pref', 'sidebar-on', {
			expires : 60
		});
	}

	// Hide sidebar
	function hideSidebar() {
		objMain.removeClass('sidebar-on');
		objMain.addClass('sidebar-off');
		$('.toggle-button').addClass('open');
		$.cookie('sidebar-pref', 'sidebar-off', {
			expires : 60
		});
	}


	objToggle.click(function(e) {
		e.preventDefault();
		if(objMain.hasClass('sidebar-on')) {
			hideSidebar();
		} else {
			showSidebar();
		}
	});
	// Load preference
	if($.cookie('sidebar-pref') == 'sidebar-off') {
		objMain.addClass('sidebar-off');
		objMain.removeClass('sidebar-on');
		$('.toggle-button').addClass('open');
	}
	// Jquery UI DIALOG
	$(function() {
		var dialogs = {};
		$('.dialog').each(function() {
			var dialog_id = $(this).prev('.open').attr('title');
			dialogs[dialog_id] = $(this).dialog({
				autoOpen : false,
				title : '<strong> ' + $(this).attr('title') + ' </strong>'
			});
		});
		$('.open').click(function(event) {
			event.preventDefault();
			dialogs[$(this).attr('title')].dialog('open');
			return false;
		});
	});
});
