/**
 * History/Remote 0.2 - jQuery plugin for enabling history and bookmarking in Ajax driven
 *                      applications in an unobtrusive and accessible manner ("Hijax").
 *
 * http://stilbuero.de/jquery/history/
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

(function($) { // simulate block scope

/**
 * Initialize the history event listener. Subsequent calls will not result in additional listeners.
 * Should be called soonest when the DOM is ready, because it will add an iframe in IE to enable
 * history support.
 *
 * @example $.ajaxHistory.initialize();
 *
 * @type undefined
 *
 * @name $.ajaxHistory.initialize()
 * @cat Plugins/Remote
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
$.ajaxHistory = new function() {

    var RESET_EVENT = 'historyReset';

    var _currentHash = location.hash;
    var _intervalId = null;
    var _observeHistory; // define outside if/else required by Opera

    this.update = function() { }; // empty function body for graceful degradation

    // create custom event for state reset
    var _defaultReset = function() {
        var output = $('.remote-output');
        if (output.children().size() > 0) {
            output.empty();
        }
    };
    $(document).bind(RESET_EVENT, _defaultReset);

    if ($.browser.msie) {

        var _historyIframe; // for IE

        // add hidden iframe
        $(function() {
            _historyIframe = $('<iframe style="display: none;"></iframe>').appendTo(document.body).get(0);
            var iframe = _historyIframe.contentWindow.document;
            iframe.open();
            iframe.close();
            iframe.location.hash = _currentHash.replace('#', '');
        });

        this.update = function(hash) {
            _currentHash = hash;
            var iframe = _historyIframe.contentWindow.document;
            iframe.open();
            iframe.close();
            iframe.location.hash = hash.replace('#', '');
        };

        _observeHistory = function() {
            var iframe = _historyIframe.contentWindow.document;
            var iframeHash = iframe.location.hash;
            if (iframeHash != _currentHash) {
                _currentHash = iframeHash;
                if (iframeHash != '#') {
                    // order does matter, set location.hash after triggering the click...
                    $('a[@href$="' + iframeHash + '"]').click();
                    location.hash = iframeHash;
                } else {
                    location.hash = '';
                    $(document).trigger(RESET_EVENT);
                }
            }
        };

    } else if ($.browser.mozilla || $.browser.opera) {

        this.update = function(hash) {
            _currentHash = hash;
        };

        _observeHistory = function() {
            if (location.hash) {
                if (_currentHash != location.hash) {
                    _currentHash = location.hash;
                    $('a[@href$="' + _currentHash + '"]').click();
                }
            } else if (_currentHash) {
                _currentHash = '';
                $(document).trigger(RESET_EVENT);
            }
        };

    } else if ($.browser.safari) {

        var _backStack, _forwardStack, _addHistory; // for Safari

        // etablish back/forward stacks
        $(function() {
            _backStack = [];
            _backStack.length = history.length;
            _forwardStack = [];

        });
        var isFirst = false;
        _addHistory = function(hash) {
            _backStack.push(hash);
            _forwardStack.length = 0; // clear forwardStack (true click occured)
            isFirst = false;
        };

        this.update = function(hash) {
            _currentHash = hash;
            _addHistory(_currentHash);
        };

        _observeHistory = function() {
            var historyDelta = history.length - _backStack.length;
            if (historyDelta) { // back or forward button has been pushed
                isFirst = false;
                if (historyDelta < 0) { // back button has been pushed
                    // move items to forward stack
                    for (var i = 0; i < Math.abs(historyDelta); i++) _forwardStack.unshift(_backStack.pop());
                } else { // forward button has been pushed
                    // move items to back stack
                    for (var i = 0; i < historyDelta; i++) _backStack.push(_forwardStack.shift());
                }
                var cachedHash = _backStack[_backStack.length - 1];
                $('a[@href$="' + cachedHash + '"]').click();
                _currentHash = location.hash;
            } else if (_backStack[_backStack.length - 1] == undefined && !isFirst) {
                // back button has been pushed to beginning and URL already pointed to hash (e.g. a bookmark)
                // document.URL doesn't change in Safari
                if (document.URL.indexOf('#') >= 0) {
                    $('a[@href$="' + '#' + document.URL.split('#')[1] + '"]').click();
                } else {
                    $(document).trigger(RESET_EVENT);
                }
                isFirst = true;
            }
        };

    }

    this.initialize = function(callback) {
        // custom callback to reset app state (no hash in url)
        if (typeof callback == 'function') {
            $(document).unbind(RESET_EVENT, _defaultReset).bind(RESET_EVENT, callback);
        }
        // look for hash in current URL (not Safari)
        if (location.hash && typeof _addHistory == 'undefined') {
            $('a.remote[@href$="' + location.hash + '"]').click();
        }
        // start observer
        if (_observeHistory && _intervalId == null) {
            _intervalId = setInterval(_observeHistory, 200); // Safari needs at least 200 ms
        }
    };

};

/**
 * Implement Ajax driven links in a completely unobtrusive and accessible manner (also known as "Hijax")
 * with support for important usability issues like the web browser's back and forward button and bookmarking.
 *
 * The link's href attribute is altered to a hash, such as "#remote-1", so that it updates the browser's
 * current URL with this anchor hash, whereas the former value of the attribute is used to load content via
 * XmlHttpRequest and update the specified element. If no target element is found, a new div element will be
 * created and appended to the body to load the content into. The link triggers a history event on click to
 * maintain the browsers history.
 *
 * jQuery's Ajax implementation adds a custom request header of the form "X-Requested-With: XmlHttpRequest"
 * to any Ajax request so that the called page can distinguish between a standard and a XmlHttpRequest.
 *
 * @example $('a.remote').remote('#output');
 * @before <a class="remote" href="/path/to/content.html">Update</a>
 * @result <a class="remote" href="#remote-1">Update</a>
 * @desc Alter a link of the class "remote" to an Ajax-enhanced link and let it load content from "/path/to/content.html" via XmlHttpRequest
 *       into an element with the id "output".
 *
 * @param String expr A string containing a CSS selector or basic XPath specifying the element to load
 *                    content into via XmlHttpRequest.
 * @type jQuery
 *
 * @name remote
 * @cat Plugins/Remote
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Implement Ajax driven links in a completely unobtrusive and accessible manner (also known as "Hijax")
 * with support for important usability issues like the web browser's back and forward button and bookmarking.
 *
 * The link's href attribute is altered to a hash, such as "#remote-1", so that it updates the browser's
 * current URL with this anchor hash, whereas the former value of the attribute is used to load content via
 * XmlHttpRequest and update the specified element. If no target element is found, a new div element will be
 * created and appended to the body to load the content into. The link triggers a history event on click to
 * maintain the browsers history.
 *
 * jQuery's Ajax implementation adds a custom request header of the form "X-Requested-With: XmlHttpRequest"
 * to any Ajax request so that the called page can distinguish between a standard and a XmlHttpRequest.
 *
 * @example $('a.remote').remote( $('#output > div')[0] );
 * @before <a class="remote" href="/path/to/content.html">Update</a>
 * @result <a class="remote" href="#remote-1">Update</a>
 * @desc Alter a link of the class "remote" to an Ajax-enhanced link and let it load content from
 *       "/path/to/content.html" via XmlHttpRequest into an element.
 *
 * @param Element elem A DOM element to load content into via XmlHttpRequest.
 * @type jQuery
 *
 * @name remote
 * @cat Plugins/Remote
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
$.fn.remote = function(output) {
    var target = $(output).size() && $(output) || $('<div></div>').appendTo('body');
    target.addClass('remote-output');
    return this.each(function(i) {
        var remoteURL = this.href;
        var hash = '#remote-' + ++i;
        this.href = hash;
        $(this).click(function(e) {
            var trueClick = e.clientX; // add to history only if true click occured, not a triggered click
            target.load(remoteURL, function() {
                if (trueClick) {
                    $.ajaxHistory.update(hash); // setting hash in callback is required to make it work in Safari
                }
            });
        });
    });
};

// Internal, used to enable history for the Tabs plugin.
$.fn.history = function() {
    return this.click(function(e) {
        var trueClick = e.clientX; // add to history only if true click occured, not a triggered click
        if (trueClick) { // add to history only if true click occured, not a triggered click
            $.ajaxHistory.update(this.hash);
        }
    });
};

})(jQuery);