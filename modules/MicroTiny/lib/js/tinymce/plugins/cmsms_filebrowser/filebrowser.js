;( function(global, $) {'use strict';

    var MTFB = global.MTFB || {};

    $(document).ready(function() {
        MTFB.load();
    });

    MTFB.load = function() {
        MTFB.sendValue();
        MTFB.toggleGrid();
    };

    MTFB.sendValue = function() {

        $('a.filepicker-file-action').click(function(e) {
            
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
       
       $('.filepicker-view-option .js-trigger').on('click',function(e) {
           var $trigger = $(this),
               $container = $('#filepicker-items');
           
           $('.filepicker-view-option .js-trigger').removeClass('active');
           $trigger.addClass('active');
           
           if ($trigger.hasClass('view-grid')) {
                $container.removeClass('list-view').addClass('grid-view');
            } else if ($trigger.hasClass('view-list')) {
                $container.removeClass('grid-view').addClass('list-view');
            }
        });
    };

} )(this, jQuery);
