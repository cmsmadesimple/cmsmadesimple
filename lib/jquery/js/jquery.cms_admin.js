/* ==========================================================
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
! function ($) {
    "use strict";
    
    var CMSMS_Admin = {

        init: function () {

            /**
             * @description Detects if Browser supports textarea resize property or .cms_resizable class was applied to textarea element, if conditions match jQueryUI .resizable() plugin is applied
             * @requires jQueryUI
             */
            var cms_resizableTextArea = (function () {

                // create textarea element for testing
                var textarea = document.createElement('textarea');

                if (textarea.style.resize === undefined || $('textarea').hasClass('cms_resizable')) {
                    $('textarea').resizable({
                        handles: 'se',
                        ghost: true
                    });
                }
            })();

            /**
             * @description handles clicking on a cms_helpicon image if div containing help text cannot be found help text is loaded via ajax.
             * @requires jQueryUI
             */
            var cms_helpDialog = (function () {

                $('.cms_help img.cms_helpicon').on('click', function () {

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
                            key: key
                        }, function (data) {
                            $('#cmshelp_' + key).html(data);
                        });
                    }

                    $('#cmshelp_' + key).dialog();
                });
            })();

            /**
             * @description Initializes tabbed content for CMSMS Admin pages
             * @function
             */
            var cms_initTabs = (function () {

                function cms_activateTab(index) {

                    var container = $('#navt_tabs'),
                        tabs = $('#navt_tabs, #page_tabs').find('div');

                    if (container.length === 0) {
                        container = $('#page_tabs');
                    }

                    container.find('div:eq(' + index + ')').mousedown();
                }

                var tabs = $('#navt_tabs, #page_tabs').find('div');

                tabs.mousedown(function () {
                    var $this = $(this);

                    tabs.each(function () {
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

            })();

            /*
             * @description jQuery backwards compatibility for togglecollapse function
             * @function
             * @param {String} cid The id name of Element toggle
             */
            var togglecollapse = (function (cid) {
                $('#' + cid).toggle();
            })();

            /**
             * @description initalizes jQueryUI .dialog() plugin to any element with class .dialog and modal window mode. Element to open the dialog needs class .open.
             * @requires jQueryUI
             */
            var cms_initModalDialog = (function () {

                // dialogs is Object
                var dialogs = {};

                $('.dialog').each(function () {

                    var $this = $(this),
                        dialog_id = $this.prev('.open').attr('title');
                        
                        // intialize .dialog() plugin
                        dialogs[dialog_id] = $this.dialog({
                            autoOpen: false,
                            modal: true,
                            title: $this.attr('title')
                        });
                });

                // handle dialog open link
                $('.open').click(function (e) {
                    var $this = $(this);

                    dialogs[$this.attr('title')].dialog('open').removeClass('invisible');
                    e.preventDefault();
                    
                });
                
            })();
            
            /**
             * @description Intitalizes jQueryUI tooltip() plugin to elements with class .tooltip Use title attribute or data-cms-description
             * @function
             * @requires jQueryUI
             */
            var cms_initTooltips = (function () {
                
                $('.tooltip').tooltip({
                    items: '[title], [data-cms-description], [data-cms-ajax]',
                    content: function(callback) {
                        
                        var el = $(this),
                            data = el.data();
                            
                            // for longer descriptions
                            if(el.is('[data-cms-description]')) {
                                var content = data.cmsDescription;
                                return content
                            }
                            
                            // for ajax content
                            if(el.is('[data-cms-ajax]')) {
                                var url = data.cmsAjax;
                                    url += "&showtemplate=false"
                                $.ajax({
                                    url: url,
                                    async: true,
                                    dataType: 'html',
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        alert('Sorry. There was a error in your request: ' + textStatus);
                                    },
                                    success: function(content) {
                                        callback(content);
                                    }
                                });
                            }
                            
                            // simple title tooltip
                            if(el.is('[title]')) {
                                return el.attr('title');
                            }
                      }
                 });
            })();
            
            /**
             * @description Initializes jQueryUI widgets without JS using HTML5 data- attributes
             * Usage example: <div data-jqui="draggable" data-add-classes="false" data-axis="x">This is draggable</div>
             * @author Lukas Olson
             * @copyright Lukas Olson https://github.com/lukasolson/jQuery-UI-Markup
             * @license https://github.com/lukasolson/jQuery-UI-Markup/blob/master/license
             * @requires jQueryUI
             */
            var cms_jquiMarkup = (function () {
                
                $('[data-jqui]').each(function (i, el) {
                    var options = $(el).data();
                    $.each(options.jqui.split(/\s+/), function (i, method) {
                        $(el)[method](options);
                    });
                });
            })();
        }
    };

    $(function () {
        CMSMS_Admin.init();
    });

}(window.jQuery);

/**
 * @description toggles all checkboxes from closest table inisde a table row when specified checkbox is checked
 * @param {String} all The element to use when selecting all
 */
function cms_checkAll(all) {
    
    $(document).on('click', all, function () {
        
        var checkall = $(this);
        
        checkall.closest('table').find('tbody tr').each(function () {
            var $this = $(this),
                $checkbox = $this.find(':checkbox');
                
            if (checkall.is(':checked')) {
                $checkbox.attr('checked', true);
            } else {
                $checkbox.attr('checked', false);
            }
            
            $($checkbox).click(function () {
                checkall.attr('checked', false);
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
function cms_initSortable(table, actionurl) {

    var uiFixHelper = function (e, ui) {
        ui.children().each(function () {
            $(this).width($(this).width());
        });
        return ui;
    };
    
    $(table + ' tbody').sortable({
        helper: uiFixHelper,
        update: function (event, ui) {
            var url = actionurl,
                info = $(this).sortable('serialize');

            $(table + ' tbody').find('tr:even').attr('class', 'row1');
            $(table + ' tbody').find('tr:odd').attr('class', 'row2');

            $.post(url + '&' + info,
                function (data) {
                    if (data.substr(0, 5) == 'ERROR') {
                        alert(data);
                    }
                });
        }
    });
}

function alert(msg,title)
{
  if( typeof(msg) == 'undefined' ) return;
  if( typeof(title) == 'undefined' ) title = cms_lang('alert');

  if( $('#cmsms_errorDialog').length == 0 ) {
    $('<div style="display: none;"><div id="cmsms_errorDialog"></div></div>').insertAfter('body');
    $('#cmsms_errorDialog').html(msg);
  }
  $('#cmsms_errorDialog').dialog({ modal: true, title: title });
}

function confirm(msg,title)
{
  if( typeof(msg) == 'undefined' ) return;
  if( typeof(title) == 'undefined' ) title = cms_lang('confirm');

  if( $('#cmsms_confirmDialog').length == 0 ) {
    $('<div style="display: none;"><div id="cmsms_confirmDialog"></div></div>').insertAfter('body');
    $('#cmsms_confirmDialog').html(msg);
  }

  var result = null;
  var vbuttons = {};
  buttons[cms_lang('ok')] = function(){
    result = true;
    alert('ok');
  };
  buttons[cms_lang('cancel')] = function(){
    result = false;
    alert('cancel');
  };

  $('#cmsms_confirmDialog').dialog({ 
    modal: true, 
    title: title,
    buttons: vbuttons
  });
}
