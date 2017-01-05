(function($){
/*jslint nomen: true , devel: true*/

    var CMSFileBrowser = {},
        info = {};

    $(document).ready(function() {
        CMSFileBrowser.load();
    });

    CMSFileBrowser.settings = top.document.CMSFileBrowser;

    CMSFileBrowser.load = function() {
        CMSFileBrowser.enable_sendValue();
        CMSFileBrowser.enable_toggleGrid();
        CMSFileBrowser.enable_filetypeFilter();
        CMSFileBrowser.enable_commands();
    };

    CMSFileBrowser.enable_sendValue = function() {

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
	    if( top.document.CMSFileBrowser && top.document.CMSFileBrowser.onselect ) {
		top.document.CMSFileBrowser.onselect(instance,file);
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

    CMSFileBrowser.enable_toggleGrid = function(){

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

    CMSFileBrowser.enable_filetypeFilter = function() {

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

    CMSFileBrowser.enable_commands = function(ev) {
	if( ($('.filepicker-cmd').length < 1 ) ) return;

	$('.filepicker-cmd').on('click',function(ev) {
	    var $trigger = $(this), $data = $trigger.data();
	    var fun = '_cmd_'+$data.cmd;
	    if( typeof(CMSFileBrowser[fun]) != 'undefined' ) CMSFileBrowser[fun](ev);
	});
    };

    CMSFileBrowser._ajax_cmd = function(cmd,val)
    {
	return $.ajax({
	    url: top.document.CMSFileBrowser.cmd_url,
	    method: 'POST',
	    data: {
		'cmd': cmd,
		'val': val,
		'cwd': top.document.CMSFileBrowser.cwd,
		'inst': top.document.CMSFileBrowser.inst,
		'sig': top.document.CMSFileBrowser.sig,
	    }
	})
    };

    CMSFileBrowser._cmd_del = function(ev)
    {
	ev.preventDefault();
	var target = ev.target.closest('.filepicker-item');
	var file = $(target).data('fb-fname');
	if( confirm(this.settings.lang.confirm_delete) ) {
	    CMSFileBrowser._ajax_cmd('del',file).done(function(msg){
		var url = window.location.href+'&nosub=1';
		window.location.href = url;
	    }).fail(function(jqXHR,textStatus,msg){
		alert('fail ajax '+msg);
	    })
	}
    };

    CMSFileBrowser._cmd_mkdir = function()
    {
	$('#mkdir_dlg').dialog({
	    modal: true,
	    width: 'auto',
	    buttons: [
		 {
		     text: CMSFileBrowser.settings.oklbl,
		     icons: {
			 primary: 'ui-icon-check'
		     },
		     click: function() {
			 var val = $('#fld_mkdir').val().trim();
			 CMSFileBrowser._ajax_cmd('mkdir',val).done(function(msg){
			     var url = window.location.href+'&nosub=1';
			     window.location.href = url;
			 }).fail(function(jqXHR,textStatus,msg){
			     alert('fail ajax '+msg);
			 })
			 // ajax call to create the directory
			 // then ajax call to refresh the screen
			 // then close the dialog.
		     }
		}
	    ]
	});
    }
})(jQuery);
