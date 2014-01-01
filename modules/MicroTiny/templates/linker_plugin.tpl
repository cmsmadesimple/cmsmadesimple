// cmsms linker plugin
tinymce.PluginManager.add('cmsms_linker',function(editor,url) {
  function cmsms_linker_open() {
    editor.windowManager.open({
      title: '{$MicroTiny->Lang('cmsms_linker')}',
      url: '{cms_action_url module=MicroTiny action=linker forjs=1}&showtemplate=false',
      inline: true,
      height: 330
    });
  }

  // add a button
  editor.addButton('cmsms_linker', {
    title: '{$MicroTiny->Lang('title_cmsms_linker')}',
    icon: 'link',
    image: '{$MicroTiny->GetModuleURLPath()}/images/cmsmslink.gif',
    onclick: cmsms_linker_open,
    onPostRender: function() {
        var ctrl = this;
 
        editor.on('NodeChange', function(e) {
            var active = false;
            dom = editor.dom;
            if( e.element.nodeName == 'A' ) {
              var href = dom.getAttrib(e.element,'href');
	      if( href.indexOf('cms_selflink') != -1 ) active = true;
            }
            ctrl.active(active);
        });
    }
  });

  // and a menu item
  editor.addMenuItem('cmsms_linker', {
    text: '{$MicroTiny->Lang('cmsms_linker')}',
    title: '{$MicroTiny->Lang('title_cmsms_linker')}',
    image: '{$MicroTiny->GetModuleURLPath()}/images/cmsmslink.gif',
    context: 'insert',
    onclick: cmsms_linker_open
  });
});