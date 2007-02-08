/**
 * Tabs 2.5.2 - jQuery plugin for accessible, unobtrusive tabs
 *
 * http://stilbuero.de/tabs/
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

(function($) { // simulate block scope

/**
 * Create an accessible, unobtrusive tab interface based on a particular HTML structure.
 *
 * The underlying HTML has to look like this:
 *
 * <div id="container">
 *     <ul>
 *         <li><a href="#section-1">Section 1</a></li>
 *         <li><a href="#section-2">Section 2</a></li>
 *         <li><a href="#section-3">Section 3</a></li>
 *     </ul>
 *     <div id="section-1">
 *
 *     </div>
 *     <div id="section-2">
 *
 *     </div>
 *     <div id="section-3">
 *
 *     </div>
 * </div>
 *
 * Each anchor in the unordered list points directly to a section below represented by one of the
 * divs (the URI in the anchor's href attribute refers to the fragment with the corresponding id).
 * Because such HTML structure is fully functional on its own, e.g. without JavaScript, the tab
 * interface is accessible and unobtrusive.
 *
 * A tab is also bookmarkable via hash in the URL. Use the History/Remote plugin (Tabs will
 * auto-detect its presence) to fix the back (and forward) button.
 *
 * @example $('#container').tabs();
 * @desc Create a basic tab interface.
 * @example $('#container').tabs(2);
 * @desc Create a basic tab interface with the second tab initially activated.
 * @example $('#container').tabs({disabled: [3, 4]});
 * @desc Create a tab interface with the third and fourth tab being disabled.
 * @example $('#container').tabs({fxSlide: true});
 * @desc Create a tab interface that uses slide down/up animations for showing/hiding tab
 *       content upon tab switching.
 *
 * @param Number initial An integer specifying the position of the tab (no zero-based index) that
 *                       gets first activated, e.g. on page load. If a hash in the URL of the page
 *                       refers to one fragment (tab container) of a tab interface, this parameter
 *                       will be ignored and instead the tab belonging to that fragment in that
 *                       specific tab interface will be activated. Defaults to 1 if omitted.
 * @param Object settings An object literal containing key/value pairs to provide optional settings.
 * @option Array<Number> disabled An array containing the position of the tabs (no zero-based index)
 *                                that should be disabled on initialization. Default value: null.
 * @option Boolean bookmarkable Boolean flag indicating if support for bookmarking and history (via
 *                              changing hash in the URL of the browser) is enabled. Default value:
 *                              false, unless the History/Remote plugin is included. In that case the
 *                              default value becomes true. @see $.ajaxHistory.initialize
 * @option Boolean fxFade Boolean flag indicating whether fade in/out animations are used for tab
 *                        switching. Can be combined with fxSlide. Will overrule fxShow/fxHide.
 *                        Default value: false.
 * @option Boolean fxSlide Boolean flag indicating whether slide down/up animations are used for tab
 *                         switching. Can be combined with fxFade. Will overrule fxShow/fxHide.
 *                         Default value: false.
 * @option String|Number fxSpeed A string representing one of the three predefined speeds ("slow",
 *                               "normal", or "fast") or the number of milliseconds (e.g. 1000) to
 *                               run an animation. Default value: "normal".
 * @option Object fxShow An object literal of the form jQuery's animate function expects for making
 *                       your own, custom animation to reveal a tab upon tab switch. Unlike fxFade
 *                       or fxSlide this animation is independent from an optional hide animation.
 *                       Default value: null. @see animate
 * @option Object fxHide An object literal of the form jQuery's animate function expects for making
 *                       your own, custom animation to hide a tab upon tab switch. Unlike fxFade
 *                       or fxSlide this animation is independent from an optional show animation.
 *                       Default value: null. @see animate
 * @option String|Number fxShowSpeed A string representing one of the three predefined speeds
 *                                   ("slow", "normal", or "fast") or the number of milliseconds
 *                                   (e.g. 1000) to run the animation specified in fxShow.
 *                                   Default value: fxSpeed.
 * @option String|Number fxHideSpeed A string representing one of the three predefined speeds
 *                                   ("slow", "normal", or "fast") or the number of milliseconds
 *                                   (e.g. 1000) to run the animation specified in fxHide.
 *                                   Default value: fxSpeed.
 * @option Boolean fxAutoHeight Boolean flag that if set to true causes all tab heights
 *                              to be constant (being the height of the tallest tab).
 *                              Default value: false.
 * @option Function onClick A function to be invoked upon tab switch, immediatly after a tab has
 *                          been clicked, e.g. before the other's tab content gets hidden. The
 *                          function gets passed three arguments: the first one is the clicked
 *                          tab (e.g. an anchor element), the second one is the DOM element
 *                          containing the content of the clicked tab (e.g. the div), the third
 *                          argument is the one of the tab that gets hidden. Default value: null.
 * @option Function onHide A function to be invoked upon tab switch, immediatly after one tab's
 *                         content got hidden (with or without an animation) and right before the
 *                         next tab is revealed. The function gets passed three arguments: the
 *                         first one is the clicked tab (e.g. an anchor element), the second one
 *                         is the DOM element containing the content of the clicked tab, (e.g. the
 *                         div), the third argument is the one of the tab that gets hidden.
 *                         Default value: null.
 * @option Function onShow A function to be invoked upon tab switch. This function is invoked
 *                         after the new tab has been revealed, e.g. after the switch is completed.
 *                         The function gets passed three arguments: the first one is the clicked
 *                         tab (e.g. an anchor element), the second one is the DOM element
 *                         containing the content of the clicked tab, (e.g. the div), the third
 *                         argument is the one of the tab that gets hidden. Default value: null.
 * @option String selectedClass The CSS class attached to the li element representing the
 *                              currently selected (active) tab. Default value: "tabs-selected".
 * @option String disabledClass The CSS class attached to the li element representing a disabled
 *                              tab. Default value: "tabs-disabled".
 * @option String hideClass The CSS class used for hiding inactive tabs. A class is used instead
 *                          of "display: none" in the style attribute to maintain control over
 *                          visibility in other media types than screen, most notably print.
 *                          Default value: "tabs-hide".
 * @option String tabStruct A CSS selector or basic XPath expression reflecting a nested HTML
 *                          structure that is different from the default single div structure
 *                          (one div with an id inside the overall container holds one tab's
 *                          content). If for instance an additional div is required to wrap up the
 *                          several tab containers such a structure is expressed by "div>div".
 *                          Default value: "div".
 * @type jQuery
 *
 * @name tabs
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
$.fn.tabs = function(initial, settings) {

    // settings
    if (typeof initial == 'object') settings = initial; // no initial tab given but a settings object
    settings = $.extend({
        initial: (initial && typeof initial == 'number' && initial > 0) ? --initial : 0,
        disabled: null,
        bookmarkable: $.ajaxHistory ? true : false,
        fxFade: null,
        fxSlide: null,
        fxShow: null,
        fxHide: null,
        fxSpeed: 'normal',
        fxShowSpeed: null,
        fxHideSpeed: null,
        fxAutoHeight: false,
        onClick: null,
        onHide: null,
        onShow: null,
        selectedClass: 'tabs-selected',
        disabledClass: 'tabs-disabled',
        hideClass: 'tabs-hide',
        tabStruct: 'div'
    }, settings || {});

    $.browser.msie6 = $.browser.msie6 || $.browser.msie && typeof XMLHttpRequest == 'function';

    // helper to prevent scroll to fragment
    var _unFocus = function() {
        scrollTo(0, 0);
    };

    // initialize tabs
    return this.each(function() {

        var container = this;
        var tabs = $('>ul:eq(0)>li>a', this);

        // retrieve active tab from hash in url
        if (location.hash) {
            tabs.each(function(i) {
                if (this.hash == location.hash) {
                    settings.initial = i;
                    // prevent page scroll to fragment
                    if ($.browser.msie || $.browser.opera) {
                        var toShow = $(location.hash);
                        var toShowId = toShow.attr('id');
                        toShow.attr('id', '');
                        setTimeout(function() {
                            toShow.attr('id', toShowId); // restore id
                        }, 500);
                    }
                    _unFocus();
                    return false; // break
                }
            });
        }
        if ($.browser.msie) {
            _unFocus(); // fix IE focussing bottom of the page for some unknown reason
        }

        // highlight tab accordingly
        $('>' + settings.tabStruct, this).filter(':eq(' + settings.initial + ')').show().end().not(':eq(' + settings.initial + ')').addClass(settings.hideClass);
        $('>ul:eq(0)>li:eq(' + settings.initial + ')', this).addClass(settings.selectedClass);

        // setup auto height
        if (settings.fxAutoHeight) {
            // helper
            var tabsContents = $('>' + settings.tabStruct, container);
            var _setAutoHeight = function(reset) {
                // get tab heights in top to bottom ordered array
                var heights = $.map(tabsContents.get(), function(el) {
                    var h, jq = $(el);
                    if (reset) {
                        if ($.browser.msie6) {
                            el.style.removeExpression('behaviour');
                            el.style.height = '';
                            el.minHeight = null;
                        }
                        /* This does not work reliable
                        if (jq.is(':visible')) {
                            // prevent too much flicker
                            var clone = jq.clone().css({display: 'block', position: 'absolute', visibility: 'hidden', 'min-height': '', height: ''}).appendTo(container);
                            h = clone.get(0).offsetHeight;
                            clone.remove();
                        } else {
                            h = jq.css({'min-height': ''}).height(); // use jQuery's height() to get hidden element values
                        }*/
                        h = jq.css({'min-height': ''}).height(); // use jQuery's height() to get hidden element values
                    } else {
                        h = jq.height(); // use jQuery's height() to get hidden element values
                    }
                    return h;
                }).sort(function(a, b) {
                    return b - a;
                });
                if ($.browser.msie6) {
                    tabsContents.each(function() {
                        this.minHeight = heights[0] + 'px';
                        this.style.setExpression('behaviour', 'this.style.height = this.minHeight ? this.minHeight : "1px"'); // using an expression to not make print styles useless
                    });
                } else {
                    tabsContents.css({'min-height': heights[0] + 'px'});
                }
            };
            // call once for initialization
            _setAutoHeight();
            // trigger auto height adjustment if needed
            var cachedWidth = container.offsetWidth;
            var cachedHeight = container.offsetHeight;
            var watchFontSize = $('#tabs-watch-font-size').get(0) || $('<span id="tabs-watch-font-size">M</span>').css({display: 'block', position: 'absolute', visibility: 'hidden'}).appendTo(document.body).get(0);
            var cachedFontSize = watchFontSize.offsetHeight;
            setInterval(function() {
                var currentWidth = container.offsetWidth;
                var currentHeight = container.offsetHeight;
                var currentFontSize = watchFontSize.offsetHeight;
                if (currentHeight > cachedHeight || currentWidth != cachedWidth || currentFontSize != cachedFontSize) {
                    _setAutoHeight((currentWidth > cachedWidth || currentFontSize < cachedFontSize)); // if heights gets smaller reset min-height
                    cachedWidth = currentWidth;
                    cachedHeight = currentHeight;
                    cachedFontSize = currentFontSize;
                }
            }, 50);
        }

        // setup animations
        var showAnim = {}, hideAnim = {};
        var showSpeed, hideSpeed;
        if (settings.fxSlide || settings.fxFade) {
            if (settings.fxSlide) {
                showAnim['height'] = 'show';
                hideAnim['height'] = 'hide';
            }
            if (settings.fxFade) {
                showAnim['opacity'] = 'show';
                hideAnim['opacity'] = 'hide';
            }
            showSpeed = hideSpeed = settings.fxSpeed;
        } else {
            if (settings.fxShow) {
                showAnim = settings.fxShow;
                showSpeed = settings.fxShowSpeed || settings.fxSpeed;
            } else { // use some kind of animation to prevent browser scrolling to the tab
                showAnim['min-width'] = 0; // avoid opacity, causes flicker in Firefox
                showSpeed = settings.bookmarkable ? 50 : 1; // as little as 50 is sufficient
            }
            if (settings.fxHide) {
                hideAnim = settings.fxHide;
                hideSpeed = settings.fxHideSpeed || settings.fxSpeed;
            } else { // use some kind of animation to prevent browser scrolling to the tab
                hideAnim['min-width'] = 0; // avoid opacity, causes flicker in Firefox
                hideSpeed = settings.bookmarkable ? 50 : 1; // as little as 50 is sufficient
            }
        }

        // callbacks
        var onClick = settings.onClick, onHide = settings.onHide, onShow = settings.onShow;

        // enable history support if history plugin is present
        if (settings.bookmarkable) {
            tabs.history();
            $.ajaxHistory.initialize(function() {
                tabs.eq(settings.initial).click();
            });
        }

        // attach activateTab event, required for activating a tab programmatically
        tabs.bind('triggerTab', function() {
            var hash = this.hash;
            if ($(hash).is(':hidden') && !$(this.parentNode).is('.' + settings.disabledClass)) { // trigger only if not already visible and not if disabled

                if ($.browser.msie) {

                    $(this).click();
                    if (settings.bookmarkable) {
                        $.ajaxHistory.update(hash);
                        location.hash = hash.replace('#', '');
                    }

                } else if ($.browser.safari) {

                    // Simply setting location.hash puts Safari into the eternal load state... ugh! Submit a form instead.
                    var tempForm = $('<form action="' + hash + '"><div><input type="submit" value="h" /></div></form>').get(0); // no need to append it to the body
                    tempForm.submit(); // does not trigger the form's submit event...
                    $(this).click(); // ...thus do stuff here
                    if (settings.bookmarkable) {
                        $.ajaxHistory.update(hash);
                    }

                } else {

                    if (settings.bookmarkable) {
                        location.hash = hash.replace('#', '');
                    } else {
                        $(this).click();
                    }

                }

            }
        });

        // attach disable event, required for disabling a tab
        tabs.bind('disableTab', function() {
            $(this.parentNode).addClass(settings.disabledClass);
        });

        // disabled from settings
        if (settings.disabled && settings.disabled.length) {
            for (var i = 0, k = settings.disabled.length; i < k; i++) {
                tabs.eq(--settings.disabled[i]).trigger('disableTab').end();
            }
        };

        // attach enable event, required for reenabling a tab
        tabs.bind('enableTab', function() {
            var jq = $(this.parentNode);
            jq.removeClass(settings.disabledClass);
            if ($.browser.safari) {
                jq.fadeTo(1, 1.0).css({display: '', opacity: 1}); /* Fix disappearing tab after enabling in Safari... */
                setTimeout(function() {
                    jq.css({opacity: ''});
                }, 30); // ...do not chain and use little timeout, ugh!
            }
        });

        // attach click event
        tabs.bind('click', function() {

            var jqLi = $(this.parentNode);

            if (jqLi.is('.' + settings.disabledClass)) { // if tab is disabled stop here
                return false;
            }

            if (!jqLi.is('.' + settings.selectedClass)) {

                var toShow = $(this.hash);
                if (toShow.size() > 0) {

                    // prevent scrollbar scrolling to 0 and than back in IE7, happens only if bookmarking/history is enabled
                    if (settings.bookmarkable && $.browser.msie) {
                        var toShowId = this.hash.replace('#', '');
                        toShow.attr('id', '');
                        setTimeout(function() {
                            toShow.attr('id', toShowId); // restore id
                        }, 0);
                    }

                    var clicked = this;
                    var toHide = $('>' + settings.tabStruct + ':visible', container);

                    if (typeof onClick == 'function') {
                        // without this timeout Firefox gets really confused and calls callbacks twice...
                        setTimeout(function() {
                            onClick(clicked, toShow[0], toHide[0]);
                        }, 0);
                    }

                    // switch tab, animation prevents browser scrolling to the fragment
                    toHide.animate(hideAnim, hideSpeed, function() { //
                        $(clicked.parentNode).addClass(settings.selectedClass).siblings().removeClass(settings.selectedClass);
                        if (typeof onHide == 'function') {
                            onHide(clicked, toShow[0], toHide[0]);
                        }
                        toHide.addClass(settings.hideClass).css({display: '', overflow: '', height: '', opacity: ''}); // maintain flexible height and accessibility in print etc.
                        toShow.removeClass(settings.hideClass).animate(showAnim, showSpeed, function() {
                            toShow.css({overflow: '', height: '', opacity: ''}); // maintain flexible height and accessibility in print etc.
                            if ($.browser.msie) {
                                toHide[0].style.filter = '';
                                toShow[0].style.filter = '';
                            }
                            if (typeof onShow == 'function') {
                                onShow(clicked, toShow[0], toHide[0]);
                            }
                        });
                    });

                } else {
                    alert('There is no such container.');
                }

            }

            // Set scrollbar to saved position - need to use timeout with 0 to prevent browser scroll to target of hash
            var scrollX = window.pageXOffset || document.documentElement && document.documentElement.scrollLeft || document.body.scrollLeft || 0;
            var scrollY = window.pageYOffset || document.documentElement && document.documentElement.scrollTop || document.body.scrollTop || 0;
            setTimeout(function() {
                window.scrollTo(scrollX, scrollY);
            }, 0);

            this.blur(); // prevent IE from keeping other link focussed when using the back button

            return settings.bookmarkable;

        });
    });

};

