(function (global, $) {
    'use strict';

    /**
     * Setting SX Object (Namespace) for Simplex theme functions and variables
     */
    var SX = global.SX = {};

    /** --------------------------------------------------------------------
        Setting few helper variables, maybe someone finds this stuff helpful
        -------------------------------------------------------------------- */

    /**
     * set a variable if we need touch device detection
     * @return false|true
     */
    SX.isTouch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0) || window.DocumentTouch && document instanceof DocumentTouch);

    /**
     * set a variable with user agent check and change to lower case
     * @return String - lower case User Agent (https://developer.mozilla.org/en-US/docs/Web/API/NavigatorID.userAgent)
     */
    SX.UA = navigator.userAgent.toLowerCase();

    /**
     * set a variable with a basic user agent check against few mobile devices, do not fully rely on this
     * @return null|object
     */
    SX.isMobile = SX.UA.match(/android|ipod|ipad|ipad|blackberry|blazer|dolphin|palmsource|fennec|gobrowser|iemobile|opera mobi|opera mini|skyfire|kindle|mobile|mmp|midp|pocket|psp|symbian|smartphone|sreo|up.browser|up.link|vodafone|wap/i);

    /**
     * set a variable for window screen size, can be useful if one needs something based on device screen size
     * Example:
     * if (SX.viewportWidth > 768) {
     *     // do something...
     * }
     * @return numeric - Screen width number
     */
    SX.viewportWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

    $(document).ready(function () {

        // add class to body if mobile device detected (just for convenience)
        if (SX.isMobile) {
            $('body').addClass('mobile-device');
        }

        /**
         * add Touch polyfill for DAMN Internet Explorer, i do not have a Windows Phone or Windows 8 device with touch support
         * therefore this is untested and installing a VM with Windows 8 and Windows Phone Emulator is something i do not intended
         * to do just to test this theme, if touch events do not work there, sorry, bad karma.
         */
        if (window.navigator.msPointerEnabled) {

            var tchr = document.createElement('script');
                tchr.src = './uploads/simplex/js/touchr.js';
                tchr.type = 'text/javascript';
            if ( typeof tchr.async !== 'undefined' ) {
                tchr.async = true;
            }

            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(tchr);
        }

        // initialize touch menu function
        SX.init.mobileMenu();
        // initalize swipe function
        SX.init.swipePage();
        // intialize scroll to top function
        SX.init.scrollTop();
        // initalize sequence slider in header
        SX.sequenceOptions = {
            nextButton: true,
            prevButton: true,
            pagination: true,
            animateStartingFrameIn: true,
            autoPlay: true,
            autoPlayDelay: 6000,
            preloader: true,
            preloadTheseFrames: [1]
        };

        SX.sequenceInit = $('#sx-slides').sequence(SX.sequenceOptions).data('sequence');
    });

    /** -------------------------------------------------------------------
        Functions and Logic
        ------------------------------------------------------------------- */

    SX.init = {

        /**
         * @function mobileMenu
         * @description Handles first touch event on parent menu elements to open a sub level menu
         */

        mobileMenu: function () {

            var menuItem = $('#main-menu li.parent');

            // add class to body if it's touch device, shows parent indicator arrows
            if (SX.isTouch) {
                $('body').addClass('touch-device');
            }

            if (!window.attachEvent && window.addEventListener && SX.isMobile) {

                menuItem.each(function () {

                    var currentItem = $(this),
                        parentItem = currentItem.parents('li').addBack().first(),
                        event = 'click'; // we set as default click event

                    if (SX.isTouch && !((SX.UA.indexOf('android') > -1 && SX.UA.indexOf('applewebkit') > -1) && !(SX.UA.indexOf('chrome') > -1))) {
                        event = 'touchstart'; // if it's touch device and is NOT default android browser do touchstart event
                    };


                    this.addEventListener(event, function (e) {
                        // toggle class for dropdown
                        if (!currentItem.hasClass('active')) {

                            // hide open dropdowns
                            menuItem.removeClass('active');
                            // prevent opening link on first touch
                            if (e.target === this || e.target.parentNode === this || e.target.firstChild === this) {
                                e.preventDefault();
                            }

                            // show current touched dropdown
                            parentItem.addClass('active');
                            currentItem.addClass('active')
                                .children('ul').slideDown();

                            // hide dropdown on touch outside
                            var closeDropdown = function (e) {
                                e.stopPropagation();

                                currentItem.not('.active').children('ul').hide();
                                document.removeEventListener(event, closeDropdown);
                            };

                            document.addEventListener(event, closeDropdown);
                        }
                    }, false);
                });
            }
        },

        /**
         * @function swipePage
         * @description handles detection of swipe action and changes target location based on direction.
         *              Target location is based on href value of <link> tag with rel='next' and rel='prev'.
         */
        swipePage: function () {

            var nextPage = $('link[rel="next"]').attr('href'),
                prevPage = $('link[rel="prev"]').attr('href'),
                touch,
                direction,
                start = {},
                end = {},
                threshold = 200,
                restraint = 100,
                allowedTime = 800,
                elapsedTime,
                startTime;

            $(document.body)
                .bind('touchstart', function (e) {
                    touch = e.originalEvent.touches[0];
                    start.x = touch.pageX;
                    start.y = touch.pageY;
                    startTime = new Date().getTime();
                })
                .bind('touchmove', function (e) {
                    direction = null;
                    touch = e.originalEvent.changedTouches[0];
                    end.x = touch.pageX;
                    end.y = touch.pageY;

                    if (Math.abs(end.x - start.x) >= 8 && Math.abs(end.y - start.y) <= 20) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                })
                .bind('touchend', function (e) {
                    touch = e.originalEvent.changedTouches[0];
                    end.x = touch.pageX;
                    end.y = touch.pageY;
                    elapsedTime = new Date().getTime() - startTime;

                    if (elapsedTime <= allowedTime) {
                        if (Math.abs(start.x - end.x) >= threshold && Math.abs(start.y - end.y) <= restraint) {
                            direction = (start.x - end.x) > 0 ? 'left' : 'right';
                        }
                    }
                    if (direction === 'left' && nextPage) {
                        window.location = nextPage;
                    } else if (direction === 'right' && prevPage) {
                        window.location = prevPage;
                    }
                });
        },

        /**
         * @function scrollTop
         * @description Scrolls content back to top when clicked on #scroll-top link and #main element exists
         */
        scrollTop: function () {

            var trigger = $('#scroll-top'),
                target = '#main',
                doc = (document.compatMode === 'CSS1Compat') ? document.documentElement : 'html, body'; // prevent deprecated warning

            if ($('#main').length > 0) {
                trigger.click(function (event) {

                    if (SX.UA.match(/android|ipod|ipad|iphone/i)) {
                        window.scrollTo(0);
                    } else {
                        $(doc).stop().animate({
                            scrollTop: $(target).offset().top
                        }, 500);
                    }

                    event.preventDefault();
                });
            }
        }
    };

}(this, jQuery));