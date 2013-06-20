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

            /*
             *
             * Detects if Browser supports textarea resize property or
             * .cms_resizable class was applied to textarea element
             * if conditions match jQueryUI .resizable() plugin is applied
             *
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

            /*
             *
             * handles clicking on a cms_helpicon image
             * if div containing help text cannot be found help text is loaded via ajax.
             *
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

            /*
             *
             * Initializes tabbed content
             * for CMSMS Admin pages
             *
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
             *
             * jQuery backwards compatibility
             * for togglecollapse function
             *
             */
            var cms_toggleCollapse = (function (cid) {
                $('#' + cid).toggle();
            })();

            /*
             *
             * initalizes jQueryUI .dialog() plugin
             * to any element with class .dialog and modal window mode.
             * element to open the dialog needs class .open
             *
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
                            title: '<strong> ' + $(this).attr('title') + ' </strong>'
                        });
                });

                // handle dialog open link
                $('.open').click(function (e) {
                    var $this = $(this);

                    dialogs[$this.attr('title')].dialog('open').removeClass('invisible');
                    $('.ui-dialog').css('top', '120px');
                    e.preventDefault();
                    
                });
                
            })();
        }
    };

    $(function () {
        CMSMS_Admin.init();
    });

}(window.jQuery);