/**
 * Activate a tab programmatically with the given position (no zero-based index),
 * as if the tab itself were clicked.
 *
 * @example $('#container').triggerTab(2);
 * @desc Activate the second tab of the tab interface contained in <div id="container">.
 * @example $('#container').triggerTab(1);
 * @desc Activate the first tab of the tab interface contained in <div id="container">.
 * @example $('#container').triggerTab();
 * @desc Activate the first tab of the tab interface contained in <div id="container">.
 *
 * @param Number position An integer specifying the position of the tab (no zero-based
 *                        index) to be activated. If this parameter is omitted, the first
 *                        tab will be activated.
 * @type jQuery
 *
 * @name triggerTab
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Disable a tab, so that clicking it has no effect.
 *
 * @example $('#container').disableTab(2);
 * @desc Disable the second tab of the tab interface contained in <div id="container">.
 *
 * @param Number position An integer specifying the position of the tab (no zero-based
 *                        index) to be disabled. If this parameter is omitted, the first
 *                        tab will be disabled.
 * @type jQuery
 *
 * @name disableTab
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Enable a tab that has been disabled.
 *
 * @example $('#container').enableTab(2);
 * @desc Enable the second tab of the tab interface contained in <div id="container">.
 *
 * @param Number position An integer specifying the position of the tab (no zero-based
 *                        index) to be enabled. If this parameter is omitted, the first
 *                        tab will be enabled.
 * @type jQuery
 *
 * @name enableTab
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

var tabEvents = ['triggerTab', 'disableTab', 'enableTab'];
for (var i = 0; i < tabEvents.length; i++) {
    $.fn[tabEvents[i]] = (function(tabEvent) {
        return function(tabIndex) {
            return this.each(function() {
                var i = tabIndex && tabIndex > 0 && tabIndex - 1 || 0; // fall back to 0
                $('>ul:eq(0)>li>a', this).eq(i).trigger(tabEvent);
            });
        };
    })(tabEvents[i]);
}

})(jQuery);