(function($){
/*jslint nomen: true , devel: true*/

    var CMSFileBrowser = {},
        info = {};

    $(document).ready(function() {
        CMSFileBrowser.load();
    });

    CMSFileBrowser.load = function() {
        CMSFileBrowser.enable_sendValue();
        CMSFileBrowser.enable_toggleGrid();
        CMSFileBrowser.enable_filetypeFilter();
    };

    CMSFileBrowser.enable_sendValue = function() {

        $('a.js-trigger-insert').click(function(e) {

            var $this = $(this),
                $elm = $this.closest('li'),
                $data = $elm.data(),
                $ext = $data.fbExt,
                file = $this.attr('href');

            e.preventDefault();

	    var inst = $('html').data('cmsfp-inst');
	    console.log('inst is '+inst);
	    var selector = '[data-cmsfp-instance="'+inst+'"]';
	    console.debug('selector is '+selector);
	    var elem = parent.$(selector);
	    if( elem ) {
		elem.trigger('cmsfp:change',file);
	    }
        });
    };

    CMSFileBrowser.enable_toggleGrid = function(){

       $('.filepicker-view-option .js-trigger').on('click', function(e) {
           var $trigger = $(this),
               $container = $('#filepicker-items'),
               $info = $('.filepicker-file-details');

           $('.filepicker-view-option .js-trigger').removeClass('active');
           $trigger.addClass('active');

           if ($trigger.hasClass('view-grid')) {
                $container.removeClass('list-view').addClass('grid-view');
                $info.addClass('visuallyhidden');
            } else if ($trigger.hasClass('view-list')) {
                $container.removeClass('grid-view').addClass('list-view');
                $info.removeClass('visuallyhidden');
            }
        });
    };

    CMSFileBrowser.enable_filetypeFilter = function() {

        if ($('.filepicker-type-filter').length < 1) return;

        var $items = $('#filepicker-items > li:not(.filepicker-item-heading):not(.dir)'),
            $container = $('#filepicker-items'),
            $trigger,
            $data;

        $('.filepicker-type-filter .js-trigger').on('click', function(e) {
            var $trigger = $(this),
                $data = $trigger.data();

                $('.filepicker-type-filter .js-trigger').removeClass('active');
                $trigger.addClass('active');

                if ($trigger.hasClass('active') && $data.fbType !== 'reset') {
                    $items.hide(200).removeClass('visible');
                    $('li.' + $data.fbType).show(200).addClass('visible');
                } else {
                    $items.show(200).addClass('visible');
                }
        });
    };

})(jQuery);
