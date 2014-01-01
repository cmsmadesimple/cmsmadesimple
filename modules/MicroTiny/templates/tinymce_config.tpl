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


// this is the actual tinymce initialization
var mt_selector = 'textarea.MicroTiny';
{if isset($mt_elementid) && $mt_elementid != ''}mt_selector='textarea#{$mt_elementid}';{/if}

// DEBUG: css name is: {$mt_cssname|default:'__NOT SET__'}
$(mt_selector).tinymce({
  document_base_url: '{root_url}/',
  relative_urls: true,
  {if isset($mt_cssname) && $mt_cssname != ''}
  content_css : '{cms_stylesheet name=$mt_cssname nolinks=1}',
  {/if}
  removed_menuitems: 'newdocument',
  urlconverter_callback: function(url,elm,onsave,name) {
    var self = this; var settings = self.settings;
    if (!settings.convert_urls || (elm && elm.nodeName == 'LINK') || url.indexOf('file:') === 0 || url.length === 0) return url;

    // fix entities in cms_selflink urls.
    if( url.indexOf('cms_selflink') != -1 ) {
      decodeURI(url);
      url = url.replace('%20',' ');
      return url;
    }

    // Convert to relative
    if (settings.relative_urls) return self.documentBaseURI.toRelative(url);

    // Convert to absolute
    url = self.documentBaseURI.toAbsolute(url, settings.remove_script_host);

    return url;
  },
  file_browser_callback: function(field_name, url, type, win) {
    tinymce.activeEditor.windowManager.open({
      title: '{$MicroTiny->Lang('filepickertitle')}',
      url: '{cms_action_url module=MicroTiny action='filepicker' forjs=1}&showtemplate=false&field='+field_name,
      height: Math.max(window.innerHeight * .8,250)
    }, {
      window: win
    });
  },
  statusbar: {$statusbar},
  image_advtab: true,
{if $resize == 'false'}
  resize: false,
{/if}
  toolbar: 'undo redo | formatselect styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink | image cmsms_linker ',
  plugins:  [
    "autolink link charmap anchor searchreplace wordcount code fullscreen insertdatetime media image cmsms_linker"
  ],
  setup: function(ed) {
  }
});

