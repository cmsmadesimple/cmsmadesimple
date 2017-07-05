// define cmsms_tiny object
var cmsms_tiny = {};

// this is the actual tinymce initialization
tinymce.init({
    selector: '{if isset($mt_selector) && $mt_selector != ''}{$mt_selector}{else}textarea.MicroTiny{/if}',
    language: '{$languageid}',
    cmsms_tiny: cmsms_tiny = {
        schema: 'html5',
        base_url : '{root_url}/',
        resize : {mt_jsbool($mt_profile.allowresize)},
        statusbar : {mt_jsbool($mt_profile.showstatusbar)},
        menubar : {mt_jsbool($mt_profile.menubar)},
        filepicker_title : '{$MT->Lang('filepickertitle')}',
        filepicker_url : '{$filepicker_url}&field=',
        filebrowser_title : '{$MT->Lang('title_cmsms_filebrowser')}',
        linker_text : '{$MT->Lang('cmsms_linker')}',
        linker_title : '{$MT->Lang('title_cmsms_linker')}',
        linker_image : '{$MT->GetModuleURLPath()}/lib/images/cmsmslink.gif',
        linker_url : '{$linker_url}',
        linker_autocomplete_url : '{$getpages_url}',
	mailto_text : '{$MT->Lang('mailto_text')}',
	mailto_title : '{$MT->Lang('mailto_image')}',
	mailto_image : '{$MT->GetModuleURLPath()}/lib/images/mailto.gif',
        prompt_page : '{$MT->Lang('prompt_linker')}',
        prompt_page_info : '{$MT->Lang('info_linker_autocomplete')}',
        prompt_alias : '{$MT->Lang('prompt_selectedalias')}',
        prompt_alias_info : '{$MT->Lang('tooltip_selectedalias')}',
        prompt_text : '{$MT->Lang('prompt_texttodisplay')}',
        prompt_class : '{$MT->Lang('prompt_class')}',
        prompt_rel : '{$MT->Lang('prompt_rel')}',
        prompt_target : '{$MT->Lang('prompt_target')}',
        prompt_insertmailto : '{$MT->Lang('prompt_insertmailto')}',
        prompt_email : '{$MT->Lang('prompt_email')}',
        prompt_anchortext : '{$MT->Lang('prompt_anchortext')}',
        prompt_linktext : '{$MT->Lang('prompt_linktext')}',
        tab_general : '{$MT->Lang('tab_general_title')}',
        tab_advanced : '{$MT->Lang('tab_advanced_title')}',
        target_none : '{$MT->Lang('none')}',
        target_new_window : '{$MT->Lang('newwindow')}',
        loading_info : '{$MT->Lang('loading_info')}'
    },
    document_base_url: cmsms_tiny.base_url,
    relative_urls: true,
    image_title: true,
    mysamplesetting: 'foobar',
    menubar: cmsms_tiny.menubar,
    statusbar: cmsms_tiny.statusbar,
    resize: cmsms_tiny.resize,
    removed_menuitems: 'newdocument',
    browser_spellcheck: true,
    // smarty logic stuff
{if isset($mt_cssname) && $mt_cssname != ''}
    content_css : '{cms_stylesheet name=$mt_cssname nolinks=1}',
{/if}
{if $isfrontend}
    toolbar: 'undo | bold italic underline | alignleft aligncenter alignright alignjustify indent outdent | bullist numlist | link mailto{if $mt_profile.allowimages} | image{/if}',
    plugins: ['tabfocus hr autolink paste link mailto anchor wordcount lists {if $mt_profile.allowimages} media image{/if} {if $mt_profile.allowtables}table{/if}'],
{else}
    image_advtab: true,
    toolbar: 'undo redo | cut copy paste | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify indent outdent | bullist numlist | anchor link mailto unlink cmsms_linker{if $mt_profile.allowimages} | image {/if}',
    plugins: ['tabfocus hr paste autolink link lists mailto cmsms_linker charmap anchor searchreplace wordcount code fullscreen insertdatetime {if $mt_profile.allowtables}table{/if} {if $mt_profile.allowimages}media image cmsms_filepicker {/if}'],
{/if}
    // callback functions
    urlconverter_callback: function(url, elm, onsave, name) {
        var self = this;
        var settings = self.settings;

        if (!settings.convert_urls || ( elm && elm.nodeName == 'LINK' ) || url.indexOf('file:') === 0 || url.length === 0) {
            return url;
        }

        // fix entities in cms_selflink urls.
        if (url.indexOf('cms_selflink') != -1) {
            decodeURI(url);
            url = url.replace('%20', ' ');
            return url;
        }
        // Convert to relative
        if (settings.relative_urls) {
            return self.documentBaseURI.toRelative(url);
        }
        // Convert to absolute
        url = self.documentBaseURI.toAbsolute(url, settings.remove_script_host);

        return url;
    },
    setup: function(editor) {
        editor.addMenuItem('mailto',{
           text: cmsms_tiny.prompt_insertmailto,
           cmd:  'mailto',
           context: 'insert',
        })
        editor.on('change', function(e) {
            $(document).trigger('cmsms_formchange');
        });
    },
    paste_as_text: true,
});
