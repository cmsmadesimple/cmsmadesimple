/** ==========================================================
 * CMSMS Admin Functions
 * @module CMSMS_Admin
 *
 * CMS - CMS Made Simple
 * (c)2004-2013 CMS Made Simple
 * This project's homepage is: http://www.cmsmadesimple.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * ========================================================== */

/**
 * @namespace Namespace for CMSMS_Admin classes and functions
 */
var CMSMS_Admin = CMSMS_Admin || {};

/**
 * @function namespace
 * @description A helper function to create a general purpose namespace method
 * allows to create namespace a bit easier
 * @param {String} namespace The name of namespace to be used
 */

CMSMS_Admin.namespace = function(namespace) {"use strict";

    var parts = namespace.split('.'), 
        parent = CMSMS_Admin;

    if (parts[0] === 'CMSMS_Admin') {
        parts = parts.slice(1);
    }

    for ( var i = 0; i < parts.length; i++ ) {
        var partname = parts[i];

        if ( typeof parent[partname] === 'undefined') {
            parent[partname] = {};
        }

        parent = parent[partname];
    }

    return parent;
};

/**
 * @namespace Namespace for CMSMS_Admin.Loader functions
 */
var cms_loader = CMSMS_Admin.namespace('CMSMS_Admin.Loader');

cms_loader.reload = ( function() {

    // Reload functions after AjaxSuccess where needed
    $(document).ajaxSuccess(function() {
        
        CMSMS_Admin.Helper.cms_helpDialog(),
        CMSMS_Admin.Helper.cms_initModalDialog(),
        CMSMS_Admin.Helper.cms_initTooltips();
    });
} );

cms_loader.init = ( function() {

    CMSMS_Admin.Loader.reload(),
    CMSMS_Admin.Helper.cms_resizableTextArea(),
    CMSMS_Admin.Helper.cms_helpDialog(),
    CMSMS_Admin.Helper.cms_initTabs(),
    CMSMS_Admin.Helper.cms_initModalDialog(),
    CMSMS_Admin.Helper.cms_initTooltips();
} );

/*
 * Initialize CMSMS_Admin app
 */
$(function() {"use strict";
    CMSMS_Admin.Loader.init();
});

/**
 * @namespace Namespace for CMSMS_Admin.Helper fu nctions
 */
var cms_helper = CMSMS_Admin.namespace("CMSMS_Admin.Helper");

/** =============================
 *  CMSMS_Admin helper functions
 *  ============================== */

/**
 * @description Detects if Browser supports textarea resize property or .cms_resizable class was applied to
 * textarea element, if conditions match jQueryUI .resizable() plugin is applied
 * @requires jQueryUI
 */

cms_helper.cms_resizableTextArea = ( function() {

    // create textarea element for testing
    var textarea = document.createElement('textarea');

    if (textarea.style.resize === undefined || $('textarea').hasClass('cms_resizable')) {
        $('textarea').resizable({
            handles : 'se',
            ghost : true
        });
    }
} );

/**
 * @description handles clicking on a cms_helpicon image if div containing help text
 * cannot be found help text is loaded via ajax.
 * @requires jQueryUI
 */

cms_helper.cms_helpDialog = ( function() {

    $('.cms_help img.cms_helpicon').on('click', function() {

        var txt, 
        $this = $(this), 
        key = $this.next('span.cms_helpkey').html();

        if (key.length && $('#cmshelp_' + key).length === 0) {
            // get the text via ajax
            // put it in the div.
            var i2 = key.indexOf('__'), 
                key2 = key.substr(i2 + 2), 
                e = $('<div class="cms_helptext" title="' + cms_data.title_help + ': ' + key2 + '" id="cmshelp_' + key + '" style="display: none;"></div>');

            $this.append(e);

            $.get(cms_data.ajax_help_url, {
                key : key
            }, function(data) {
                $('#cmshelp_' + key).html(data);
            });
        }

        $('#cmshelp_' + key).dialog();
    });
} );

/**
 * @description Initializes tabbed content for CMSMS Admin pages
 * @function
 */
cms_helper.cms_initTabs = ( function() {

    function cms_activateTab(index) {

        var container = $('#navt_tabs'), 
            tabs = $('#navt_tabs, #page_tabs').find('div');

        if (container.length === 0) {
            container = $('#page_tabs');
        }

        container.find('div:eq(' + index + ')').mousedown();
    }

    var tabs = $('#navt_tabs, #page_tabs').find('div');

    tabs.mousedown(function() {
        var $this = $(this);

        tabs.each(function() {
            var tab = $(this);

            tab.removeClass('active');
            $('#' + tab.attr('id') + '_c').hide();
        });

        $this.addClass('active');
        $('#' + $this.attr('id') + '_c').show();

        return true;
    });

    // intialize active tab
    if (tabs.filter('.active').mousedown().length === 0) {
        cms_activateTab(0);
    }

} );

