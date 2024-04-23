// define cmsms_tiny object
var cmsms_tiny = { };
// this is the actual tinymce initialization
tinymce.init({
    selector: '{if isset($mt_selector) && $mt_selector != ''}{$mt_selector}{else}textarea.MicroTiny{/if}',
    language: '{$languageid}',
    cmsms_tiny: cmsms_tiny = {
        schema: 'html5',
        base_url: '{root_url}/',
        resize: {if (bool)$mt_profile.allowresize}true{else}false{/if},
        statusbar: {if (bool)$mt_profile.showstatusbar}true{else}false{/if},
        menubar: {if (bool)$mt_profile.menubar}true{else}false{/if},
        filepicker_title: '{$MT->Lang('filepickertitle')|escape:javascript}',
        filepicker_url: '{$filepicker_url}&field=',
        filebrowser_title: '{$MT->Lang('title_cmsms_filebrowser')|escape:javascript}',
        linker_text: '{$MT->Lang('cmsms_linker')|escape:javascript}',
        linker_title: '{$MT->Lang('title_cmsms_linker')|escape:javascript}',
        linker_image: '{$MT->GetModuleURLPath()}/lib/images/cmsmslink.gif',
        linker_url: '{$linker_url}',
        linker_autocomplete_url: '{$getpages_url}',
        mailto_text: '{$MT->Lang('mailto_text')|escape:javascript}',
        mailto_title: '{$MT->Lang('mailto_image')|escape:javascript}',
        mailto_image: '{$MT->GetModuleURLPath()}/lib/images/mailto.gif',
        prompt_page: '{$MT->Lang('prompt_linker')|escape:javascript}',
        prompt_page_info: '{$MT->Lang('info_linker_autocomplete')|escape:javascript}',
        prompt_alias: '{$MT->Lang('prompt_selectedalias')|escape:javascript}',
        prompt_alias_info: '{$MT->Lang('tooltip_selectedalias')|escape:javascript}',
        prompt_text: '{$MT->Lang('prompt_texttodisplay')|escape:javascript}',
        prompt_class: '{$MT->Lang('prompt_class')|escape:javascript}',
        prompt_rel: '{$MT->Lang('prompt_rel')|escape:javascript}',
        prompt_target: '{$MT->Lang('prompt_target')|escape:javascript}',
        prompt_insertmailto: '{$MT->Lang('prompt_insertmailto')|escape:javascript}',
        prompt_email: '{$MT->Lang('prompt_email')|escape:javascript}',
        prompt_anchortext: '{$MT->Lang('prompt_anchortext')|escape:javascript}',
        prompt_linktext: '{$MT->Lang('prompt_linktext')|escape:javascript}',
        tab_general: '{$MT->Lang('tab_general_title')|escape:javascript}',
        tab_advanced: '{$MT->Lang('tab_advanced_title')|escape:javascript}',
        target_none: '{$MT->Lang('none')|escape:javascript}',
        target_new_window: '{$MT->Lang('newwindow')|escape:javascript}',
        loading_info: '{$MT->Lang('loading_info')|escape:javascript}'
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
    content_css: '{cms_stylesheet name=$mt_cssname nolinks=1}',
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
