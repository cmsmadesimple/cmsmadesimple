tinymce.PluginManager.add('cmsms_filebrowser', function(editor) {

    function cmsmsFileBrowser() {
        
        editor.focus(true);
        
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
        
        win = editor.windowManager.open({
            title : cmsms_tiny.filepicker_title,
            file : cmsms_tiny.filepicker_url,
            classes : 'filepicker',
            width : width,
            height : height,
            inline : 1,
            resizable : true,
            maximizable : true
        });
        
    }

    editor.addButton('cmsms_filebrowser', {
        icon : 'browse',
        tooltip : cmsms_tiny.filebrowser_title,
        onclick : cmsmsFileBrowser
    });

    editor.addMenuItem('cmsms_filebrowser', {
        icon : 'browse',
        text : cmsms_tiny.filebrowser_title,
        onclick : cmsmsFileBrowser,
        context : 'insert',
        prependToContext: true
    });
});
