window.onload = function() {
	linksExternal();
	defaultFocus();
	if(document.getElementById('navt_tabs')) {
		var el = document.getElementById('navt_tabs');
		_add_show_handlers(el);
	}
	if(document.getElementById('page_tabs')) {
		var el = document.getElementById('page_tabs');
		_add_show_handlers(el);
	}
}
function _add_show_handlers(navbar) {
	var tabs = navbar.getElementsByTagName('div');
	for(var i = 0; i < tabs.length; i += 1) {
		tabs[i].onmousedown = function() {
			for(var j = 0; j < tabs.length; j += 1) {
				tabs[j].className = '';
				document.getElementById(tabs[j].id + "_c").style.display = 'none';
			}
			this.className = 'active';
			document.getElementById(this.id + "_c").style.display = 'block';
			return true;
		};
	}
	var activefound = 0;
	for(var i = 0; i < tabs.length; i += 1) {
		if(tabs[i].className == 'active')
			activefound = i;
	}
	tabs[activefound].onmousedown();
}

function activatetab(index) {
	var el = 0;
	if(document.getElementById('navt_tabs')) {
		el = document.getElementById('navt_tabs');

	} else {
		if(document.getElementById('page_tabs')) {
			el = document.getElementById('page_tabs');
		}
	}
	if(el == 0)
		return;
	var tabs = navbar.getElementsByTagName('div');
	tabs[index].onmousedown();
}

function linksExternal() {
	if(document.getElementsByTagName) {
		var anchors = document.getElementsByTagName("a");
		for(var i = 0; i < anchors.length; i++) {
			var anchor = anchors[i];
			if(anchor.getAttribute("rel") == "external") {
				anchor.target = "_blank";
			}
		}
	}
}

//use <input class="defaultfocus" ...>
function defaultFocus() {

	if(!document.getElementsByTagName) {
		return;
	}

	var anchors = document.getElementsByTagName("input");
	for(var i = 0; i < anchors.length; i++) {
		var anchor = anchors[i];
		var classvalue;

		//IE is broken!
		if(navigator.appName == 'Microsoft Internet Explorer') {
			classvalue = anchor.getAttribute('className');
		} else {
			classvalue = anchor.getAttribute('class');
		}

		if(classvalue != null) {
			var defaultfocuslocation = classvalue.indexOf("defaultfocus");
			if(defaultfocuslocation != -1) {
				anchor.focus();
				var defaultfocusselect = classvalue.indexOf("selectall");
				if(defaultfocusselect != -1) {
					anchor.select();
				}
			}
		}
	}
}

function togglecollapse(cid) {
	document.getElementById(cid).style.display = (document.getElementById(cid).style.display != "block") ? "block" : "none";
}


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
	jQuery(function() {
		$('#pagemenu ul').css('display','none');
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
		jQuery('body').on('cms_ajax_apply', function(e) {
			// gotta get langified string here.
			$('button[name=cancel]').button('option', 'label', e.close);
			var htmlShow = '';
			if(e.response == 'Success') {
				htmlShow = '<aside class="message pagemcontainer" role="status"><p>' + e.details + '<\/p><\/aside>';
			} else {
				htmlShow = '<aside class="message pageerrorcontainer" role="alert"><ul>';
				htmlShow += e.details;
				htmlShow += '<\/ul><\/aside>';
			}
			$('#main').append(htmlShow).slideDown(1000, function() {
				window.setTimeout(function() {
					$('.message').slideUp();
				}, 10000);
			});
		});
	});
	// SHOW/HIDE NOTIFICATIONS
	jQuery(function() {
		$('.pagewarning').prepend('<span class="close-warning ui-icon ui-icon-circle-close"></span>');
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
				}, 10000);
			});
		});
	});
	// Equal height
	function resizeFrame() {
		equalHeight($('.dashboard-inner'));
	}


	jQuery.event.add(window, "load", resizeFrame);
	jQuery.event.add(window, "resize", resizeFrame);

	// end
});
