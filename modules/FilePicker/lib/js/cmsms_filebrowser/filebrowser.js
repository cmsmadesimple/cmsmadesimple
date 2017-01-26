    /*jslint nomen: true , devel: true*/
    function CMSFileBrowser(_settings) {
	var self = this;
	var settings = _settings;
	if( top.document.CMSFileBrowser ) {
	    settings = $.extend( {}, top.document.CMSFileBrowser, settings );
	    console.debug('got global settings');
	}

	function enable_sendValue() {
            $('a.js-trigger-insert').click(function(e) {
		var $this = $(this),
                    $elm = $this.closest('li'),
                    $data = $elm.data(),
                    $ext = $data.fbExt,
                    file = $this.attr('href');

		e.preventDefault();
		var selector;
		var instance = $('html').data('cmsfp-inst');
		var o = {
		    name: 'cmsfp:change',
		    target: instance,
		    file: file
		};
		if( settings && settings.onselect ) {
		    settings.onselect(instance,file);
		    return;
		}

		var selector = '[data-cmsfp-instance="'+instance+'"]';
		var target = parent.$(selector);
		if( target && target.length ) {
		    if( target.is(':input') ) {
			target.val(file);
			target.trigger('change');
		    };
		    target.trigger('cmsfp:change',file);
		}
            });
	};

	function enable_toggleGrid() {
	    $('.filepicker-view-option .js-trigger').on('click', function(e) {
		var $trigger = $(this),
		    $container = $('#filepicker-items'),
		    $info = $('.filepicker-file-details');

		$('.filepicker-view-option .js-trigger').removeClass('active');
		$trigger.addClass('active');

		if ($trigger.hasClass('view-grid')) {
                    $container.removeClass('list-view').addClass('grid-view');
                    $info.addClass('visuallyhidden');
		} else if ($trigger.hasClass('view-list')) {
                    $container.removeClass('grid-view').addClass('list-view');
                    $info.removeClass('visuallyhidden');
		}
            });
	};

	function enable_filetypeFilter() {
            if ($('.filepicker-type-filter').length < 1) return;

            var $items = $('#filepicker-items > li:not(.filepicker-item-heading):not(.dir)'),
		$container = $('#filepicker-items'),
		$trigger,
		$data;

            $('.filepicker-type-filter .js-trigger').on('click', function(e) {
		var $trigger = $(this),
                    $data = $trigger.data();

                $('.filepicker-type-filter .js-trigger').removeClass('active');
                $trigger.addClass('active');

                if ($trigger.hasClass('active') && $data.fbType !== 'reset') {
                    $items.hide(200).removeClass('visible');
                    $('li.' + $data.fbType).show(200).addClass('visible');
                } else {
                    $items.show(200).addClass('visible');
                }
            });
	};

	function enable_upload() {
	    var dropzone = $('body.cmsms-filepicker');
	    $('#filepicker-file-upload').fileupload({
		url: settings.cmd_url,
		dropZone: dropzone,
		dataType: 'json',
		maxChunkSize: 1800000,
		formData: {
		    'cmd': 'upload',
		    'cwd': settings.cwd,
		    'inst': settings.inst,
		    'sig': settings.sig,
		},
		done: function(ev,data) {
		    if( data.result.length == 0 ) return;
		    var n_errors = 0;
		    for( var i = 0; i < data.result.length; i++ ) {
			res = data.result[i];
			if( res.error != undefined ) {
			    n_errors++;
			    var msg = settings.lang.error_problem_upload+' '+res.name;
			    if( res.errormsg != undefined ) msg += '.\n'+res.errormsg;
			    alert(msg); // can't use cms_alert
			}
		    }
		    if( n_errors < data.result.length ) {
		        var url = window.location.href+'&nosub=1';
		        window.location.href = url;
		    }
		},
		fail: function(xhr,txtstatus,msg) {
		    console.debug('problem with ajax upload: '+msg);
		    alert(settings.lang.error_failed_ajax);
		}
	    });
	};

	function enable_commands() {
	    if( ($('.filepicker-cmd').length < 1 ) ) return;

	    $('.filepicker-cmd').on('click',function(ev) {
		var $trigger = $(this), $data = $trigger.data();
		var fun = '_cmd_'+$data.cmd;
		if( typeof(fun) != 'undefined' ) fun(ev);
	    });
	};

	function _ajax_cmd(cmd,val) {
	    return $.ajax({
		url: settings.cmd_url,
		method: 'POST',
		data: {
		    'cmd': cmd,
		    'val': val,
		    'cwd': settings.cwd,
		    'inst': settings.inst,
		    'sig': settings.sig,
		}
	    })
	};

	function _cmd_del(ev) {
	    ev.preventDefault();
	    var target = ev.target.closest('.filepicker-item');
	    var file = $(target).data('fb-fname');
	    if( confirm(settings.lang.confirm_delete) ) {
		_ajax_cmd('del',file).done(function(msg){
		    var url = window.location.href+'&nosub=1';
		    window.location.href = url;
		}).fail(function(jqXHR,textStatus,msg){
		    console.debug('filepicker command failed: '+msg);
		})
	    }
	};

	function _cmd_mkdir(ev) {
	    ev.preventDefault();
	    $('#mkdir_dlg').dialog({
		modal: true,
		width: 'auto',
		buttons: [
		    {
			text: $('#mkdir_dlg').data('oklbl'),
			icons: {
			    primary: 'ui-icon-check'
			},
			click: function() {
			    var val = $('#fld_mkdir').val().trim();
			    _ajax_cmd('mkdir',val).done(function(msg){
				var url = window.location.href+'&nosub=1';
				window.location.href = url;
			    }).fail(function(jqXHR,textStatus,msg){
				console.debug('filepicker mkdir failed: '+msg);
			    })
			    // ajax call to create the directory
			    // then ajax call to refresh the screen
			    // then close the dialog.
			}
		    }
		]
	    });
	}

        enable_sendValue();
        enable_toggleGrid();
        enable_filetypeFilter();
        enable_commands();
	enable_upload();
    } /* object */