/**
 * @description initalizes jQueryUI .dialog() plugin to any element with class .dialog and modal window mode.
 * Element to open the dialog needs class .open.
 * @requires jQueryUI
 */
cms_helper.cms_initModalDialog = ( function() {

    // dialogs is Object
    var dialogs = {};

    $('.dialog').each(function () {

        var $this = $(this),
            dialog_id = $(this).prev('.open').attr('title');
            
            // intialize .dialog() plugin
            dialogs[dialog_id] = $this.dialog({
                autoOpen: false,
                modal: true
                
            });
    });

    // handle dialog open link
    $(document).on('click', '.open', function (e) {
        var $this = $(this);
            dialog_id = $this.attr('title');

        dialogs[dialog_id].dialog('open').removeClass('invisible');
            e.preventDefault();
            
        });

});

cms_helper.cms_initTooltips = ( function() {

    $('.tooltip').tooltip({
        items : '[title], [data-cms-description], [data-cms-ajax]',
        content : function(callback) {
            var el = $(this), data = el.data();

            // for longer descriptions
            if (el.is('[data-cms-description]')) {
                var content = data.cmsDescription;
                return content;
            }

            // for ajax content
            if (el.is('[data-cms-ajax]')) {
                var url = data.cmsAjax;
                url += "&showtemplate=false";

                console.debug(url);

                $.ajax({
                    url : url,
                    async : true,
                    dataType : 'html',
                    error : function(jqXHR, textStatus, errorThrown) {
                        console.log('Sorry. There was a error in your request: ' + textStatus + ' ' + errorThrown);
                    },
                    success : function(content) {
                        callback(content);
                    }
                });
            }

            // simple title tooltip
            if (el.is('[title]')) {
                return el.attr('title');
            }
        }
    });

} );

/**
 * @description Initializes jQueryUI widgets without JS using HTML5 data- attributes
 * Usage example: <div data-jqui="draggable" data-add-classes="false" data-axis="x">This is draggable</div>
 * @author Lukas Olson
 * @copyright Lukas Olson https://github.com/lukasolson/jQuery-UI-Markup
 * @license https://github.com/lukasolson/jQuery-UI-Markup/blob/master/license
 * @requires jQueryUI
 */
cms_helper.cms_jquiMarkup = ( function() {

    $('[data-jqui]').each(function(i, el) {
        var options = $(el).data();
        $.each(options.jqui.split(/\s+/), function(i, method) {
            $(el)[method](options);
        });
    });
} );

/** ===================
 *  GLOBAL FUNCTIONS
 *  ===================*/

/**
 * @description toggles all checkboxes from closest table inisde a table row when specified checkbox is checked
 * @param {Object} obj
 * @requires jQuery
 */
function cms_checkAll(obj) {"use strict";

    var checkall = $(obj), table = $(obj).closest('table');

    $('tbody [type=checkbox]', table).click(function() {
        var v = $(this).val();

        checkall.attr('checked', false);
        $(this).trigger('cms_checkall_toggle', {
            checked : v
        });
    });

    table.on('click', obj, function() {
        var v = $(this).is(':checked');

        $('tbody [type=checkbox]', table).each(function() {
            $(this).attr('checked', v);
            $(this).trigger('cms_checkall_toggle', {
                checked : v
            });
        });
    });
}

/**
 * @description Intializes jQueryUI .sortable() widget on specified table element
 * @param {String} table The id of the table where sortable should be applied
 * @param {String} actionurl The URL for the action that should be performed on update event
 * @requires jQueryUI
 */
function cms_initSortable(table, actionurl) {"use strict";

    var uiFixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    $(table + ' tbody').sortable({
        helper : uiFixHelper,
        update : function(event, ui) {
            var url = actionurl, info = $(this).sortable('serialize');

            $(table + ' tbody').find('tr:even').attr('class', 'row1');
            $(table + ' tbody').find('tr:odd').attr('class', 'row2');

            $.post(url + '&' + info, function(data) {
                if (data.substr(0, 5) === 'ERROR') {
                    alert(data);
                }
            });
        }
    });
}

/*
 * @description jQuery backwards compatibility for togglecollapse function
 * @function
 * @param {String} cid The id name of Element toggle
 */
function togglecollapse(cid) {
    $('#' + cid).toggle();
}

/*
 function alert(msg,title)
 {
 if( typeof(msg) == 'undefined' ) return;
 if( typeof(title) == 'undefined' ) title = cms_lang('alert');

 if( $('#cmsms_errorDialog').length == 0 ) {
 $('<div style="display: none;"><div id="cmsms_errorDialog"></div></div>').insertAfter('body');
 }
 $('#cmsms_errorDialog').html(msg);
 $('#cmsms_errorDialog').dialog({ modal: true, title: title });
 }
 */