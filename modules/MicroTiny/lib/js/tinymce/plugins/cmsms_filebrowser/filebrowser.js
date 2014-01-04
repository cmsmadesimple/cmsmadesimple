;( function(global, $) {'use strict';

    var MTFB = global.MTFB || {};

    $(document).ready(function() {
        MTFB.load();
    });

    MTFB.load = function() {
        MTFB.sendValue();
        MTFB.toggleGrid();
        
        if ($('.filepicker-type-filter').length > 0) {
            MTFB.filetypeFilter();
        }
    };

    MTFB.sendValue = function() {

        $('a.js-trigger-insert').click(function(e) {
            
            var file = $(this).attr('href'), 
                win,
                el;
            e.preventDefault();

            if (filebrowser_global.field_id.length > 0) {
                
                win = parent.tinymce.activeEditor.windowManager.getParams().window;
                el = win.document.getElementById(filebrowser_global.field_id);
                el.value = file;
                el.selectionStart = el.selectionEnd = el.value.length;

                parent.tinymce.activeEditor.windowManager.close();
            } else {
                // TODO create logic for inserting content like <img> <video> <audio> and other stuff
                parent.tinymce.activeEditor.selection.setContent(file);
                parent.tinymce.activeEditor.windowManager.close();
            }
        });
    };
    
    MTFB.toggleGrid = function(){
       
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
    
    MTFB.filetypeFilter = function() {
        
        var $items = $('#filepicker-items > li:not(.filepicker-item-heading)'),
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

} )(this, jQuery);
