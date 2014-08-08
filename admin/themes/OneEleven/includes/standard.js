/** ==========================================================
 * CMSMS OneEleven theme Functions
 * @package CMS Made Simple
 * @module OE
 * @author Goran Ilic - uniqu3 <ja@ich-mach-das.at>
 * ========================================================== */

( function(global, $) {'use strict';
    /*jslint nomen: true , devel: true*/

    /**
     * @namespace OE
     */
    var OE = global.OE = {};

    $(document).ready(function() {
        OE.helper.init();
        OE.view.init();
    });

    /**
     * @namespace OE.helper
     */
    OE.helper = {

        init : function() {
            var _this = this;

            // open external links with rel="external" attribute in new window
            $('a[rel=external]').attr('target', '_blank');
            // focus on input with .defaultfocus class
            $('input.defaultfocus:eq(0), input[autofocus]').focus();
            // load cookie.js if localStorage is not supported
            if (!_this._isLocalStorage()) {
                _this.loadScript('themes/OneEleven/includes/jquery.cookie.min.js');
            }
        },

        /**
         * @description conditional load script helper function
         * @author Brad Vincent https://gist.github.com/2313262
         * @memberof OE.helper
         * @function loadScript(url, arg1, arg2)
         * @param {string} url
         * @callback requestCallback
         * @param {requestCallback|boolean} arg1
         * @param {requestCallback|boolean} arg2
         */
        loadScript : function(url, arg1, arg2) {
            var cache = true,
                callback = null,
                load = true;
            //arg1 and arg2 can be interchangable
            if ($.isFunction(arg1)) {
                callback = arg1;
                cache = arg2 || cache;
            } else {
                cache = arg1 || cache;
                callback = arg2 || callback;
            }

            //check all existing script tags in the page for the url
            $('script[type="text/javascript"]').each(function() {
                var load = ( url !== $(this).attr('src') );
                return load;
            });

            if (load) {
                //didn't find it in the page, so load it
                $.ajax({
                    type : 'GET',
                    url : url,
                    async : false,
                    success : callback,
                    dataType : 'script',
                    cache : cache
                });
            } else {
                //already loaded so just call the callback
                if ($.isFunction(callback)) {
                    callback.call(this);
                }
            }
        },

        /**
         * @description saves a defined key and value to localStorage if localStorgae is supported, else falls back to jquery cookie plugin
         * @requires cookie https://github.com/carhartl/jquery-cookie/blob/master/jquery.cookie.js
         * @memberof OE.helper
         * @function setStorageValue(key, value)
         * @param {string} key
         * @param {string} value
         * @param {number} expires (number in days)
         */
        setStorageValue : function(key, value, expires) {
            var _this = this,
                expiration = new Date().getTime() + ( expires * 24 * 60 * 60 * 1000 ),
                obj = {};

            try {
                if (_this._isLocalStorage() === true) {
                    localStorage.removeItem(key);

                    if (expires !== null) {

                        obj = {
                            value : value,
                            timestamp : expiration
                        };
                    } else {
                        obj = {
                            value : value,
                            timestamp : ''
                        };
                    }

                    localStorage.setItem(key, JSON.stringify(obj));

                } else {
                    if (expires !== null) {
                        obj = {
                            expires : expires
                        };
                    }

                    if ($.fn.cookie !== 'undefined') {
                        $.cookie(key, value, obj);
                    }
                }
            } catch(error) {
                console.log('localStorage Error: set(' + key + ', ' + value + ')');
                console.log(error);
            }
        },

        /**
         * @description gets value for defined key from localStorage if localStorgae is supported, else falls back to jquery cookie plugin
         * @requires cookie https://github.com/carhartl/jquery-cookie/blob/master/jquery.cookie.js
         * @memberof OE.helper
         * @function getStorageValue(key)
         * @param {string} key
         */
        getStorageValue : function(key) {
            var _this = this,
                data = '',
                value;

            if (_this._isLocalStorage()) {
                data = JSON.parse(localStorage.getItem(key));

                if (data !== null && data.timestamp < new Date().getTime()) {
                    _this.removeStorageValue(key);
                } else if (data !== null) {
                    value = data.value;
                }
            } else {
                if ($.fn.cookie !== 'undefined') {
                    value = $.cookie(key);
                }
            }
            return value;
        },

        /**
         * @description removes defined key from localStorage if localStorgae is supported, else falls back to jquery cookie plugin
         * @requires cookie https://github.com/carhartl/jquery-cookie/blob/master/jquery.cookie.js
         * @memberof OE.helper
         * @function removeStorageValue(key)
         * @param {string} key
         */
        removeStorageValue : function(key) {
            var _this = this;

            if (_this._isLocalStorage()) {
                localStorage.removeItem(key);
            } else {
                $.cookie(key, null);
            }
        },

        /**
         * @description Sets equal height on specified element group
         * @memberof OE.helper
         * @function equalHeight(obj)
         * @param {object}
         */
        equalHeight : function(obj) {
            var tallest = 0,
                elHeight;

            obj.each(function() {
                var el = $(this),
                    elHeight = el.height();

                if (elHeight > tallest) {
                    tallest = elHeight;
                }

            });

            obj.height(tallest);
        },

        /**
         * @description detects if localStorage is supported by browser
         * @function _isLocalStorage()
         * @private
         */
        _isLocalStorage : function() {
            return typeof (Storage) !== 'undefined';
        },

        /**
         * @description Basic check for common mobile devices and touch capability
         * @function _isMobileDevice()
         * @private
         */
        _isMobileDevice : function() {
            var ua = navigator.userAgent.toLowerCase(),
            devices = /(Android|iPhone|iPad|iPod|Blackberry|Dolphin|IEMobile|WPhone|Windows Mobile|IEMobile9||IEMobile10||IEMobile11|Kindle|Mobile|MMP|MIDP|Pocket|PSP|Symbian|Smartphone|Sreo|Up.Browser|Up.Link|Vodafone|WAP|Opera Mini|Opera Tablet|Mobile|Fennec)/i;

            if (ua.match(devices) && (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0) || window.DocumentTouch && document instanceof DocumentTouch)) {
                return true;
            }
        }

    };

    /**
     * @namespace OE.view
     */
    OE.view = {

        init : function() {
            var _this = this,
                $sidebar_toggle = $('.toggle-button'), // object for sidebar toggle
                $container = $('#oe_container'), // page container
                $menu = $('#oe_pagemenu'), // page menu
                $toggle_dropzone = $('.toggle-dropzone'), // dropzone toggle trigger
                $dropzone = $('.pageheader'); // dropzone container

            // fix footer, breaks when max-width 1024 kicks in and there is less content then height of menu
            $('#oe_admin-content').css('min-height', ($('#oe_sidebar').height()));

            // handle navigation sidebar toggling
            $sidebar_toggle.on('click', function(e) {

                e.preventDefault();
                if ($container.hasClass('sidebar-on')) {
                    _this._closeSidebar($container, $menu);
                } else {
                    _this._showSidebar($container, $menu);
                }
            });

            // trigger hide/reveal dropzone
            _this.toggleDropzone($toggle_dropzone, $dropzone);
            // toggle hide/reveal menu children
            _this.toggleSubMenu($menu, 50);
            // handle notifications
            _this.showNotifications();
            // apply jQueryUI buttons
            _this.setUIButtons();
            // handle functions that need window resize
            _this.updateDisplay();

            $(window).resize(function() {
                $('.dashboard-inner').css('height', 'auto');
                _this.updateDisplay();
            });
        },

        /**
         * @description Checks for saved state of sidebar
         * @function handleSidebar(trigger, container)
         * @param {object} trigger
         * @param {object} container
         * @memberof OE.view
         */
        handleSidebar : function(trigger, container) {
            var viewportWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

            if (OE.helper.getStorageValue('sidebar-pref') === 'sidebar-off' && viewportWidth > 768) {
                container.addClass('sidebar-off').removeClass('sidebar-on');
                trigger.addClass('open-sidebar');
            } else {
                container.addClass('sidebar-on').removeClass('sidebar-off');
                trigger.addClass('close-sidebar');
            }
        },

        /**
         * @description Sets sidebar menu to fixed position on scroll
         * @function stickyMenu()
         * @param {object} obj
         * @memberof OE.view
         */
        stickyMenu : function(obj) {
            var viewportWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
                viewportHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
                offset = obj.offset(),
                topOffset = offset.top,
                leftOffset = offset.left,
                scrollTop = $(window).scrollTop();

            $(window).scroll(function() {
                if (viewportWidth < 768) {
                    obj.css({
                        marginTop : '45px',
                        position : 'relative'
                    });
                } else if (( viewportWidth >= 768 ) && ( viewportHeight >= obj.height() )) {
                    scrollTop = $(window).scrollTop();

                    if (scrollTop >= topOffset) {
                        obj.css({
                            marginTop : '-100px',
                            position : 'fixed'
                        });
                    } else if (scrollTop < topOffset) {
                        obj.css({
                            marginTop : '45px',
                            position : 'relative'
                        });
                    }
                }
            });
        },

        /**
         * @description Handles open/close state of FileManager dropzone in OneEleven theme
         * @function toggleDropzone()
         * @param {object} trigger - click event handler
         * @param {object} obj
         * @memberof OE.view
         */
        toggleDropzone : function(trigger, obj) {

            trigger.click(function(e) {
                e.preventDefault();

                $('.drop-inner').toggleClass('hidden');

                if ($('.drop-inner').hasClass('hidden')) {
                    OE.helper.setStorageValue('dropzone-pref', 'hidden', 60);
                } else {
                    OE.helper.removeStorageValue('dropzone-pref');
                }
            });

            if (OE.helper.getStorageValue('dropzone-pref') === 'hidden') {
                $('.drop-inner').addClass('hidden');
            } else {
                $('.drop-inner').removeClass('hidden');
            }
        },

        /**
         * @description Handles toggling of main menu child items
         * @function toggleSubMenu(obj)
         * @param {object} obj - Menu container object
         * @param {number} duration - A positive number for toggle speed control
         * @memberof OE.view
         */
        toggleSubMenu : function(obj, duration) {
            obj.find('li.current span').addClass('open-sub');
            obj .find('> li > span').click(function() {
                var ul = $(this).next();

                if (ul.is(':visible') === false) {
                    obj.find('ul').slideUp(duration);
                }

                ul.slideToggle(duration);
            });
        },

        /**
         * @description Handles Core and Module messages
         * @function showNotification()
         */
        // TODO Rethink this in next versions, define a object based on type or something (maybe use plugin http://akquinet.github.io/jquery-toastmessage-plugin/demo/demo.html), move messages to global function in cms_admin.js so it can be reused by other themes
        showNotifications : function() {

            $('.pagewarning, .message, .pageerrorcontainer, .pagemcontainer').prepend('<span class="close-warning"></span>');
            $(document).on('click', '.close-warning', function() {
                $(this).parent().hide();
                $(this).parent().remove();
            });

            // pagewarning status hidden?
            var key = $('body').attr('id') + '_notification';
            $('.pagewarning .close-warning').click(function() {
                OE.helper.setStorageValue(key, 'hidden', 60);
            });

            if (OE.helper.getStorageValue(key) === 'hidden') {
                $('.pagewarning').addClass('hidden');
            }

            $('.message:not(.no-slide)').click(function() {
                $('.message').slideUp();
            });

            $('.message:not(.no-slide), .pageerrorcontainer:not(.no-slide), .pagemcontainer:not(.no-slide)').each(function() {
                var message = $(this);
                $(message).hide().slideDown(1000, function() {
                    window.setTimeout(function() {
                        message.slideUp();
                    }, 10000);
                });
            });

            $(document).on('cms_ajax_apply', function(e) {
                $('button[name=cancel], button[name=m1_cancel]').fadeOut();
                $('button[name=cancel], button[name=m1_cancel]').button('option', 'label', e.close);
                $('button[name=cancel], button[name=m1_cancel]').fadeIn();

                var htmlShow = '';

                if (e.response === 'Success') {
                    htmlShow = '<aside class="message pagemcontainer" role="status"><span class="close-warning">Close</span><p class="pagemessage">' + e.details + '<\/p><\/aside>';
                } else {
                    htmlShow = '<aside class="message pageerrorcontainer" role="alert"><span class="close-warning">Close</span><ul class="pageerror">';
                    htmlShow += e.details;
                    htmlShow += '<\/ul><\/aside>';
                }

                $('body').append(htmlShow).slideDown(1000, function() {
                    window.setTimeout(function() {
                        $('.message').slideUp();
                        $('.message').remove();
                    }, 10000);
                });

                $(document).on('click', '.close-warning', function() {
                    $('.message').slideUp();
                    $('.message').remove();
                });
            });
        },

        /**
         * @description Applies jQueryUI button function to input buttons
         * @function setUIButtons()
         */
        setUIButtons : function() {

            // Standard input buttons
            $('input[type="submit"], :button[data-ui-icon]').each(function() {
                var button = $(this),
                    icon = button.data('uiIcon') || 'ui-icon-circle-check',
                    label = button.val(),
                    $btn = $('<button />');

                if (!button.hasClass('noautobtn') || !button.hasClass('no-ui-btn')) {
                    if (button.is('[name*=apply]')) {
                        icon = button.data('uiIcon') || 'ui-icon-disk';
                    } else if (button.is('[name*=cancel]')) {
                        icon = button.data('uiIcon') ||'ui-icon-circle-close';
                    } else if (button.is('[name*=resettodefault]') || button.attr('id') === 'refresh') {
                        icon = button.data('uiIcon') ||'ui-icon-refresh';
                    }
                }

                if (button.is(':button')) {
                    label = button.text();
                }

                $(this.attributes).each(function(index, attribute) {
                    $btn.attr(attribute.name, attribute.value);
                });

                $btn.button({
                    icons : {
                        primary : icon
                    },
                    label : label
                });
                button.replaceWith($btn);
            });

            // Back links
            $('a.pageback').addClass('ui-state-default ui-corner-all')
                .prepend('<span class="ui-icon ui-icon-arrowreturnthick-1-w">')
                .hover(function() {
                    $(this).addClass('ui-state-hover');
                }, function() {
                    $(this).removeClass('ui-state-hover');
                });

        },

        /**
         * @description Placeholder function for functions that need to be triggered on window resize
         * @memberof OE.view
         * @function updateDisplay()
         */
        updateDisplay : function() {
            var _this = this,
                viewportWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
                $sidebar_toggle = $('.toggle-button'), // toggle trigger object
                $container = $('#oe_container'), // page container object
                $menu_container = $('#oe_menu'), // menu container object
                $group = $('.dashboard-inner'); // object for equalHeight(obj) function

                // set sidebar visibility
                _this.handleSidebar($sidebar_toggle, $container);
                // handle equal height blocks
                OE.helper.equalHeight($group);
                // handle sticky menu if it's not mobile device
                if (!OE.helper._isMobileDevice()) {
                    _this.stickyMenu($menu_container);
                }
        },

        /**
         * @description Handles setting for Sidebar and sets open state
         * @private
         * @function _showSidebar(obj, target)
         * @params {object} obj
         * @params {object} target
         */
        _showSidebar : function(obj, target) {

            obj.addClass('sidebar-on').removeClass('sidebar-off');
            target.find('li.current ul').show();

            OE.helper.setStorageValue('sidebar-pref', 'sidebar-on', 60);
        },

        /**
         * @description Handles setting for Sidebar and sets closed state
         * @private
         * @function _closeSidebar(obj, target)
         * @params {object} obj
         * @params {object} target
         */
        _closeSidebar : function(obj, target) {

            obj.removeClass('sidebar-on').addClass('sidebar-off');
            target.find('li ul').hide();

            OE.helper.setStorageValue('sidebar-pref', 'sidebar-off', 60);
        },

    };
} )(this, jQuery);