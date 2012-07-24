// ACTIVATE A TAB
function activatetab(index) {
	var container = jQuery('#navt_tabs');
	if(container.length === 0)
	{
		container = jQuery('#page_tabs');
	}
	container.find('div:eq(' + index + ')').mousedown();
}

// JQUERY WRAPPER FOR BACKWARDS COMPATIBILITY
function togglecollapse(cid) {
	jQuery('#' + cid).toggle();
}

jQuery(document).ready(function($) {

	// EXTERNAL LINKS
	$('a[rel=external]').attr('target', '_blank');
	// AUTOFOCUS
	$('input.defaultfocus:eq(0)').focus();

	// INIT NAV TABS
	var tabs = $('#navt_tabs, #page_tabs').find('div');
	tabs.mousedown(function(){
		tabs.each(function(){
			$(this).removeClass('active');
			$('#' + $(this).attr('id') + '_c').hide();
		});
		$(this).addClass('active');
		$('#' + $(this).attr('id') + '_c').show();
		return true;
	});
	if(tabs.filter('.active').mousedown().length === 0){
		activatetab(0);
	}

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

	// SIDEBAR
	var objMain = $('#oe_container');
	var objSide = $('#oe_menu');
	var objToggle = $('.toggle-button');
	// Show sidebar
	function showSidebar() {
		objMain.addClass('sidebar-on').removeClass('sidebar-off');
		$('#oe_pagemenu').find('li.current ul').show();
		$.cookie('sidebar-pref', 'sidebar-on', {
			expires : 60
		});
	}

	// Hide sidebar
	function hideSidebar() {
		objMain.removeClass('sidebar-on').addClass('sidebar-off');
		$('#oe_pagemenu').find('li ul').hide();
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
	if(($.cookie('sidebar-pref') == 'sidebar-off') && ($(window).width() > 768)) {
		objMain.addClass('sidebar-off');
		objMain.removeClass('sidebar-on');
		$('.toggle-button').addClass('open-sidebar');
	}
	
	// STICKY MENU
    var obj 	   = $('#oe_menu');
    var offset 	   = obj.offset();
	var topOffset  = offset.top;
	var leftOffset = offset.left;
	var marginTop  = obj.css("marginTop");
	var marginLeft = obj.css("marginLeft");

	function stickyMenu(width) {
		width  = parseInt(width);
		height = $(window).height();
		if(width < 768) {
			var scrollTop = $(window).scrollTop();
			if(scrollTop >= topOffset){
				obj.css({
					marginTop: marginTop,
    				position: 'relative'
    			});
  			}
  			if(scrollTop < topOffset){
  				obj.css({
  					marginTop: marginTop,
  					position: 'relative'
  				});
  			} 
  			showSidebar();
  			
    	} else if((width >= 768) && (height >= $('#oe_menu').height())) {
    		var scrollTop = $(window).scrollTop();
    		if(scrollTop >= topOffset){
    			obj.css({
    				marginTop: '-100px',
    				position: 'fixed'
    			});
  			}
  			if(scrollTop < topOffset){
  				obj.css({
  					marginTop: marginTop,
  					position: 'relative'
  				});
  			}
    	}
	}	

	// toggle dropzone
	$('.toggle-dropzone').click(function() {
		$('.drop').toggleClass('hidden');
		if($('.drop').hasClass('hidden')) {
			$('.pageheader').addClass('drop-hidden');
			$.cookie('dropzone-pref', 'hidden', {
				expires : 60
			});
		} else {
			$('.pageheader').removeClass('drop-hidden');
			$.cookie('dropzone-pref', 'visible', {
				expires : 60
			});
		}
		return false;
	});
	if($.cookie('dropzone-pref') != 'hidden') {
		$('.drop').addClass('visible');
		$('.pageheader').addClass('drop-visible');
	} else {
		$('.drop').addClass('hidden');
		$('.pageheader').addClass('drop-hidden');
	}

	// Jquery UI DIALOG
	jQuery(function() {
		var dialogs = {};
		$('.dialog').each(function() {
			var dialog_id = $(this).prev('.open').attr('title');
			dialogs[dialog_id] = $(this).dialog({
				autoOpen : false,
				modal: true,
				title : '<strong> ' + $(this).attr('title') + ' </strong>'
			});
		});
		$('.open').click(function(event) {
			event.preventDefault();
			dialogs[$(this).attr('title')].dialog('open').removeClass('invisible');
			$('.ui-dialog').css('top', '120px');
			return false;
		});
	});
	// SIDEBAR MENU
	jQuery(function() {
		var pagemenu = $('#oe_pagemenu');
		pagemenu.find('li.current span').addClass('open-sub');
		pagemenu.find('> li > span').click(function() {
			var ul = $(this).next();
			if(ul.is(':visible') === false) {
				pagemenu.find('ul').slideUp(0);
			}
			ul.slideToggle(0);
		});
	});
	// SIDEBAR MENU Mobile check
	jQuery(function() {
		if(navigator.userAgent.match(/(Android|iPhone|iPad|iPod|Blackberry|Dolphin|IEMobile|Kindle|Mobile|MMP|MIDP|Pocket|PSP|Symbian|Smartphone|Sreo|Up.Browser|Up.Link|Vodafone|WAP|Opera Mini|Opera Tablet|Mobile|Fennec)/)) {
		} else {
			$(window).scroll(function() {
				stickyMenu($(window).width());
					$(window).resize(function() {
						stickyMenu($(window).width());
				});
			});
		} 
	});
	// BUTTONS
	jQuery(function() {
		jQuery('body').off('cms_ajax_apply');
		$('input[type="submit"], input[type="button"]').each(function() {
			if($(this).attr('name') == 'apply' || $(this).attr('name') == 'm1_apply') {
				var icon = 'ui-icon-disk';
			} else if($(this).attr('name') == 'cancel' || $(this).attr('name') == 'm1_cancel') {
				var icon = 'ui-icon-circle-close';
			} else if($(this).attr('resettodefault') || $(this).attr('name') == 'm1_resettodefault' || $(this).attr('id') == 'refresh') {
				var icon = 'ui-icon-refresh';
			} else {
				var icon = 'ui-icon-circle-check';
			}
			var btn = $('<button />');
			// ADOPT ALL ATTRIBUTES
			$(this.attributes).each(function(index, attribute){
				btn.attr(attribute.name, attribute.value);
			})
			btn.button({
				icons : {
					primary : icon
				},
				label : $(this).val()
			});
			$(this).replaceWith(btn);
		});
		$('a.pageback').addClass('ui-state-default ui-corner-all')
			.prepend('<span class="ui-icon ui-icon-arrowreturnthick-1-w">')
			.hover(function() {
				$(this).addClass('ui-state-hover');
			}, function() {
				$(this).removeClass('ui-state-hover');
			});
		// Handle ajax apply
		jQuery('body').on('cms_ajax_apply', function(e) {
			// gotta get langified string here.
			$('button[name=cancel], button[name=m1_cancel]').fadeOut();
			$('button[name=cancel], button[name=m1_cancel]').button('option', 'label', e.close);
			$('button[name=cancel], button[name=m1_cancel]').fadeIn();

			var htmlShow = '';
			if(e.response == 'Success') {
				htmlShow = '<aside class="message pagemcontainer" role="status"><span class="close-warning">Close</span><p class="pagemessage">' + e.details + '<\/p><\/aside>';
			} else {
				htmlShow = '<aside class="message pageerrorcontainer" role="alert"><span class="close-warning">Close</span><ul class="pageerror">';
				htmlShow += e.details;
				htmlShow += '<\/ul><\/aside>';
			}

			$('#oe_mainarea').append(htmlShow).slideDown(1000, function() {
				window.setTimeout(function() {
					$('.message').slideUp();
				}, 10000);
			});
			$('.message').click(function() {
				$('.message').slideUp();
			});
		});
	});
	// SHOW/HIDE NOTIFICATIONS
	jQuery(function() {
		$('.pagewarning, .message, .pageerrorcontainer, .pagemcontainer').prepend('<span class="close-warning"></span>');
		$('.close-warning').click(function() {
			$(this).parent().hide();
		});
		// pagewarning status hidden?
		var loc = $('body').attr('id') + '_notification';
		$('.pagewarning .close-warning').click(function() {
			$.cookie(loc, 'hidden', {
				expires : 60
			});
		});
		// if cookie is not hidden show pagewarning
		if($.cookie(loc) != 'hidden') {
			$('.pagewarning').addClass('visible');
		} else {
			$('.pagewarning').addClass('hidden');
		}
		
		$('.message').click(function() {
				$('.message').slideUp();
			});
		$('.message, .pageerrorcontainer, .pagemcontainer').each(function() {
			var message = $(this);
			$(message).hide()
				.slideDown(1000, function() {
					window.setTimeout(function() {
						message.slideUp();
					}, 10000);
				});
		});
	}); 
	// Equal height
	function resizeFrame() {
		equalHeight($('.dashboard-inner'));
	}
	// fix footer, breaks when max-width 1024 kicks in and there is less content then height of menu 
	$('#oe_admin-content').css('min-height', ($('#oe_sidebar').height()));
	
	jQuery.event.add(window, "load", resizeFrame);
	jQuery.event.add(window, "resize", resizeFrame);
});