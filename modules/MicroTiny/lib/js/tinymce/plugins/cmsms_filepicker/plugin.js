tinymce.PluginManager.add('cmsms_filepicker', function(editor) {

    tinymce.activeEditor.settings.file_browser_callback = function(field_name, url, type, win) {

        var height, 
            width;

        ( function(window) {
            height = '600';
            width = '900';
            if (window.innerHeight < 650) {
                height = Math.max(window.innerHeight * 0.8, 250);
            }
            if (window.innerWidth < 950) {
                width = Math.max(window.innerWidth * 0.8, 250);
            }
        } )(window);

        tinymce.activeEditor.windowManager.open({
            title : cmsms_tiny.filepicker_title,
            file : cmsms_tiny.filepicker_url + field_name + '&type=' + type,
            classes : 'filepicker',
            height : height,
            width : width,
            inline : 1,
            resizable : true,
            maximizable : true
        }, {
            window : win
        });
    };
    return false;
});
