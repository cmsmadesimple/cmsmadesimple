// this is the actual tinymce initialization
var mt_selector = 'textarea.MicroTiny';
{if isset($mt_selector) && $mt_selector != ''}mt_selector='{$mt_selector}';{/if}

$(mt_selector).tinymce({
  document_base_url: '{root_url}/',
  relative_urls: true,
  {if isset($mt_cssname) && $mt_cssname != ''}
  content_css : '{cms_stylesheet name=$mt_cssname nolinks=1}',
  {/if}
  removed_menuitems: 'newdocument',
  setup: function(editor) {
    editor.on('change', function(e) {
      $(document).trigger('cmsms_formchange');
    });
  },
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
{if !$isfrontend}
  file_browser_callback: function(field_name, url, type, win) {
    tinymce.activeEditor.windowManager.open({
      title: '{$MicroTiny->Lang('filepickertitle')}',
      url: '{cms_action_url module=MicroTiny action='filepicker' forjs=1}&showtemplate=false&field='+field_name,
      height: Math.max(window.innerHeight * .8,250)
    }, {
      window: win
    });
  },
  image_advtab: true,
{/if}
{if !$mt_profile.menubar}
  menubar: false,
{/if}
  statusbar: {mt_jsbool($mt_profile.showstatusbar)},
  resize: {mt_jsbool($mt_profile.allowresize)},
  theme_advanced_styles: 'Foo=foo;Bar=bar',
  importcss_selector_filter: ".microtiny-",
{if $isfrontend}
  toolbar: 'undo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link{if $mt_profile.allowimages} | image{/if}',
  plugins:  [
    "autolink link anchor wordcount {if $mt_profile.allowimages} media image{/if}"
  ],
{else}
  toolbar: 'undo redo | cut copy paste | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link unlink{if $mt_profile.allowimages} |{/if} image cmsms_linker',
  plugins:  [
    "autolink link charmap anchor searchreplace wordcount code fullscreen insertdatetime {if $mt_profile.allowimages}media image{/if} cmsms_linker"
  ],
{/if}
});

