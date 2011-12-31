jQuery(document).ready(function($) {
	// EQUAL HEIGHT COLS
	function equalHeight(group) {
		var tallest = 0;
		group.each(function() {
			var thisHeight = $(this).height();
			if(thisHeight > tallest) {
				tallest = thisHeight;
			}
		});
		group.height(tallest);
	}	
	// TOGGLE SIDEBAR
	// Variables
	var objMain = $('#container');
	var objToggle = $('.toggle-button');
	// Show sidebar
	function showSidebar() {
		objMain.addClass('sidebar-on');
		objMain.removeClass('sidebar-off');
		$('.toggle-button').removeClass('open-sidebar');
		$('#pagemenu li.current ul').show();
		$.cookie('sidebar-pref', 'sidebar-on', {
			expires : 60
		});
	}

	// Hide sidebar
	function hideSidebar() {
		objMain.removeClass('sidebar-on');
		objMain.addClass('sidebar-off');
		$('.toggle-button').addClass('open-sidebar');
		$('#pagemenu li ul').hide();
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
		$('.toggle-button').addClass('open-sidebar');
	}
	// Jquery UI DIALOG
	jQuery(function($) {
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
			dialogs[$(this).attr('title')].dialog('open').removeClass('invisible');
			return false;
		});
	});
	// SIDEBAR MENU
	jQuery(function($) {
		if($('#pagemenu li').hasClass('current')) {
			$('#pagemenu li.current span').addClass('open-sub');
		}
		$('#pagemenu > li > span').click(function() {
			if(false === $(this).next().is(':visible')) {
				$('#container.sidebar-on #pagemenu ul').slideUp(0);
			}
			$(this).next().slideToggle(0);
		});
	});
	// BUTTONS
	jQuery(function() {
                $('body').off('cms_ajax_apply');
		$('input[type="submit"]').each(function() {
			if($(this).attr('name') == 'apply') {
				var icon = 'ui-icon-disk';
			}
			else if($(this).attr('name') == 'cancel') {
				var icon = 'ui-icon-circle-close';
			}		
			else {
				var icon = 'ui-icon-circle-check';
			}
			//alert(icon);
			$(this).hide().after('<button type="' + $(this).attr('type') + '" name="' + $(this).attr('name') + '">').next().button({
				icons : {
					primary : icon
				},
			      label : $(this).val()
		        });
		});
		$('a.pageback').addClass('ui-state-default ui-corner-all');
		$('a.pageback').prepend('<span class="ui-icon ui-icon-arrowreturnthick-1-w">');
		$('a.pageback').hover(function() {
			$(this).addClass('ui-state-hover');
		}, function() {
			$(this).removeClass('ui-state-hover');
		});

   	        // Handle ajax apply
  	        jQuery('body').on('cms_ajax_apply',function(e){
		    // gotta get langified string here.
		    $('button[name=cancel]').button('option','label',e.close);
                });
	});
	// SHOW/HIDE NOTIFICATIONS
	jQuery(function() {
		$('.pagewarning, .message').prepend('<span class="close-warning ui-icon ui-icon-circle-close"></span>');
		$('.close-warning').click(function() {
			$(this).parent().hide(0, function() {
				$(this).hide();
			});
		});
		$('.message').each(function() {
			var message = $(this);
			$(message).hide();
			$(message).slideDown(1000, function() {
				window.setTimeout(function() {
					message.slideUp();
				}, 14000);
			});
		});
	});
	// Equal height 
	jQuery(function() {
		equalHeight($('.dashboard-box'));
	});
	// end
});
