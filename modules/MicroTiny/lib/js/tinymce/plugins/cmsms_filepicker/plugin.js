tinymce.PluginManager.add('cmsms_filepicker', function(editor) {

    tinymce.activeEditor.settings.file_browser_callback = function(field_name, url, type, win) {
        
        tinymce.activeEditor.windowManager.open({
            title : cmsms_tiny.filepicker_title,
            file : cmsms_tiny.filepicker_url + field_name + '&type=' + type,
            classes : 'filepicker',
            width : 900,
            height : 600,
            inline : 1,
            resizable : true,
            maximizable : true
        }, {
            window : win
        });
    };
    return false;
});
