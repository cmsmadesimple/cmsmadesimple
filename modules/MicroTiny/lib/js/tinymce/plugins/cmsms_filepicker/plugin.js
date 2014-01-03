// minify when this is working, need this file as we use tinymce.min.js and plugins load based on tinymce core file either min or regular
tinymce.PluginManager.add('cmsms_filepicker', function(editor) {

    tinymce.activeEditor.settings.file_browser_callback = function(field_name, url, type, win) {
        console.log(type);
        /** action.filepicker.php needs check against type, else when clicking on image button all files are shown, making it possible to
        * insert index.html as <img> which isn't that smart, we could do a check in template .js logic once it's ready but it doesn't make sense
        * because then i would have to add some info popup and other crap to tell the user that only images can be selected, so i guess we better
        * do this logic check based on url param in php file.
        * Not sure yet how to execute this whole plugin, but well it's a start :-D
        **/
        
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
