;( function(global, $) {'use strict';
/*jslint nomen: true , devel: true*/

    var MTFB = global.MTFB || {},
        win,
        el,
        info = {};

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
            
            var $this = $(this),
                $elm = $this.closest('li'),
                $data = $elm.data(),
                $ext = $data.fbExt,
                file = $this.attr('href');
                
            e.preventDefault();

            if (filebrowser_global.field_id.length > 0) {
                
                win = parent.tinymce.activeEditor.windowManager.getParams().window;
                el = win.document.getElementById(filebrowser_global.field_id);
                el.value = file;
                el.selectionStart = el.selectionEnd = el.value.length;

                parent.tinymce.activeEditor.windowManager.close();
            } else {
                
                MTFB.saveFile($ext, file);
            }
        });
    };
    
    MTFB._setFileType = function(ext) {
        
        var images = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg', 'wbmp', 'webp'], // image extension array
            videos = ['mp4', 'webm', 'ogg'], // video extension array
            flash = ['flv', 'swf'], // video extension array
            sounds = ['mp3', 'wav']; // music extension array (how to defer between sound and video ogg??)
            
            info.type = 'file';
            info.mime = '';
            
            if ($.inArray(ext, images) > -1) {
                info.type = 'image';
            }
            
            if ($.inArray(ext, videos) > -1) {
                info.type = 'video';
                info.mime = 'video/' + ext;
            }
            
            if ($.inArray(ext, flash) > -1) {
                info.type = 'flash';
                info.mime = 'application/x-shockwave-flash';
            }
            
            if ($.inArray(ext, sounds) > -1) {
                info.type = 'sound';
                info.mime = 'audio/' + ext;
            }
            
            if (ext === 'js') {
                info.type = 'file';
                info.mime = 'text/javascript';
            }
    };
    
    MTFB._createMarkup = function(ext, file) {
        
        MTFB._setFileType(ext);
        
        var type = info.type,
            mime = info.mime,
            html = '',
            text,
            data,
            src,
            alt,
            title,
            width,
            height,
            cssclass,
            id,
            selection = parent.tinymce.activeEditor.selection.getNode();
        
        // check selected file attributes
        data = MTFB._selectionToData(selection);
        
        if (selection.length > 0) {
            text = selection;
        } else {
            text = file.split('/').pop();
        }
        
        if (data.alt !== undefined) {
            alt = ' alt="' + data.alt + '" ';
        } else {
            alt = file.split('/').pop();
        }
        
        if (data.title !== undefined) {
            title = ' title="' + data.title + '" ';
        } else {
            title = file.split('/').pop();
        }
        
        if (data.width !== undefined) {
            width = ' width="' + data.width + '" ';
        }
        
        if (data.height !== undefined) {
            height = ' height="' + data.height + '" ';
        }
        
        if (data.class !== undefined) {
            cssclass = ' class="' + data.class + '" ';
        }
        
        if (data.id !== undefined) {
            id = ' id="' + data.id + '" ';
        }

        // create html markup
        if (type === 'video') {
            html = '<video controls="controls"' + width + height + title + cssclass + id +'>\n' +
                       '<source src="' + file + '"' + ' type="' + mime + '" />\n' +
                   '</video>';
        } 
        
        else if (type === 'flash') {
            html = '<object src="' + file + '"' + ' type="' + mime + '"' + width + height + cssclass + id + '></object>';
        }
        
        else if (type === 'sound') {
            html = '<audio src="' + file + '"' + ' controls="controls" type="' + mime + '"' + width + height + title + cssclass + id +'></audio>';
        }
        
        else if (type === 'image') {
            html = '<img src="' + file + '"' + alt + height + width + title + cssclass + id + ' />';
        }
        
        else if (type === 'file' && mime === 'text/javascript') {
            html = '<script src="' + file + '"' + ' type="' + mime + '"></script>';
        }
        
        else {
            html = '<a href="' + file + '"' + cssclass + id + title + '>' + text + '</a>';
        }
        
        return html;
    };
    
    MTFB._selectionToData = function(elm) {

        var content = parent.tinymce.activeEditor.selection.serializer.serialize(elm, {selection: true}),
            data = {};
       
        new parent.tinymce.html.SaxParser({
            validate: false,
            allow_conditional_comments: true,
            special: 'script,noscript',
            start: function(name, attrs) {
                data = {
                    source: attrs.map.src,
                    alt: attrs.map.alt,
                    title: attrs.map.title,
                    width: attrs.map.width,
                    height: attrs.map.height,
                    class: attrs.map.class,
                    id: attrs.map.id 
                };
            }
        }).parse(content);
        
        return data;
    };
    
    MTFB.saveFile = function(ext, file) {
        
        var markup = MTFB._createMarkup(ext, file);
        
        //parent.tinymce.activeEditor.selection.setContent(markup);
        parent.tinymce.activeEditor.insertContent(markup);
        parent.tinymce.activeEditor.windowManager.close();
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

}(this, jQuery));
