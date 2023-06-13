    /*jslint nomen: true , devel: true*/
    function CMSFileBrowser(_settings) {
	var self = this;
	var container = $('#filepicker-items');
	var gridview_btn = $('.filepicker-view-option .view-grid');
	var listview_btn = $('.filepicker-view-option .view-list');
	var progress_bar = $('#filepicker-progress');
	var progress_text = $('#filepicker-progress-text');

	var settings = _settings;
	if( top.document.CMSFileBrowser ) {
	    settings = $.extend( {}, top.document.CMSFileBrowser, settings );
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
		if( settings.prefix ) file = settings.prefix + file;
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
	    gridview_btn.on('click',function(){
		container.removeClass('list-view').addClass('grid-view');
		$('.filepicker-file-details').addClass('visuallyhidden');
		localStorage.setItem('view-type','grid');
		listview_btn.removeClass('active');
		$(this).addClass('active');
	    });
	    listview_btn.on('click',function(){
		container.addClass('list-view').removeClass('grid-view');
		$('.filepicker-file-details').removeClass('visuallyhidden');
		localStorage.setItem('view-type','list');
		gridview_btn.removeClass('active');
		$(this).addClass('active');
	    });
	    /*
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
            */
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
	    var n_errors;
	    dropzone.on('dragover',function(e){
		$(this).addClass('dragging');
	    }).on('dragleave',function(e){
		$(this).removeClass('dragging');
	    });
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
		start: function(ev) {
		    n_errors = 0;
		    progress_bar.children().hide();
		    progress_bar.progressbar({ max: 100 });
		    progress_text.show();
		    cms_busy();
		},
		progressall: function(ev,data) {
		    var percent = parseInt(data.loaded / data.total * 100,10);
		    progress_bar.progressbar('value',percent);
		    progress_text.text(percent+'%');
		},
		done: function(ev,data) {
		    if( data.result.length == 0 ) return;
		    for( var i = 0; i < data.result.length; i++ ) {
			res = data.result[i];
			if( res.error != undefined ) {
			    n_errors++;
			    var msg = settings.lang.error_problem_upload+' '+res.name;
			    if( res.errormsg != undefined ) msg += '.<br/>'+res.errormsg;
			    cms_alert(msg);
			}
		     }
		},
		stop: function(ev) {
		    progress_bar.children().show();
		    progress_text.hide();
		    progress_bar.progressbar('destroy');
		    cms_busy(false);
		    if( n_errors == 0 ) {
		        var url = window.location.href+'&nosub=1';
		        window.location.href = url;
		    }
		}
	    });
	};

	function enable_commands() {
	    if( ($('.filepicker-cmd').length < 1 ) ) return;

	    $('.filepicker-cmd').on('click',function(ev) {
		var $trigger = $(this), $data = $trigger.data();
		var fun = '_cmd_'+$data.cmd;
		if( typeof(self[fun]) != 'undefined' ) self[fun](ev);
	    });
	};

	function setup_view() {
	    var view_type = localStorage.getItem('view-type');
	    if( !view_type ) view_type = 'grid';
	    if( view_type == 'list' ) {
		listview_btn.trigger('click');
	    } else {
		gridview_btn.trigger('click');
	    }
	}

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

	this._cmd_del = function(ev) {
	    ev.preventDefault();
	    var target = ev.target.closest('.filepicker-item');
	    var file = $(target).data('fb-fname');
	    cms_confirm(settings.lang.confirm_delete).done(function(){
		_ajax_cmd('del',file).done(function(msg){
		    var url = window.location.href+'&nosub=1';
		    window.location.href = url;
		}).fail(function(jqXHR,textStatus,msg){
		    console.debug('filepicker command failed: '+msg);
		})
	    });
	};

	this._cmd_mkdir = function(ev) {
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
	setup_view()
    } /* object */
