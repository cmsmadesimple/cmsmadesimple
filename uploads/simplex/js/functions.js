/* -----------------------------------------------  
  Detect few Mobile device
  
  This a !!!!DEMO!!! do not rely on this detection!!!
  If you want it to work perfect use proper plugins
  or functions available on the interwebs. 
  For example use Modernizr (http://modernizr.com/)
  and check against Modernizr.touch even though
  one can touch a Screen to.
  
------------------------------------------------ */
function detectMobile() {
           
        if (navigator.userAgent.match(/(Android|iPhone|iPad|iPod|Blackberry|Dolphin|IEMobile|Kindle|Mobile|MMP|MIDP|Pocket|PSP|Symbian|Smartphone|Sreo|Up.Browser|Up.Link|Vodafone|WAP|Opera Mini)/)) {
            
            return true;
            
        } else {
            
            return  false;
        }
}

/* -----------------------------------------------  
  Touch Menu
  
  This a !!!!DEMO!!! do not rely on this!!!
  It is intended as demonstration purpose, there is 
  plethora of different touch and mobile capable
  menus in the world of www, pick one that is suitable
  for your project.
  
------------------------------------------------ */

var mobileMenu = {

    init : function() {

        var menuItem = $('#nav ul > li.parent');

        menuItem.each(function() {

            var currentItem = $(this);

            this.addEventListener('touchstart', function(e) {
                if (e.touches.length === 1) {
                    e.stopPropagation();

                    // toggle class for dropdown
                    if (!currentItem.hasClass('active')) {

                        // prevent opening link on first touch
                        if (e.target === this || e.target.parentNode === this) {
                            e.preventDefault();
                        }

                        // hide open dropdowns
                        menuItem.removeClass('active');
                        menuItem.not('.active').children('ul').hide();
                        
                        // show current touched dropdown
                        currentItem.addClass('active');
                        currentItem.children('ul').show();
                        currentItem.children('ul').append('<span class="close-button">close</span>');
                        
                        closeButton = $('.close-button');

                        // hide dropdown on touch outside or close button
                        closeDropdown = function(e) {
                            e.stopPropagation();

                            currentItem.removeClass('active');
                            currentItem.not('.active').children('ul').hide();
                            
                            closeButton.hide();
                            document.removeEventListener('touchstart', closeDropdown);
                        }
                        
                        document.addEventListener('touchstart', closeDropdown);
                        closeButton.on('click', closeDropdown);
                    }
                }
            }, false);
        });
    }
}

/* -----------------------------------------------  
  Sample image Slider
  
  AGAIN this a !!!!DEMO!!! do not rely on this!!!
  Slider in Simplex is plain simple and not something you
  should consider as perfect for production use.
  There are numerous jQuery plugins for this task,
  so pick one you like and use it!
  
  If you need a slider with responsive support look at 
  some nice jQuery solutions like (or use a search engine!!):
  
  http://dmmalam.github.com/Responsly.js/
  http://swipejs.com/
  http://marktyrrell.com/labs/blueberry/ 
  
  No need for bug reports on this! 
  
------------------------------------------------ */

var simpleSlide = {
    
    init: function() {
        
        var imageHolder = $('.banner-image'),
            textHolder = $('.banner-text');
            
            // Attempt to resize image placeholder div ( could fail to so do not rely on it)
            imageHolder.height(textHolder.height());
            
            // do same on window resize
            $(window).resize(function() {
                imageHolder.height(textHolder.height());
            });
            
            // Actual slides
            
            $('.banner-image > div:gt(0)').hide(); // hide if not first
            
            // set slide interval
            setInterval(function() {
                $('.banner-image > div:first')
                    .fadeOut(1000)
                    .next()
                    .fadeIn(1000)
                    .end()
                    .appendTo('.banner-image');
            }, 6000);
    }
}

/* -----------------------------------------------  
  Menu generic
  Adds first and last classes to first and last 
  list elemnt in menu
------------------------------------------------ */

var listClass = {
    
    init: function() {
        
        $('#nav li:first-child').addClass('first');
        $('#nav li:last-child').addClass('last');
    }
}

/* -----------------------------------------------  
  Beam me up, Scotty!
------------------------------------------------ */

$(function() {
    if (detectMobile()) {
        mobileMenu.init();
    }
    
    simpleSlide.init();
    listClass.init();
    
});