jQuery(document).ready(function () {
    // .banner slider 
    /*
     * this is just a quick sample slider
     * If you need a slider with repsonsive support look at some nice jQuery solutions like (or use a search engine):
     * http://dmmalam.github.com/Responsly.js/
     * http://swipejs.com/   
     * http://marktyrrell.com/labs/blueberry/
     */
    // trying to resize image wrapping div so it fits current window size                           
    $('.banner-image').height($('.banner-image div').height());
    $(window).resize(function () {
        $('.banner-image').height($('.banner-image div').height());
    });
    // fade images 
    $('.banner-image > div:gt(0)').hide();
    setInterval(function () {
        $('.banner-image > div:first').fadeOut(1000).next().fadeIn(1000).end().appendTo('.banner-image');
    }, 6000);
    
    // check for common mobile devices and change menu behavior 
    /*
     * Do not fully rely on below detection
     * This is just a quick detection of common devices, but as you know there are more around there
     * You should look at different solutions depending on your current project
     * there is nothing perfect that works out of the box, pick what best fits the task
     */                    
    if (navigator.userAgent.match(/(Android|iPhone|iPad|iPod|Blackberry|Dolphin|IEMobile|Kindle|Mobile|MMP|MIDP|Pocket|PSP|Symbian|Smartphone|Sreo|Up.Browser|Up.Link|Vodafone|WAP|Opera Mini)/)) {
        
        // if a device matches above prevent default hyperlink "click" event and show submenu
        /*  Original code from: https://github.com/mehrpadin/Superfish-for-Drupal/sftouchscreen.js
         * sf-Touchscreen v1.0b - Provides touchscreen compatibility for the jQuery Superfish plugin.
         *
         * Developer's note:
         * Built as a part of the Superfish project for Drupal (http://drupal.org/project/superfish) 
         * Found any bug? have any cool ideas? contact me right away! http://drupal.org/user/619294/contact
         *
         * jQuery version: 1.3.x or higher.
         *
         * Dual licensed under the MIT and GPL licenses:
         *  http://www.opensource.org/licenses/mit-license.php
         *  http://www.gnu.org/licenses/gpl.html
         */
        $('#nav ul').each(function () {
            // Select hyperlinks from parent menu items.
            $(this).find('>li>ul').parent('li').children('a').each(function () {
                var $item = $(this);
                // append close button to sublevel ul
                $item.next('ul').append('<span class="close-button">close</span>');
                // if close button is clicked/touched remove active class from parent and hide submenu
                $('.close-button').click(function() {
                	if ($item.hasClass('active')) {
                		// remove class
                        $item.removeClass('active');
                    }
                    // hide submenu
                    $(this).parent('ul').hide();
                });
                // No .toggle() here as it's not possible to reset it.
                $item.click(function(e) {
                	// show submenu
                	$(this).next('ul').show();
                    // Already clicked? proceed to the URI.
                    if ($item.hasClass('active')) {
                        var $uri = $item.attr('href');
                        window.location = $uri;
                    } else {
                        e.preventDefault();
                        $item.addClass('active');
                    }
                }).parent('li').mouseleave(function () {
                    // So, we reset everything.
                    $item.removeClass('active');
                });
            });
        });
    } // end device detection
    // add class to first and last child
    $('#nav li:first-child').addClass('first');
    $('#nav li:last-child').addClass('last');
});