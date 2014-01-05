{* first get MicroTiny module, we don't need wrong variables and strings *}
{$MT = cms_utils::get_module('MicroTiny')}
// define cmsms_tiny object
var cmsms_tiny = {};

// this is the actual tinymce initialization
tinymce.init({
    selector: '{if isset($mt_selector) && $mt_selector != ''}{$mt_selector}{else}textarea.MicroTiny{/if}',
    cmsms_tiny: cmsms_tiny = {
        base_url : '{root_url}/',
        resize : '{mt_jsbool($mt_profile.allowresize)}',
        statusbar : '{mt_jsbool($mt_profile.showstatusbar)}',
        filepicker_title : '{$MT->Lang('filepickertitle')}',
        filepicker_url : '{cms_action_url module='MicroTiny' action='filepicker' forjs=1}&showtemplate=false&field=',
        filebrowser_title : '{$MT->Lang('title_cmsms_filebrowser')}',
        linker_text : '{$MT->Lang('cmsms_linker')}',
        linker_title : '{$MT->Lang('title_cmsms_linker')}',
        linker_image : '{$MT->GetModuleURLPath()}/lib/images/cmsmslink.gif',
        linker_url : '{cms_action_url module='MicroTiny' action=linker forjs=1}&showtemplate=false',
        linker_autocomplete_url : '{cms_action_url action='ajax_getpages' module='MicroTiny' forjs=1}&showtemplate=false',
        prompt_page : '{$MT->Lang('prompt_linker')}',
        prompt_page_info : '{$MT->Lang('info_linker_autocomplete')}',
        prompt_alias : '{$MT->Lang('prompt_selectedalias')}',
        prompt_alias_info : '{$MT->Lang('tooltip_selectedalias')}',
        prompt_text : '{$MT->Lang('prompt_texttodisplay')}',
        prompt_class : '{$MT->Lang('prompt_class')}',
        prompt_rel : '{$MT->Lang('prompt_rel')}',
        prompt_target : '{$MT->Lang('prompt_target')}',
        tab_general : '{$MT->Lang('tab_general_title')}',
        tab_advanced : '{$MT->Lang('tab_advanced_title')}',
        target_none : '{$MT->Lang('none')}',
        target_new_window : '{$MT->Lang('newwindow')}',
        loading_info : '{$MT->Lang('loading_info')}'
    },
    document_base_url: cmsms_tiny.base_url,
    relative_urls: true,
    mysamplesetting: 'foobar',
    statusbar: cmsms_tiny.statusbar,
    resize: cmsms_tiny.resize,
    removed_menuitems: 'newdocument',
    // smarty logic stuff
{if isset($mt_cssname) && $mt_cssname != ''}
    content_css : '{cms_stylesheet name=$mt_cssname nolinks=1}',
{/if}
{if $isfrontend}
    toolbar: 'undo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link{if $mt_profile.allowimages} | image{/if}',
    plugins: ['autolink link anchor wordcount {if $mt_profile.allowimages} media image{/if}'],
{else}
    image_advtab: true,
    toolbar: 'undo redo | cut copy paste | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link unlink cmsms_linker{if $mt_profile.allowimages} | image cmsms_filebrowser{/if}',
    plugins: ['autolink link cmsms_linker charmap anchor searchreplace wordcount code fullscreen insertdatetime {if $mt_profile.allowimages}media image cmsms_filepicker cmsms_filebrowser{/if}'],
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
        editor.on('change', function(e) {
            $(document).trigger('cmsms_formchange');
        });
    }
});
