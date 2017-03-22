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
( function(global, $) {'use strict';
        /*jslint nomen: true , devel: true*/
    var method,
        noop = function () {},
        methods = [
            'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
            'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
            'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
            'timeStamp', 'trace', 'warn'
        ],
        length = methods.length,
        console = (window.console = window.console || {});

        while (length--) {
            method = methods[length];
            // Only stub undefined methods.
            if (!console[method]) {
                console[method] = noop;
            }
        }

        /**
         * @namespace Namespace for CMSMS_Admin classes and functions
         */
        var CMSMS_Admin = global.CMSMS_Admin || {};

        /*
         * Initialize CMSMS_Admin app
         */
        $(document).ready(function() {
            CMSMS_Admin.Loader.init();
        });

        /**
         * @function namespace
         * @description A helper function to create a general purpose namespace method
         * allows to create namespace a bit easier
         * @param {String} namespace The name of namespace to be used
         */

        CMSMS_Admin.namespace = function(namespace) {

            var parts = namespace.split('.'),
                parent = CMSMS_Admin,
                i,
                partname;

            if (parts[0] === 'CMSMS_Admin') {
                parts = parts.slice(1);
            }

            for ( i = 0; i < parts.length; i += 1 ) {
                partname = parts[i];

                if ( typeof parent[partname] === 'undefined') {
                    parent[partname] = {};
                }

                parent = parent[partname];
            }

            return parent;
        };

        /** =============================
         *  CMSMS_Admin loader functions
         *  ============================== */

        /**
         * @namespace Namespace for CMSMS_Admin.Loader functions
         */
        var cms_loader = CMSMS_Admin.namespace('CMSMS_Admin.Loader');

        cms_loader.reload = function() {

            // Reload functions after AjaxSuccess where needed
            $(document).ajaxComplete(function() {

                CMSMS_Admin.Helper.cms_helpDialog();
                CMSMS_Admin.Helper.cms_initTooltips();
            });
        };

        cms_loader.init = function() {

            CMSMS_Admin.Loader.reload();
            CMSMS_Admin.Helper.cms_resizableTextArea();
            CMSMS_Admin.Helper.cms_helpDialog();
            CMSMS_Admin.Helper.cms_initTabs();
            CMSMS_Admin.Helper.cms_initModalDialog();
            CMSMS_Admin.Helper.cms_initTooltips();

            $('form').submit(function(ev){

                if( $(this).attr('novalidate') ) {
                    return;
                }

                var total = 0;
                $('input[type=file]',this).each(function(idx, el){
                    if( el.files.length === 0 ) {
                        return;
                    }

                    total = total + el.files[0].size;
                });
                // handle situation where multiple files added together exceed upload limit
                if (typeof cms_data != 'undefined' && cms_data.max_upload_size && (total > cms_data.max_upload_size)) {
                    alert(cms_data.lang_largeupload);
                    return false;
                }
             });

        };//end

        /** =============================
         *  CMSMS_Admin helper functions
         *  ============================== */

        /**
         * @namespace Namespace for CMSMS_Admin.Helper fu nctions
         */
        var cms_helper = CMSMS_Admin.namespace("CMSMS_Admin.Helper");

        /**
         * @description Detects if Browser supports textarea resize property or .cms_resizable class was applied to
         * textarea element, if conditions match jQueryUI .resizable() plugin is applied
         * @requires jQueryUI
         */

        cms_helper.cms_resizableTextArea = function() {

            // create textarea element for testing
            var textarea = document.createElement('textarea');

            $('textarea').each(function() {

                var $this = $(this);
                if ((textarea.style.resize === undefined || $this.hasClass('cms_resizable')) && (!$this.hasClass('MicroTiny')) && (!$this.hasClass('no-resize'))) {
                    $this.resizable({
                        handles : 'se',
                        ghost : true
                    });
                }
            });
        };// end

        /**
         * @description handles clicking on a cms_helpicon image if div containing help text
         * cannot be found help text is loaded via ajax.
         * @requires jQueryUI
         */

        cms_helper.cms_helpDialog = function() {

            $('.cms_help img.cms_helpicon').on('click', function() {

                var txt,
                    $this = $(this),
                    data  = $this.parent().data(),
                    title = data.cmshelpTitle,
                    key   = data.cmshelpKey;

                if (key.length && $('#cmshelp_' + key).length === 0) {
                    // get the text via ajax
                    // put it in the div.
                    var i2 = key.indexOf('__'),
                        key2 = key.substr(i2 + 2),
                        e = $('<div class="cms_helptext" title="' + cms_data.title_help + ': ' + title + '" id="cmshelp_' + key + '" style="display: none;"></div>');

                    $this.append(e);

                    $.get(cms_data.ajax_help_url, {
                        key : key
                    }, function(data) {
                        $('#cmshelp_' + key).html(data);
                    });
                }

                $('#cmshelp_' + key).dialog();
            });
        }; //end

        /**
         * @description Initializes tabbed content for CMSMS Admin pages
         * @function
         */
        cms_helper.cms_initTabs = function() {

            function _cms_activateTab(index) {

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

	    tabs.focus(function(ev){
		$(this).addClass('focus');
	    });

	    tabs.blur(function(ev){
		$(this).removeClass('focus');
	    })

	    tabs.keyup(function(ev){
		if( ev.keyCode == $.ui.keyCode.ENTER ) {
		    var _i = tabs.index(this);
		    _cms_activateTab(_i);
		}
	    });

            // intialize active tab
	    tabs.attr('tabindex','0');
            if (tabs.filter('.active').mousedown().length === 0) {
                _cms_activateTab(0);
            }

        }; // end

        /**
         * @description initalizes jQueryUI .dialog() plugin to any element with class .dialog and modal window mode.
         * Element to open the dialog needs class .open.
         * @requires jQueryUI
         */
        cms_helper.cms_initModalDialog = function() {

            // dialogs is Object
            var dialogs = {};

            $('.dialog').each(function() {

                var $this = $(this),
                    dialog_id = $(this).prev('.open').attr('title');

                // intialize .dialog() plugin
                dialogs[dialog_id] = $this.dialog({
                    autoOpen : false,
                    modal : true

                });
            });

            // handle dialog open link
            $(document).on('click', '.open', function(e) {
                var $this = $(this),
                    dialog_id = $this.attr('title');

                dialogs[dialog_id].dialog('open').removeClass('invisible');
                e.preventDefault();

            });

        }; //end

        cms_helper.cms_initTooltips = function() {

            $('.tooltip').tooltip({
                items : '[title], [data-cms-description], [data-cms-ajax]',
                content : function(callback) {
                    var el = $(this),
                        data = el.data(),
                        content,
                        url;

                    // for longer descriptions
                    if (el.is('[data-cms-description]')) {
                        content = data.cmsDescription;
                        return content;
                    }

                    // for ajax content
                    if (el.is('[data-cms-ajax]')) {
                        url = data.cmsAjax;
                        url += "&showtemplate=false";

                        //console.debug(url);

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

        }; // end

        /**
         * @description Initializes jQueryUI widgets without JS using HTML5 data- attributes
         * Usage example: <div data-jqui="draggable" data-add-classes="false" data-axis="x">This is draggable</div>
         * @author Lukas Olson
         * @copyright Lukas Olson https://github.com/lukasolson/jQuery-UI-Markup
         * @license https://github.com/lukasolson/jQuery-UI-Markup/blob/master/license
         * @requires jQueryUI
         */
        cms_helper.cms_jquiMarkup = function() {

            $('[data-jqui]').each(function(i, el) {
                var options = $(el).data();
                $.each(options.jqui.split(/\s+/), function(i, method) {
                    $(el)[method](options);
                });
            });
        }; // end

    }(this, jQuery) );

/** ========================
 *  GLOBAL PLUGIN FUNCTIONS
 *  =======================*/

/**
 * @description toggles all checkboxes from closest target inisde a table row when specified checkbox is checked
 * @requires jQuery
 * @example
 * $('#selectall').cmsms_checkall();
 */
( function($) {'use strict';
        /*jslint nomen: true , devel: true*/
        var cmsms_checkall = 'cmsms_checkall',
            defaults = {
                target : 'table'
            };

        function Plugin(element, options) {
            this.element = element;
            this.settings = $.extend({}, defaults, options);
            this.defaults = defaults;
            this._name = cmsms_checkall;

            this.init();
        }


        Plugin.prototype = {

            init : function() {

                this._toggle(this.element, this.settings.target);
            },
            // @ignore
            _toggle : function(obj, container) {

                var target = $(obj).closest(container),
                    $el = $(obj);

                // Handle single checkbox click
                $('[type=checkbox]', target).not($el).click(function() {
                    var $this = $(this),
                        v = $this.prop('checked', !$this.prop('checked'));

                    $el.prop('checked', false);
                    $this.prop('checked', !$this.prop('checked'));
                    $this.trigger('cms_checkall_toggle', {
                        checked : v
                    });
                });

                // toggle all checkboxes on obj click
                $el.on('click', function() {

                    var v = $el.is(':checked');

                    $('[type=checkbox]', target).each(function() {
                        var $this = $(this);

                        $this.attr('checked', v);
                        $this.trigger('cms_checkall_toggle', {
                            checked : v
                        });
                    });
                });
            }
        };

        $.fn[cmsms_checkall] = function(options) {
            return this.each(function() {
                if (!$.data(this, 'plugin_' + cmsms_checkall)) {
                    $.data(this, 'plugin_' + cmsms_checkall, new Plugin(this, options));
                }
            });
        };

    }(jQuery) ); // end

/**
 * @description Intializes jQueryUI .sortable() widget on specified table element
 * @param {String} actionurl The URL for the action that should be performed on update event
 * @param {callback} The callback that handles the response after ui.sortable update event
 * @callback callback
 * @requires jQueryUI
 */
( function($) {'use strict';
        /*jslint nomen: true , devel: true, regexp: true*/
        $.widget('cmsms.cmsms_sortable_table', $.extend({}, $.ui.sortable.prototype, {

            options : {
                actionurl : null,
                update : null,
                helper : null,
                callback: function(data) {}
            },

            _create : function() {

                var self = this;

                this.element.data('sortable', this.element.data('cmsms_sortable_table'));
                this.options.update = function(event, ui) {
                    self._update(self.options, self.element);
                };
                this.options.helper = this._uiFixHelper;
                return $.ui.sortable.prototype._create.apply(this, arguments);
            },

            // @ignore override update option
            _update : function(options, el) {

                var url = options.actionurl,
                    info = this.serialize($(el));

                $(el).find('tr:even').attr('class', 'row1');
                $(el).find('tr:odd').attr('class', 'row2');
                $.post(url + '&' + info, function(data) {
                    options.callback(data);
                });
            },

            serialize : function(o) {

                var items = this._getItemsAsjQuery(o && o.connected),
                    str = [];
                    o = o || {};

                $(items).each(function() {
                    var res = ( $(o.item || this).attr(o.attribute || 'id') || '' ).match(o.expression || ( /(.+)[\-=_](.+)/ ));
                    if (res) {
                        str.push(( o.key || res[1] + '[]' ) + '=' + ( o.key && o.expression ? res[1] : res[2] ));
                    }
                });

                if (!str.length && o.key) {
                    str.push(o.key + "=");
                }

                return str.join('&');

            },

            // @ignore fix Ui helper for tables
            _uiFixHelper : function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });

                return ui;
            }
        }));

        $.cmsms.cmsms_sortable_table.prototype.options = $.extend({}, $.ui.sortable.prototype.options, $.cmsms.cmsms_sortable_table.prototype.options);

    }(jQuery) ); // end

/** ===================
 *  GLOBAL FUNCTIONS
 *  ===================*/

/*
 * @description jQuery backwards compatibility for togglecollapse function
 * @function
 * @param {String} cid The id name of Element toggle
 */
function togglecollapse(cid) {
    'use strict';
    $('#' + cid).toggle();
} // end

/*
* @description jquery simple alert dialog.
* @function
* @param (String) msg The message to display
* @param (String) title An optional title string.
* @return promise
*/
function cms_alert(msg, title) {
    'use strict';
    var _d = $.Deferred();
    if ( typeof ( msg ) === 'undefined') return;
    if ( typeof ( title ) === 'undefined') title = cms_lang('alert');

    if ($('#cmsms_errorDialog').length === 0) {
        $('<div style="display: none;"><div id="cmsms_errorDialog"></div></div>').insertAfter('body');
    }
    $('#cmsms_errorDialog').html(msg);
    $('#cmsms_errorDialog').dialog({
        modal : true,
        title : title,
	close: function( event, ui ) {
	    _d.resolve();
	}
    });
    return _d.promise();
} // end

/*
* @description jquery simple confirm dialog.
* @function
* @param (String) msg The message to display
* @param (String) title An optional title string.
* @param (String) yestxt Optional text for the yes button.
* @param (String) notxt Optional text for the no button.
* @return promise
*/
function cms_confirm(msg, title, yestxt, notxt) {
    'use strict';
    var _d = new $.Deferred();
    if ( typeof ( msg ) === 'undefined') return;
    if ( typeof ( title ) === 'undefined') title = cms_lang('confirm');
    if ( typeof ( yestxt ) === 'undefined' ) yestxt = cms_data.lang_yes;
    if ( typeof ( notxt ) === 'undefined' ) notxt = cms_data.lang_no;

    if ($('#cmsms_confirmDialog').length === 0) {
        $('<div style="display: none;"><div id="cmsms_confirmDialog"></div></div>').insertAfter('body');
    }
    $('#cmsms_confirmDialog').html(msg);
    $('#cmsms_confirmDialog').dialog({
        modal : true,
        title : title,
	buttons: [
	    {
		text: yestxt,
		icons: {
		    primary: 'ui-icon-check'
		},
		click: function(ev) {
		    $(this).dialog('close');
		    _d.resolve(yestxt);
		}
	    },
	    {
		text: notxt,
		icons: {
		    primary: 'ui-icon-close'
		},
		click: function(ev) {
		    $(this).dialog('close');
		    _d.reject(notxt);
		}
	    }
	],
    });
    return _d.promise();
} // end

/**
* @description a function to toggle a busy spinner.
* @function
* @param (Bool) flag Wether to enable or disable the busy state.
*/
function cms_busy(flag)
{
    if( typeof flag == 'undefined' ) flag = true;

    var $_div = $('#cms_busy');
    if( !$_div.length ) {
      // gotta add one.
      var _e = $('<div/>').attr('id','cms_busy').addClass('busy').hide();
      $('body').append(_e);
    }

    if( flag ) {
	// see if we can find a busy div
	setTimeout(function(){
 	    $_div.show();
	}, 10);
    }
    else {
	if( $_div.length ) $_div.hide();
    }
}
