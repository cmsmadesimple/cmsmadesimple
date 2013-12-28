tinymce.init({
  selector: 'textarea.MicroTiny',
  document_base_url: '{root_url}/',
  relative_urls: true,
  removed_menuitems: 'newdocument',
  file_browser_callback: function(field_name, url, type, win) {
    tinymce.activeEditor.windowManager.open({
      title: '{$mod->Lang('filepickertitle')}',
      url: '{cms_action_url action='filepicker' forjs=1}&showtemplate=false&field='+field_name,
      height: Math.max(window.innerHeight * .8,250)
    }, {
      window: win
    });

    window.cmsms_addimage_activeEditor = tinymce.activeEditor;
  },
  statusbar: {$statusbar},
  image_advtab: true,
{if $resize == 'false'}
  resize: false,
{/if}
  toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image',
  plugins:  [
    "autolink link charmap anchor searchreplace wordcount code fullscreen insertdatetime media image"
  ],
  setup: function(ed) {
    ed.addCommand('cmsms_insert_image',function(ui, v) {
      ed.windowmanager.alert('got insert image command');
    });
  }
});

